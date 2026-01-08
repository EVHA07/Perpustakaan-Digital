@extends('layouts.admin')

@section('title', 'Tambah Buku')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Tambah Buku</h1>

    @if(session('error'))
    <div class="glass border-red-500/50 text-red-400 px-4 py-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="glass border-red-500/50 text-red-400 px-4 py-4 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-semibold mb-2">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="glass rounded-lg p-6">
        @csrf

        <div class="mb-4">
            <label for="judul" class="block text-sm font-medium text-text-muted mb-1">Judul</label>
            <input type="text" name="judul" id="judul" required
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('judul') ? 'border-red-500' : '' }}"
                value="{{ old('judul') }}">
            @error('judul')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="kategori" class="block text-sm font-medium text-text-muted mb-1">Kategori</label>
            <input type="text" name="kategori" id="kategori" required
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('kategori') ? 'border-red-500' : '' }}"
                value="{{ old('kategori') }}">
            @error('kategori')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="sinopsis" class="block text-sm font-medium text-text-muted mb-1">Sinopsis</label>
            <textarea name="sinopsis" id="sinopsis" rows="4" required
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('sinopsis') ? 'border-red-500' : '' }}">{{ old('sinopsis') }}</textarea>
            @error('sinopsis')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="cover_image" class="block text-sm font-medium text-text-muted mb-1">Cover Image</label>
            <input type="file" name="cover_image" id="cover_image" accept="image/jpeg,image/png,image/jpg" required
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('cover_image') ? 'border-red-500' : '' }}">
            <p class="mt-1 text-xs text-text-light">Format: JPEG, PNG, JPG | Maks: 2MB</p>
            @error('cover_image')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="book_file" class="block text-sm font-medium text-text-muted mb-1">File Buku (PDF/EPUB)</label>
            <input type="file" name="book_file" id="book_file" accept=".pdf,.epub" required
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('book_file') ? 'border-red-500' : '' }}">
            <p class="mt-1 text-xs text-text-light">Format: PDF, EPUB | Maks: 50MB</p>
            @error('book_file')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="total_pages" class="block text-sm font-medium text-text-muted mb-1">Total Halaman</label>
            <input type="number" name="total_pages" id="total_pages" min="0"
                class="w-full px-3 py-2 glass-input rounded-lg focus:outline-none focus:ring-2 focus:ring-accent {{ $errors->has('total_pages') ? 'border-red-500' : '' }}"
                value="{{ old('total_pages') }}">
            <p class="mt-1 text-xs text-text-light">Akan otomatis terisi dari file PDF yang diupload</p>
            @error('total_pages')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" checked
                    class="w-4 h-4 text-accent bg-transparent border-border rounded focus:ring-accent">
                <span class="ml-2 text-sm text-text-muted">Aktif</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('admin.books.index') }}" class="flex-1 glass-input text-text px-4 py-2 rounded-lg hover:bg-surface-hover transition text-center">
                Batal
            </a>
            <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg transition-colors">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
