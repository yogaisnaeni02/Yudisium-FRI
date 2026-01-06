<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with all submissions.
     */
    public function dashboard(): View
    {
        $submissions = Submission::with('student.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_submissions' => Submission::count(),
            'approved' => Submission::where('status', 'approved')->count(),
            'under_review' => Submission::where('status', 'under_review')->count(),
            'draft' => Submission::where('status', 'draft')->count(),
            'rejected' => Submission::where('status', 'rejected')->count(),
        ];

        return view('admin.dashboard', [
            'submissions' => $submissions,
            'stats' => $stats,
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

        if ($allApproved) {
            $submission->update(['status' => 'approved']);
            \App\Models\Activity::log('approve', 'Semua dokumen disetujui, pengajuan diterima', 'Submission', $submission->id);
        } elseif ($submission->status === 'draft') {
            $submission->update(['status' => 'under_review']);
        }

        return redirect()->back()
            ->with('success', 'Status dokumen berhasil diperbarui!');
    }

    /**
     * Download document.
     */
    public function downloadDocument(Document $document)
    {
        return \Storage::download($document->file_path, $document->name);
    }
}
