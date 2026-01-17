<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$students = \App\Models\Student::select('nim', 'nama', 'prodi', 'tak', 'ipk')->get();

echo "\n=== Student Data ===\n";
echo str_repeat("-", 80) . "\n";
echo sprintf("%-15s | %-20s | %-20s | %-12s | %-8s", "NIM", "Nama", "Prodi", "TAK", "IPK");
echo "\n" . str_repeat("-", 80) . "\n";

foreach ($students as $s) {
    echo sprintf("%-15s | %-20s | %-20s | %-12s | %-8s", $s->nim, $s->nama, $s->prodi ?? '-', $s->tak ?? '-', $s->ipk);
    echo "\n";
}

echo str_repeat("-", 80) . "\n";
?>
