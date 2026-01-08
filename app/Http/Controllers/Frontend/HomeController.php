<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $continueReading = History::with('book')
            ->where('user_id', Auth::id())
            ->orderBy('last_read_at', 'desc')
            ->limit(4)
            ->get();

        $latestBooks = Book::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Calculate reading stats from histories (server-side truth)
        $totalTimeSpent = History::where('user_id', Auth::id())->sum('total_time_spent');
        $totalBooksRead = History::where('user_id', Auth::id())->distinct('book_id')->count('book_id');

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
