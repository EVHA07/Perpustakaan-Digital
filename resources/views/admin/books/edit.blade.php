@extends('layouts.admin')

@section('title', 'Edit Buku')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Edit Buku</h1>

    <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="glass rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul</label>
            <input type="text" name="judul" id="judul" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                value="{{ old('judul', $book->judul) }}">
        </div>

        <div class="mb-4">
            <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
            <input type="text" name="kategori" id="kategori" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                value="{{ old('kategori', $book->kategori) }}">
        </div>

        <div class="mb-4">
            <label for="sinopsis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sinopsis</label>
            <textarea name="sinopsis" id="sinopsis" rows="4" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('sinopsis', $book->sinopsis) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cover Image (biarkan kosong jika tidak ingin mengubah)</label>
            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        </div>

        <div class="mb-4">
            <label for="book_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File Buku (PDF/EPUB) (biarkan kosong jika tidak ingin mengubah)</label>
            <input type="file" name="book_file" id="book_file" accept=".pdf,.epub"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        </div>

        <div class="mb-4">
            <label for="total_pages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Halaman</label>
            <input type="number" name="total_pages" id="total_pages" min="0"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                value="{{ old('total_pages', $book->total_pages) }}">
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" {{ $book->is_active ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('admin.books.index') }}" class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition text-center">
                Batal
            </a>
            <button type="submit" class="flex-1 bg-blue-500 dark:bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 transition">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
