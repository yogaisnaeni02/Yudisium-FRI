<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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

        return view('student.dashboard', [
            'student' => $student,
            'submission' => $submission,
            'documents' => $documents,
            'progress' => $progress,
            'latestArticles' => $latestArticles,
        ]);
    }

    /**
     * Upload document (supports single upload and grouped bulk uploads per section).
     */
    public function uploadDocument(Request $request)
    {
        \Log::info('Upload document called', [
            'has_files' => $request->hasFile('files'),
            'all_files' => $request->allFiles(),
            'all_data' => $request->all()
        ]);

        // No validation required - we'll handle it gracefully in the loop

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
        if ($request->has('files') && is_array($request->file('files')) && !empty($request->file('files'))) {
            \Log::info('Processing bulk upload', [
                'files_structure' => $request->file('files'),
                'raw_request_files' => $request->allFiles(),
                'request_all' => $request->all()
            ]);
            $results = [];
            $processedCount = 0;
            $skippedCount = 0;

            // Define all valid document types and their display names
            // Note: These slugs must match exactly what \Illuminate\Support\Str::slug() generates from the display names
            $validDocumentTypes = [
                'surat-pernyataan' => 'Surat Pernyataan',
                'form-biodata-izajah-transkip' => 'Form Biodata Izajah & Transkip',
                'ktp' => 'KTP',
                'akta-lahir' => 'Akta Lahir',
                'ijazah-pendidikan-terakhir' => 'Ijazah Pendidikan Terakhir',
                'buku-ta-yang-disahkan' => 'Buku TA yang Disahkan',
                'slide-ppt' => 'Slide PPT',
                'screenshot-gracias' => 'Screenshot (Gracias)',
                'berkas-referensi-minimal-10' => 'Berkas Referensi (Minimal 10)',
                'bukti-approval-revisi-sofi' => 'Bukti Approval Revisi (SOFI)',
                'bukti-approval-skpi' => 'Bukti Approval SKPI',
                'surat-keterangan-bebas-pustaka-skbp' => 'Surat Keterangan Bebas Pustaka (SKBP)',
                'dokumen-cumlaude-publikasilombahki' => 'Dokumen Cumlaude (Publikasi/Lomba/HKI)',
                'dokumen-pendukung-tambahan' => 'Dokumen Pendukung Tambahan',
            ];

            foreach ($request->file('files') as $typeSlug => $files) {
                \Log::info('Processing typeSlug: ' . $typeSlug, [
                    'files_type' => gettype($files),
                    'files_count' => is_array($files) ? count($files) : (is_object($files) ? 'object' : 'not_array'),
                    'files_value' => is_array($files) ? 'array' : (is_object($files) ? get_class($files) : $files)
                ]);

                // Skip if this typeSlug is not in our valid list
                if (!isset($validDocumentTypes[$typeSlug])) {
                    \Log::warning('Invalid document type slug received', ['typeSlug' => $typeSlug]);
                    $skippedCount++;
                    continue;
                }

                // Handle both single file and array of files
                $filesArray = is_array($files) ? $files : [$files];

                \Log::info('Files array for ' . $typeSlug, [
                    'array_count' => count($filesArray),
                    'array_keys' => array_keys($filesArray)
                ]);

                foreach ($filesArray as $index => $file) {
                    \Log::info('Processing file at index ' . $index, [
                        'filename' => $file ? $file->getClientOriginalName() : 'null',
                        'is_valid' => $file ? $file->isValid() : false,
                        'file_type' => gettype($file),
                        'file_class' => is_object($file) ? get_class($file) : 'not_object'
                    ]);

                    if (!$file || !$file->isValid()) {
                        \Log::warning('File is invalid or null', ['typeSlug' => $typeSlug, 'index' => $index]);
                        $skippedCount++;
                        continue;
                    }

                    $ext = strtolower($file->getClientOriginalExtension());
                    if (!in_array($ext, ['pdf','doc','docx'])) {
                        \Log::warning('Invalid file extension', ['ext' => $ext, 'typeSlug' => $typeSlug, 'filename' => $file->getClientOriginalName()]);
                        $skippedCount++;
                        continue;
                    }

                    if ($file->getSize() > 5 * 1024 * 1024) {
                        \Log::warning('File too large', ['size' => $file->getSize(), 'typeSlug' => $typeSlug, 'filename' => $file->getClientOriginalName()]);
                        $skippedCount++;
                        continue;
                    }

                    // Use the predefined document type name
                    $typeName = $validDocumentTypes[$typeSlug];

                    // Try to find existing document by exact type name match
                    $existingDocument = $submission->documents->first(function ($d) use ($typeName) {
                        return $d->type === $typeName;
                    });

                    \Log::info('Existing document check', [
                        'typeSlug' => $typeSlug,
                        'typeName' => $typeName,
                        'existing_found' => $existingDocument ? true : false,
                        'existing_id' => $existingDocument ? $existingDocument->id : null
                    ]);

                    $filePath = $file->store('yudisium/documents', 'private');
                    $fileContent = base64_encode(file_get_contents($file->getRealPath()));

                    \Log::info('Creating/updating document', [
                        'typeName' => $typeName,
                        'filename' => $file->getClientOriginalName()
                    ]);

                    if ($existingDocument) {
                        $existingDocument->update([
                            'type' => $typeName,
                            'file_path' => $filePath,
                            'file_content' => $fileContent,
                            'mime_type' => $file->getMimeType(),
                            'original_filename' => $file->getClientOriginalName(),
                            'status' => 'pending',
                            'feedback' => null,
                        ]);

                        \App\Models\Activity::log('upload', 'Mengunggah ulang dokumen: ' . $typeName, 'Document', $existingDocument->id);
                        $document = $existingDocument;
                    } else {
                        $document = Document::create([
                            'submission_id' => $submission->id,
                            'type' => $typeName,
                            'name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_content' => $fileContent,
                            'mime_type' => $file->getMimeType(),
                            'original_filename' => $file->getClientOriginalName(),
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

                    $processedCount++;
                    \Log::info('Document processed successfully', ['count' => $processedCount, 'type' => $typeName]);
                }
            }

            \Log::info('Bulk upload completed', [
                'total_processed' => $processedCount,
                'total_skipped' => $skippedCount,
                'results_count' => count($results)
            ]);

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
        $fileContent = base64_encode(file_get_contents($request->file('file')->getRealPath()));

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
                'file_content' => $fileContent,
                'mime_type' => $request->file('file')->getMimeType(),
                'original_filename' => $request->file('file')->getClientOriginalName(),
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
                'file_content' => $fileContent,
                'mime_type' => $request->file('file')->getMimeType(),
                'original_filename' => $request->file('file')->getClientOriginalName(),
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

        return view('student.pengajuan-yudisium', [
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

        return view('student.articles', [
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

        return view('student.article-detail', [
            'article' => $article,
        ]);
    }
}
