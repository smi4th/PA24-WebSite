<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

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
            'services' => $services
        ]);
    }
    public function showPrestation(Request $request, $type, $id)
    {
        try {
            $client = new Client();
            $response = $client->getAsync(env('API_URL') . 'services?uuid=' . $id."&service_type=".$type, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $service = json_decode($response->getBody()->getContents());
            $service = $service->data;

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
            'service' => $service
        ]);
    }
}
