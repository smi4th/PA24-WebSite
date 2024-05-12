<?php

use App\Http\Middleware\CheckIfAuth;
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
        $middleware->validateCsrfTokens(
            except: [
                'basketPayment/*',
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //dd($exceptions);

        $exceptions->render(function (Throwable $e) {
            //if app is in debug mode,don't show the exception
            if (config('app.debug')) {
                return null;
            }
            return response()->view('error', [
                'message' => 'Error occurred!',
                'code' => $e->getCode()
            ], $e->getCode());
        });
    })->create();

