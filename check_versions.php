<?php
require 'vendor/autoload.php';

echo 'PHP version: ' . phpversion() . PHP_EOL;

// Check if the TemplateProcessor class exists
echo 'TemplateProcessor class exists: ' . (class_exists('PhpOffice\PhpWord\TemplateProcessor') ? 'Yes' : 'No') . PHP_EOL;

// Check if we can instantiate a PhpWord object
try {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    echo 'PhpWord object can be instantiated: Yes' . PHP_EOL;
} catch (Exception $e) {
    echo 'PhpWord object can be instantiated: No - ' . $e->getMessage() . PHP_EOL;
}

// Check if the required extensions are loaded
echo 'ZIP extension loaded: ' . (extension_loaded('zip') ? 'Yes' : 'No') . PHP_EOL;
echo 'XML extension loaded: ' . (extension_loaded('xml') ? 'Yes' : 'No') . PHP_EOL;
echo 'XMLWriter extension loaded: ' . (extension_loaded('xmlwriter') ? 'Yes' : 'No') . PHP_EOL;
