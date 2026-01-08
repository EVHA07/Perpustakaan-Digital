<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($students as $student) {
            $totalTime = History::where('user_id', $student->id)->sum('total_time_spent');
            $totalBooks = History::where('user_id', $student->id)->distinct('book_id')->count('book_id');

            $days = floor($totalTime / 86400);
            $hours = floor(($totalTime % 86400) / 3600);
            $minutes = floor(($totalTime % 3600) / 60);
            $seconds = $totalTime % 60;

            if ($days > 0) {
                $student->reading_time_formatted = "{$days}h {$hours}j";
            } elseif ($hours > 0) {
                $student->reading_time_formatted = "{$hours}j {$minutes}m";
            } elseif ($minutes > 0) {
                $student->reading_time_formatted = "{$minutes}m {$seconds}s";
            } else {
                $student->reading_time_formatted = "{$seconds}s";
            }

            $student->total_books_read = $totalBooks;
        }

        return view('admin.users.index', compact('students'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            Log::info('User create - Start', [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'has_password' => $request->has('password'),
                'password_confirmation' => $request->has('password_confirmation'),
                'is_active' => $request->has('is_active'),
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'is_active' => 'nullable|accepted',
            ], [
                'name.required' => 'Nama harus diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 6 karakter',
                'password.confirmed' => 'Password konfirmasi tidak cocok',
                'is_active.accepted' => 'Status tidak valid',
            ]);

            Log::info('User create - Validation passed');

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => 'student',
                'is_active' => isset($validated['is_active']),
            ]);

            Log::info('User create - Success');

            return redirect()->route('admin.users.index')
                ->with('success', 'Siswa berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('User create - Validation failed', [
                'errors' => $e->errors(),
            ]);
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal. Silakan periksa input Anda.');
        } catch (\Exception $e) {
            Log::error('User create - Failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mengedit admin');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mengedit admin');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = isset($validated['is_active']);

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus admin');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Siswa berhasil dihapus');
    }
}
