<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FilledLetter;
use App\Models\LetterType;
use App\Models\LetterQueue;
use App\Models\Announcement;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }

    public function index()
    {
        $data = [
            'letterTypes' => LetterType::all(),
            'pendingLetters' => FilledLetter::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'approvedLetters' => FilledLetter::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->count(),
            'printedLetters' => FilledLetter::where('user_id', Auth::id())
                ->where('status', 'dicetak')
                ->count(),
            'recentLetters' => FilledLetter::with('letterType')
                ->where('user_id', Auth::id())
                ->latest()
                ->take(5)
                ->get(),
            'totalLetters' => FilledLetter::where('user_id', Auth::id())->count(),
            'nextQueuedLetter' => LetterQueue::with('filledLetter.letterType')
                ->whereHas('filledLetter', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', 'waiting')
                ->orderBy('scheduled_date')
                ->first(),
            'activeAnnouncements' => Announcement::active()->latest()->get(),
            'pausedSchedules' => ServiceSchedule::where('is_active', true)
                ->where('is_paused', true)
                ->whereNotNull('pause_message')
                ->get()
        ];

        return view('user.dashboard', compact('data'));
    }
}
