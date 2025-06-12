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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $user = Auth::user();
        $templateQuery = TemplateSurat::query();

        if ($user->sub_role) {
            $templateQuery->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('share_setting', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('share_setting', 'limited')
                         ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                             $q3->where('users.id', $user->id);
                         });
                  });
            });
        } else {
            $templateQuery->where('owner_id', $user->id)
                          ->orWhere('share_setting', 'public')
                          ->orWhere(function ($q2) use ($user) {
                              $q2->where('share_setting', 'limited')
                                 ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                                     $q3->where('users.id', $user->id);
                                 });
                          });
        }

        // Ambil data untuk card-card dashboard
        $data = [
            'totalTemplates' => $templateQuery->count(),
            'totalLetterTypes' => LetterType::whereHas('templateSurat', function ($q) use ($templateQuery) {
                $q->whereIn('id', $templateQuery->pluck('id'));
            })->count(),
            'totalUsers' => User::where('role', 'user')->count(),
        ];

        // Query dasar untuk FilledLetter yang akan difilter
        $filledLetterBaseQuery = FilledLetter::query();

        // Terapkan filter kepemilikan template atau pengaturan berbagi jika sub_role
        if ($user->sub_role) {
            $filledLetterBaseQuery->whereHas('letterType.templateSurat', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('share_setting', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('share_setting', 'limited')
                         ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                             $q3->where('users.id', $user->id);
                         });
                  });
            });
        } else {
            // Admin utama melihat semua surat, tidak perlu filter user_id
            // Karena halaman ini untuk melihat semua pengajuan surat yang relevan
        }

        $data['pendingLetters'] = (clone $filledLetterBaseQuery)->where('status', 'pending')->count();
        $data['approvedLetters'] = (clone $filledLetterBaseQuery)->where('status', 'approved')->count();
        $data['printedLetters'] = (clone $filledLetterBaseQuery)->where('status', 'dicetak')->count();
        $data['recentLetters'] = (clone $filledLetterBaseQuery)->with(['user', 'letterType'])->latest()->take(5)->get();

        // Ambil jadwal pelayanan yang aktif dari admin yang sedang login
        $serviceSchedule = ServiceSchedule::where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        // Tambahkan jadwal pelayanan ke data
        if ($serviceSchedule) {
            $data['serviceSchedule'] = $serviceSchedule;
        }

        // Ambil data antrian saat ini
        $now = Carbon::now();

        // Cari antrian yang sedang berlangsung (status processing) dari jadwal admin yang sedang login
        $processingQueue = LetterQueue::with(['filledLetter.user'])
            ->where('status', 'processing')
            ->whereHas('serviceSchedule', function($q) {
                $q->where('user_id', auth()->id());
            })
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
            // Jika tidak ada yang sedang diproses, cari antrian berikutnya dari jadwal admin yang sedang login
            // Cari antrian dengan status waiting yang jadwalnya paling dekat dengan waktu sekarang
            $nextQueue = LetterQueue::with(['filledLetter.user'])
                ->where('status', 'waiting')
                ->whereHas('serviceSchedule', function($q) {
                    $q->where('user_id', auth()->id());
                })
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
