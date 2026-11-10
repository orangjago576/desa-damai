<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->query('debug') === '1') {
                return response(
                    "ERROR CLASS: " . get_class($e) . "\n" .
                    "MESSAGE: " . $e->getMessage() . "\n" .
                    "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n\n" .
                    "TRACE:\n" . $e->getTraceAsString(),
                    500,
                    ['Content-Type' => 'text/plain']
                );
            }
        });
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL') !== false) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
