<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HolidayService
{
    private $apiKey;
    private $calendarId = 'en.indonesian%23holiday%40group.v.calendar.google.com';
    
    public function __construct()
    {
        $this->apiKey = env('GOOGLE_CALENDAR_API_KEY');
    }
    
    /**
     * Cek apakah tanggal tertentu adalah hari libur
     */
    public function isHoliday(Carbon $date): bool
    {
        // Cek hanya hari Minggu (Sabtu masih masuk kerja)
        if ($date->isSunday()) {
            return true;
        }
        
        // Jika tidak ada API key, hanya cek hari Minggu
        if (!$this->apiKey) {
            Log::warning('Google Calendar API key tidak tersedia, hanya mengecek hari Minggu');
            return false;
        }
        
        // Cek hari libur nasional dari Google Calendar
        return $this->isNationalHoliday($date);
    }
    
    /**
     * Cek hari libur nasional dari Google Calendar API
     */
    private function isNationalHoliday(Carbon $date): bool
    {
        $cacheKey = 'holidays_' . $date->year;
        
        // Cek cache terlebih dahulu
        $holidays = Cache::get($cacheKey);
        
        if (!$holidays) {
            $holidays = $this->fetchHolidays($date->year);
            // Cache selama 1 bulan
            Cache::put($cacheKey, $holidays, now()->addMonth());
        }
        
        $dateString = $date->format('Y-m-d');
        return in_array($dateString, $holidays);
    }
    
    /**
     * Ambil daftar hari libur dari Google Calendar API
     */
    private function fetchHolidays(int $year): array
    {
        try {
            $timeMin = $year . '-01-01T00:00:00Z';
            $timeMax = $year . '-12-31T23:59:59Z';
            
            $response = Http::timeout(10)->get('https://www.googleapis.com/calendar/v3/calendars/' . $this->calendarId . '/events', [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => 'true',
                'orderBy' => 'startTime',
                'key' => $this->apiKey
            ]);
            
            if ($response->successful()) {
                $events = $response->json()['items'] ?? [];
                $holidays = [];
                
                foreach ($events as $event) {
                    if (isset($event['start']['date'])) {
                        $holidays[] = $event['start']['date'];
                    }
                }
                
                Log::info('Berhasil mengambil ' . count($holidays) . ' hari libur untuk tahun ' . $year);
                return $holidays;
            } else {
                Log::error('Gagal mengambil data hari libur: ' . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data hari libur: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cari hari kerja berikutnya (bukan hari Minggu dan bukan hari libur nasional)
     */
    public function getNextWorkingDay(Carbon $date): Carbon
    {
        $nextDay = $date->copy()->addDay();
        
        // Maksimal cek 30 hari ke depan untuk menghindari infinite loop
        $maxAttempts = 30;
        $attempts = 0;
        
        while ($this->isHoliday($nextDay) && $attempts < $maxAttempts) {
            $nextDay->addDay();
            $attempts++;
        }
        
        if ($attempts >= $maxAttempts) {
            Log::warning('Tidak dapat menemukan hari kerja dalam 30 hari ke depan, menggunakan tanggal asli');
            return $date->copy()->addDay();
        }
        
        return $nextDay;
    }
    
    /**
     * Cek apakah tanggal adalah hari kerja
     */
    public function isWorkingDay(Carbon $date): bool
    {
        return !$this->isHoliday($date);
    }
}