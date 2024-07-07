<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreatePrestation;
use App\Http\Requests\PutRequestPrestation;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestationController extends Controller
{
    var string $view_path = "prestation_section.";

    private function isAuth()
    {
        if (session()->has('token')) {
            return true;
        }else{
            return false;
        }
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
    public function index(Request $request)
    {
        try{
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'services_types?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ])->wait();
            $servicesTypes = json_decode($response->getBody()->getContents());
            $servicesTypes = $servicesTypes->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $servicesTypes = [];
        }

        return view("default",[
            'file_path' => $this->view_path . "main_prestation_page",
            'stack_css' => 'main_prestation',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'servicesTypes' => $servicesTypes
        ]);
    }
    public function showSubPrestation(Request $request, $type)
    {
        try {
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'services?service_type=' . $type, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $services = json_decode($response->getBody()->getContents());
            $services = $services->data;

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $services = [];
            return redirect('/prestations', 302, [], false);
        }

        return view("default", [
            'file_path' => $this->view_path . "sub_prestation",
            'stack_css' => 'sub_prestation',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'type' => $type,
            'services' => $services,
            'admin' => $this->isAdmin($request->session()->get('token'))
        ]);
    }
    public function showPrestation(Request $request, $type, $id)
    {
        try {
            $client = new Client();
            $response = $client->get(env('API_URL') . 'services?uuid=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $service = json_decode($response->getBody()->getContents());
            $service = $service->data;
            $account = $service[0]->account;

            $response = $client->getAsync(env('API_URL') . 'disponibility?account='.$account, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $disponibility = json_decode($response->getBody()->getContents());
            $disponibility = $disponibility->data;

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $service = [];
            return redirect('/prestations/'.$type, 302, [], false);
        }

        return view("default", [
            'file_path' => $this->view_path . "prestation",
            'stack_css' => 'prestation',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'service' => $service,
            'type' => $type,
            'id' => $id,
            'disponibility' => $disponibility,
            'admin' => $this->isAdmin($request->session()->get('token'))

        ]);
    }
    function doReservationPrestation(Request $request, $type, $id)
    {
        //dd($request->all());
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
            return redirect('prestations/'.$type."/". $id, 302, [], false)->withErrors(['error' => "Erreur d'authentification"]);
        }
        $data = $request->validate([
            'start_date' => 'required|date'
        ]);

        $date_start = $data['start_date'];

        try{
            $response = $client->get(env('API_URL') . 'basket?account=' . $accountUuid, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());

            $find = false;

            $baskets = $data->baskets;
            $currentBasket = null;
            for($i = 0; $i < count($baskets); $i++){
                if($baskets[$i]->paid == '0'){
                    $find = true;
                    $currentBasket = $baskets[$i];
                    break;
                }
            }

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type.'/'.$id, 302, [], false)->withErrors(['error' => 'Erreur lors de la réservation']);
        }

        try{
            if (!$find){
                $response = $client->post(env('API_URL') . 'basket', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $request->session()->get('token')
                    ],
                    'json' => [
                        'account' => $accountUuid
                    ]
                ]);
                $currentBasket = json_decode($response->getBody()->getContents());
            }
            $currentBasketUUID = $currentBasket->uuid;

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type.'/'.$id, 302, [], false)->withErrors(['error' => 'Erreur lors de la réservation']);
        }

        try{
            $body = [
                'basket' => $currentBasketUUID,
                'services' => $id,
                'start_time' => $date_start
            ];
            dd($body);
            $client = new Client();
            $response = $client->post(env('API_URL') . 'basket/services', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => $body
            ]);

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type.'/'.$id, 302, [], false)->withErrors(['error' => 'Erreur lors de la réservation']);
        }
        return redirect('prestations/'.$type.'/'.$id, 302, [], false)->with('success', 'Réservation éffectué avec succès');
    }

    function createPrestation(Request $request)
    {
        try{
            $client = new Client();
            $response = $client->get(env('API_URL') . 'services_types?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $servicesTypes = json_decode($response->getBody()->getContents());
            $servicesTypes = $servicesTypes->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $servicesTypes = [];
        }


        return view("default", [
            'file_path' => $this->view_path . "create_prestation",
            'stack_css' => 'create_prestation',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'servicesTypes' => $servicesTypes
        ]);
    }

    function doCreateService(Request $request)
    {
        //dd($request->all());
        $messages = [
            'service.required' => 'Le service est obligatoire',
            'img.image' => 'Le fichier doit être une image',
            'img.mimes' => 'Le fichier doit être une image de type jpeg, png ou svg',
            'img.max' => 'Le fichier ne doit pas dépasser 2Mo',
            'img.extensions' => 'Le fichier doit être une image de type jpeg, png ou svg'
        ];

        $validator = Validator::make($request->all(), [
            'service' => 'required|string',
            'img' => 'sometimes|nullable|image|mimes:jpeg,png,svg|max:2048|extensions:jpeg,png,svg'
        ], $messages);

        if ($validator->fails()) {
            return redirect('prestations/createPrestation')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $service = $data['service'];
        $img = $request->file('img');

        if (!$img) {
            return redirect('prestations/createPrestation')
                ->withErrors(['img' => 'Image is required'])
                ->withInput();
        }


        //dd($data);
        $service = $data['service'];
        $img = $data['img'] ?? null;

        if($img == null || $img == ""){
            $img = "NULL";
        }
        //dd($img,$service);
        $client = new Client();
        try{
            $response = $client->post(env('API_URL') . 'services_types', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    'type' => $service,
                    'imgPath' => $img->getClientOriginalName()
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/createPrestation', 302, [], false)->withErrors(['error' => 'Erreur lors de la création du service']);
        }

        Storage::disk('wasabi')->makeDirectory('services');

        Storage::disk('wasabi')->putFileAs('services', $img, $img->getClientOriginalName());

        //echo '<img src="'.Storage::disk('wasabi')->url('services/'.$img->getClientOriginalName()).'"/>';
        //die();
        return redirect('prestations/createPrestation', 302, [], false)->with('success', 'Service créer');

    }

    function doCreatePrestation(PostCreatePrestation $request)
    {
        $data = $request->validated();

        $description = $data['description'];
        $price = $data['price'];
        $duration = $data['duration'];
        $imgPath = $data['imgPath'];
        $service_id = $data['service_id'];

        $client = new Client();
        try{
            $response = $client->get(env('API_URL') . 'account?token=' . $request->session()->get('token'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());

            $accountUuid = $account->data[0]->uuid;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/createPrestation', 302, [], false)->withErrors(['error' => 'Erreur lors de la création de la prestation'])->withInput(
                $request->all()
            );
        }

        $body = [
            'description' => $description,
            'price' => $price,
            'duration' => $duration,
            'imgPath' => $imgPath->getClientOriginalName(),
            'service_type' => $service_id,
            'account' => $accountUuid
        ];

        //dd($body);

        try{
            $response = $client->post(env('API_URL') . 'services', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ],
                'json' => $body
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/createPrestation', 302, [], false)->withErrors(['error' => 'Erreur lors de la création de la prestation'])->withInput(
                $request->all()
            );
        }

        $service = json_decode($response->getBody()->getContents());
        $serviceUuid = $service->uuid;

        Storage::disk('wasabi')->makeDirectory('services/'.$serviceUuid);

        Storage::disk('wasabi')->putFileAs('services/'.$serviceUuid, $imgPath, $imgPath->getClientOriginalName());

        return redirect('prestations/createPrestation', 302, [], false)->with('success', 'Prestation créée');

    }

    function removePrestation(Request $request, $type, $id)
    {
        $client = new Client();
        try{
            $response = $client->get(env('API_URL') . 'basket?all=true',[
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);

            $baskets = json_decode($response->getBody()->getContents());
            $baskets = $baskets->baskets;

            foreach ($baskets as $basket){

                foreach($basket->SERVICES as $service){
                    if($service->uuid == $id){
                        $response = $client->delete(env('API_URL') . 'basket/services?basket=' . $basket->uuid.'&services='.$id, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $request->session()->get('token')
                            ]
                        ]);
                    }
                }
            }

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type, 302, [], false)->withErrors(['error' => 'Erreur lors de la suppression de la prestation']);
        }

        try {
            $client = new Client();
            $response = $client->get(env('API_URL') . 'review?uuid=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $reviews = json_decode($response->getBody()->getContents());
            $reviews = $reviews->data;

            foreach ($reviews as $review) {
                if ($review->services == $id) {
                    $response = $client->delete(env('API_URL') . 'review?uuid=' . $review->uuid, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $request->session()->get('token')
                        ]
                    ]);
                }
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type, 302, [], false)->withErrors(['error' => 'Erreur lors de la suppression de la prestation']);
        }


        try{
            $response = $client->delete(env('API_URL') . 'services?uuid=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type, 302, [], false)->withErrors(['error' => 'Erreur lors de la suppression de la prestation']);
        }

        return redirect('prestations/'.$type, 302, [], false)->with('success', 'Prestation supprimée');
    }

    function approuvePrestation(Request $request, $type, $id)
    {
        $client = new Client();
        try{
            $response = $client->put(env('API_URL') . 'services?uuid=' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ],
                'json' => [
                    'validated' => '1'
                ]
            ]);

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('prestations/'.$type, 302, [], false)->withErrors(['error' => 'Erreur lors de l\'approbation de la prestation']);
        }

        return redirect('prestations/'.$type, 302, [], false)->with('success', 'Prestation approuvée');
    }

}
