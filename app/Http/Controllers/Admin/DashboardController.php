<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilledLetter;
use App\Models\LetterQueue;
use App\Models\LetterType;
use App\Models\ServiceSchedule;
use App\Models\TemplateSurat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        // Ambil data untuk card-card dashboard
        $data = [
            'totalTemplates' => TemplateSurat::count(),
            'totalLetterTypes' => LetterType::count(),
            'totalUsers' => User::where('role', 'user')->count(),
            'pendingLetters' => FilledLetter::where('status', 'pending')->count(),
            'approvedLetters' => FilledLetter::where('status', 'approved')->count(),
            'printedLetters' => FilledLetter::where('status', 'dicetak')->count(),
            'recentLetters' => FilledLetter::with(['user', 'letterType'])->latest()->take(5)->get()
        ];

        // Ambil jadwal pelayanan yang aktif
        $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

        // Tambahkan jadwal pelayanan ke data
        if ($serviceSchedule) {
            $data['serviceSchedule'] = $serviceSchedule;
        }

        // Ambil data antrian saat ini
        $now = Carbon::now();

        // Cari antrian yang sedang berlangsung (status processing)
        $processingQueue = LetterQueue::with(['filledLetter.user'])
            ->where('status', 'processing')
            ->first();

        if ($processingQueue) {
            // Jika ada antrian yang sedang diproses, tampilkan itu
            $data['currentQueue'] = $processingQueue;

            // Hitung waktu selesai berdasarkan waktu mulai + durasi pemrosesan
            if ($serviceSchedule) {
                $data['currentQueueEndTime'] = Carbon::parse($processingQueue->scheduled_date)
                    ->addMinutes($serviceSchedule->processing_time);
            } else {
                // Default 30 menit jika tidak ada jadwal pelayanan
                $data['currentQueueEndTime'] = Carbon::parse($processingQueue->scheduled_date)->addMinutes(30);
            }
        } else {
            // Jika tidak ada yang sedang diproses, cari antrian berikutnya
            // Cari antrian dengan status waiting yang jadwalnya paling dekat dengan waktu sekarang
            $nextQueue = LetterQueue::with(['filledLetter.user'])
                ->where('status', 'waiting')
                ->orderBy('scheduled_date', 'asc')
                ->first();

            if ($nextQueue) {
                // Jika ada antrian berikutnya
                $data['currentQueue'] = $nextQueue;

                // Hitung waktu selesai
                if ($serviceSchedule) {
                    $data['currentQueueEndTime'] = Carbon::parse($nextQueue->scheduled_date)
                        ->addMinutes($serviceSchedule->processing_time);
                } else {
                    // Default 30 menit jika tidak ada jadwal pelayanan
                    $data['currentQueueEndTime'] = Carbon::parse($nextQueue->scheduled_date)->addMinutes(30);
                }
            }
            // Jika tidak ada antrian sama sekali, currentQueue tidak akan diset
        }

        return view('admin.dashboard', compact('data'));
    }
}
