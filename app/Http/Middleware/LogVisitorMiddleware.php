<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat request GET pada halaman utama (abaikan file statis, polling state, atau ajax asset)
        if ($request->isMethod('GET') && ! $request->ajax() && ! $request->is('_boost*') && ! $request->is('storage*') && ! $request->is('api*')) {
            try {
                $user = $request->user();
                VisitorLog::create([
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'url' => substr($request->fullUrl(), 0, 255),
                    'user_id' => $user?->id,
                    'role' => $user?->role ?? 'guest',
                ]);
            } catch (\Throwable $e) {
                // Jangan gagalkan request jika logging error
            }
        }

        return $response;
    }
}
