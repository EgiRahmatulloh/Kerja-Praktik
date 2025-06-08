<?php
require 'vendor/autoload.php';

try {
    echo "Testing PhpOffice\PhpWord\TemplateProcessor with a real template\n";

    // Create a simple template file for testing
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    $section->addText('Hello, ${name}!');
    $section->addText('This is a test template created on ${date}.');

    // Save the template
    $templateFile = __DIR__ . '/test_template.docx';
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($templateFile);

    echo "Created test template at: {$templateFile}\n";

    // Now try to use the TemplateProcessor with this template
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templateFile);
    $templateProcessor->setValue('name', 'John Doe');
    $templateProcessor->setValue('date', date('Y-m-d'));

    // Save the processed document
    $outputFile = __DIR__ . '/test_output.docx';
    $templateProcessor->saveAs($outputFile);

    echo "Successfully processed template and saved to: {$outputFile}\n";
    echo "Test completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error type: " . get_class($e) . "\n";
    echo "Error trace: \n" . $e->getTraceAsString() . "\n";
}
