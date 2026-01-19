<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use App\Models\Article;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    /**
     * Display student dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return view('student.no-data');
        }

        $submission = $student->submissions()->latest()->first();
        
        // If no submission exists, create a draft one
        if (!$submission) {
            $submission = Submission::create([
                'student_id' => $student->id,
                'status' => 'draft',
            ]);
        }

        $documents = $submission->documents()->get();
        $progress = $submission->getProgressPercentage();

        // Get latest 5 published articles for dashboard
        $latestArticles = Article::published()
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Get unread notifications count
        $unreadNotificationsCount = \App\Services\NotificationService::getUnreadCount($user->id);

        return view('student.dashboard', [
            'student' => $student,
            'submission' => $submission,
            'documents' => $documents,
            'progress' => $progress,
            'latestArticles' => $latestArticles,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Upload document (supports single upload and grouped bulk uploads per section).
     */
    public function uploadDocument(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $submission = $student->submissions()->latest()->first();

        if (!$submission) {
            $submission = Submission::create([
                'student_id' => $student->id,
                'status' => 'draft',
            ]);
        }

        // If grouped files (bulk per section) were sent
        if ($request->has('group_name')) {
            $results = [];

            // Define all possible document types with their exact names
            $allDocumentTypes = [
                'Surat Pernyataan',
                'Form Biodata Izajah & Transkip',
                'KTP',
                'Akta Lahir',
                'Ijazah Pendidikan Terakhir',
                'Buku TA yang Disahkan',
                'Slide PPT',
                'Screenshot (Gracias)',
                'Berkas Referensi (Minimal 10)',
                'Bukti Approval Revisi (SOFI)',
                'Bukti Approval SKPI',
                'Surat Keterangan Bebas Pustaka (SKBP)',
                'Dokumen Cumlaude (Publikasi/Lomba/HKI)',
                'Dokumen Pendukung Tambahan',
            ];

            // Create mapping from slug to actual type name
            $documentTypeMap = [];
            foreach ($allDocumentTypes as $typeName) {
                $slug = \Illuminate\Support\Str::slug($typeName);
                $documentTypeMap[$slug] = $typeName;
            }

            foreach ($request->file('files') as $typeSlug => $file) {
                // Handle both single file and array of files (for backward compatibility)
                $files = is_array($file) ? $file : [$file];
                
                foreach ($files as $file) {
                    if (!$file || !$file->isValid()) {
                        continue;
                    }

                    $ext = strtolower($file->getClientOriginalExtension());
                    if (!in_array($ext, ['pdf','doc','docx'])) {
                        continue;
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        continue;
                    }

                    // Get the correct type name from mapping
                    $typeName = $documentTypeMap[$typeSlug] ?? null;
                    
                    // If not in mapping, try to find existing document by slug matching
                    if (!$typeName) {
                        $existingDocument = $submission->documents->first(function ($d) use ($typeSlug) {
                            return \Illuminate\Support\Str::slug($d->type) === $typeSlug;
                        });
                        
                        if ($existingDocument) {
                            $typeName = $existingDocument->type;
                        } else {
                            // Last resort: try to find in all document types by comparing slugs
                            foreach ($allDocumentTypes as $docType) {
                                if (\Illuminate\Support\Str::slug($docType) === $typeSlug) {
                                    $typeName = $docType;
                                    break;
                                }
                            }
                            
                            // If still not found, use the slug converted to title case
                            if (!$typeName) {
                                $typeName = \Illuminate\Support\Str::title(str_replace('-', ' ', $typeSlug));
                            }
                        }
                    }

                    // Try to find existing document by exact type name match
                    $existingDocument = $submission->documents->first(function ($d) use ($typeName) {
                        return $d->type === $typeName;
                    });

                    $filePath = $file->store('yudisium/documents', 'private');

                    // Only allow replace if document is rejected or revision
                    if ($existingDocument) {
                        // Check if document can be replaced (rejected or revision status)
                        if (in_array($existingDocument->status, ['rejected', 'revision'])) {
                            $existingDocument->update([
                                'file_path' => $filePath,
                                'name' => $file->getClientOriginalName(),
                                'status' => 'pending',
                                'feedback' => null,
                            ]);

                            \App\Models\Activity::log('upload', 'Mengunggah ulang dokumen: ' . $typeName, 'Document', $existingDocument->id);
                            $document = $existingDocument;
                        } else {
                            // Document already exists and cannot be replaced
                            continue;
                        }
                    } else {
                        $document = Document::create([
                            'submission_id' => $submission->id,
                            'type' => $typeName,
                            'name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'status' => 'pending',
                        ]);

                        \App\Models\Activity::log('upload', 'Mengunggah dokumen: ' . $typeName, 'Document', $document->id);
                    }

                    $results[] = [
                        'id' => $document->id,
                        'name' => $document->name,
                        'type' => $document->type,
                        'download_url' => route('admin.download-document', $document),
                    ];
                }
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berkas seksi berhasil diunggah',
                    'documents' => $results,
                ], 200);
            }

            return redirect()->back()->with('success', 'Berkas berhasil diunggah');
        }

        // Fallback: single-file upload (existing behavior)
        $request->validate([
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'period_yudisium' => 'nullable|string|max:50',
            'reference_count' => 'nullable|integer|min:0',
            'certificate_type' => 'nullable|string|max:100',
            'is_skpi' => 'nullable|boolean',
            'additional_note' => 'nullable|string|max:1000',
        ]);

        // Check if document type already exists
        $existingDocument = $submission->documents()
            ->where('type', $request->document_type)
            ->first();

        $filePath = $request->file('file')->store('yudisium/documents', 'private');

        // Build metadata from optional inputs
        $metadata = [];
        if ($request->filled('period_yudisium')) {
            $metadata['period_yudisium'] = $request->input('period_yudisium');
        }
        if ($request->filled('reference_count')) {
            $metadata['reference_count'] = (int) $request->input('reference_count');
        }
        if ($request->filled('certificate_type')) {
            $metadata['certificate_type'] = $request->input('certificate_type');
        }
        if ($request->has('is_skpi')) {
            $metadata['is_skpi'] = (bool) $request->input('is_skpi');
        }
        if ($request->filled('additional_note')) {
            $metadata['additional_note'] = $request->input('additional_note');
        }

        if ($existingDocument) {
            // Update existing document (new version)
            $existingDocument->update([
                'file_path' => $filePath,
                'status' => 'pending',
                'feedback' => null,
                'metadata' => array_merge($existingDocument->metadata ?? [], $metadata),
            ]);

            \App\Models\Activity::log('upload', 'Mengunggah ulang dokumen: ' . $request->document_type, 'Document', $existingDocument->id);
            $document = $existingDocument;
        } else {
            // Create new document with metadata
            $newDocument = Document::create([
                'submission_id' => $submission->id,
                'type' => $request->document_type,
                'name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'pending',
                'metadata' => $metadata,
            ]);

            \App\Models\Activity::log('upload', 'Mengunggah dokumen: ' . $request->document_type, 'Document', $newDocument->id);
            $document = $newDocument;
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah!',
                'document' => [
                    'id' => $document->id,
                    'name' => $document->name,
                    'status' => $document->status,
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                    'file_path' => $document->file_path,
                    'download_url' => route('admin.download-document', $document),
                ],
            ], 200);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Dokumen berhasil diunggah!');
    }

    /**
     * Submit yudisium application.
     */
    public function submitApplication(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $submission = $student->submissions()->latest()->first();

        $submission->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        \App\Models\Activity::log('submit', 'Mengirim pengajuan yudisium', 'Submission', $submission->id);

        return redirect()->route('student.dashboard')
            ->with('success', 'Pengajuan yudisium berhasil dikirim!');
    }

    /**
     * Display pengajuan yudisium page.
     */
    public function pengajuanYudisium(): View
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return view('student.no-data');
        }

        $submission = $student->submissions()->latest()->first();
        
        if (!$submission) {
            $submission = Submission::create([
                'student_id' => $student->id,
                'status' => 'draft',
            ]);
        }

        $documents = $submission->documents()->get();
        $progress = $submission->getProgressPercentage();

        return view('student.pengajuan.yudisium', [
            'student' => $student,
            'submission' => $submission,
            'documents' => $documents,
            'progress' => $progress,
        ]);
    }

    /**
     * Display articles/information page for students.
     */
    public function articles(): View
    {
        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('student.articles.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Display single article detail.
     */
    public function showArticle(Article $article): View
    {
        // Only show published articles
        if ($article->status !== 'published' || ($article->published_at && $article->published_at->isFuture())) {
            abort(404);
        }

        // Increment views
        $article->incrementViews();

        return view('student.articles.detail', [
            'article' => $article,
        ]);
    }

    /**
     * Show profile edit page.
     */
    public function editProfile(): View
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(404);
        }

        return redirect()->route('profile.edit');
    }

    /**
     * Update student profile photo.
     */
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->foto && \Storage::exists('public/' . $user->foto)) {
            \Storage::delete('public/' . $user->foto);
        }

        // Store new photo
        $path = $request->file('foto')->store('users/photos', 'public');

        $user->update([
            'foto' => $path,
        ]);

        return response()->json([
            'message' => 'Foto profile berhasil diperbarui',
            'foto' => \Storage::url($path),
        ]);
    }

    /**
     * Get notifications for the authenticated user.
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();
        $notifications = Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $notifications->where('is_read', false)->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationAsRead(Notification $notification): JsonResponse
    {
        $user = Auth::user();

        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): JsonResponse
    {
        $user = Auth::user();
        Notification::forUser($user->id)->unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function deleteAllNotifications(): JsonResponse
    {
        $user = Auth::user();
        Notification::forUser($user->id)->delete();

        return response()->json(['success' => true]);
    }
}
