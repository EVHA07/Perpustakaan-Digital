@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Banner Promo -->
    <div class="glass-glow rounded-lg p-8 mb-8 bg-gradient-to-r from-blue-600/30 to-purple-600/30">
        <h1 class="text-3xl font-bold mb-2 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Selamat Datang di Perpustakaan Digital</h1>
        <p class="text-lg text-text-muted">Temukan koleksi buku digital terbaik dan baca kapan saja</p>
    </div>

    <!-- Reading Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="glass rounded-lg p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 glass-input rounded-full">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12,6 12,12 16,14"></polyline>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted">Total Waktu Membaca</p>
                    <p class="text-2xl font-bold">{{ $timeFormatted ?? '0 menit' }}</p>
                </div>
            </div>
        </div>

        <div class="glass rounded-lg p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 glass-input rounded-full">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted">Buku Dibaca</p>
                    <p class="text-2xl font-bold">{{ $totalBooksRead ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lanjutkan Membaca -->
    @if($continueReading->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Lanjutkan Membaca</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($continueReading as $history)
            <a href="{{ route('book.show', $history->book_id) }}" class="block">
                <div class="glass rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <img src="{{ asset('storage/' . $history->book->cover_image) }}" alt="{{ $history->book->judul }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold truncate">{{ $history->book->judul }}</h3>
                        <p class="text-sm text-text-muted mt-1">Halaman {{ $history->last_page }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Koleksi Terbaru -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Koleksi Terbaru</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($latestBooks as $book)
            <a href="{{ route('book.show', $book->id) }}" class="block">
                <div class="glass rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->judul }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold truncate">{{ $book->judul }}</h3>
                        <p class="text-sm text-text-muted mt-1">{{ $book->kategori }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
