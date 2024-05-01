<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
         * Cette fonction doit être mis à jour pour voir comment bien faire le check du staff
         */
        if (!$request->session()->has('auth') && !$request->session()->has('staff')) {
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }
        return $next($request);
    }
}
