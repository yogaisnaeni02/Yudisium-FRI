<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking cumlaude documents:\n";

$docs = App\Models\Document::where('type', 'like', '%cumlaude%')->get();

if ($docs->count() == 0) {
    echo "No documents found with 'cumlaude' in type\n";
} else {
    foreach($docs as $doc) {
        echo "ID: {$doc->id}, Type: {$doc->type}, Submission: {$doc->submission_id}, Status: {$doc->status}\n";
    }
}

echo "\nChecking all documents with exact type:\n";
$exactDocs = App\Models\Document::where('type', 'Dokumen Cumlaude (Publikasi/Lomba/HKI)')->get();

if ($exactDocs->count() == 0) {
    echo "No documents found with exact type\n";
} else {
    foreach($exactDocs as $doc) {
        echo "ID: {$doc->id}, Type: {$doc->type}, Submission: {$doc->submission_id}, Status: {$doc->status}\n";
    }
}

echo "\nChecking recent documents:\n";
$recentDocs = App\Models\Document::orderBy('created_at', 'desc')->limit(10)->get();

foreach($recentDocs as $doc) {
    echo "ID: {$doc->id}, Type: {$doc->type}, Submission: {$doc->submission_id}, Created: {$doc->created_at}\n";
}
