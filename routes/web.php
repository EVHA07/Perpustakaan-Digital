<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\HistoryController;
use App\Http\Controllers\Frontend\BookController as FrontendBookController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/theme/toggle', [LoginController::class, 'toggleTheme'])->name('theme.toggle');

Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [AdminBookController::class, 'index'])->name('index');
        Route::get('/create', [AdminBookController::class, 'create'])->name('create');
        Route::post('/', [AdminBookController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminBookController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminBookController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminBookController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'is.student'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/buku/{id}', [FrontendBookController::class, 'show'])->name('book.show');
    Route::get('/buku/{id}/read', [FrontendBookController::class, 'read'])->name('book.read');
    Route::post('/buku/{id}/progress', [FrontendBookController::class, 'updateReadingProgress'])->name('book.progress');
    Route::post('/buku/{id}/reading/start', [FrontendBookController::class, 'startReadingSession'])->name('book.reading.start');
    Route::post('/buku/{id}/reading/sync', [FrontendBookController::class, 'syncReadingTime'])->name('book.reading.sync');
    Route::post('/buku/{id}/reading/end', [FrontendBookController::class, 'endReadingSession'])->name('book.reading.end');
});

Route::get('/test-upload', function () {
    return view('test-upload');
})->name('test-upload');

Route::post('/test-upload', function (Illuminate\Http\Request $request) {
    try {
        Log::info('Test upload started', [
            'files' => array_keys($request->allFiles()),
            'has_cover' => $request->hasFile('cover_image'),
            'has_file' => $request->hasFile('book_file'),
        ]);

        if (!$request->hasFile('cover_image')) {
            return back()->with('error', 'No cover image uploaded');
        }

        if (!$request->hasFile('book_file')) {
            return back()->with('error', 'No file uploaded');
        }

        $coverPath = $request->file('cover_image')->store('books/covers', 'public');
        $filePath = $request->file('book_file')->store('books/files', 'public');

        Log::info('Test upload success', [
            'cover' => $coverPath,
            'file' => $filePath,
        ]);

        return redirect()->route('test-upload')
            ->with('success', 'Upload success! Cover: ' . $coverPath . ', File: ' . $filePath);

    } catch (\Exception $e) {
        Log::error('Test upload failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
})->name('test-upload.post');
