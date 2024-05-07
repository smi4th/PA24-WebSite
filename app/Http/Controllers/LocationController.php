<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LocationController extends Controller
{
    var string $view_path = "travel_section.";

    private function isAuth()
    {
        if (session()->has('token')) {
            return true;
        }else{
            return false;
        }
    }
    public function index(Request $request)
    {
        try{
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'housing?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ])->wait();
            $locations = json_decode($response->getBody()->getContents());
            $locations = $locations->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $locations = [];
        }

        return view("default",[
            'file_path' => $this->view_path . "main_travel_page",
            'stack_css' => 'main_travel',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'locations' => $locations
        ]);
    }

    public function showLocation(Request $request, $id)
    {
        try {
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'housing?uuid=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ])->wait();
            $locations = json_decode($response->getBody()->getContents());
            $locations = $locations->data[0];

            $response = $client->getAsync(env('API_URL') . 'reservation_housing?housing=' . $id , [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $reviews = json_decode($response->getBody()->getContents());
            $reviews = $reviews->data;

            $user = [];
            $nameUsers = [];
            for($i = 0; $i < count($reviews); $i++){

                $response = $client->getAsync(env('API_URL') . 'account?uuid=' . $reviews[$i]->account, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ])->wait();
                $data = json_decode($response->getBody()->getContents());

                $nameUsers[$i] = $reviews[$i];
                $user[$i] = $data->data[$i];
            }

            $equipments = [];
            $response = $client->getAsync(env('API_URL') . 'equipment?housing=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $equipments = json_decode($response->getBody()->getContents());
            $equipments = $equipments->data;


        } catch (\Exception $e) {
            error_log($e->getMessage());
            $locations = empty($locations) ? [] : $locations;
            $nameUsers = empty($nameUsers) ? [] : $nameUsers;
            $equipments = empty($equipments) ? [] : $equipments;
        }
        return view("default", [
            'file_path' => $this->view_path . "offer",
            'stack_css' => 'offers',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'location' => $locations,
            'reviews' => $nameUsers,
            'users' => $user,
            'equipments' => $equipments
        ]);
    }

    public function showReservation(ReservationRequest $request, $id)
    {
        //il faut montrer le nombre de chambre disponible avec des cases à cocher
        //la même chose pour les équipements payants
        //puis faire le calcul du prix total
        //afficher les disponibilités du logement
        //afficher le nombre de place disponible en fonction des cases cochées et de la date

        $data = $request->validated();
        dd($data);
    }

    public function doReservation(Request $request, $id)
    {
        $data = $request->validated();
        dd($data);
    }
}
