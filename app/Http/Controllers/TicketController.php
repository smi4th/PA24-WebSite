<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class TicketController extends Controller
{
    public function showDemandSupport(Request $request)
    {
        try{
            $client = new Client();
            $response = $client->get(env('API_URL') . 'status?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $status = json_decode($response->getBody()->getContents());
            $status = $status->data;

        }catch (\Exception $e){
            error_log($e->getMessage());
            $status = [];
        }

        return view("default",[
            'file_path' => "demand_ticket",
            'stack_css' => 'demand_ticket',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'status' => $status
        ]);
    }

    public function doDemandSupport(TicketRequest $request)
    {
        $data = $request->validated();

        $client = new Client();
        try {
            $response = $client->get(env('API_URL') . 'account?token=' . $request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
            ]);
            $response = json_decode($response->getBody()->getContents());
            $accountUuid = $response->data[0]->uuid;
        }
        catch (\Exception $e) {
            error_log($e->getMessage());
            return redirect('/demandSupport',302, [], true)->withErrors(['Une erreur est survenue lors de la création du ticket'])->withInput(
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'status' => "1"
                ]
            );
        }

        try {
            $body = [
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
                'account' => $accountUuid
            ];

            $response = $client->post(env('API_URL') . 'ticket', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => $body
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/demandSupport',302, [], true)->withErrors(['Une erreur est survenue lors de la création du ticket'])->withInput(
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'status' => $data['status']
                ]
            );
        }

        return redirect('/demandSupport',302, [], true)->with('success', 'Le ticket a bien été créé');
    }
    /*public function showTickets(Request $request)
    {

        $client = new Client();
        try {
            $response = $client->getAsync(env('API_URL') . 'status?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ])->wait();
            $status = json_decode($response->getBody()->getContents());
            $status = $status->data;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $status = [];
        }

        return view("default",[
            'file_path' => "demand_ticket",
            'stack_css' => 'demand_ticket',
            'connected' => $this->isAuth(),
            'profile' => false,
            'light' => false,
            'status' => $status
        ]);
    }*/

}
