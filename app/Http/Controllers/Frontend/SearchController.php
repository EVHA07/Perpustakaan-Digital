<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::where('is_active', true);

        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'like', "%{$searchTerm}%")
                    ->orWhere('kategori', 'like', "%{$searchTerm}%")
                    ->orWhere('sinopsis', 'like', "%{$searchTerm}%");
            });
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('frontend.search', compact('books'));
    }
}
