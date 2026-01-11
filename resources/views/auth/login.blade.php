@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center
    bg-gradient-to-br from-blue-50 via-white to-blue-50
    dark:from-slate-950 dark:via-gray-950 dark:to-slate-950
    transition-colors duration-300 relative">


    <!-- CARD -->
    <div class="w-full max-w-md p-8 rounded-2xl
        bg-white dark:bg-gray-900
        border border-gray-200 dark:border-gray-700
        shadow-xl transition">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                Perpustakaan Digital
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Akses Buku Digital Anda
            </p>
        </div>

        @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/30
            border-l-4 border-red-500 text-red-700 dark:text-red-200 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 rounded-lg
                    bg-white dark:bg-gray-800
                    border border-gray-300 dark:border-gray-600
                    focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg
                    bg-white dark:bg-gray-800
                    border border-gray-300 dark:border-gray-600
                    focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit"
                class="w-full py-3 rounded-lg text-white font-semibold
                bg-gradient-to-r from-blue-600 to-indigo-600
                hover:from-blue-700 hover:to-indigo-700 transition">
                Login
            </button>
        </form>

        <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} Perpustakaan Digital
        </p>
    </div>
</div>

<script>
const toggleBtn = document.getElementById('theme-toggle');

toggleBtn.addEventListener('click', () => {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
});
</script>
@endsection
