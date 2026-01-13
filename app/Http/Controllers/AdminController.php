<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics only.
     */
    public function dashboard(): View
    {
        // Calculate stats
        $totalSubmissions = Submission::count();
        $totalUsers = User::count();
        $totalStudents = Student::count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        // Submission stats
        $submissionStats = [
            'total' => $totalSubmissions,
            'approved' => Submission::where('status', 'approved')->count(),
            'under_review' => Submission::whereIn('status', ['under_review', 'submitted'])->count(),
            'draft' => Submission::where('status', 'draft')->count(),
            'rejected' => Submission::where('status', 'rejected')->count(),
        ];

        // Calculate progress stats
        $allSubmissions = Submission::with('documents')->get();
        $completedCount = $allSubmissions->filter(function($submission) {
            return $submission->getProgressPercentage() === 100 && $submission->documents->count() > 0;
        })->count();
        
        $activeCount = $allSubmissions->filter(function($submission) {
            $progress = $submission->getProgressPercentage();
            return $progress < 100 || $submission->documents->count() === 0;
        })->count();

        // Recent submissions (last 5)
        $recentSubmissions = Submission::with('student.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'submissionStats' => $submissionStats,
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalAdmins' => $totalAdmins,
            'completedCount' => $completedCount,
            'activeCount' => $activeCount,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }

    /**
     * Display verifikasi pengajuan page with submissions list.
     */
    public function verifikasiPengajuan(Request $request): View
    {
        $viewType = $request->get('view', 'active'); // 'active' or 'completed'
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'asc'); // 'asc' or 'desc'

        // Base query
        $query = Submission::with('student.user', 'documents');

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($studentQuery) use ($search) {
                    $studentQuery->where('nim', 'like', "%{$search}%")
                                 ->orWhere('nama', 'like', "%{$search}%")
                                 ->orWhereHas('user', function($userQuery) use ($search) {
                                     $userQuery->where('name', 'like', "%{$search}%");
                                 });
                });
            });
        }

        // Get all submissions first (we need to calculate progress)
        $allSubmissions = $query->orderBy('created_at', $sort)->get();

        // Filter by progress percentage
        $filteredSubmissions = $allSubmissions->filter(function($submission) use ($viewType) {
            $progress = $submission->getProgressPercentage();
            if ($viewType === 'completed') {
                // Show only 100% progress submissions that have at least one document
                return $progress === 100 && $submission->documents->count() > 0;
            } else {
                // Show active submissions (progress < 100% or no documents)
                return $progress < 100 || $submission->documents->count() === 0;
            }
        });

        // Paginate manually
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $filteredSubmissions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $submissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $filteredSubmissions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.verifikasi-pengajuan', [
            'submissions' => $submissions,
            'viewType' => $viewType,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /**
     * View submission details.
     */
    public function viewSubmission(Submission $submission): View
    {
        $submission->load('student.user', 'documents');

        return view('admin.submission-detail', [
            'submission' => $submission,
        ]);
    }

    /**
     * Update document status.
     */
    public function updateDocumentStatus(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:approved,revision,rejected',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $document->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
        ]);

        \App\Models\Activity::log('verify', 'Memverifikasi dokumen: ' . $document->type . ' - Status: ' . $request->status, 'Document', $document->id);

        // Update submission status based on documents
        $submission = $document->submission;
        $allApproved = $submission->documents()
            ->where('status', '!=', 'approved')
            ->doesntExist();

        if ($allApproved && $submission->documents()->count() > 0) {
            $submission->update(['status' => 'approved']);
            \App\Models\Activity::log('approve', 'Semua dokumen disetujui, pengajuan diterima', 'Submission', $submission->id);
        } elseif (in_array($submission->status, ['draft', 'submitted'])) {
            $submission->update(['status' => 'under_review']);
        }

        return redirect()->back()
            ->with('success', 'Status dokumen berhasil diperbarui!');
    }

    /**
     * Batch update multiple documents status.
     */
    public function batchUpdateDocuments(Request $request, Submission $submission)
    {
        \Log::info('batchUpdateDocuments called', [
            'submission_id' => $submission->id,
            'request_documents' => $request->input('documents'),
        ]);

        $request->validate([
            'documents' => 'required|array',
            'documents.*.status' => 'nullable|in:approved,revision,rejected',
            'documents.*.feedback' => 'nullable|string|max:1000',
        ]);

        $documents = $request->input('documents');
        $updatedCount = 0;

        foreach ($documents as $documentId => $data) {
            // Skip if status is not set
            if (!isset($data['status']) || empty($data['status'])) {
                continue;
            }

            $document = Document::findOrFail($documentId);
            
            // Verify document belongs to this submission
            if ($document->submission_id !== $submission->id) {
                continue;
            }

            \Log::info('Updating document', [
                'document_id' => $document->id,
                'old_status' => $document->status,
                'new_status' => $data['status'],
            ]);

            $document->update([
                'status' => $data['status'],
                'feedback' => $data['feedback'] ?? null,
            ]);

            \Log::info('Document updated', [
                'document_id' => $document->id,
                'status_now' => $document->status,
            ]);

            \App\Models\Activity::log('verify', 'Memverifikasi dokumen: ' . $document->type . ' - Status: ' . $data['status'], 'Document', $document->id);
            $updatedCount++;
        }

        // Update submission status based on all documents
        $submission->refresh();
        $allApproved = $submission->documents()
            ->where('status', '!=', 'approved')
            ->doesntExist();

        if ($allApproved && $submission->documents()->count() > 0) {
            $submission->update(['status' => 'approved']);
            \App\Models\Activity::log('approve', 'Semua dokumen disetujui, pengajuan diterima', 'Submission', $submission->id);
        } elseif (in_array($submission->status, ['draft', 'submitted'])) {
            $submission->update(['status' => 'under_review']);
        }

        return redirect()->back()
            ->with('success', "Total {$updatedCount} dokumen berhasil diperbarui!");
    }

    /**
     * Download document.
     */
    public function downloadDocument(Document $document)
    {
        return \Storage::download($document->file_path, $document->name);
    }

    /**
     * Display users management page.
     */
    public function users(Request $request): View
    {
        $search = $request->get('search', '');

        $query = User::with('student');

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('student', function($studentQuery) use ($search) {
                      $studentQuery->where('nim', 'like', "%{$search}%")
                                   ->orWhere('nama', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    /**
     * Show create user form.
     */
    public function createUser(): View
    {
        return view('admin.create-user');
    }

    /**
     * Store new user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,student',
            // Student fields (conditional)
            'nim' => 'required_if:role,student|nullable|string|max:20|unique:students,nim',
            'nama' => 'required_if:role,student|nullable|string|max:255',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'total_sks' => 'nullable|integer|min:0',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // If student role, create student record
        if ($request->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'nama' => $request->nama ?? $request->name,
                'ipk' => $request->ipk ?? 0,
                'total_sks' => $request->total_sks ?? 0,
                'status_kelulusan' => 'belum_lulus',
            ]);
        }

        \App\Models\Activity::log('create_user', 'Membuat user baru: ' . $user->name . ' (Role: ' . $user->role . ')', 'User', $user->id);

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Show edit user form.
     */
    public function editUser(User $user): View
    {
        $user->load('student');
        
        return view('admin.edit-user', [
            'user' => $user,
        ]);
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,student',
            // Student fields (conditional)
            'nim' => 'required_if:role,student|nullable|string|max:20|unique:students,nim,' . ($user->student ? $user->student->id : 'NULL') . ',id',
            'nama' => 'required_if:role,student|nullable|string|max:255',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'total_sks' => 'nullable|integer|min:0',
        ]);

        // Update user
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Handle student record
        if ($request->role === 'student') {
            if ($user->student) {
                // Update existing student record
                $user->student->update([
                    'nim' => $request->nim,
                    'nama' => $request->nama ?? $request->name,
                    'ipk' => $request->ipk ?? $user->student->ipk ?? 0,
                    'total_sks' => $request->total_sks ?? $user->student->total_sks ?? 0,
                ]);
            } else {
                // Create new student record if user was admin before
                Student::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'nama' => $request->nama ?? $request->name,
                    'ipk' => $request->ipk ?? 0,
                    'total_sks' => $request->total_sks ?? 0,
                    'status_kelulusan' => 'belum_lulus',
                ]);
            }
        } else {
            // If role changed from student to admin, delete student record
            if ($user->student) {
                $user->student->delete();
            }
        }

        \App\Models\Activity::log('update_user', 'Mengupdate user: ' . $user->name . ' (Role: ' . $user->role . ')', 'User', $user->id);

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $userName = $user->name;
        $userId = $user->id;
        
        // Log before deleting
        \App\Models\Activity::log('delete_user', 'Menghapus user: ' . $userName, 'User', $userId);
        
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show import users form.
     */
    public function showImportUsers(): View
    {
        return view('admin.import-users');
    }

    /**
     * Import users from CSV/Excel file.
     */
    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // 5MB max
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            if ($extension === 'csv') {
                // Handle CSV file
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle); // Get header row
                
                // Expected columns: name, email, password, role, nim (optional), nama (optional), ipk (optional), total_sks (optional)
                $rowNumber = 1;
                
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map CSV columns (assuming: name, email, password, role, nim, nama, ipk, total_sks)
                    $data = array_combine($header, $row);
                    
                    // Clean data
                    $name = trim($data['name'] ?? '');
                    $email = trim($data['email'] ?? '');
                    $password = trim($data['password'] ?? 'password123'); // Default password
                    $role = strtolower(trim($data['role'] ?? 'student'));
                    $nim = trim($data['nim'] ?? '');
                    $nama = trim($data['nama'] ?? $name);
                    $ipk = isset($data['ipk']) && $data['ipk'] !== '' ? floatval($data['ipk']) : 0;
                    $totalSks = isset($data['total_sks']) && $data['total_sks'] !== '' ? intval($data['total_sks']) : 0;

                    // Validate required fields
                    if (empty($name) || empty($email)) {
                        $errors[] = "Baris {$rowNumber}: Nama dan Email wajib diisi";
                        $errorCount++;
                        continue;
                    }

                    // Validate role
                    if (!in_array($role, ['admin', 'student'])) {
                        $errors[] = "Baris {$rowNumber}: Role harus 'admin' atau 'student'";
                        $errorCount++;
                        continue;
                    }

                    // Check if email already exists
                    if (User::where('email', $email)->exists()) {
                        $errors[] = "Baris {$rowNumber}: Email {$email} sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Create user
                    try {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => Hash::make($password),
                            'role' => $role,
                        ]);

                        // If student role, create student record
                        if ($role === 'student') {
                            if (empty($nim)) {
                                $errors[] = "Baris {$rowNumber}: NIM wajib diisi untuk role student";
                                $errorCount++;
                                $user->delete();
                                continue;
                            }

                            // Check if NIM already exists
                            if (Student::where('nim', $nim)->exists()) {
                                $errors[] = "Baris {$rowNumber}: NIM {$nim} sudah terdaftar";
                                $errorCount++;
                                $user->delete();
                                continue;
                            }

                            Student::create([
                                'user_id' => $user->id,
                                'nim' => $nim,
                                'nama' => $nama,
                                'ipk' => $ipk,
                                'total_sks' => $totalSks,
                                'status_kelulusan' => 'belum_lulus',
                            ]);
                        }

                        \App\Models\Activity::log('import_user', 'Import user: ' . $user->name . ' (Role: ' . $user->role . ')', 'User', $user->id);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                        $errorCount++;
                    }
                }
                
                fclose($handle);
            } else {
                // Handle Excel file (xlsx, xls)
                // For Excel, we'll use a simple approach with PhpSpreadsheet if available
                // For now, return error asking to convert to CSV
                return redirect()->back()
                    ->with('error', 'Format Excel belum didukung. Silakan konversi ke CSV terlebih dahulu.');
            }

            $message = "Import selesai! {$successCount} user berhasil diimport.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} baris gagal.";
            }

            return redirect()->route('admin.users')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Display articles management page.
     */
    public function articles(): View
    {
        $articles = Article::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.articles', [
            'articles' => $articles,
        ]);
    }

    /**
     * Show create article form.
     */
    public function createArticle(): View
    {
        return view('admin.create-article');
    }

    /**
     * Store new article.
     */
    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $articleData = [
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            $articleData['image'] = $imagePath;
        }

        // Set published_at if status is published
        if ($request->status === 'published') {
            $articleData['published_at'] = now();
        }

        $article = Article::create($articleData);

        \App\Models\Activity::log('create_article', 'Membuat informasi: ' . $article->title, 'Article', $article->id);

        return redirect()->route('admin.articles')
            ->with('success', 'Informasi berhasil dibuat!');
    }

    /**
     * Show edit article form.
     */
    public function editArticle(Article $article): View
    {
        return view('admin.edit-article', [
            'article' => $article,
        ]);
    }

    /**
     * Update article.
     */
    public function updateArticle(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $articleData = [
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $imagePath = $request->file('image')->store('articles', 'public');
            $articleData['image'] = $imagePath;
        }

        // Set published_at if status changed to published
        if ($request->status === 'published' && $article->status !== 'published') {
            $articleData['published_at'] = now();
        }

        $article->update($articleData);

        \App\Models\Activity::log('update_article', 'Mengupdate informasi: ' . $article->title, 'Article', $article->id);

        return redirect()->route('admin.articles')
            ->with('success', 'Informasi berhasil diperbarui!');
    }

    /**
     * Delete article.
     */
    public function deleteArticle(Article $article)
    {
        // Delete image if exists
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $articleTitle = $article->title;
        $articleId = $article->id;
        
        \App\Models\Activity::log('delete_article', 'Menghapus informasi: ' . $articleTitle, 'Article', $articleId);
        
        $article->delete();

        return redirect()->route('admin.articles')
            ->with('success', 'Informasi berhasil dihapus!');
    }
}
