@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center
    bg-gradient-to-br from-blue-50 via-white to-blue-50
    dark:from-slate-950 dark:via-gray-950 dark:to-slate-950
    transition-colors duration-300
    relative">

    <!-- Theme Toggle Button -->
    <button 
        id="theme-toggle" 
        class="absolute top-6 right-6 p-3 rounded-full
            bg-white dark:bg-gray-800
            border border-gray-200 dark:border-gray-700
            text-gray-600 dark:text-gray-400
            hover:bg-gray-100 dark:hover:bg-gray-700
            shadow-lg
            transition-all duration-300
            z-10"
        onclick="toggleTheme()"
        title="Toggle Theme">
        <svg id="sun-icon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <svg id="moon-icon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>

    <div class="w-full max-w-md p-8 rounded-2xl
        bg-white dark:bg-gray-900
        border border-gray-200 dark:border-gray-700
        shadow-xl dark:shadow-2xl
        transition-all duration-300">

        <!-- Header with Icon -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                Perpustakaan Digital
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Akses Buku Digital Anda
            </p>
        </div>

        <!-- Error -->
        @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg
            bg-red-50 dark:bg-red-900/30
            border-l-4 border-red-500 dark:border-red-400
            text-red-700 dark:text-red-200 text-sm
            transition-colors duration-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $errors->first() }}
            </div>
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full px-4 py-3 rounded-lg
                        border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800
                        text-gray-900 dark:text-white
                        placeholder-gray-400 dark:placeholder-gray-500
                        focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500
                        transition-all duration-300"
                    placeholder="nama@email.com">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 rounded-lg
                        border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800
                        text-gray-900 dark:text-white
                        placeholder-gray-400 dark:placeholder-gray-500
                        focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500
                        transition-all duration-300"
                    placeholder="••••••••">
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="w-full py-3 rounded-lg
                    bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                    text-white font-semibold
                    shadow-lg hover:shadow-xl
                    transform hover:scale-[1.02] active:scale-[0.98]
                    transition-all duration-300">
                Login
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} Perpustakaan Digital. All rights reserved.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = document.body.dataset.theme;
    const html = document.documentElement;
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');
    
    if (currentTheme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
});

function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    
    fetch('{{ route("theme.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ theme: isDark ? 'dark' : 'light' })
    });
}
</script>
@endsection
