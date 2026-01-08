<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalTimeSpent = $user->total_reading_time;
        $totalBooksRead = $user->total_books_read;

        $hours = floor($totalTimeSpent / 3600);
        $minutes = floor(($totalTimeSpent % 3600) / 60);
        $timeFormatted = $hours > 0 ? "{$hours} jam {$minutes} menit" : "{$minutes} menit";

        $histories = History::with('book')
            ->where('user_id', Auth::id())
            ->orderBy('last_read_at', 'desc')
            ->get();

        return view('frontend.history', compact('histories', 'timeFormatted', 'totalBooksRead'));
    }
}
