<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

try {
    // Simulate the FilledLetterController::generateDocx method
    echo "Simulating FilledLetterController::generateDocx method\n";

    // Path to the template file
    $templatePath = __DIR__ . '/storage/app/public/templates/7IbMBmAzzSJSe58QCqCNaAZE0hdr3F6lqJCs0u6m.docx';

    // Check if template file exists
    if (!file_exists($templatePath)) {
        echo "Template file not found at: {$templatePath}\n";
        exit(1);
    }

    echo "Template file found at: {$templatePath}\n";

    // Simulate filled_data from FilledLetter model
    $filled_data = [
        'nama' => 'John Doe',
        'alamat' => 'Jl. Example No. 123',
        'tglSurat' => date('Y-m-d'),
        'ttd' => 'John Doe',
        'namaTtd' => 'John Doe'
    ];

    // Simulate no_surat
    $no_surat = 'ABC/123/' . date('Y');

    echo "Creating TemplateProcessor instance...\n";

    // Create template processor
    $templateProcessor = new TemplateProcessor($templatePath);

    echo "TemplateProcessor created successfully\n";

    // Get all variables in the template
    $variables = $templateProcessor->getVariables();
    echo "Variables found in template: " . implode(', ', $variables) . "\n";

    echo "Setting values for variables...\n";

    // Set values for all filled_data
    foreach ($filled_data as $key => $value) {
        echo "Setting {$key} = {$value}\n";
        $templateProcessor->setValue($key, $value);
        // Also set with data. prefix as in the controller
        $templateProcessor->setValue('data.' . $key, $value);
    }

    // Format nomor surat
    echo "Setting noSurat = {$no_surat}\n";
    $templateProcessor->setValue('noSurat', $no_surat);
    $templateProcessor->setValue('data.noSurat', $no_surat);

    // Ganti variabel tanggal surat
    $tglSurat = $filled_data['tglSurat'] ?? date('Y-m-d');
    echo "Setting tglSurat = {$tglSurat}\n";
    $templateProcessor->setValue('tglSurat', $tglSurat);
    $templateProcessor->setValue('data.tglSurat', $tglSurat);

    // Format tanggal yang lebih kompleks
    $formattedDate = date('d M Y', strtotime($tglSurat));
    echo "Setting formattedDate = {$formattedDate}\n";
    $templateProcessor->setValue('formattedDate', $formattedDate);
    $templateProcessor->setValue('data.formattedDate', $formattedDate);

    // Ganti variabel bulan dan tahun
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Array nama bulan dalam bahasa Indonesia
    $namaBulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    $bulanHuruf = $namaBulan[$currentMonth];

    // Bulan dalam format angka
    echo "Setting bulan = {$currentMonth}\n";
    $templateProcessor->setValue('bulan', $currentMonth);
    $templateProcessor->setValue('data.bulan', $currentMonth);

    // Bulan dalam format huruf
    echo "Setting bulanHuruf = {$bulanHuruf}\n";
    $templateProcessor->setValue('bulanHuruf', $bulanHuruf);
    $templateProcessor->setValue('data.bulanHuruf', $bulanHuruf);

    // Tahun
    echo "Setting tahun = {$currentYear}\n";
    $templateProcessor->setValue('tahun', $currentYear);
    $templateProcessor->setValue('data.tahun', $currentYear);

    // Ganti variabel ttd dan namaTtd
    if (isset($filled_data['ttd'])) {
        echo "Setting ttd = {$filled_data['ttd']}\n";
        $templateProcessor->setValue('ttd', $filled_data['ttd']);
        $templateProcessor->setValue('data.ttd', $filled_data['ttd']);
    }

    if (isset($filled_data['namaTtd'])) {
        echo "Setting namaTtd = {$filled_data['namaTtd']}\n";
        $templateProcessor->setValue('namaTtd', $filled_data['namaTtd']);
        $templateProcessor->setValue('data.namaTtd', $filled_data['namaTtd']);
    }

    // Simulate saving the file
    $letterTypeName = 'TestLetterType';
    $userName = 'JohnDoe';
    $currentDate = date('Y-m-d');
    $letterId = '123';

    $fileName = $letterTypeName . '_' . $userName . '_' . $currentDate . '_' . $letterId . '.docx';
    $outputPath = __DIR__ . '/' . $fileName;

    echo "Saving document to: {$outputPath}\n";
    $templateProcessor->saveAs($outputPath);

    echo "Document successfully generated at: {$outputPath}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
