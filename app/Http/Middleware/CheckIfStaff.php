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
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
         * Cette fonction doit être mis à jour pour voir comment bien faire le check du staff
         */
        if (!$request->session()->has('token')) {
            return response()->view('error', [
                'message' => 'You must login first!',
                'code' => 401
            ], 401);
        }
        try{
            $requestGetAccounts =$this->client->get($_ENV['API_URL'] . 'account?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetAccounts->getBody()->getContents());
            $accounts = $data->data;
            $getAccountType = [];

            $requestGetNameTypeAccount = $this->client->get($_ENV['API_URL'] . 'account_type?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetNameTypeAccount->getBody()->getContents());
            $data = $data->data;

            foreach ($accounts as $key => $value){
                foreach ($data as $account_type){
                    if ($key == $account_type->uuid){
                        $accounts[$account_type->type] = $value;
                        unset($accounts[$key]);
                    }
                }
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
