<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\History;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalBooks = Book::count();
        $totalHistories = History::count();
        $totalReadingTime = History::sum('total_time_spent');

        $days = floor($totalReadingTime / 86400);
        $hours = floor(($totalReadingTime % 86400) / 3600);
        $minutes = floor(($totalReadingTime % 3600) / 60);
        $seconds = $totalReadingTime % 60;

        if ($days > 0) {
            $totalReadingTimeFormatted = "{$days} hari {$hours} jam";
        } elseif ($hours > 0) {
            $totalReadingTimeFormatted = "{$hours} jam {$minutes} menit";
        } elseif ($minutes > 0) {
            $totalReadingTimeFormatted = "{$minutes} menit {$seconds} detik";
        } else {
            $totalReadingTimeFormatted = "{$seconds} detik";
        }

        $recentStudents = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentStudents as $student) {
            $totalTime = History::where('user_id', $student->id)->sum('total_time_spent');
            $totalBooks = History::where('user_id', $student->id)->distinct('book_id')->count('book_id');

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
            'totalHistories',
            'totalReadingTimeFormatted',
            'recentStudents',
            'recentBooks'
        ));
    }
}
