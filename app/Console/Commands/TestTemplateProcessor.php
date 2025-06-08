<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\TemplateSurat;
use App\Models\LetterType;
use App\Models\FilledLetter;
use Illuminate\Support\Facades\Storage;

class TestTemplateProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:template-processor {template_id? : ID of the template to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the PhpWord TemplateProcessor with a template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing PhpWord TemplateProcessor');

        // Get template ID from argument or list templates to choose from
        $templateId = $this->argument('template_id');

        if (!$templateId) {
            $templates = TemplateSurat::where('aktif', true)->get();

            if ($templates->isEmpty()) {
                $this->error('No active templates found!');
                return 1;
            }

            $this->info('Available templates:');
            foreach ($templates as $template) {
                $this->line("ID: {$template->id} - {$template->nama_template}");
            }

            $templateId = $this->ask('Enter template ID to test');
        }

        // Get the template
        $template = TemplateSurat::find($templateId);

        if (!$template) {
            $this->error("Template with ID {$templateId} not found!");
            return 1;
        }

        $this->info("Testing template: {$template->nama_template}");
        $this->info("Template path: {$template->template_path}");
        $this->info("Full path: {$template->full_path}");

        // Check if template file exists
        if (!file_exists($template->full_path)) {
            $this->error("Template file not found at: {$template->full_path}");
            return 1;
        }

        $this->info("Template file exists. Size: " . filesize($template->full_path) . " bytes");

        try {
            // Create template processor
            $this->info("Creating TemplateProcessor instance...");
            $templateProcessor = new TemplateProcessor($template->full_path);
            $this->info("TemplateProcessor created successfully");

            // Get all variables in the template
            $variables = $templateProcessor->getVariables();
            $this->info("Variables found in template: " . implode(', ', $variables));

            // Create sample data
            $this->info("Setting sample values for variables...");
            $sampleData = [
                'nama' => 'John Doe',
                'alamat' => 'Jl. Example No. 123',
                'tglSurat' => date('Y-m-d'),
                'ttd' => 'John Doe',
                'namaTtd' => 'John Doe'
            ];

            // Set values for all sample data
            foreach ($sampleData as $key => $value) {
                $this->line("Setting {$key} = {$value}");
                $templateProcessor->setValue($key, $value);
                // Also set with data. prefix as in the controller
                $templateProcessor->setValue('data.' . $key, $value);
            }

            // Set values for all variables found in the template
            foreach ($variables as $variable) {
                if (!isset($sampleData[$variable]) && !str_starts_with($variable, 'data.')) {
                    $this->line("Setting {$variable} = [Sample Value]");
                    $templateProcessor->setValue($variable, '[Sample Value]');
                }
            }

            // Save the processed document
            $outputPath = storage_path('app/test_output.docx');
            $this->info("Saving document to: {$outputPath}");
            $templateProcessor->saveAs($outputPath);

            $this->info("Document successfully generated at: {$outputPath}");
            $this->info("File size: " . filesize($outputPath) . " bytes");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
