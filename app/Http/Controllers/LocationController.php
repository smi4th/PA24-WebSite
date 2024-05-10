<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DateTime;

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
        /**
         * Récupère les informations de la location et les avis des utilisateurs
         * et les équipements disponibles pour cette location
         */

        try {
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'housing?uuid=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ])->wait();
            $locations = json_decode($response->getBody()->getContents());
            $locations = $locations->data[0];

            $response = $client->getAsync(env('API_URL') . 'review?housing=' . $id , [
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

            return redirect('/travel', 302, [], false);
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
        /**
         * Récupère les chambres et les équipements disponibles pour une location
         * et les dates de réservation et affiche toutes les informations
         */

        $data = $request->validated();
        $start = $data['date_start'] ?? date('Y-m-d');
        $end = $data['date_end'] ?? date('Y-m-d', strtotime('+1 day'));


        try {
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'bed_room?housing=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $bedRooms = json_decode($response->getBody()->getContents());
            $bedRooms = $bedRooms->data;


            foreach($bedRooms as $bedRoom){
                $response = $client->getAsync(env('API_URL') . 'bed_room/available?start_time=' . $start . '&end_time=' . $end . '&bedroom=' . $bedRoom->uuid, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ])->wait();
                $available = json_decode($response->getBody()->getContents());
                $bedRoom->available = $available;

                $response = $client->getAsync(env('API_URL') . 'bed_room/reservation?bedroom='. $bedRoom->uuid, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ])->wait();
                $reservations = json_decode($response->getBody()->getContents());
                $bedRoom->reservations = $reservations;
            }

            $response = $client->getAsync(env('API_URL') . 'equipment?housing=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $equipments = json_decode($response->getBody()->getContents());
            $equipments = $equipments->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $bedRooms = empty($bedRooms) ? [] : $bedRooms;
            $equipments = empty($equipments) ? [] : $equipments;

            return redirect('/travel/'. $id, 302, [], false);
        }


        return view("default", [
            'file_path' => $this->view_path . "reservation_location",
            'stack_css' => 'reservation_location',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'bedRooms' => $bedRooms,
            'equipments' => $equipments,
            'start' => $start,
            'end' => $end,
            'housing' => $id
        ]);
    }

    public function doReservationLocation(LocationRequest $request, $id){
        /**
         * Créer un panier pour un compte si il n'en a pas déjà un
         * Ajoute les équipements et les chambres au panier et la location en entier
         * si les toutes les chambres sont réservées
         * Puis calcule le coût total du panier pour cette réservation
         */
        $data = $request->validated();

        $dates = $data['dates_form'];
        $equipments = $data['equipment_form'];

        $dates = json_decode($dates);

        $equipments = json_decode($equipments);
        for($i = 0; $i < count($equipments); $i++){
            $equipments[$i]->number = 1;
            $equipments[$i]->id_equipment = str_replace('equipment', '', $equipments[$i]->id_equipment);
        }

        if (empty($dates)) {
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'Veuillez remplir au moins une date']);
        }

        $totalPrice = 0;
        $client = new Client();


        try {
            $response = $client->get(env('API_URL') . 'account?token=' . $request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            $accountUuid = $account->data[0]->uuid;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred']);
        }

        try{

            $response = $client->get(env('API_URL') . 'basket?account=' . $accountUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $basket = json_decode($response->getBody()->getContents());
            $basketUuid;
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred when get basket']);
        }

        try{
            if($basket->count === 0){
                $body = [
                    'account' => $accountUuid
                ];
                $response = $client->post(env('API_URL') . 'basket', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => $body
                ]);
                $basketUuid = json_decode($response->getBody()->getContents())->uuid;

            }
            else{
                $basketUuid = $basket->baskets[0]->uuid;
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred when create basket']);
        }

        try {

            $totalBedRoom = 0;
            $response = $client->get(env('API_URL') . 'bed_room?housing=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $bedRooms = json_decode($response->getBody()->getContents());
            $totalBedRoom = $bedRooms->total;
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred when get bedRooms']);
        }

        try{

            foreach($dates as $bedRoom){
                $response = $client->get(env('API_URL') . 'bed_room/available?start_time=' . $bedRoom->start . '&end_time=' . $bedRoom->end . '&bedroom=' . $bedRoom->id, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ]);
                $available = json_decode($response->getBody()->getContents());
                if($available->available === false){
                    return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'Les chambres ne sont pas toutes disponibles']);
                }
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'The bedrooms are not all available']);
        }
        try {
            foreach ($dates as $bedRoom) {

                $bedRoom->start = str_replace('/', '-', $bedRoom->start);
                $bedRoom->end = str_replace('/', '-', $bedRoom->end);


                $bedRoom->start = date('Y-m-d', strtotime($bedRoom->start));
                $bedRoom->end = date('Y-m-d', strtotime($bedRoom->end));

                $response = $client->post(env('API_URL') . 'basket/bedroom', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => [
                        'basket' => $basketUuid,
                        'bedroom' => $bedRoom->id,
                        'start_time' => $bedRoom->start,
                        'end_time' => $bedRoom->end
                    ]
                ]);


            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred when add bedroom']);
        }
        try {

            for ($i = 0; $i < count($equipments); $i++) {
                for ($j = $i + 1; $j < count($equipments); $j++) {
                    if ($equipments[$i]->id_equipment === $equipments[$j]->id_equipment) {
                        $equipments[$i]->number++;
                        unset($equipments[$j]);
                    }
                }
            }

            foreach ($equipments as $equipment){


                $response = $client->post(env('API_URL') . 'basket/equipment', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => [
                        'basket' => $basketUuid,
                        'equipment' => $equipment->id_equipment,
                        'number' => str($equipment->number)
                    ]
                ]);
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred when add equipment']);
        }

        try {

             if($totalBedRoom === count($dates)){
                $response = $client->post(env('API_URL') . 'basket/housing', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => [
                        'basket' => $basketUuid,
                        'housing' => $id,
                        'start_time' => $dates[0]->start,
                        'end_time' => $dates[count($dates) - 1]->end
                    ]
                ]);
            }
            $totalPrice = $this->getTotalPriceBasketLocation($request,$id,$accountUuid);

            return redirect('/travel/reservation/'. $id, 302, [], false)->with('success', 'Reservation success! price = ' . $totalPrice . '€');

        }catch (\Exception $e){

            error_log($e->getMessage());
            $this->deleteTheCurrentBasket($request,$bedRooms,$id,$equipments,$basketUuid);
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['errors' => 'An error occurred ']);
        }

    }
    private function getTotalPriceBasketLocation($request, $id,$accountUuid)
    {
        /**
         *  Calcule le prix du panier sans les services ni les réductions possible de l'abonnement
         */
        try {

            $client = new Client();
            $response = $client->get(env('API_URL') . 'basket?account=' . $accountUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $basket = json_decode($response->getBody()->getContents());
            $basket = $basket->baskets[0];

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $e = $e->getBody();
            return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'An error occurred :'.$e->message]);
        }

        $totalPrice = 0;

        foreach($basket->EQUIPMENTS as $equipment) {
            $totalPrice += $equipment->price*$equipment->number;
        }

        if(count($basket->HOUSING) !== 0){
            $totalPrice += $basket->HOUSING[0]->price * ((int)$basket->HOUSING[0]->endTime - (int)$basket->HOUSING[0]->startTime);
        }else{
            foreach ($basket->BEDROOMS as $bedroom) {
                $totalPrice += $bedroom->price * ($bedroom->end_time - $bedroom->start_time);
            }
        }

        return $totalPrice;
    }

    private function deleteTheCurrentBasket($request,$bedRooms,$housing,$equipments,$basketUuid){
        /**
         * Supprime les chambres, les équipements et la location du panier mais pas le panier
         * cette fonction est appelée si une erreur survient lors de la réservation et que le panier est à moitié rempli
         */
        $client = new Client();

        try {
            foreach($equipments as $equipment){

                $equipment->id_equipment = str_replace('equipment', '', $equipment->id_equipment);

                $response = $client->delete(env('API_URL') . 'basket/equipment?equipment=' . $equipment->id_equipment . '&basket=' . $basketUuid, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ]);
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/travel/reservation/'. $housing, 302, [], false)->withErrors(['error' => 'An error occurred when delete basket']);
        }
        try{
            foreach($bedRooms as $bedRoom){
                $response = $client->delete(env('API_URL') . 'basket/bedroom?bedroom=' . $bedRoom->id . '&basket=' . $basketUuid, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ]);
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/travel/reservation/'. $housing, 302, [], false)->withErrors(['error' => 'An error occurred when delete basket']);
        }
        try {
            $response = $client->delete(env('API_URL') . 'basket/housing?housing=' . $housing . '&basket=' . $basketUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/travel/reservation/'. $housing, 302, [], false)->withErrors(['error' => 'An error occurred when delete basket']);
        }
        return true;
    }
}
