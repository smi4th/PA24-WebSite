<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PutRequestPrestation;
use App\Http\Requests\ReviewsRequest;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\ErrorLogHandler;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    var string $view_path = "profile.";

    function index(Request $request)
    {
        $dataUser = $this->getInfoprofile($request);

        return view("default", [
            'file_path' => $this->view_path . "main_profile",
            'stack_css' => 'main_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'data' => $dataUser,
        ]);
    }

    private function getInfoprofile(Request $request)
    {
        try{
            $client = new Client();
            $token = $request->session()->get('token');

            $response = $client->get(env("API_URL") . 'account?token=' . $token, [
                'headers' => [
                    "Authorization" => "Bearer ". $token

                ]
            ]);

            $responseBody = json_decode($response->getBody()->getContents());
            return $responseBody;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return $e;
        }
    }
    public function editProfile($inputName)
    {
        //renvoie la vue de modification du profil
        switch ($inputName) {
            case "password":
                $nom = "Mot de passe";
                break;
            case "email":
                $nom = "Email";
                break;
            case "username":
                $nom = "Nom d'utilisateur";
                break;
            case "name":
                $nom = "Nom";
                break;
            default:
                error_log("Input introuvable");
                break;
        }
        return view('profile.edit_profile', ['inputName' => $inputName, 'nom' => $nom]);
    }

    private function verifyPassword($password)
    {
        try {
            //verifie si le mot de passe est correct
            $client = new Client();
            $token = session('token');
            $dataPwd = $client->get(env("API_URL") . 'account/verifyPassword?password=' . $password, [
                'headers' => [
                    'authorization' => 'Bearer ' . $token,
                ]
            ]);
            $isPwd = json_decode($dataPwd->getBody()->getContents(), true);
            return $isPwd['correct'];
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function updateProfile(Request $request)
    {
        //met a jour le profil, et renvoie la vue du profil
        $client = new Client();
        $token = session('token');
        $response = $client->get(env("API_URL") . 'account?token=' . $token, [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
        $password = $request->input('password');
        if (!$this->verifyPassword($password)) {
            return redirect('/profile', 302, [], false)->withErrors(["error" => "Error : wrong password"]);
        }

        // recuperer la provenance de la requete (son url) pour savoir quel champ a été modifié
        $url = url()->previous();
        $url = explode("/", $url);
        $field = $url[count($url) - 1];
        $value = $request->input('name');
        if ($field == "name") {
            $field1 = "first_name";
            $field2 = "last_name";
        } else if ($field == "password") {
            if ($request->input('newpassword') != $request->input('newpasswordconfirm')) {
                return redirect('/profile', 302, [], false)->withErrors(["error" => "Error : new password and confirm password are not the same"]);
            }
            $field = "password";
            $value = $request->input('newpassword');
        }

        // Mise à jour selon le champ spécifié
        $client = new Client();
        if (isset($field1)) {
            $body = [$field1 => $request->input('firstname'), $field2 => $request->input('lastname')];
        } else {
            //dd($field);
            $body = [$field => $value];
        }

        try {
            $infosAcc = $this->getInfoprofile($request);
            $uuid = json_decode($infosAcc->getBody()->getContents(), true)['data'][0]['uuid'];
            $response = $client->put(env("API_URL") . 'account?uuid=' . $uuid, [
                'headers' => ['authorization' => 'Bearer ' . $token],
                'json' => $body
            ]);


            if ($response->getStatusCode() == 200) {
                return redirect('/profile', 302, [], false)->with('success', 'Profile updated!');
            } else {
                return redirect('/profile', 302, [], false)->withErrors([
                    "error" => "Error when update profile: " . $response->getBody()->getContents() . " " . $body[$field]
                ]);
            }
        } catch (\Exception $e) {
            return redirect('/profile', 302, [], false)->withErrors(["error" => "Error when update profile 2: " . $e->getMessage()]);
        }
    }
    public function uploadProfileImage(Request $request)
    {
        $messsages = [
            'profile_image.required' => 'L\'image est obligatoire',
            'profile_image.image' => 'Le fichier doit être une image',
            'profile_image.mimes' => 'Le fichier doit être une image de type jpeg, png ou svg',
            'profile_image.max' => 'Le fichier ne doit pas dépasser 2Mo',
            'profile_image.extensions' => 'Le fichier doit être une image de type jpeg, png ou svg',
            'profile_image.uploaded' => 'Erreur lors de l\'upload de l\'image'
        ];

        $request->validate([
            'profile_image' => 'required|image|max:2048|extensions:jpeg,png,svg|mimes:jpeg,png,svg'
        ], $messsages);

        $data = $request->all();
        $newImage = $data['profile_image'];

        $dataUser = $this->getInfoprofile($request);

        $accountUUID = $dataUser->data[0]->uuid;

        $client = new Client();
        try{
            $response = $client->put(env("API_URL") . 'account?uuid=' . $accountUUID, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token'),
                ],
                'json' => [
                    'imgPath' => $newImage->getClientOriginalName()
                ]
            ]);

            Storage::disk('wasabi')->putFileAs('pfp/', $newImage, $newImage->getClientOriginalName());

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du changement de l'image"
            ]);
        }

        return redirect('/profile', 302, [], false)->with('success', 'Image de profil modifiée!');

    }
    public function showReviews(Request $request)
    {
        try{
            $client = new Client();
            $token = $request->session()->get('token');

            $response = $client->get(env("API_URL") . 'account?token='.$token, [
                'headers' => [
                    "Authorization" => "Bearer ". $token
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            $account = $account->data[0]->uuid;

            $response = $client->get(env("API_URL") . 'basket?account='.$account, [
                'headers' => [
                    "Authorization" => "Bearer ". $token
                ]
            ]);
            $baskets = json_decode($response->getBody()->getContents());

            $allBaskets = [];
            $baskets = $baskets->baskets;

            for ($i = 0; $i < count($baskets); $i++){

                if($baskets[$i]->paid != 1){
                    continue;
                }
                $allBaskets[] = $baskets[$i];
            }

            $response = $client->get(env("API_URL") . 'review?account='.$account, [
                'headers' => [
                    "Authorization" => "Bearer ". $token
                ]
            ]);
            $reviews = json_decode($response->getBody()->getContents());
            $reviews = $reviews->data;

            if(count($allBaskets) == 0){
                return redirect('/profile', 302, [], false)->withErrors([
                    "error" => "Vous n'avez pas de reviews en attente"
                ]);
            }

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Error when get reviews: " . $e->getMessage()
            ]);
        }

        return view("default", [
            'file_path' => $this->view_path . "reviews",
            'stack_css' => 'reviews',
            'connected' => true,
            'profile' => true,
            'light' => false,
            "basketsPaid" => $allBaskets,
            "reviews" => $reviews,
            "currentDate" => strtotime(date("Y-m-d"))
        ]);
    }

    public function addReviews(ReviewsRequest $request){
        //var_dump($request);
        $data = $request->validated();
        $client = new Client();
        $accountUUID;
        try {
            $response = $client->get(env("API_URL") . 'account?token='.$request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer ". $request->session()->get('token')
                ]
            ]);
            $accountUUID = json_decode($response->getBody()->getContents());
            $accountUUID = $accountUUID->data[0]->uuid;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile/reviews', 302, [], false)->withErrors([
                "error" => "Error when get account: "
            ]);
        }

        $body = [
            "content" => $data['comment'],
            "note" => $data['note'],
            "account" => $accountUUID,
        ];

        if(isset($request->bedroom)){
            $body["bedRoom"] = $request->bedroom;
        }
        elseif(isset($request->service)){
            $body["service"] = $request->service;
        }
        elseif(isset($request->housing)){
            $body["housing"] = $request->housing;
        }else{
            return redirect('/profile/reviews', 302, [], false)->withErrors([
                "error" => "Error when add review: housing is required"
            ]);
        }
        //dd($body);
        try {
            $response = $client->post(env("API_URL") . 'review', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => $body
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile/reviews', 302, [], false)->withErrors([
                "error" => "Error when add review"
            ]);
        }

        return redirect('/profile/reviews', 302, [], false)->with('success', 'Review added!');
    }

    public function removeReviews(Request $request, $id)
    {
        $client = new Client();
        try {
            $response = $client->delete(env("API_URL") . 'review?uuid=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return back()->withErrors([
                "error" => "Error when remove review"
            ]);
        }

        return back()->with('success', 'Review removed!');
    }

    public function showMyPrestations(Request $request)
    {
        $dataUser = $this->getInfoprofile($request);

        $prestations = [];

        $accountUUID = $dataUser->data[0]->uuid;

        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'services?account=' . $accountUUID, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);

            $prestations = json_decode($response->getBody()->getContents());
            $prestations = $prestations->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos prestations"
            ]);
        }

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
            'file_path' => $this->view_path . "prestations",
            'stack_css' => 'prestations_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'prestations' => $prestations,
            'servicesTypes' => $servicesTypes
        ]);
    }

    function updatePrestation(PutRequestPrestation $request , $id)
    {

        $data = $request->validated();

        $description = $data['description'];
        $price = $data['price'];
        $duration = $data['duration'];
        $image = $data['image'];
        $category = $data['category'];

        $body = [
            'description' => $description,
            'price' => $price,
            'duration' => $duration,
            'service_type' => $category,
            'validated' => '0'
        ];


        if ($image != null){
            $body['imgPath'] = $image->getClientOriginalName();
        }

        $client = new Client();
        try{
            $response = $client->put(env("API_URL") . 'services?uuid=' . $id, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => $body
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile/prestations', 302, [], false)->withErrors([
                "error" => "Erreur lors de la mise à jour de la prestation"
            ]);
        }

        if ($image != null){
            Storage::disk('wasabi')->putFileAs('services/'.$id, $image, $image->getClientOriginalName());
        }

        return redirect('/profile/prestations', 302, [], false)->with('success', 'Prestation modifiée!');
    }

    function showBills(Request $request)
    {
        $infoProfile = $this->getInfoprofile($request);
        $accountUUID = $infoProfile->data[0]->uuid;

        try{
            $client = new Client();
            $response = $client->get(env("API_URL") . 'basket?account=' . $accountUUID, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $baskets = json_decode($response->getBody()->getContents());
            $baskets = $baskets->baskets;

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos factures"
            ]);
        }

        $allBills = [];
        foreach ($baskets as $basket){
            if($basket->paid == 1){
                $allBills[] = $basket->uuid;
            }
        }

        return view("default", [
            'file_path' => $this->view_path . "bills",
            'stack_css' => 'bills_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'bills' => $allBills
        ]);
    }

    function showManagementPrestation(Request $request)
    {
        $dataUser = $this->getInfoprofile($request);

        $accountUUID = $dataUser->data[0]->uuid;

        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'basket?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);

            $result = json_decode($response->getBody()->getContents());
            $baskets = $result->baskets;

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos prestations"
            ]);
        }

        $allPrestations = [];
        $infoCustomer = [];

        foreach ($baskets as $basket){

            $currentAccount = $basket->account;
            $status = $basket->paid;
            $allServices = $basket->SERVICES;

            foreach ($allServices as $service){

                if($service->account == $accountUUID){
                    $allPrestations[] = $service;
                    $service->customer = $currentAccount;
                    $service->status = $status;
                    $service->basket = $basket->uuid;
                }
            }
        }

        try{
            foreach ($allPrestations as $prestation) {
                $response = $client->get(env("API_URL") . 'account?uuid=' . $prestation->customer, [
                    'headers' => [
                        "Authorization" => "Bearer " . $request->session()->get('token')
                    ]
                ]);
                $data = json_decode($response->getBody()->getContents());

                $infoCustomer[$prestation->customer] = $data->data[0]->username;

            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos commandes"
            ]);
        }


        return view("default", [
            'file_path' => $this->view_path . "management_prestation",
            'stack_css' => 'management_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'prestations' => $allPrestations,
            'customers' => $infoCustomer
        ]);
    }

    function generateInterventionForm(Request $request)
    {
        $messages = [
            'customer.required' => 'Le client est obligatoire',
            'startTime.required' => 'L\'heure de début est obligatoire',
            'duration.required' => 'La durée est obligatoire',
            'duration.regex' => 'La durée doit être au format HH:MM:SS',
            'price.required' => 'Le prix est obligatoire',
            'price.numeric' => 'Le prix doit être un nombre',
            'prestation.required' => 'La prestation est obligatoire',
            'comment.required' => 'Le commentaire est obligatoire',
            'basket.required' => 'Le panier est obligatoire'
        ];

        $data = $request->validate([
            'customer' => 'required|string',
            'startTime' => 'required|date',
            'duration' => 'required|regex:/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/',
            'price' => 'required|numeric',
            'prestation' => 'required|uuid',
            'comment' => 'required|string|max:255',
            'basket' => 'required|uuid'
        ], $messages);

        $pdf = (new PdfGeneratorController())->generateInterventionForm($data,$request);

        if(!$pdf){
            return redirect('/profile/prestations/management', 302, [], false)->withErrors([
                "error" => "Erreur lors de la génération du formulaire"
            ]);
        }

        return redirect('/profile/prestations/management', 302, [], false)->with('success', "Fiche d'intervention créée");
    }

    function showReviewsPrestation(Request $request)
    {
        $infoProfile = $this->getInfoprofile($request);

        $accountUUID = $infoProfile->data[0]->uuid;

        $prestationsUUID = [];

        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'services?account=' . $accountUUID, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $prestations = json_decode($response->getBody()->getContents());
            $prestations = $prestations->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos avis"
            ]);
        }

        foreach ($prestations as $prestation){
            $prestationsUUID[] = $prestation->uuid;
        }

        $allReviews = [];

        try{
            $client = new Client();
            $response = $client->get(env("API_URL") . 'review?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $reviews = json_decode($response->getBody()->getContents());
            $reviews = $reviews->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos avis"
            ]);
        }

        foreach ($reviews as $review){
            if(in_array($review->services, $prestationsUUID)){
                $allReviews[] = $review;
            }
        }

        return view("default", [
            'file_path' => $this->view_path . "reviews_prestations",
            'stack_css' => 'reviews_prestations',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'reviews' => $allReviews
        ]);

    }

    function showReviewsMyLocation(Request $request)
    {
        $infoProfile = $this->getInfoprofile($request);

        $accountUUID = $infoProfile->data[0]->uuid;

        $locationsUUID = [];

        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'housing?account=' . $accountUUID, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $locations = json_decode($response->getBody()->getContents());
            $locations = $locations->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos avis"
            ]);
        }

        foreach ($locations as $location){
            $locationsUUID[] = $location->uuid;
        }

        $allReviews = [];

        try{
            $client = new Client();
            $response = $client->get(env("API_URL") . 'review?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $reviews = json_decode($response->getBody()->getContents());
            $reviews = $reviews->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos avis"
            ]);
        }

        foreach ($reviews as $review){
            if(in_array($review->housing, $locationsUUID)){
                $allReviews[] = $review;
            }
        }

        return view("default", [
            'file_path' => $this->view_path . "reviews_prestations",
            'stack_css' => 'reviews_prestations',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'reviews' => $allReviews
        ]);
    }

    function showMyLocations(Request $request)
    {
        $dataUser = $this->getInfoprofile($request);

        $accountUUID = $dataUser->data[0]->uuid;

        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'housing?account=' . $accountUUID, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);

            $locations = json_decode($response->getBody()->getContents());
            $locations = $locations->data;
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement de vos locations"
            ]);
        }

        return view("default", [
            'file_path' => $this->view_path . "locations",
            'stack_css' => 'locations_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'locations' => $locations
        ]);
    }

}
