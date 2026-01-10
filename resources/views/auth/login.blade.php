@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-bg dark:bg-dark-bg transition-colors duration-200">
    <div class="max-w-md w-full space-y-8 bg-surface dark:bg-dark-surface-secondary p-8 rounded-lg shadow-md border border-border dark:border-dark-border transition-colors duration-200">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-text dark:text-dark-text">Perpustakaan Digital</h1>
            <p class="mt-2 text-text-muted dark:text-dark-text-muted">Silakan login untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded transition-colors duration-200">
                {{ $errors->first() }}
            </div>
            @endif

            <div>
                <label for="email" class="block text-sm font-medium text-text dark:text-dark-text-muted">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-border dark:border-dark-border bg-surface dark:bg-dark-surface text-text dark:text-dark-text rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors duration-200"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text dark:text-dark-text-muted">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-border dark:border-dark-border bg-surface dark:bg-dark-surface text-text dark:text-dark-text rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors duration-200"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-accent hover:bg-accent-hover text-white py-3 px-4 rounded-md font-medium transition-colors duration-200"
            >
                Login
            </button>
        </form>
    </div>
</div>
@endsection
