<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilledLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Memeriksa notifikasi baru
     */
    public function check()
    {
        // Ambil ID surat terakhir yang sudah dilihat dari session
        $lastSeenId = Session::get('last_seen_letter_id', 0);
        
        // Cek apakah ada surat dengan ID yang lebih kecil atau sama dengan lastSeenId
        // Jika tidak ada, reset lastSeenId ke 0 (kemungkinan database di-reset)
        if ($lastSeenId > 0) {
            $existingLetter = FilledLetter::where('id', '<=', $lastSeenId)->exists();
            if (!$existingLetter) {
                $lastSeenId = 0;
                Session::put('last_seen_letter_id', 0);
            }
        }
        
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = FilledLetter::with('user', 'letterType')
            ->where('id', '>', $lastSeenId)
            ->where('status', 'pending')
            ->orderBy('id', 'desc');

        // Terapkan filter kepemilikan template atau pengaturan berbagi jika sub_role
        if ($user->sub_role) {
            $query->whereHas('letterType.templateSurat', function ($q) use ($user) {
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
            // Admin utama melihat semua surat, tetapi tetap kecualikan surat yang dibuat oleh admin lain
            $adminUserIds = \App\Models\User::where('role', 'admin')->pluck('id');
            $query->whereNotIn('user_id', $adminUserIds);
        }

        $newLetters = $query->get();
        
        // Jika ada surat baru, update last_seen_letter_id di session
        if ($newLetters->count() > 0) {
            Session::put('last_seen_letter_id', $newLetters->first()->id);
        }
        
        // Format notifikasi
        $notifications = [];
        foreach ($newLetters as $letter) {
            $notifications[] = [
                'title' => 'Surat Baru Masuk',
                'message' => "Mahasiswa {$letter->user->name} mengajukan surat {$letter->letterType->nama_jenis}",
                'time' => $letter->created_at->diffForHumans(),
                'url' => route('admin.filled-letters.show', $letter->id)
            ];
        }
        
        return response()->json([
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Menandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = FilledLetter::latest();

        // Terapkan filter kepemilikan template atau pengaturan berbagi jika sub_role
        if ($user->sub_role) {
            $query->whereHas('letterType.templateSurat', function ($q) use ($user) {
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
            // Admin utama melihat semua surat, tetapi tetap kecualikan surat yang dibuat oleh admin lain
            $adminUserIds = \App\Models\User::where('role', 'admin')->pluck('id');
            $query->whereNotIn('user_id', $adminUserIds);
        }

        // Ambil ID surat terbaru yang relevan dengan admin
        $latestLetter = $query->first();
        
        if ($latestLetter) {
            Session::put('last_seen_letter_id', $latestLetter->id);
        }
        
        return response()->json([
            'success' => true
        ]);
    }
}
