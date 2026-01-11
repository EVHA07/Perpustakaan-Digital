<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\UserBookStats;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalBooks = Book::count();

        $recentStudents = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentStudents as $student) {
            $totalTime = UserBookStats::where('user_id', $student->id)->sum('total_seconds');
            $totalBooks = UserBookStats::where('user_id', $student->id)->where('total_seconds', '>', 0)->count();

            $days = floor($totalTime / 86400);
            $hours = floor(($totalTime % 86400) / 3600);
            $minutes = floor(($totalTime % 3600) / 60);
            $seconds = $totalTime % 60;

            if ($days > 0) {
                $student->reading_time_formatted = "{$days}h {$hours}j";
            } elseif ($hours > 0) {
                $student->reading_time_formatted = "{$hours}j {$minutes}m";
            } elseif ($minutes > 0) {
                $student->reading_time_formatted = "{$minutes}m {$seconds}s";
            } else {
                $student->reading_time_formatted = "{$seconds}s";
            }

            $student->total_books_read = $totalBooks;
        }

        $recentBooks = Book::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalBooks',
            'recentStudents',
            'recentBooks'
        ));
    }
}
