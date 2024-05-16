<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Monolog\Handler\ErrorLogHandler;
use Illuminate\Support\Facades\Hash;

class PlanningController extends Controller
{
    private function getInfoprofile()
    {
        //renvoie les infos du compte
        $client = new Client();
        $token = session('token');
        return $client->get(env("API_URL") . 'account?token=' . $token, [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
    }
    private function getReservations($uuid){
        $client = new Client();
        $token = session('token');
        return $client->get(env("API_URL") . 'basket?account=' . $uuid, [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
    }
    public function showPlanning(){

        $infoUser = $this->getInfoprofile();
        $infoUser = json_decode($infoUser->getBody()->getContents(), true)['data'][0];
        //dd($infoUser['uuid']);
        $reservations = $this->getReservations($infoUser['uuid']);
        $reservations = json_decode($reservations->getBody()->getContents(), true);

        return view(
            'profile.planning_profile',[
                'reservations' => $reservations
            ]

        
        );
    }


}
