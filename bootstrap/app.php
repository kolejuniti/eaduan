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
        $middleware->alias([
            'user-access' => \App\Http\Middleware\MultiAuthUser::class,
            'auth.student' => \App\Http\Middleware\RedirectIfNotStudent::class,  
            'auth.staff' => \App\Http\Middleware\RedirectIfNotStaff::class,           
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->render(function ($request, $exception) {
        //     if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        //         if ($request->is('student') || $request->is('student/*')) {
        //             return redirect()->route('student.login');
        //         }
        //         if ($request->is('staff') || $request->is('staff/*')) {
        //             return redirect()->route('staff.login');
        //         }
        //     }
        // });
    })->create();
