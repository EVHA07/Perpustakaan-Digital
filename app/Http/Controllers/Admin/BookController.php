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
            // Method 1: Use pdftotext if available
            $whichPdftotext = shell_exec('which pdftotext 2>/dev/null');
            if (!empty(trim($whichPdftotext)) || file_exists('/mingw64/bin/pdftotext')) {
                $output = shell_exec('pdftotext -layout "' . escapeshellarg($filePath) . '" - 2>/dev/null');
                if ($output !== null && $output !== false) {
                    // Count form feeds (page breaks)
                    $pageCount = substr_count($output, "\f");
                    if ($pageCount > 0) {
                        return $pageCount;
                    }
                    // If no form feeds, try counting by line estimate
                    $lines = substr_count($output, "\n");
                    $estimatedPages = ceil($lines / 50);
                    if ($estimatedPages > 0) {
                        return $estimatedPages;
                    }
                }
            }

            // Method 2: Count using grep for page numbers
            $output = shell_exec('pdftotext "' . escapeshellarg($filePath) . '" - 2>/dev/null | wc -l');
            if ($output) {
                $lines = intval(trim($output));
                // Estimate pages based on lines (roughly 45-55 lines per page)
                $estimatedPages = ceil($lines / 50);
                if ($estimatedPages > 0) {
                    return $estimatedPages;
                }
            }

            // Method 3: Use file size heuristic (fallback)
            $size = filesize($filePath);
            if ($size > 0) {
                // Rough estimate: 50KB per page on average
                $estimatedPages = ceil($size / 50000);
                return max(1, min($estimatedPages, 1000)); // Cap at 1000 pages
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('PDF page count failed', [
                'error' => $e->getMessage(),
                'file' => $filePath,
            ]);
            return 0;
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
                'is_active' => 'nullable|accepted',
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
                'is_active.accepted' => 'Status tidak valid',
            ]);

            Log::info('Book upload - Validation passed');

            $coverFile = $request->file('cover_image');
            $bookFile = $request->file('book_file');

            Log::info('Book upload - File info', [
                'cover_original_name' => $coverFile ? $coverFile->getClientOriginalName() : 'null',
                'cover_size' => $coverFile ? $coverFile->getSize() : 0,
                'book_original_name' => $bookFile ? $bookFile->getClientOriginalName() : 'null',
                'book_size' => $bookFile ? $bookFile->getSize() : 0,
                'storage_root' => storage_path('app/public'),
                'storage_exists' => is_dir(storage_path('app/public')),
                'storage_writable' => is_writable(storage_path('app/public')),
            ]);

            $coverPath = $coverFile->store('books/covers', 'public');

            Log::info('Book upload - Cover uploaded', [
                'cover_path' => $coverPath,
                'cover_full_path' => storage_path('app/public/' . $coverPath),
                'cover_exists' => file_exists(storage_path('app/public/' . $coverPath)),
            ]);

            $tempPath = $bookFile->store('books/temp', 'public');
            $tempFilePath = storage_path('app/public/' . $tempPath);

            // Auto-calculate total pages from PDF
            $totalPages = $this->getPdfPageCount($tempFilePath);

            Log::info('Book upload - PDF page count', [
                'temp_path' => $tempPath,
                'temp_file_exists' => file_exists($tempFilePath),
                'total_pages' => $totalPages,
            ]);

            $filePath = $bookFile->store('books/files', 'public');

            // Delete temp file
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }

            Log::info('Book upload - Book file uploaded', [
                'file_path' => $filePath,
                'file_full_path' => storage_path('app/public/' . $filePath),
                'file_exists' => file_exists(storage_path('app/public/' . $filePath)),
            ]);

            Book::create([
                'judul' => $validated['judul'],
                'kategori' => $validated['kategori'],
                'sinopsis' => $validated['sinopsis'],
                'cover_image' => $coverPath,
                'file_path' => $filePath,
                'total_pages' => $totalPages > 0 ? $totalPages : ($validated['total_pages'] ?? 0),
                'is_active' => isset($validated['is_active']),
            ]);

            Log::info('Book upload - Success');

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Book upload - Validation failed', [
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
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage());
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
                'is_active' => 'nullable|accepted',
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
                'is_active.accepted' => 'Status tidak valid',
            ]);

            $book->judul = $validated['judul'];
            $book->kategori = $validated['kategori'];
            $book->sinopsis = $validated['sinopsis'];
            $book->total_pages = $validated['total_pages'] ?? $book->total_pages;
            $book->is_active = isset($validated['is_active']);

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
