<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Requests\PostLocationRequest;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PhpParser\Node\Expr\Array_;

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
        $client = new Client();
        try{
            $response = $client->getAsync(env('API_URL') . 'account?token='. $request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $account = json_decode($response->getBody()->getContents());
            $accountUuid = $account->data[0]->uuid;
        }catch (\Exception $e){
            error_log($e->getMessage());
            $accountUuid = "";
            return redirect('/', 302, [], false);
        }

        try{
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
        try{
            $response = $client->getAsync(env('API_URL') . 'account_subscription?account=' . $accountUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();

            $subscriptions = json_decode($response->getBody()->getContents());
            if($subscriptions->total === 0){
                $subscriptions = 1;
            }else{
                $subscriptions = $subscriptions->data[0]->subscription;
            }

            //dd($subscriptions->data[0]);
            $response = $client->getAsync(env('API_URL') . 'subscription?uuid=' . $subscriptions, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();

            $subscription = json_decode($response->getBody()->getContents());
            $subscription = $subscription->data[0]->ads;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $subscription = 0;
        }

        $publicPath = public_path('assets/publicity');
        if($subscription == 1) {
            $allPublicities = [];
        }else{
            $allPublicities = File::allFiles($publicPath);
        }
        //dd(File::allFiles($publicPath));


        return view("default",[
            'file_path' => $this->view_path . "main_travel_page",
            'stack_css' => 'main_travel',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'locations' => $locations,
            'admin' => $this->isAdmin($request->session()->get('token')),
            'publicities' => $allPublicities
        ]);
    }

    public function showLocation(Request $request, $id)
    {
        /**
         * Récupère les informations de la location et les avis des utilisateurs
         * et les équipements disponibles pour cette location
         */

        //try {
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
                echo "</br>";
                $data = json_decode($response->getBody()->getContents());

                $nameUsers[$i] = $reviews[$i];
                $user[$i] = $data->data[0];
            }

            $equipments = [];
            $response = $client->getAsync(env('API_URL') . 'equipment?housing=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $equipments = json_decode($response->getBody()->getContents());
            $equipments = $equipments->data;


        /*} catch (\Exception $e) {
            error_log($e->getMessage());
            $locations = empty($locations) ? [] : $locations;
            $nameUsers = empty($nameUsers) ? [] : $nameUsers;
            $equipments = empty($equipments) ? [] : $equipments;

            return redirect('/travel', 302, [], false);
        }*/
        $images = [];

        $path = 'public/locations/' . $id;

        if (Storage::exists($path)) {
            $images = Storage::files('public/locations/' . $id);
        }

        foreach ($images as $key => $image) {
            $images[$key] = str_replace('public/', '', $image);
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
            'equipments' => $equipments,
            'images' => $images,
            'admin' => $this->isAdmin($request->session()->get('token'))
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
            'housing' => $id,
            'admin' => $this->isAdmin($request->session()->get('token'))
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
                $basketUuid = $basket->baskets;
                $find = false;
                foreach ($basketUuid as $item) {
                    if ($item->paid == 0){
                        $basketUuid = $item->uuid;
                        $find = true;
                    }
                }
                if (!$find){
                   return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'Your basket is empty']);
                }
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
            $basket = $basket->baskets;

            $find = false;
            foreach ($basket as $item) {
                if ($item->paid == 0){
                    $basket = $item;
                    $find = true;
                }
            }
            if (!$find){
                return redirect('/travel/reservation/'. $id, 302, [], false)->withErrors(['error' => 'Your basket is empty']);
            }

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

            $last = count($basket->HOUSING) - 1;
            $totalPrice += $basket->HOUSING[$last]->price * ((int)$basket->HOUSING[$last]->endTime - (int)$basket->HOUSING[$last]->startTime);

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

    public function showCreateLocation(Request $request)
    {
        try{
            $client = new Client();
            $response = $client->get(env('API_URL') . 'house_type?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $typehouse = json_decode($response->getBody()->getContents());
            $typehouse = $typehouse->data;

            //equipment type
            $response = $client->get(env('API_URL') . 'equipment_type?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $typeequipment = json_decode($response->getBody()->getContents());
            $typeequipment = $typeequipment->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $typehouse = [];
            $typeequipment = [];
        }


        return view("default", [
            'file_path' => $this->view_path . "create_location",
            'stack_css' => 'create_location',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'house_types' => $typehouse,
            'equipment_types' => $typeequipment
        ]);
    }

    public function doCreateLocation(PostLocationRequest $request)
    {
        $data = $request->validated();
        $client = new Client();
        //dd($data);


        try {
            //la création du housing
            $response = $client->get(env('API_URL') . 'account?token=' . $request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            $accountUuid = $account->data[0]->uuid;

            $body = [
                'surface' => $data['surface'],
                'price' => $data['price_housing'],
                'street_nb' => $data['street_nb'],
                'city' => $data['city'],
                'zip_code' => $data['zip_code'],
                'street' => $data['street'],
                'description' => $data['description_housing'],
                'house_type' => $data['house_type'],
                'title' => $data['title'],
                'imgPath' => "null",
                'account' => $accountUuid
            ];
            $response = $client->post(env('API_URL') . 'housing', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => $body
            ]);
            $housing = json_decode($response->getBody()->getContents());
            $housingUuid = $housing->uuid;

            File::ensureDirectoryExists('public/locations/' . $housingUuid);
            //Storage::makeDirectory('public/locations/' . $housingUuid);
            foreach ($data['imgPathHousing'] as $img){
                $img->storeAs($housingUuid, $img->getClientOriginalName(), 'locations');
            }

            //dd($data['imgPathHousing'][0]->getClientOriginalName());
            $response = $client->put(env('API_URL') . 'housing?uuid=' . $housingUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    'imgPath' => $data['imgPathHousing'][0]->getClientOriginalName()
                ]
            ]);

            //la création des chambres

            $priceBedRoom = $data['price'];
            $nbPlaces = $data['nbPlaces'];
            $description = $data['description'];
            $imgPath = $data['imgPath'];

            if (count($priceBedRoom) !== count($nbPlaces) || count($priceBedRoom) !== count($description) || count($priceBedRoom) !== count($imgPath)){
                return redirect('/travel/creationLocation', 302, [], false)->withErrors(['error' => 'An error occurred when create housing']);
            }

            for($i = 0; $i < count($priceBedRoom); $i++) {
                $body = [
                    'price' => $priceBedRoom[$i+1],
                    'nbPlaces' => $nbPlaces[$i+1],
                    'description' => $description[$i+1],
                    'imgPath' => "null",
                    'housing' => $housingUuid,
                    "title" => "Chambre " . $i + 1,
                ];
                $response = $client->post(env('API_URL') . 'bed_room', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => $body
                ]);
                $bedRoom = json_decode($response->getBody()->getContents());
                $bedRoomUuid = $bedRoom->uuid;

                File::ensureDirectoryExists('public/bedrooms/' . $bedRoomUuid);
                $imgPath[$i+1]->storeAs('public/bedrooms/' . $bedRoomUuid, $imgPath[$i+1]->getClientOriginalName(), 'bedrooms');

                $response = $client->put(env('API_URL') . 'bed_room?uuid=' . $bedRoomUuid, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => [
                        'imgPath' => $imgPath[$i+1]->getClientOriginalName()
                    ]
                ]);

            }

            $equipmentType = $data['equipment_type'];
            $nameEquipment = $data['nameEquipment'];
            $descriptionEquipment = $data['descriptionEquipment'];
            $imgPathEquipment = $data['imgPathEquipment'];
            if (count($equipmentType) !== count($nameEquipment) || count($equipmentType) !== count($descriptionEquipment) || count($equipmentType) !== count($imgPathEquipment)){
                return redirect('/travel/creationLocation', 302, [], false)->withErrors(['error' => 'An error occurred when create housing'])->withInput(
                    $request->all()
                );
            }

            for($j = 0; $j < count($equipmentType); $j++){
                $body = [
                    'name' => $nameEquipment[$j+1],
                    'description' => $descriptionEquipment[$j+1],
                    'price' => $data['priceEquipement'][$j+1],
                    'imgPath' => "null",
                    'equipment_type' => $equipmentType[$j+1],
                    'housing' => $housingUuid,
                    'imgPath' => $imgPathEquipment[$j+1]->getClientOriginalName(),
                ];
                $response = $client->post(env('API_URL') . 'equipment', [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ],
                    'json' => $body
                ]);
                $equipment = json_decode($response->getBody()->getContents());
                $equipmentUuid = $equipment->data->uuid;

                File::ensureDirectoryExists('public/equipments/' . $equipmentUuid);
                $imgPathEquipment[$j+1]->storeAs('public/equipments/' . $equipmentUuid, $imgPathEquipment[$j+1]->getClientOriginalName(), 'equipments');

            }

            return redirect('/travel/creationLocation', 302, [], false)->with('success', 'Housing created');


        }catch (\Exception $e){
            $this->undoLocation($request, $accountUuid);
            error_log($e->getMessage());
            return redirect('/travel/creationLocation', 302, [], false)->withErrors(['error' => 'An error occurred when create housing'])->WithInput(
                $request->all()
            );
        }
    }
    private function undoLocation(Request $request, $accountUUid , $deleteBug = true)
    {
        $admin = $this->isAdmin($request->session()->get('token'));
        if (!$admin){
            return;
        }

        try{
            $client = new Client();
            $response = $client->get(env('API_URL') . 'housing?account=' . $accountUUid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $housing = json_decode($response->getBody()->getContents());
            $housing = $housing->data[0];

        }catch (\Exception $e){
            error_log($e->getMessage());
        }

        try{
            $response = $client->delete(env('API_URL') . 'bed_room?uuid=' . $housing->uuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
        }

        try {
            $response = $client->get(env('API_URL') . 'equipment?housing=' . $housing->uuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $response = json_decode($response->getBody()->getContents());
            $equipments = $response->data;
            $equipmentsUUID = [];
            foreach ($equipments as $equipment){
                $equipmentsUUID[] = $equipment->uuid;
            }

            foreach ($equipmentsUUID as $equipmentUUID){
                $response = $client->delete(env('API_URL') . 'equipment?uuid=' . $equipmentUUID, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ]);
            }

            $response = $client->delete(env('API_URL') . 'housing?uuid=' . $housing->uuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);

            if ($deleteBug){
                return redirect('/travel/creationLocation', 302, [], false)->withErrors(['error' => 'An error occurred when create housing'])->WithInput(
                    $request->all()
                );
            }
            else{
                return;
            }

        }catch (\Exception $e){
            error_log($e->getMessage());
        }

        return;
    }

    public function removeLocation(Request $request, $id)
    {
        if (!$this->isAdmin($request->session()->get('token'))){
            return redirect('/travel/'.$id, 302, [], false);
        }
        $this->undoLocation($request, $id, false);

        return redirect('/travel', 302, [], false);
    }
    private function isAdmin($token)
    {
        try {
            $client = new Client();

            $response = $client->get(env('API_URL') . 'account?token=' . $token, [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            $accountUuid = $account->data[0]->uuid;

            $response = $client->get(env('API_URL') . 'admin?account=' . $accountUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            return (bool)$account->admin;

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return false;
    }

    public function approuveLocation(Request $request, $id)
    {
        if (!$this->isAdmin($request->session()->get('token'))){
            return redirect('/travel/'.$id, 302, [], false);
        }

        try {
            $client = new Client();
            $response = $client->put(env('API_URL') . 'housing?uuid=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    'validated' => 1
                ]
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

        }

        return redirect('/travel/'.$id, 302, [], false);
    }
}
