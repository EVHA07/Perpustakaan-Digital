<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    private function getPdfPageCount($filePath)
    {
        try {
            if (!file_exists($filePath)) {
                return 0;
            }

            $size = filesize($filePath);
            if ($size > 0) {
                // Heuristic: ~50KB per page on average for text-based PDFs
                // Cap at reasonable limits
                $estimatedPages = ceil($size / 50000);
                return max(1, min($estimatedPages, 1000));
            }

            return 1;
        } catch (\Exception $e) {
            Log::error('PDF page count failed', [
                'error' => $e->getMessage(),
                'file' => $filePath,
            ]);
            return 1;
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Book upload - Start', [
                'all_files' => array_keys($request->allFiles()),
                'has_cover' => $request->hasFile('cover_image'),
                'has_file' => $request->hasFile('book_file'),
                'is_active' => $request->input('is_active'),
                'is_active_checked' => $request->has('is_active'),
                'content_type' => $request->header('Content-Type'),
            ]);

            // Check if file was actually uploaded
            if (!$request->hasFile('book_file')) {
                Log::error('Book upload - No file uploaded', [
                    'files' => $request->allFiles(),
                    'all_inputs' => $request->all(),
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'File buku tidak terupload. Kemungkinan ukuran file terlalu besar atau koneksi terputus. Pastikan ukuran file kurang dari 50MB dan koneksi internet stabil.');
            }

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'kategori' => 'required|string|max:100',
                'sinopsis' => 'required|string',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                'book_file' => 'required|file|max:51200',
                'total_pages' => 'nullable|integer|min:0',
                'is_active' => 'nullable',
            ], [
                'judul.required' => 'Judul harus diisi',
                'kategori.required' => 'Kategori harus diisi',
                'sinopsis.required' => 'Sinopsis harus diisi',
                'cover_image.required' => 'Cover image harus diisi',
                'cover_image.image' => 'Cover harus berupa file gambar',
                'cover_image.mimes' => 'Cover harus berformat JPEG, PNG, atau JPG',
                'cover_image.max' => 'Ukuran cover maksimal 10MB',
                'book_file.required' => 'File buku harus diisi',
                'book_file.file' => 'File buku harus berupa file',
                'book_file.max' => 'Ukuran file buku maksimal 50MB',
                'total_pages.integer' => 'Total halaman harus berupa angka',
                'total_pages.min' => 'Total halaman tidak boleh negatif',
            ]);

            $coverFile = $request->file('cover_image');
            $bookFile = $request->file('book_file');

            $coverPath = $coverFile->store('books/covers', 'public');
            $filePath = $bookFile->store('books/files', 'public');

            // Verify files were saved
            if (!Storage::disk('public')->exists($coverPath)) {
                throw new \Exception('Gagal menyimpan cover image');
            }
            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('Gagal menyimpan file buku');
            }

            $totalPages = $this->getPdfPageCount(storage_path('app/public/' . $filePath));

            Book::create([
                'judul' => $validated['judul'],
                'kategori' => $validated['kategori'],
                'sinopsis' => $validated['sinopsis'],
                'cover_image' => $coverPath,
                'file_path' => $filePath,
                'total_pages' => $totalPages > 0 ? $totalPages : ($validated['total_pages'] ?? 0),
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku "' . $validated['judul'] . '" berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Book upload - Validation failed', [
                'errors' => $e->errors(),
            ]);
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal. Silakan periksa input Anda.');
        } catch (\Exception $e) {
            Log::error('Book upload - Failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan buku. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);

        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'kategori' => 'required|string|max:100',
                'sinopsis' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'book_file' => 'nullable|file|mimes:pdf,epub|max:10240',
                'total_pages' => 'nullable|integer|min:0',
                'is_active' => 'nullable',
            ], [
                'judul.required' => 'Judul harus diisi',
                'kategori.required' => 'Kategori harus diisi',
                'sinopsis.required' => 'Sinopsis harus diisi',
                'cover_image.image' => 'Cover harus berupa file gambar',
                'cover_image.mimes' => 'Cover harus berformat JPEG, PNG, atau JPG',
                'cover_image.max' => 'Ukuran cover maksimal 2MB',
                'book_file.file' => 'File buku harus berupa file',
                'book_file.mimes' => 'File buku harus berformat PDF atau EPUB',
                'book_file.max' => 'Ukuran file buku maksimal 10MB',
                'total_pages.integer' => 'Total halaman harus berupa angka',
                'total_pages.min' => 'Total halaman tidak boleh negatif',
            ]);

            $book->judul = $validated['judul'];
            $book->kategori = $validated['kategori'];
            $book->sinopsis = $validated['sinopsis'];
            $book->total_pages = $validated['total_pages'] ?? $book->total_pages;
            $book->is_active = $request->has('is_active');

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $book->cover_image = $request->file('cover_image')->store('books/covers', 'public');
            }

            if ($request->hasFile('book_file')) {
                if ($book->file_path) {
                    Storage::disk('public')->delete($book->file_path);
                }
                $book->file_path = $request->file('book_file')->store('books/files', 'public');
            }

            $book->save();

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Book update - Failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate buku: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus');
    }
}
