<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LetterQueue;
use App\Models\FilledLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterQueueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }
    
    /**
     * Menampilkan daftar antrian surat milik user
     */
    public function index()
    {
        $queues = LetterQueue::with(['filledLetter.letterType'])
            ->whereHas('filledLetter', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);
            
        return view('user.letter-queues.index', compact('queues'));
    }
    
    /**
     * Menampilkan detail antrian surat
     */
    public function show($id)
    {
        $queue = LetterQueue::with(['filledLetter.letterType'])
            ->whereHas('filledLetter', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->findOrFail($id);
            
        return view('user.letter-queues.show', compact('queue'));
    }
    public function download($id)
    {
        $queue = LetterQueue::whereHas('filledLetter', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        if (!$queue->file_path) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/' . $queue->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        $originalFilename = $queue->original_filename ?: basename($filePath);

        return response()->download($filePath, $originalFilename);
    }
}