<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'student' || !Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun tidak memiliki akses.');
        }

        return $next($request);
    }
}
