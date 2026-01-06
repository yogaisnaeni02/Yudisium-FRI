<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('student.dashboard', [
            'student' => $student,
            'submission' => $submission,
            'documents' => $documents,
            'progress' => $progress,
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
        if ($request->hasFile('files')) {
            $results = [];

            foreach ($request->file('files') as $typeSlug => $files) {
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

                    // Try to find existing document by slug matching existing document types
                    $existingDocument = $submission->documents->first(function ($d) use ($typeSlug) {
                        return \Illuminate\Support\Str::slug($d->type) === $typeSlug;
                    });

                    $filePath = $file->store('yudisium/documents', 'private');

                    $typeName = $existingDocument ? $existingDocument->type : \Illuminate\Support\Str::title(str_replace('-', ' ', $typeSlug));

                    if ($existingDocument) {
                        $existingDocument->update([
                            'file_path' => $filePath,
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

        return view('student.pengajuan-yudisium', [
            'student' => $student,
            'submission' => $submission,
            'documents' => $documents,
            'progress' => $progress,
        ]);
    }
}
