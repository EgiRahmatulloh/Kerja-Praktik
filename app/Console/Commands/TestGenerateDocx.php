<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\FilledLetterController;
use App\Models\FilledLetter;
use Illuminate\Support\Facades\Storage;

class TestGenerateDocx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:generate-docx {letter_id? : ID of the filled letter to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the generateDocx method in FilledLetterController';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing FilledLetterController::generateDocx method');

        // Get letter ID from argument or list letters to choose from
        $letterId = $this->argument('letter_id');

        if (!$letterId) {
            $letters = FilledLetter::with(['user', 'letterType'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($letters->isEmpty()) {
                $this->error('No filled letters found!');
                return 1;
            }

            $this->info('Recent filled letters:');
            foreach ($letters as $letter) {
                $this->line("ID: {$letter->id} - Type: {$letter->letterType->name} - User: {$letter->user->name} - Status: {$letter->status}");
            }

            $letterId = $this->ask('Enter letter ID to test');
        }

        // Get the letter
        $letter = FilledLetter::with(['user', 'letterType', 'letterType.templateSurat'])
            ->find($letterId);

        if (!$letter) {
            $this->error("Letter with ID {$letterId} not found!");
            return 1;
        }

        $this->info("Testing letter: ID {$letter->id} - Type: {$letter->letterType->name}");

        // Check if template exists
        if (!$letter->letterType->templateSurat) {
            $this->error("Template not found for letter type: {$letter->letterType->name}");
            return 1;
        }

        $templatePath = $letter->letterType->templateSurat->full_path;
        $this->info("Template path: {$templatePath}");

        // Check if template file exists
        if (!file_exists($templatePath)) {
            $this->error("Template file not found at: {$templatePath}");
            return 1;
        }

        $this->info("Template file exists. Size: " . filesize($templatePath) . " bytes");

        // Display filled_data
        $this->info("Filled data:");
        foreach ($letter->filled_data as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        try {
            // Create controller instance
            $controller = new FilledLetterController();

            // Capture the response
            $this->info("Calling FilledLetterController::generateDocx({$letterId})...");

            // Instead of directly calling the controller method which would return a download response,
            // we'll simulate the process and save the file locally
            $this->simulateGenerateDocx($letter);

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Simulate the generateDocx method without returning a download response
     */
    private function simulateGenerateDocx($letter)
    {
        $this->info("Simulating generateDocx method...");

        $template = $letter->letterType->templateSurat;

        // Ambil path file template DOCX
        $templatePath = $template->full_path;

        // Baca template DOCX yang sudah ada
        $this->info("Creating TemplateProcessor instance...");
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $this->info("TemplateProcessor created successfully");

        // Get all variables in the template
        $variables = $templateProcessor->getVariables();
        $this->info("Variables found in template: " . implode(', ', $variables));

        // Ganti semua variabel dengan nilai sebenarnya menggunakan TemplateProcessor
        $this->info("Setting values for variables...");
        foreach ($letter->filled_data as $key => $value) {
            $this->line("Setting {$key} = {$value}");
            $templateProcessor->setValue($key, $value);
            $templateProcessor->setValue('data.' . $key, $value);
        }

        // Format nomor surat
        $formattedNoSurat = $letter->no_surat;
        $this->line("Setting noSurat = {$formattedNoSurat}");
        $templateProcessor->setValue('noSurat', $formattedNoSurat);
        $templateProcessor->setValue('data.noSurat', $formattedNoSurat);

        // Ganti variabel tanggal surat
        $tglSurat = date('Y-m-d');
        if (isset($letter->filled_data['tglSurat'])) {
            $tglSurat = $letter->filled_data['tglSurat'];
        }
        $this->line("Setting tglSurat = {$tglSurat}");
        $templateProcessor->setValue('tglSurat', $tglSurat);
        $templateProcessor->setValue('data.tglSurat', $tglSurat);

        // Format tanggal yang lebih kompleks
        $formattedDate = date('d M Y', strtotime($tglSurat));
        $this->line("Setting formattedDate = {$formattedDate}");
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
        $this->line("Setting bulan = {$currentMonth}");
        $templateProcessor->setValue('bulan', $currentMonth);
        $templateProcessor->setValue('data.bulan', $currentMonth);

        // Bulan dalam format huruf
        $this->line("Setting bulanHuruf = {$bulanHuruf}");
        $templateProcessor->setValue('bulanHuruf', $bulanHuruf);
        $templateProcessor->setValue('data.bulanHuruf', $bulanHuruf);

        // Tahun
        $this->line("Setting tahun = {$currentYear}");
        $templateProcessor->setValue('tahun', $currentYear);
        $templateProcessor->setValue('data.tahun', $currentYear);

        // Ganti variabel ttd dan namaTtd
        if (isset($letter->filled_data['ttd'])) {
            $this->line("Setting ttd = {$letter->filled_data['ttd']}");
            $templateProcessor->setValue('ttd', $letter->filled_data['ttd']);
            $templateProcessor->setValue('data.ttd', $letter->filled_data['ttd']);
        }

        if (isset($letter->filled_data['namaTtd'])) {
            $this->line("Setting namaTtd = {$letter->filled_data['namaTtd']}");
            $templateProcessor->setValue('namaTtd', $letter->filled_data['namaTtd']);
            $templateProcessor->setValue('data.namaTtd', $letter->filled_data['namaTtd']);
        }

        // Simpan hasil template yang sudah diproses
        // Buat nama file yang unik dengan nama user dan tanggal
        $userName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $letter->user->name);
        $letterTypeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $letter->letterType->name);
        $currentDate = date('Y-m-d');

        $fileName = $letterTypeName . '_' . $userName . '_' . $currentDate . '_' . $letter->id . '.docx';
        $outputPath = storage_path('app/test_' . $fileName);

        $this->info("Saving document to: {$outputPath}");
        $templateProcessor->saveAs($outputPath);

        $this->info("Document successfully generated at: {$outputPath}");
        $this->info("File size: " . filesize($outputPath) . " bytes");
    }
}
