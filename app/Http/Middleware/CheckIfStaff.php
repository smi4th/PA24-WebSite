<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
class CheckIfStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     */
    private $client;
    public function handle(Request $request, Closure $next): Response
    {
        error_log("CheckIfStaff Middleware");
        /*
         * Cette fonction doit être mis à jour pour voir comment bien faire le check du staff
         */
        if (!$request->session()->has('token')) {
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }
        $this->client = new Client();
        try{

            $getUuid = $this->client->get(env("API_URL") . 'account?token=' . $request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $id = json_decode($getUuid->getBody()->getContents());
            $id = $id->data[0]->uuid;

            $isAdmin = $this->client->get($_ENV['API_URL'] . 'admin?account='. $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($isAdmin->getBody()->getContents());
            error_log($data->admin);
            if($data->admin == false){
                return response()->view('error', [
                    'message' => 'You must be an admin to access this page!',
                    'code' => 401
                ], 401);
            }

        }catch (\Exception $e){
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }


        return $next($request);
    }
}
