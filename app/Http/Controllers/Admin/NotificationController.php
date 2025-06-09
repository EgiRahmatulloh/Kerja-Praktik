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
        
        // Ambil surat baru yang belum dilihat
        $newLetters = FilledLetter::with('user', 'letterType')
            ->where('id', '>', $lastSeenId)
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();
        
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
        // Ambil ID surat terbaru
        $latestLetter = FilledLetter::latest()->first();
        
        if ($latestLetter) {
            Session::put('last_seen_letter_id', $latestLetter->id);
        }
        
        return response()->json([
            'success' => true
        ]);
    }
}