@extends('layouts.app')

@section('title', 'Search')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Bar -->
    <div class="mb-8">
        <form action="{{ route('search') }}" method="GET" class="relative">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari buku..."
                class="w-full px-6 py-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
            >
            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-blue-500 dark:bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700">
                Search
            </button>
        </form>
    </div>

    <!-- Semua Koleksi -->
    <div>
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Semua Koleksi</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($books as $book)
            <a href="{{ route('book.show', $book->id) }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->judul }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $book->judul }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $book->kategori }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{ $books->links() }}
    </div>
</div>
@endsection
