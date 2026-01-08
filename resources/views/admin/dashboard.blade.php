@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-text-muted mb-1">Total Siswa</h3>
            <p class="text-3xl font-bold">{{ $totalStudents }}</p>
        </div>
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-text-muted mb-1">Total Buku</h3>
            <p class="text-3xl font-bold">{{ $totalBooks }}</p>
        </div>
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-text-muted mb-1">Total Riwayat</h3>
            <p class="text-3xl font-bold">{{ $totalHistories }}</p>
        </div>
        <div class="glass rounded-lg p-6">
            <h3 class="text-sm text-text-muted mb-1">Total Waktu Membaca</h3>
            <p class="text-3xl font-bold">{{ $totalReadingTimeFormatted ?? '0s' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Students -->
        <div class="glass rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Siswa Terbaru</h2>
                <a href="{{ route('admin.users.index') }}" class="text-accent hover:underline">Lihat Semua</a>
            </div>
            @if($recentStudents->count() > 0)
            <div class="space-y-3">
                @foreach($recentStudents as $student)
                <div class="flex items-center justify-between p-3 glass-input rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">{{ $student->name }}</p>
                        <p class="text-sm text-text-muted">{{ $student->email }}</p>
                        <p class="text-xs text-blue-400 mt-1">{{ $student->reading_time_formatted ?? '0s' }} - {{ $student->total_books_read ?? 0 }} buku</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $student->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-text-muted">Belum ada siswa</p>
            @endif
        </div>

        <!-- Recent Books -->
        <div class="glass rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Buku Terbaru</h2>
                <a href="{{ route('admin.books.index') }}" class="text-accent hover:underline">Lihat Semua</a>
            </div>
            @if($recentBooks->count() > 0)
            <div class="space-y-3">
                @foreach($recentBooks as $book)
                <div class="flex items-center space-x-3 p-3 glass-input rounded-lg">
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->judul }}" class="w-12 h-16 object-cover rounded">
                    <div class="flex-1">
                        <p class="font-medium truncate">{{ $book->judul }}</p>
                        <p class="text-sm text-text-muted">{{ $book->kategori }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $book->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $book->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-text-muted">Belum ada buku</p>
            @endif
        </div>
    </div>
</div>
@endsection
