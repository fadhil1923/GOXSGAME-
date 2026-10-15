<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\LogVisitorMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            LogVisitorMiddleware::class,
        ]);
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'teacher/rooms/*/next',
            'teacher/rooms/*/play',
            'teacher/rooms/*/end',
            'student/room/*/answer',
            'student/room/*/powerup',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('teacher/rooms/*') || $request->is('student/*')) {
                return back()->with('info', 'Sesi telah diperbarui otomatis. Silakan lanjutkan.');
            }
        });
    })->create();
