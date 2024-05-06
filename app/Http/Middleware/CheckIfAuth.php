<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class CheckIfAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('token') && !$request->session()->has('auth')) {
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }
        try{
            $client = new Client();
            $response = $client->get(env("API_URL") . 'login', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() === 200) {
                $request->session()->put('auth', true);
            }else{
                $request->session()->flush();

                return response()->view('error', [
                    'message' => 'You must login first!',
                    'code' => 401
                ], 401);
            }

        }catch (\Exception $e){
            $request->session()->flush();
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }

        return $next($request);
    }
}
