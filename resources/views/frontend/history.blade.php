@extends('layouts.app')

@section('title', 'History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistik -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Lama Membaca</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $timeFormatted }}</p>
        </div>
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Buku Dibaca</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalBooksRead }}</p>
        </div>
    </div>

    <!-- Riwayat -->
    <div>
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Riwayat Membaca</h2>
        @if($histories->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($histories as $history)
            <a href="{{ route('book.show', $history->book_id) }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <img src="{{ asset('storage/' . $history->book->cover_image) }}" alt="{{ $history->book->judul }}" class="w-full h-48 object-cover">
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat membaca</p>
        </div>
        @endif
    </div>
</div>
@endsection
