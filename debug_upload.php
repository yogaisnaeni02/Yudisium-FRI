<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use App\Models\User;

echo "=== DEBUG UPLOAD PROCESS ===\n\n";

// Check if there are any students
$students = Student::all();
echo "Total students: " . $students->count() . "\n";

if ($students->count() > 0) {
    $student = $students->first();
    echo "First student: NIM {$student->nim}, Name: {$student->nama}\n";

    // Check submissions for this student
    $submissions = $student->submissions;
    echo "Submissions for this student: " . $submissions->count() . "\n";

    if ($submissions->count() > 0) {
        $submission = $submissions->first();
        echo "First submission: ID {$submission->id}, Status: {$submission->status}\n";

        // Check documents for this submission
        $documents = $submission->documents;
        echo "Documents for this submission: " . $documents->count() . "\n";

        foreach ($documents as $doc) {
            echo "  - Document ID {$doc->id}: Type '{$doc->type}', Name '{$doc->name}', Status '{$doc->status}'\n";
        }

        // Check specifically for cumlaude documents
        $cumlaudeDocs = $documents->filter(function($doc) {
            return str_contains(strtolower($doc->type), 'cumlaude');
        });
        echo "Cumlaude documents found: " . $cumlaudeDocs->count() . "\n";

        // Check for exact type
        $exactCumlaude = $documents->where('type', 'Dokumen Cumlaude (Publikasi/Lomba/HKI)');
        echo "Exact cumlaude type documents: " . $exactCumlaude->count() . "\n";
    }
}

// Check all documents in the system
$allDocuments = Document::all();
echo "\nAll documents in system: " . $allDocuments->count() . "\n";

$cumlaudeTypes = [];
foreach ($allDocuments as $doc) {
    if (str_contains(strtolower($doc->type), 'cumlaude')) {
        $cumlaudeTypes[] = $doc->type;
    }
}

echo "Unique cumlaude types found: " . implode(', ', array_unique($cumlaudeTypes)) . "\n";

// Check the mapping in StudentController
echo "\n=== CHECKING CONTROLLER MAPPING ===\n";
$controller = new App\Http\Controllers\StudentController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('uploadDocument');
$method->setAccessible(true);

// We can't easily test the mapping without a full request, but let's check the code structure
echo "Controller method exists and is accessible\n";

// Check if the slug mapping works
$testType = 'Dokumen Cumlaude (Publikasi/Lomba/HKI)';
$slug = \Illuminate\Support\Str::slug($testType);
echo "Slug for '{$testType}': '{$slug}'\n";

echo "\n=== CHECKING FILESYSTEM ===\n";
$privatePath = storage_path('app/private/yudisium/documents');
echo "Private storage path exists: " . (file_exists($privatePath) ? 'YES' : 'NO') . "\n";
echo "Private storage path: {$privatePath}\n";

if (file_exists($privatePath)) {
    $files = scandir($privatePath);
    $documentFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..', '.gitignore']);
    });
    echo "Files in private storage: " . count($documentFiles) . "\n";
    foreach ($documentFiles as $file) {
        echo "  - {$file}\n";
    }
}

echo "\n=== DONE ===\n";
