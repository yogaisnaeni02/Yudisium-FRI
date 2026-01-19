<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use App\Models\User;
use App\Models\Article;
use App\Models\Periode;
use App\Models\YudisiumSiding;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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
        $query = Submission::with('student.user', 'documents', 'periode');

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

        // For completed submissions, check and auto-create yudisium_siding if conditions are met
        if ($viewType === 'completed') {
            foreach ($filteredSubmissions as $submission) {
                $this->autoCreateYudisiumSiding($submission);
            }
        }

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

        return view('admin.verifikasi.pengajuan', [
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
        $submission->load('student.user', 'documents', 'periode');
        $yudisiumResult = $submission->student->yudisiumResults()->latest()->first();

        return view('admin.verifikasi.submission-detail', [
            'submission' => $submission,
            'yudisiumResult' => $yudisiumResult,
        ]);
    }

    /**
     * Update verification (periode and predikat).
     */
    public function updateVerification(Request $request, Submission $submission)
    {
        $request->validate([
            'periode_id' => 'nullable|exists:periodes,id',
            'predikat' => 'nullable|in:memuaskan,sangat_memuaskan,cumlaude,summa_cumlaude',
        ]);

        // Update periode in submission (only if provided)
        if ($request->periode_id) {
            $submission->update(['periode_id' => $request->periode_id]);
        }

        // Update predikat in yudisium result if it exists
        if ($request->predikat) {
            $yudisiumResult = $submission->student->yudisiumResults()->latest()->first();
            if ($yudisiumResult) {
                $yudisiumResult->update(['predikat_kelulusan' => $request->predikat]);
            } else {
                // Create new yudisium result if none exists
                \App\Models\YudisiumResult::create([
                    'student_id' => $submission->student->id,
                    'ipk' => $submission->student->ipk,
                    'predikat_kelulusan' => $request->predikat,
                ]);
            }
        }

        \App\Models\Activity::log('verify', 'Update periode dan predikat pengajuan', 'Submission', $submission->id);

        // Refresh submission to get latest data
        $submission->refresh();
        
        // Auto-create yudisium siding if conditions are met
        $this->autoCreateYudisiumSiding($submission);

        return redirect()->back()->with('success', 'Periode dan predikat berhasil diperbarui!');
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
            
            // Auto-create yudisium siding if conditions are met
            $this->autoCreateYudisiumSiding($submission);
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

            // Create notification for student based on document status
            $studentId = $document->submission->student->user_id;
            $statusMessages = [
                'approved' => 'Dokumen Anda "' . $document->type . '" telah disetujui.',
                'revision' => 'Dokumen Anda "' . $document->type . '" memerlukan revisi.',
                'rejected' => 'Dokumen Anda "' . $document->type . '" telah ditolak.',
            ];

            if (isset($statusMessages[$data['status']])) {
                \App\Services\NotificationService::createForStudent(
                    $studentId,
                    'document_' . $data['status'],
                    'Status Dokumen Diperbarui',
                    $statusMessages[$data['status']],
                    ['document_id' => $document->id, 'submission_id' => $document->submission_id]
                );
            }

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
            
            // Auto-create yudisium siding if conditions are met
            $this->autoCreateYudisiumSiding($submission);
        } elseif (in_array($submission->status, ['draft', 'submitted'])) {
            $submission->update(['status' => 'under_review']);
        }

        return redirect()->back()
            ->with('success', "Total {$updatedCount} dokumen berhasil diperbarui!");
    }

    /**
     * Preview document (open in new tab).
     */
    public function previewDocument(Document $document)
    {
        // If file content exists in database, serve from there
        if ($document->file_content) {
            $fileContent = base64_decode($document->file_content);
            $mimeType = $document->mime_type ?: 'application/octet-stream';

            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . ($document->original_filename ?: $document->name) . '"');
        }

        // Fallback to filesystem if file_content is not available
        // File is stored in 'private' disk
        if (Storage::disk('private')->exists($document->file_path)) {
            $filePath = Storage::disk('private')->path($document->file_path);
            $mimeType = Storage::disk('private')->mimeType($document->file_path) ?: 'application/octet-stream';
            
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->name . '"',
            ]);
        }

        // If file doesn't exist, return 404
        abort(404, 'File not found');
    }

    /**
     * Download document.
     */
    public function downloadDocument(Document $document)
    {
        // If file content exists in database, serve from there
        if ($document->file_content) {
            $fileContent = base64_decode($document->file_content);
            $filename = $document->original_filename ?: $document->name;

            return response($fileContent)
                ->header('Content-Type', $document->mime_type ?: 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        // Fallback to filesystem if file_content is not available
        // File is stored in 'private' disk
        return Storage::disk('private')->download($document->file_path, $document->name);
    }

    /**
     * Display users management page.
     */
    public function users(Request $request): View
    {
        $search = $request->get('search', '');
        $role = $request->get('role', ''); // 'admin', 'student', or empty for all

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

        // Role filter
        if ($role && in_array($role, ['admin', 'student'])) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
        ]);
    }

    /**
     * Show create user form.
     */
    public function createUser(): View
    {
        return view('admin.users.create');
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
            'ipk' => 'nullable|numeric|min:0|max:4',
            'total_sks' => 'nullable|integer|min:0',
            'tak' => 'nullable|numeric|min:0',
            'skor_eprt' => 'nullable|integer|min:310|max:677'
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
                'nama' => $request->name,
                'ipk' => $request->ipk ?? 0,
                'total_sks' => $request->total_sks ?? 0,
                'status_kelulusan' => 'belum_lulus',
                'tak' => $request->tak ?? 0,
                'skor_eprt' => $request->skor_eprt ?? null,
                'prodi' => $request->prodi ?? '',
                'dosen_wali' => '',
                'pembimbing_1' => '',
                'pembimbing_2' => '',
                'penguji_ketua' => '',
                'penguji_anggota' => ''
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
        
        return view('admin.users.edit', [
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
            'ipk' => 'nullable|numeric|min:0|max:4',
            'total_sks' => 'nullable|integer|min:0',
            'prodi' => 'nullable|string|max:255',
            'tak' => 'nullable|numeric|min:0',
            'skor_eprt' => 'nullable|integer|min:310|max:677',
            'dosen_wali' => 'nullable|string|max:255',
            'pembimbing_1' => 'nullable|string|max:255',
            'pembimbing_2' => 'nullable|string|max:255',
            'penguji_ketua' => 'nullable|string|max:255',
            'penguji_anggota' => 'nullable|string|max:255'
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
                    'nama' => $request->name,
                    'ipk' => $request->ipk ?? $user->student->ipk ?? 0,
                    'total_sks' => $request->total_sks ?? $user->student->total_sks ?? 0,
                    'prodi' => $request->prodi ?? $user->student->prodi ?? '',
                    'tak' => $request->tak ?? $user->student->tak ?? 0,
                    'skor_eprt' => $request->skor_eprt ?? $user->student->skor_eprt ?? null,
                ]);
            } else {
                // Create new student record if user was admin before
                Student::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'nama' => $request->name,
                    'ipk' => $request->ipk ?? 0,
                    'total_sks' => $request->total_sks ?? 0,
                    'status_kelulusan' => 'belum_lulus',
                    'prodi' => $request->prodi ?? '',
                    'tak' => $request->tak ?? 0,
                    'skor_eprt' => $request->skor_eprt ?? null,
                    'dosen_wali' => $request->dosen_wali ?? '',
                    'pembimbing_1' => $request->pembimbing_1 ?? '',
                    'pembimbing_2' => $request->pembimbing_2 ?? '',
                    'penguji_ketua' => $request->penguji_ketua ?? '',
                    'penguji_anggota' => $request->penguji_anggota ?? '',
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
        return view('admin.users.import');
    }

    /**
     * Import users from CSV/Excel file.
     */
    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120', // 5MB max
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['csv', 'xlsx', 'xls'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('The file must be a file of type: ' . implode(', ', $allowedExtensions) . '.');
                    }
                },
            ],
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
                    $ipk = isset($data['ipk']) && $data['ipk'] !== '' ? floatval($data['ipk']) : 0;
                    $totalSks = isset($data['total_sks']) && $data['total_sks'] !== '' ? intval($data['total_sks']) : 0;
                    $prodi = trim($data['prodi'] ?? '');
                    $tak = isset($data['tak']) && $data['tak'] !== '' ? floatval($data['tak']) : 0;
                    $skorEprt = isset($data['skor_eprt']) && $data['skor_eprt'] !== '' ? intval($data['skor_eprt']) : null;
                    $dosenWali = trim($data['dosen_wali'] ?? '');
                    $pembimbing_1 = trim($data['pembimbing_1'] ?? '');
                    $pembimbing_2 = trim($data['pembimbing_2'] ?? '');
                    $penguji_ketua = trim($data['penguji_ketua'] ?? '');
                    $penguji_anggota = trim($data['penguji_anggota'] ?? '');

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
                                'nama' => $name,
                                'ipk' => $ipk,
                                'total_sks' => $totalSks,
                                'status_kelulusan' => 'belum_lulus',
                                'prodi' => $prodi,
                                'tak' => $tak,
                                'skor_eprt' => $skorEprt,
                                'dosen_wali' => $dosenWali,
                                'pembimbing_1' => $pembimbing_1,
                                'pembimbing_2' => $pembimbing_2,
                                'penguji_ketua' => $penguji_ketua,
                                'penguji_anggota' => $penguji_anggota,
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

        return view('admin.articles.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Show create article form.
     */
    public function createArticle(): View
    {
        return view('admin.articles.create');
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

        // Create notification for all students if article is published
        if ($request->status === 'published') {
            \App\Services\NotificationService::createForAllStudents(
                'article',
                'Informasi Yudisium Baru',
                'Informasi baru telah dipublikasikan: ' . $article->title,
                ['article_id' => $article->id]
            );
        }

        return redirect()->route('admin.articles')
            ->with('success', 'Informasi berhasil dibuat!');
    }

    /**
     * Show edit article form.
     */
    public function editArticle(Article $article): View
    {
        return view('admin.articles.edit', [
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

    /**
     * Display periode management page.
     */
    public function periodes(): View
    {
        $periodes = Periode::orderBy('tanggal_mulai', 'desc')->paginate(15);
        
        return view('admin.periodes.index', [
            'periodes' => $periodes,
        ]);
    }

    /**
     * Show create periode form.
     */
    public function createPeriode(): View
    {
        return view('admin.periodes.create');
    }

    /**
     * Store new periode.
     */
    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:periodes,nama',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:active,inactive',
        ]);

        $periode = Periode::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        \App\Models\Activity::log('create_periode', 'Membuat periode: ' . $periode->nama, 'Periode', $periode->id);

        return redirect()->route('admin.periodes')
            ->with('success', 'Periode berhasil dibuat!');
    }

    /**
     * Show edit periode form.
     */
    public function editPeriode(Periode $periode): View
    {
        return view('admin.periodes.edit', [
            'periode' => $periode,
        ]);
    }

    /**
     * Update periode.
     */
    public function updatePeriode(Request $request, Periode $periode)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:periodes,nama,' . $periode->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:active,inactive',
        ]);

        $periode->update([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        \App\Models\Activity::log('update_periode', 'Mengupdate periode: ' . $periode->nama, 'Periode', $periode->id);

        return redirect()->route('admin.periodes')
            ->with('success', 'Periode berhasil diperbarui!');
    }

    /**
     * Delete periode.
     */
    public function deletePeriode(Periode $periode)
    {
        $periodeName = $periode->nama;
        $periodeId = $periode->id;
        
        \App\Models\Activity::log('delete_periode', 'Menghapus periode: ' . $periodeName, 'Periode', $periodeId);
        
        $periode->delete();

        return redirect()->route('admin.periodes')
            ->with('success', 'Periode berhasil dihapus!');
    }

    // ==================== YUDISIUM SIDING METHODS ====================

    /**
     * Get the correct student foreign key column name.
     */
    private function getStudentForeignKeyColumn(): string
    {
        static $columnName = null;

        if ($columnName === null) {
            try {
                $columns = Schema::getColumnListing('yudisium_sidings');

                if (!in_array('student_id', $columns)) {
                    throw new \RuntimeException('Column student_id not found in yudisium_sidings table');
                }

                $columnName = 'student_id';
            } catch (\Throwable $e) {
                // FAIL FAST – lebih baik error daripada silent bug
                throw $e;
            }
        }

        return $columnName;
    }

    /**
     * Auto-create yudisium siding when all conditions are met:
     * 1. Submission status is 'approved'
     * 2. All documents are approved
     * 3. Submission has periode_id
     * 4. Student has predikat in YudisiumResult
     */
    private function autoCreateYudisiumSiding(Submission $submission): void
    {
        /**
         * 1. Submission harus approved
         */
        if ($submission->status !== 'approved') {
            return;
        }

        /**
         * 2. Semua dokumen harus approved
         */
        $allApproved = $submission->documents()
            ->where('status', '!=', 'approved')
            ->doesntExist();

        if (!$allApproved || $submission->documents()->count() === 0) {
            return;
        }

        /**
         * 3. Harus punya periode
         */
        if (!$submission->periode_id) {
            return;
        }

        /**
         * 4. Mahasiswa harus punya hasil yudisium
         */
        $yudisiumResult = $submission->student
            ->yudisiumResults()
            ->latest()
            ->first();

        if (!$yudisiumResult || !$yudisiumResult->predikat_kelulusan) {
            return;
        }

        /**
         * 5. Cek apakah yudisium sidang sudah ada
         */
        $existingSiding = YudisiumSiding::where('student_id', $submission->student_id)
            ->where('periode_id', $submission->periode_id)
            ->first();

        if ($existingSiding) {
            return;
        }

        /**
         * 6. Mapping predikat
         */
        $predikatMap = [
            'memuaskan'         => 'MEMUASKAN',
            'sangat_memuaskan'  => 'SANGAT MEMUASKAN',
            'cumlaude'          => 'CUMLAUDE',
            'summa_cumlaude'    => 'SUMMA CUMLAUDE',
        ];

        $predikatYudisium = $predikatMap[$yudisiumResult->predikat_kelulusan]
            ?? strtoupper($yudisiumResult->predikat_kelulusan);

        /**
         * 7. Status cumlaude
         */
        $statusCumlaude = null;
        if (in_array($yudisiumResult->predikat_kelulusan, ['cumlaude', 'summa_cumlaude'])) {
            $statusCumlaude = $yudisiumResult->predikat_kelulusan;
        }

        /**
         * 8. Data insert (WAJIB student_id)
         */
        $yudisiumSidingData = [
            'periode_id'       => $submission->periode_id,
            'student_id'     => $submission->student_id,
            'tanggal_sidang'   => now(),
            'predikat_yudisium'=> $predikatYudisium,
            'status_cumlaude'  => $statusCumlaude,
            'status_yudisium'  => 'pending',
        ];

        /**
         * 9. Insert ke DB
         */
        $yudisiumSiding = YudisiumSiding::create($yudisiumSidingData);

        /**
         * 10. Log activity
         */
        \App\Models\Activity::log(
            'auto_create_yudisium_siding',
            'Auto-create sidang yudisium untuk mahasiswa: '
                . $submission->student->nama
                . ' (Periode: ' . $submission->periode->nama . ')',
            'YudisiumSiding',
            $yudisiumSiding->id
        );
    }

    /**
     * Display list of yudisium sidings.
     */
    public function yudisiumSidings(Request $request): View
    {
        // Auto-create yudisium_siding for all eligible submissions
        // Get all submissions that are completed (100% progress, all documents approved)
        $completedSubmissions = Submission::with('student.user', 'documents', 'periode')
            ->where('status', 'approved')
            ->get()
            ->filter(function($submission) {
                $progress = $submission->getProgressPercentage();
                return $progress === 100 && $submission->documents->count() > 0;
            });

        // Auto-create yudisium_siding for each eligible submission
        foreach ($completedSubmissions as $submission) {
            $this->autoCreateYudisiumSiding($submission);
        }

        $search = $request->get('search', '');
        $periodeFilter = $request->get('periode', '');

        $query = YudisiumSiding::with(['student.user', 'periode']);

        // Search by student name or NIM
        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by periode
        if ($periodeFilter) {
            $query->where('periode_id', $periodeFilter);
        }

        $sidings = $query->orderBy('tanggal_sidang', 'desc')
            ->paginate(15);

        $periodes = Periode::orderBy('nama', 'desc')->get();

        return view('admin.yudisium-sidings.index', [
            'sidings' => $sidings,
            'periodes' => $periodes,
            'search' => $search,
            'periodeFilter' => $periodeFilter,
        ]);
    }

    /**
     * Show create yudisium siding form.
     */
    public function createYudisiumSiding(): View
    {
        $students = Student::with('user')->orderBy('nama')->get();
        $periodes = Periode::where('status', 'active')->orderBy('nama', 'desc')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();

        return view('admin.yudisium-sidings.create', [
            'students' => $students,
            'periodes' => $periodes,
            'dosens' => $dosens,
        ]);
    }

    /**
     * Store new yudisium siding.
     */
    public function storeYudisiumSiding(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'periode_id' => 'required|exists:periodes,id',
            'tanggal_sidang' => 'required|date_format:Y-m-d\TH:i',
            
            // Dosen Wali
            'dosen_wali_nama' => 'nullable|string|max:255',
            'dosen_wali_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Pembimbing 1
            'pembimbing_1_nama' => 'nullable|string|max:255',
            'pembimbing_1_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pembimbing_1_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Pembimbing 2
            'pembimbing_2_nama' => 'nullable|string|max:255',
            'pembimbing_2_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pembimbing_2_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Penguji Ketua
            'penguji_ketua_nama' => 'nullable|string|max:255',
            'penguji_ketua_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penguji_ketua_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Penguji Anggota
            'penguji_anggota_nama' => 'nullable|string|max:255',
            'penguji_anggota_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penguji_anggota_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Tugas Akhir
            'judul_tugas_akhir' => 'nullable|string',
            'jenis_tugas_akhir' => 'nullable|string|max:255',
            
            // Nilai
            'nilai_total' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:10',
            
            // Status
            'predikat_yudisium' => 'nullable|string|max:255',
            'status_cumlaude' => 'nullable|in:cumlaude,summa_cumlaude,tidak',
            'pemenuhan_jurnal' => 'nullable|string',
            'status_yudisium' => 'required|in:lulus,tidak_lulus,pending',
            'catatan' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('dosen_wali_foto')) {
            $validated['dosen_wali_foto'] = $request->file('dosen_wali_foto')->store('yudisium/dosen_wali', 'public');
        }

        if ($request->hasFile('pembimbing_1_foto')) {
            $validated['pembimbing_1_foto'] = $request->file('pembimbing_1_foto')->store('yudisium/pembimbing_1', 'public');
        }

        if ($request->hasFile('pembimbing_2_foto')) {
            $validated['pembimbing_2_foto'] = $request->file('pembimbing_2_foto')->store('yudisium/pembimbing_2', 'public');
        }

        if ($request->hasFile('penguji_ketua_foto')) {
            $validated['penguji_ketua_foto'] = $request->file('penguji_ketua_foto')->store('yudisium/penguji_ketua', 'public');
        }

        if ($request->hasFile('penguji_anggota_foto')) {
            $validated['penguji_anggota_foto'] = $request->file('penguji_anggota_foto')->store('yudisium/penguji_anggota', 'public');
        }

        $siding = YudisiumSiding::create($validated);

        $this->syncToStudent($siding->student, $validated);

        \App\Models\Activity::log('create_yudisium_siding', 
            'Membuat data sidang yudisium untuk mahasiswa: ' . $siding->student->nama, 
            'YudisiumSiding', 
            $siding->id
        );

        return redirect()->route('admin.yudisium-sidings.show', $siding)
            ->with('success', 'Data sidang yudisium berhasil dibuat!');
    }

    /**
     * Show yudisium siding detail (view like the image).
     */
    public function showYudisiumSiding(YudisiumSiding $yudisiumSiding): View
    {
        $yudisiumSiding->load(['student.user', 'periode']);

        return view('admin.yudisium-sidings.show', [
            'siding' => $yudisiumSiding,
        ]);
    }

    /**
     * Export yudisium siding detail to PDF.
     */
    public function exportYudisiumSidingPDF(YudisiumSiding $yudisiumSiding)
    {
        $yudisiumSiding->load(['student.user', 'periode']);

        // Get dosen data for NIP
        $dosens = [];
        if ($yudisiumSiding->dosen_wali_nama) {
            $dosens['dosen_wali'] = Dosen::where('nama_dosen', $yudisiumSiding->dosen_wali_nama)->first();
        }
        if ($yudisiumSiding->pembimbing_1_nama) {
            $dosens['pembimbing_1'] = Dosen::where('nama_dosen', $yudisiumSiding->pembimbing_1_nama)->first();
        }
        if ($yudisiumSiding->pembimbing_2_nama) {
            $dosens['pembimbing_2'] = Dosen::where('nama_dosen', $yudisiumSiding->pembimbing_2_nama)->first();
        }
        if ($yudisiumSiding->penguji_ketua_nama) {
            $dosens['penguji_ketua'] = Dosen::where('nama_dosen', $yudisiumSiding->penguji_ketua_nama)->first();
        }
        if ($yudisiumSiding->penguji_anggota_nama) {
            $dosens['penguji_anggota'] = Dosen::where('nama_dosen', $yudisiumSiding->penguji_anggota_nama)->first();
        }

        // Convert image paths to base64 for PDF
        $imagePaths = [];
        
        // Helper function to convert image to base64
        $getImageBase64 = function($path, $disk = 'public') {
            if (!$path) return null;
            
            try {
                if (Storage::disk($disk)->exists($path)) {
                    $imageData = Storage::disk($disk)->get($path);
                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mimeType = 'image/jpeg';
                    if ($extension === 'png') $mimeType = 'image/png';
                    if ($extension === 'gif') $mimeType = 'image/gif';
                    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                }
            } catch (\Exception $e) {
                \Log::error('Error loading image for PDF: ' . $e->getMessage());
            }
            return null;
        };
        
        if ($yudisiumSiding->dosen_wali_foto) {
            $imagePaths['dosen_wali'] = $getImageBase64($yudisiumSiding->dosen_wali_foto);
        }
        if ($yudisiumSiding->pembimbing_1_foto) {
            $imagePaths['pembimbing_1'] = $getImageBase64($yudisiumSiding->pembimbing_1_foto);
        }
        if ($yudisiumSiding->pembimbing_2_foto) {
            $imagePaths['pembimbing_2'] = $getImageBase64($yudisiumSiding->pembimbing_2_foto);
        }
        if ($yudisiumSiding->penguji_ketua_foto) {
            $imagePaths['penguji_ketua'] = $getImageBase64($yudisiumSiding->penguji_ketua_foto);
        }
        if ($yudisiumSiding->penguji_anggota_foto) {
            $imagePaths['penguji_anggota'] = $getImageBase64($yudisiumSiding->penguji_anggota_foto);
        }
        if ($yudisiumSiding->student->foto) {
            $imagePaths['student'] = $getImageBase64($yudisiumSiding->student->foto);
        }

        // Render PDF view
        $html = view('admin.yudisium-sidings.pdf', [
            'siding' => $yudisiumSiding,
            'dosens' => $dosens,
            'imagePaths' => $imagePaths,
        ])->render();

        // Configure DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', public_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate filename
        $filename = 'Sidang_Yudisium_' . $yudisiumSiding->student->nim . '_' . date('Y-m-d') . '.pdf';

        return $dompdf->stream($filename);
    }

    /**
     * Show edit yudisium siding form.
     */
    public function editYudisiumSiding(YudisiumSiding $yudisiumSiding): View
    {
        $students = Student::with('user')->orderBy('nama')->get();
        $periodes = Periode::where('status', 'active')->orderBy('nama', 'desc')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();

        return view('admin.yudisium-sidings.edit', [
            'siding' => $yudisiumSiding,
            'students' => $students,
            'periodes' => $periodes,
            'dosens' => $dosens,
        ]);
    }

    /**
     * Update yudisium siding.
     */
    public function updateYudisiumSiding(Request $request, YudisiumSiding $yudisiumSiding)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'periode_id' => 'required|exists:periodes,id',
            'tanggal_sidang' => 'required|date_format:Y-m-d\TH:i',
            
            // Dosen Wali
            'dosen_wali_nama' => 'nullable|string|max:255',
            'dosen_wali_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Pembimbing 1
            'pembimbing_1_nama' => 'nullable|string|max:255',
            'pembimbing_1_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pembimbing_1_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Pembimbing 2
            'pembimbing_2_nama' => 'nullable|string|max:255',
            'pembimbing_2_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pembimbing_2_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Penguji Ketua
            'penguji_ketua_nama' => 'nullable|string|max:255',
            'penguji_ketua_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penguji_ketua_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Penguji Anggota
            'penguji_anggota_nama' => 'nullable|string|max:255',
            'penguji_anggota_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penguji_anggota_nilai' => 'nullable|numeric|min:0|max:100',
            
            // Tugas Akhir
            'judul_tugas_akhir' => 'nullable|string',
            'jenis_tugas_akhir' => 'nullable|string|max:255',
            
            // Nilai
            'nilai_total' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:10',
            
            // Status
            'predikat_yudisium' => 'nullable|string|max:255',
            'status_cumlaude' => 'nullable|in:cumlaude,summa_cumlaude,tidak',
            'pemenuhan_jurnal' => 'nullable|string',
            'status_yudisium' => 'required|in:lulus,tidak_lulus,pending',
            'catatan' => 'nullable|string',
        ]);

        // Handle file uploads (only if new file is uploaded)
        if ($request->hasFile('dosen_wali_foto')) {
            // Delete old file if exists
            if ($yudisiumSiding->dosen_wali_foto) {
                Storage::disk('public')->delete($yudisiumSiding->dosen_wali_foto);
            }
            $validated['dosen_wali_foto'] = $request->file('dosen_wali_foto')->store('yudisium/dosen_wali', 'public');
        } else {
            unset($validated['dosen_wali_foto']);
        }

        if ($request->hasFile('pembimbing_1_foto')) {
            // Delete old file if exists
            if ($yudisiumSiding->pembimbing_1_foto) {
                Storage::disk('public')->delete($yudisiumSiding->pembimbing_1_foto);
            }
            $validated['pembimbing_1_foto'] = $request->file('pembimbing_1_foto')->store('yudisium/pembimbing_1', 'public');
        } else {
            unset($validated['pembimbing_1_foto']);
        }

        if ($request->hasFile('pembimbing_2_foto')) {
            // Delete old file if exists
            if ($yudisiumSiding->pembimbing_2_foto) {
                Storage::disk('public')->delete($yudisiumSiding->pembimbing_2_foto);
            }
            $validated['pembimbing_2_foto'] = $request->file('pembimbing_2_foto')->store('yudisium/pembimbing_2', 'public');
        } else {
            unset($validated['pembimbing_2_foto']);
        }

        if ($request->hasFile('penguji_ketua_foto')) {
            // Delete old file if exists
            if ($yudisiumSiding->penguji_ketua_foto) {
                Storage::disk('public')->delete($yudisiumSiding->penguji_ketua_foto);
            }
            $validated['penguji_ketua_foto'] = $request->file('penguji_ketua_foto')->store('yudisium/penguji_ketua', 'public');
        } else {
            unset($validated['penguji_ketua_foto']);
        }

        if ($request->hasFile('penguji_anggota_foto')) {
            // Delete old file if exists
            if ($yudisiumSiding->penguji_anggota_foto) {
                Storage::disk('public')->delete($yudisiumSiding->penguji_anggota_foto);
            }
            $validated['penguji_anggota_foto'] = $request->file('penguji_anggota_foto')->store('yudisium/penguji_anggota', 'public');
        } else {
            unset($validated['penguji_anggota_foto']);
        }

        $yudisiumSiding->update($validated);

        $this->syncToStudent($yudisiumSiding->student, $validated);

        \App\Models\Activity::log('update_yudisium_siding', 
            'Mengupdate data sidang yudisium untuk mahasiswa: ' . $yudisiumSiding->student->nama, 
            'YudisiumSiding', 
            $yudisiumSiding->id
        );

        return redirect()->route('admin.yudisium-sidings.show', $yudisiumSiding)
            ->with('success', 'Data sidang yudisium berhasil diperbarui!');
    }

    /**
     * Delete yudisium siding.
     */
    public function deleteYudisiumSiding(YudisiumSiding $yudisiumSiding)
    {
        $studentName = $yudisiumSiding->student->nama;
        $sidingId = $yudisiumSiding->id;

        // Delete photos if exist
        if ($yudisiumSiding->dosen_wali_foto) {
            Storage::disk('public')->delete($yudisiumSiding->dosen_wali_foto);
        }
        if ($yudisiumSiding->pembimbing_1_foto) {
            Storage::disk('public')->delete($yudisiumSiding->pembimbing_1_foto);
        }

        \App\Models\Activity::log('delete_yudisium_siding', 
            'Menghapus data sidang yudisium untuk mahasiswa: ' . $studentName, 
            'YudisiumSiding', 
            $sidingId
        );

        $yudisiumSiding->delete();

        return redirect()->route('admin.yudisium-sidings')
            ->with('success', 'Data sidang yudisium berhasil dihapus!');
    }

    private function syncToStudent(Student $student, array $data): void
    {
        $student->update([
            'dosen_wali'    => $data['dosen_wali_nama'] ?? $student->dosen_wali,
            'pembimbing_1'   => $data['pembimbing_1_nama'] ?? $student->pembimbing_1,
            'pembimbing_2'   => $data['pembimbing_2_nama'] ?? $student->pembimbing_2,
            'penguji_ketua'  => $data['penguji_ketua_nama'] ?? $student->penguji_ketua,
            'penguji_anggota'=> $data['penguji_anggota_nama'] ?? $student->penguji_anggota,
        ]);
    }

    /**
     * Export yudisium sidings data to CSV.
     */
    public function exportYudisiumSidings(Request $request)
    {
        $search = $request->get('search', '');
        $periodeFilter = $request->get('periode', '');

        $query = YudisiumSiding::with(['student.user', 'periode']);

        // Search by student name or NIM
        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by periode
        if ($periodeFilter) {
            $query->where('periode_id', $periodeFilter);
        }

        $sidings = $query->orderBy('tanggal_sidang', 'desc')->get();

        // Create CSV content
        $csvData = [];

        // Add headers
        $csvData[] = [
            'NIM',
            'Nama Mahasiswa',
            'Periode',
            'Tanggal Sidang',
            'Nilai Total',
            'Nilai Huruf',
            'Predikat Yudisium',
            'Status Cumlaude',
            'Status Yudisium',
            'Dosen Wali',
            'Pembimbing 1',
            'Pembimbing 2',
            'Penguji Ketua',
            'Penguji Anggota',
            'Judul Tugas Akhir',
            'Jenis Tugas Akhir',
            'Pemenuhan Jurnal',
            'Catatan'
        ];

        // Add data rows
        foreach ($sidings as $siding) {
            $csvData[] = [
                $siding->student->nim ?? '',
                $siding->student->nama ?? '',
                $siding->periode->nama ?? '',
                $siding->tanggal_sidang ? $siding->tanggal_sidang->format('d M Y') : '',
                $siding->nilai_total ? number_format($siding->nilai_total, 2) : '',
                $siding->nilai_huruf ?? '',
                $siding->predikat_yudisium ?? '',
                $siding->status_cumlaude ?? '',
                $siding->status_yudisium ?? '',
                $siding->dosen_wali_nama ?? '',
                $siding->pembimbing_1_nama ?? '',
                $siding->pembimbing_2_nama ?? '',
                $siding->penguji_ketua_nama ?? '',
                $siding->penguji_anggota_nama ?? '',
                $siding->judul_tugas_akhir ?? '',
                $siding->jenis_tugas_akhir ?? '',
                $siding->pemenuhan_jurnal ?? '',
                $siding->catatan ?? ''
            ];
        }

        // Generate filename with timestamp
        $filename = 'daftar_sidang_yudisium_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // Create CSV response
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export users data to CSV.
     */
    public function exportUsers(Request $request)
    {
        $search = $request->get('search', '');
        $role = $request->get('role', '');

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

        // Role filter
        if ($role && in_array($role, ['admin', 'student'])) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Create CSV content
        $csvData = [];

        // Add headers
        $csvData[] = [
            'Nama',
            'Email',
            'Role',
            'NIM',
            'Nama Lengkap',
            'IPK',
            'Total SKS',
            'Prodi',
            'TAK',
            'Dosen Wali',
            'Pembimbing 1',
            'Pembimbing 2',
            'Penguji Ketua',
            'Penguji Anggota',
            'Tanggal Dibuat'
        ];

        // Add data rows
        foreach ($users as $user) {
            $csvData[] = [
                $user->name ?? '',
                $user->email ?? '',
                $user->role ?? '',
                $user->student->nim ?? '',
                $user->student->nama ?? '',
                $user->student->ipk ?? '',
                $user->student->total_sks ?? '',
                $user->student->prodi ?? '',
                $user->student->tak ?? '',
                $user->student->dosen_wali ?? '',
                $user->student->pembimbing_1 ?? '',
                $user->student->pembimbing_2 ?? '',
                $user->student->penguji_ketua ?? '',
                $user->student->penguji_anggota ?? '',
                $user->created_at ? $user->created_at->format('d M Y H:i') : ''
            ];
        }

        // Generate filename with timestamp
        $filename = 'daftar_users_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // Create CSV response
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export submissions data to CSV.
     */
    public function exportSubmissions(Request $request)
    {
        $viewType = $request->get('view', 'active'); // 'active' or 'completed'
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'asc'); // 'asc' or 'desc'

        // Base query
        $query = Submission::with('student.user', 'documents', 'periode');

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

        // Create CSV content
        $csvData = [];

        // Add headers
        $csvData[] = [
            'NIM',
            'Nama Mahasiswa',
            'Email',
            'Periode',
            'Status',
            'Progress (%)',
            'Tanggal Submit',
            'Tanggal Dibuat',
            'IPK',
            'Total SKS',
            'Prodi',
            'Dosen Wali',
            'Pembimbing 1',
            'Pembimbing 2',
            'Penguji Ketua',
            'Penguji Anggota',
            'Jumlah Dokumen',
            'Dokumen Approved',
            'Dokumen Pending',
            'Dokumen Rejected'
        ];

        // Add data rows
        foreach ($filteredSubmissions as $submission) {
            $documentStats = [
                'approved' => $submission->documents->where('status', 'approved')->count(),
                'pending' => $submission->documents->where('status', 'revision')->count() + $submission->documents->where('status', '')->count(),
                'rejected' => $submission->documents->where('status', 'rejected')->count(),
            ];

            $csvData[] = [
                $submission->student->nim ?? '',
                $submission->student->nama ?? $submission->student->user->name ?? '',
                $submission->student->user->email ?? '',
                $submission->periode->nama ?? '',
                $submission->status ?? '',
                $submission->getProgressPercentage() ?? 0,
                $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : '',
                $submission->created_at ? $submission->created_at->format('d M Y H:i') : '',
                $submission->student->ipk ?? '',
                $submission->student->total_sks ?? '',
                $submission->student->prodi ?? '',
                $submission->student->dosen_wali ?? '',
                $submission->student->pembimbing_1 ?? '',
                $submission->student->pembimbing_2 ?? '',
                $submission->student->penguji_ketua ?? '',
                $submission->student->penguji_anggota ?? '',
                $submission->documents->count(),
                $documentStats['approved'],
                $documentStats['pending'],
                $documentStats['rejected']
            ];
        }

        // Generate filename with timestamp and view type
        $viewLabel = $viewType === 'completed' ? 'selesai' : 'pengajuan';
        $filename = "daftar_pengajuan_{$viewLabel}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        // Create CSV response
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display list of dosens.
     */
    public function dosens(Request $request): View
    {
        $search = $request->get('search', '');
        $prodi = $request->get('prodi', '');

        $query = Dosen::query();

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                  ->orWhere('kode_dosen', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by prodi
        if ($prodi) {
            $query->where('prodi', $prodi);
        }

        $dosens = $query->orderBy('nama_dosen', 'asc')->paginate(15);

        // Get unique prodi for filter
        $prodis = Dosen::distinct()->pluck('prodi')->filter()->sort()->values();

        return view('admin.dosens.index', [
            'dosens' => $dosens,
            'search' => $search,
            'prodi' => $prodi,
            'prodis' => $prodis,
        ]);
    }

    /**
     * Show create dosen form.
     */
    public function createDosen(): View
    {
        return view('admin.dosens.create');
    }

    /**
     * Store new dosen.
     */
    public function storeDosen(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'kode_dosen' => 'required|string|max:50|unique:dosens,kode_dosen',
            'prodi' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:dosens,nip',
        ]);

        $dosen = Dosen::create([
            'nama_dosen' => $request->nama_dosen,
            'kode_dosen' => $request->kode_dosen,
            'prodi' => $request->prodi,
            'nip' => $request->nip,
        ]);

        \App\Models\Activity::log('create', 'Membuat data dosen baru: ' . $dosen->nama_dosen, 'Dosen', $dosen->id);

        return redirect()->route('admin.dosens')
            ->with('success', 'Data dosen berhasil ditambahkan!');
    }

    /**
     * Show edit dosen form.
     */
    public function editDosen(Dosen $dosen): View
    {
        return view('admin.dosens.edit', [
            'dosen' => $dosen,
        ]);
    }

    /**
     * Update dosen.
     */
    public function updateDosen(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'kode_dosen' => 'required|string|max:50|unique:dosens,kode_dosen,' . $dosen->id,
            'prodi' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:dosens,nip,' . $dosen->id,
        ]);

        $dosen->update([
            'nama_dosen' => $request->nama_dosen,
            'kode_dosen' => $request->kode_dosen,
            'prodi' => $request->prodi,
            'nip' => $request->nip,
        ]);

        \App\Models\Activity::log('update', 'Memperbarui data dosen: ' . $dosen->nama_dosen, 'Dosen', $dosen->id);

        return redirect()->route('admin.dosens')
            ->with('success', 'Data dosen berhasil diperbarui!');
    }

    /**
     * Delete dosen.
     */
    public function deleteDosen(Dosen $dosen)
    {
        $namaDosen = $dosen->nama_dosen;
        $dosenId = $dosen->id;

        $dosen->delete();

        \App\Models\Activity::log('delete', 'Menghapus data dosen: ' . $namaDosen, 'Dosen', $dosenId);

        return redirect()->route('admin.dosens')
            ->with('success', 'Data dosen berhasil dihapus!');
    }

    /**
     * Export dosens to CSV.
     */
    public function exportDosens(Request $request)
    {
        $search = $request->get('search', '');
        $prodi = $request->get('prodi', '');

        $query = Dosen::query();

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                  ->orWhere('kode_dosen', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by prodi
        if ($prodi) {
            $query->where('prodi', $prodi);
        }

        $dosens = $query->orderBy('nama_dosen', 'asc')->get();

        // Create CSV content
        $csvData = [];

        // Add headers
        $csvData[] = [
            'Nama Dosen',
            'Kode Dosen',
            'Program Studi',
            'NIP',
            'Tanggal Dibuat',
            'Tanggal Diperbarui'
        ];

        // Add data rows
        foreach ($dosens as $dosen) {
            $csvData[] = [
                $dosen->nama_dosen,
                $dosen->kode_dosen,
                $dosen->prodi,
                $dosen->nip,
                $dosen->created_at->format('Y-m-d H:i:s'),
                $dosen->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // Generate filename with timestamp
        $filename = 'dosens_export_' . date('Y-m-d_His') . '.csv';

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import dosens form.
     */
    public function showImportDosens(): View
    {
        return view('admin.dosens.import');
    }

    /**
     * Import dosens from CSV file.
     */
    public function importDosens(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120', // 5MB max
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['csv', 'xlsx', 'xls'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('File harus berformat: ' . implode(', ', $allowedExtensions) . '.');
                    }
                },
            ],
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
                
                // Expected columns: nama_dosen, kode_dosen, prodi, nip
                $rowNumber = 1;
                
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map CSV columns
                    $data = array_combine($header, $row);
                    
                    // Clean data
                    $namaDosen = trim($data['nama_dosen'] ?? '');
                    $kodeDosen = trim($data['kode_dosen'] ?? '');
                    $prodi = trim($data['prodi'] ?? '');
                    $nip = trim($data['nip'] ?? '');

                    // Validate required fields
                    if (empty($namaDosen) || empty($kodeDosen) || empty($prodi) || empty($nip)) {
                        $errors[] = "Baris {$rowNumber}: Semua kolom (nama_dosen, kode_dosen, prodi, nip) wajib diisi";
                        $errorCount++;
                        continue;
                    }

                    // Check if kode_dosen already exists
                    if (Dosen::where('kode_dosen', $kodeDosen)->exists()) {
                        $errors[] = "Baris {$rowNumber}: Kode dosen '{$kodeDosen}' sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Check if nip already exists
                    if (Dosen::where('nip', $nip)->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIP '{$nip}' sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Create dosen
                    try {
                        Dosen::create([
                            'nama_dosen' => $namaDosen,
                            'kode_dosen' => $kodeDosen,
                            'prodi' => $prodi,
                            'nip' => $nip,
                        ]);

                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                        $errorCount++;
                    }
                }

                fclose($handle);
            } else {
                return redirect()->back()
                    ->with('error', 'Format file Excel belum didukung. Silakan gunakan format CSV.');
            }

            // Prepare response message
            $message = "Import selesai! Berhasil: {$successCount}, Gagal: {$errorCount}";
            
            if ($errorCount > 0 && !empty($errors)) {
                $message .= "\n\nError detail:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... dan " . (count($errors) - 10) . " error lainnya";
                }
            }

            if ($successCount > 0) {
                \App\Models\Activity::log('import', "Import data dosen: {$successCount} berhasil, {$errorCount} gagal", 'Dosen', null);
            }

            return redirect()->route('admin.dosens')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }
}
