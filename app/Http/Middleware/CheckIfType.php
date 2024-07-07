<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role ): Response
    {

        try {

            $client = new Client();
            $token = session('token');
            $response = $client->get(env("API_URL") . 'account?token=' . $token, [
                'headers' => [
                    'authorization' => 'Bearer ' . $token,
                ]
            ]);
            $data = json_decode($response->getBody()->getContents());
            $dataUser = $data;

        }catch (\Exception $e){
            $data = [];
            $accountType = "";
            return redirect('/', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des informations de l'utilisateur "
            ]);
        }

        try{
            $client = new Client();
            $accountType = $data->data[0]->account_type;

            $response = $client->get(env("API_URL") . 'account_type?uuid='.$accountType, [
                'headers' => [
                    "Authorization" => "Bearer " . $token,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            $accountType = $data->data[0]->type;
        }catch (\Exception $e){
            $accountType = "";
        }

        if ($accountType != $role){
            return redirect('/error', 302, [], false)->withErrors([
                "error" => "Vous n'avez pas les droits pour accéder à cette page"
            ]);
        }

        return $next($request);
    }
}
