<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

try {
    // Path to the template file
    $templatePath = __DIR__ . '/storage/app/public/templates/7IbMBmAzzSJSe58QCqCNaAZE0hdr3F6lqJCs0u6m.docx';

    // Check if template file exists
    if (!file_exists($templatePath)) {
        echo "Template file not found at: {$templatePath}\n";
        exit(1);
    }

    echo "Template file found at: {$templatePath}\n";

    // Create template processor
    $templateProcessor = new TemplateProcessor($templatePath);

    // Get all variables in the template
    $variables = $templateProcessor->getVariables();
    echo "Variables found in template: " . implode(', ', $variables) . "\n";

    // Set values for the variables
    $templateProcessor->setValue('noSurat', 'ABC/123/2023');
    $templateProcessor->setValue('tglSurat', date('Y-m-d'));
    $templateProcessor->setValue('formattedDate', date('d M Y'));
    $templateProcessor->setValue('bulan', date('m'));
    $templateProcessor->setValue('bulanHuruf', 'Juni'); // Example month name
    $templateProcessor->setValue('tahun', date('Y'));
    $templateProcessor->setValue('nama', 'John Doe');

    // Save the processed document
    $outputPath = __DIR__ . '/test_output.docx';
    $templateProcessor->saveAs($outputPath);

    echo "Document successfully generated at: {$outputPath}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
