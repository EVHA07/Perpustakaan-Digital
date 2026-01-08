<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function show($id)
    {
        $book = Book::findOrFail($id);

        $history = History::where('user_id', Auth::id())
            ->where('book_id', $id)
            ->first();

        $hasHistory = $history !== null;

        return view('frontend.book', compact('book', 'history', 'hasHistory'));
    }

    public function read($id)
    {
        $book = Book::findOrFail($id);

        // Check if user has access (must be student with active account)
        if (!Auth::check() || Auth::user()->role !== 'student' || !Auth::user()->is_active) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        // Get or create reading history - JANGAN RESET total_time_spent!
        $history = History::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $id,
            ],
            [
                'last_page' => 1,
                'total_time_spent' => 0,
                'last_read_at' => now(),
                'last_ping_at' => now(),
            ]
        );

        // JANGAN update last_ping_at di sini! Biarkan tetap old value
        // Sehingga delta dihitung dari waktu terakhir baca, bukan dari page load

        return view('frontend.reader', compact('book', 'history'));
    }

    public function startReading($id)
    {
        $book = Book::findOrFail($id);

        // HAPUS updateOrCreate! Gunakan firstOrCreate saja
        // Jangan overwrite total_time_spent, last_page, dll!
        History::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $id,
            ],
            [
                'last_page' => 1,
                'total_time_spent' => 0,
                'last_read_at' => now(),
                'last_ping_at' => now(),
            ]
        );

        // Redirect to reader page
        return redirect()->route('book.read', $id);
    }

    public function updateReadingProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'last_page' => 'required|integer|min:0',
        ]);

        $history = History::where('user_id', Auth::id())
            ->where('book_id', $id)
            ->firstOrFail();

        $history->last_page = (int) $validated['last_page'];
        $history->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function ping(Request $request, $id)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $history = History::where('user_id', Auth::id())
            ->where('book_id', $id)
            ->first();

        // If no history, create fresh one
        if (!$history) {
            $history = History::create([
                'user_id' => Auth::id(),
                'book_id' => $id,
                'last_page' => 1,
                'total_time_spent' => 0,
                'last_read_at' => now(),
                'last_ping_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'total_time_spent' => 0,
                'delta' => 0,
                'is_new' => true,
            ]);
        }

        $now = now();
        $delta = 0;

        if ($history->last_ping_at) {
            $delta = $now->diffInSeconds($history->last_ping_at);

            // HITUNG SEMUA DELTA - JANGAN DISCARD!
            // Max 600 detik (10 menit) per ping untuk anti-cheating
            if ($delta > 0) {
                $timeToAdd = min($delta, 600);
                $history->total_time_spent += $timeToAdd;
            }
        }

        $history->last_ping_at = $now;
        $history->last_read_at = $now;
        $history->save();

        return response()->json([
            'success' => true,
            'total_time_spent' => $history->total_time_spent,
            'delta' => $delta,
            'is_new' => false,
        ]);
    }
}
