@extends('layouts.app')

@section('title', $book->judul)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-200 px-4 py-4 rounded mb-6 flex items-center animate-fade-in">
        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Cover -->
            <div class="md:w-1/3">
                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover">
            </div>

            <!-- Detail -->
            <div class="md:w-2/3 p-8">
                <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm mb-4">
                    {{ $book->kategori }}
                </span>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $book->judul }}</h1>
                <div class="prose max-w-none mb-6">
                    <p class="text-gray-600 dark:text-gray-300">{{ $book->sinopsis }}</p>
                </div>

                <!-- Tombol -->
                @if($hasHistory)
                    <button onclick="startReading()" class="w-full bg-green-500 dark:bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-600 dark:hover:bg-green-700 font-semibold">
                        Lanjutkan Membaca (Halaman {{ $history->last_page }})
                    </button>
                @else
                    <button onclick="startReading()" class="w-full bg-blue-500 dark:bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 font-semibold">
                        Mulai Membaca
                    </button>
                @endif

                <!-- Hidden form untuk submit -->
                <form id="startReadingForm" method="GET" action="{{ route('book.read', $book->id) }}">
                </form>
            </div>
        </div>
    </div>

    <script>
        function startReading() {
            document.getElementById('startReadingForm').submit();
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</div>
@endsection
