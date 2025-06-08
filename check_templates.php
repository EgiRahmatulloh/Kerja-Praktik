<?php
require 'vendor/autoload.php';

// We're running outside of Laravel's application context
// Use direct path instead
$storagePath = __DIR__ . '/storage/app/public/templates';

echo "Storage path: {$storagePath}\n";

// Check if directory exists
if (!is_dir($storagePath)) {
    echo "Directory does not exist!\n";
    exit(1);
}

// List all DOCX files
$files = glob($storagePath . '/*.docx');
echo "Found " . count($files) . " DOCX files:\n";

if (count($files) > 0) {
    foreach ($files as $file) {
        echo basename($file) . " (" . filesize($file) . " bytes)\n";

        // Try to open the file with TemplateProcessor
        try {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($file);
            echo "  - Successfully opened with TemplateProcessor\n";

            // Get all variables in the template
            $variables = $templateProcessor->getVariables();
            echo "  - Variables found: " . implode(', ', $variables) . "\n";
        } catch (\Exception $e) {
            echo "  - Error opening with TemplateProcessor: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
} else {
    echo "No DOCX files found in the templates directory.\n";

    // List all files in the directory
    $allFiles = scandir($storagePath);
    echo "\nAll files in directory:\n";
    foreach ($allFiles as $file) {
        if ($file != '.' && $file != '..') {
            echo $file . "\n";
        }
    }
}
