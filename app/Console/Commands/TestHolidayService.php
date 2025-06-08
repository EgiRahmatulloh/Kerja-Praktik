<?php

namespace App\Console\Commands;

use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestHolidayService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holiday:test {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test holiday service untuk mengecek hari libur';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $holidayService = new HolidayService();
        
        // Ambil tanggal dari argument atau gunakan hari ini
        $dateInput = $this->argument('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::today();
        
        $this->info("Testing HolidayService untuk tanggal: {$date->format('Y-m-d (l)')}\n");
        
        // Test apakah hari libur
        $isHoliday = $holidayService->isHoliday($date);
        $this->info("Apakah {$date->format('Y-m-d')} hari libur? " . ($isHoliday ? 'YA' : 'TIDAK'));
        
        // Test apakah hari kerja
        $isWorkingDay = $holidayService->isWorkingDay($date);
        $this->info("Apakah {$date->format('Y-m-d')} hari kerja? " . ($isWorkingDay ? 'YA' : 'TIDAK'));
        
        // Test hari kerja berikutnya
        $nextWorkingDay = $holidayService->getNextWorkingDay($date);
        $this->info("Hari kerja berikutnya setelah {$date->format('Y-m-d')}: {$nextWorkingDay->format('Y-m-d (l)')}\n");
        
        // Test beberapa tanggal ke depan
        $this->info("Testing 10 hari ke depan:");
        for ($i = 0; $i < 10; $i++) {
            $testDate = $date->copy()->addDays($i);
            $isHoliday = $holidayService->isHoliday($testDate);
            $status = $isHoliday ? 'LIBUR' : 'KERJA';
            $this->line("{$testDate->format('Y-m-d (l)')} - {$status}");
        }
        
        return 0;
    }
}