<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

try {
    // Check if class exists
    if (class_exists('PhpOffice\\PhpWord\\TemplateProcessor')) {
        echo "TemplateProcessor class exists\n";
    } else {
        echo "TemplateProcessor class does not exist\n";
        exit(1);
    }

    // Check if we can reflect the class
    try {
        $reflectionClass = new ReflectionClass('PhpOffice\\PhpWord\\TemplateProcessor');
        echo "Successfully reflected TemplateProcessor class\n";
    } catch (Exception $e) {
        echo "Failed to reflect TemplateProcessor class: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Try to instantiate with a dummy file to catch potential errors
    try {
        $templateProcessor = new TemplateProcessor('dummy.docx');
        echo "Successfully instantiated TemplateProcessor\n";
    } catch (Exception $e) {
        echo "Error instantiating TemplateProcessor: " . $e->getMessage() . "\n";
        // Don't exit here, as this error is expected with a dummy file
    }

    echo "Test completed\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
