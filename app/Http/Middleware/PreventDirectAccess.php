<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PreventDirectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPath = $request->path();

        // Routes that require authentication
        $protectedRoutes = [
            'home',
            'search',
            'history',
            'admin',
            'buku',
        ];

        $isProtected = false;
        foreach ($protectedRoutes as $route) {
            if (str_starts_with($currentPath, $route)) {
                $isProtected = true;
                break;
            }
        }

        if ($isProtected && !Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu.');
        }

        // Check referer for sensitive operations
        $sensitiveMethods = ['POST', 'PUT', 'DELETE'];
        $sensitiveRoutes = [
            'admin/users',
            'admin/books',
            'buku/*/start',
            'buku/*/progress',
            'theme/toggle',
        ];

        if (in_array($request->method(), $sensitiveMethods) && Auth::check()) {
            foreach ($sensitiveRoutes as $route) {
                $pattern = str_replace('*', '.*', $route);
                if (preg_match('#^' . $pattern . '$#', $currentPath)) {
                    $referer = $request->headers->get('referer');
                    $host = $request->getSchemeAndHttpHost();

                    if (!$referer || !str_starts_with($referer, $host)) {
                        abort(403, 'Akses ditolak. Request tidak valid.');
                    }
                    break;
                }
            }
        }

        return $next($request);
    }
}
