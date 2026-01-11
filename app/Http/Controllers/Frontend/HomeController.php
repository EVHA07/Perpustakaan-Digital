<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\History;
use App\Models\UserBookStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $continueReading = History::with('book')
            ->where('user_id', Auth::id())
            ->orderBy('last_read_at', 'desc')
            ->limit(4)
            ->get();

        $latestBooks = Book::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $totalTimeSpent = UserBookStats::where('user_id', Auth::id())->sum('total_seconds');
        $totalBooksRead = UserBookStats::where('user_id', Auth::id())->where('total_seconds', '>', 0)->count();

        $days = floor($totalTimeSpent / 86400);
        $hours = floor(($totalTimeSpent % 86400) / 3600);
        $minutes = floor(($totalTimeSpent % 3600) / 60);
        $seconds = $totalTimeSpent % 60;

        if ($days > 0) {
            $timeFormatted = "{$days} hari {$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            $timeFormatted = "{$hours} jam {$minutes} menit";
        } elseif ($minutes > 0) {
            $timeFormatted = "{$minutes} menit {$seconds} detik";
        } else {
            $timeFormatted = "{$seconds} detik";
        }

        return view('frontend.home', compact(
            'continueReading',
            'latestBooks',
            'timeFormatted',
            'totalBooksRead'
        ));
    }
}
