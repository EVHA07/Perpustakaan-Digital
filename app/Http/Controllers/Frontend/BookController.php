<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\History;
use App\Models\UserBookStats;
use App\Models\ReadingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        if (!Auth::check() || Auth::user()->role !== 'student' || !Auth::user()->is_active) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        if (!$book->file_path) {
            return redirect()->route('home')->with('error', 'Buku tidak memiliki file.');
        }

        $history = History::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $id,
            ],
            [
                'last_page' => 1,
                'total_time_spent' => 0,
                'last_read_at' => now(),
            ]
        );

        return view('frontend.reader', compact('book', 'history'));
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

    public function startReadingSession(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $book = Book::findOrFail($id);

        DB::beginTransaction();
        try {
            $stats = UserBookStats::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'book_id' => $id,
                ],
                [
                    'total_seconds' => 0,
                ]
            );

            ReadingSession::where('user_id', Auth::id())
                ->where('book_id', $id)
                ->where('last_ping_at', '>=', now()->subMinutes(5))
                ->delete();

            $session = ReadingSession::create([
                'user_id' => Auth::id(),
                'book_id' => $id,
                'started_at' => now(),
                'last_ping_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to start reading session', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'book_id' => $id,
            ]);
            return response()->json(['success' => false, 'error' => 'Failed to start session'], 500);
        }
    }

    public function syncReadingTime(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'session_id' => 'required|integer',
            'delta_seconds' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $session = ReadingSession::where('id', $validated['session_id'])
                ->where('user_id', Auth::id())
                ->where('book_id', $id)
                ->first();

            if (!$session) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => 'Session not found'], 404);
            }

            $stats = UserBookStats::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'book_id' => $id,
                ],
                [
                    'total_seconds' => 0,
                ]
            );

            $stats->incrementTotalSeconds($validated['delta_seconds']);

            $session->last_ping_at = now();
            $session->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'total_seconds' => $stats->total_seconds,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync reading time', [
                'error' => $e->getMessage(),
                'session_id' => $validated['session_id'],
                'user_id' => Auth::id(),
                'book_id' => $id,
            ]);
            return response()->json(['success' => false, 'error' => 'Failed to sync'], 500);
        }
    }

    public function endReadingSession(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'session_id' => 'required|integer',
            'delta_seconds' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $session = ReadingSession::where('id', $validated['session_id'])
                ->where('user_id', Auth::id())
                ->where('book_id', $id)
                ->first();

            if (!$session) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => 'Session not found'], 404);
            }

            $stats = UserBookStats::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'book_id' => $id,
                ],
                [
                    'total_seconds' => 0,
                ]
            );

            $stats->incrementTotalSeconds($validated['delta_seconds']);

            $session->last_ping_at = now();
            $session->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'total_seconds' => $stats->total_seconds,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to end reading session', [
                'error' => $e->getMessage(),
                'session_id' => $validated['session_id'],
                'user_id' => Auth::id(),
                'book_id' => $id,
            ]);
            return response()->json(['success' => false, 'error' => 'Failed to end session'], 500);
        }
    }
}
