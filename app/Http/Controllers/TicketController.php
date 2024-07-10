<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

class TicketController extends Controller
{
    var string $view_path = "message.";

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

    function getAllTicket($token,$accountUUID)
    {
        $client = new Client();
        $discussion = [];

        try{
            $response = $client->get(env('API_URL') . "status?all=true",[
                'headers' => [
                    "Authorization" => "Bearer ". $token
                ]
            ]);

            $status = json_decode($response->getBody()->getContents());
            $status = $status->data;

            $allStatus = [];
            foreach($status as $stat){
                $allStatus[$stat->uuid] = $stat->status;
            }


            $response = $client->get(env('API_URL') . "ticket?account=" . $accountUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $token

                ]
            ]);

            $allTickets = json_decode($response->getBody()->getContents());

            foreach($allTickets->data as $ticket){
                $discussion[] = [
                    'uuid' => $ticket->uuid,
                    'title' => $ticket->title,
                    'status' => $allStatus[$ticket->status],
                ];
            }

            return $discussion;


        }catch (\Exception $e){
            error_log($e->getMessage());

            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages"
            ]);
        }

    }

    function getTicketMesssageByUUID(Request $request, $ticketUUID)
    {

        $infoProfile = $this->getInfoprofile($request);
        $accountUUID = $infoProfile->data[0]->uuid;

        $client = new Client();
        $chats = [];

        try{
            $response = $client->get(env('API_URL') . "ticket?uuid=" . $ticketUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $request->session()->get('token')

                ]
            ]);

            $allTickets = json_decode($response->getBody()->getContents());

            $ticket = $allTickets->data[0];

            $chats[] = [
                "content" => $ticket->description,
                "creation_date" => $ticket->creation_date,
                "ticket" => $ticketUUID,
                "account"=> $accountUUID
            ];

            $response = $client->get(env('API_URL') . "tmessage?ticket=" . $ticketUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $request->session()->get('token')
                ]
            ]);

            $messages = json_decode($response->getBody()->getContents());

            foreach ($messages->data as $message){
                $chats[] = [
                    "content" => $message->content,
                    "creation_date" => $message->creation_date,
                    "ticket" => $ticketUUID,
                    "account" => $message->account
                ];
            }

            return $chats;

        }catch (\Exception $e){
            error_log($e->getMessage());

            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages du ticket"
            ]);
        }


    }

    function getAllInfo(Request $request)
    {
        $infoProfile = $this->getInfoprofile($request);

        $accountUUID = $infoProfile->data[0]->uuid;

        $listTickets = $this->getAllTicket($request->session()->get('token'),$accountUUID);

        $allTicketsMessages = [];

        foreach($listTickets as $ticket){
            $allTicketsMessages[] = $this->getTicketMesssageByUUID($request,$ticket['uuid']);
        }

        return [$listTickets,$allTicketsMessages,$accountUUID];
    }

    function showMyTickets(Request $request)
    {
        $allTickets = $this->getAllInfo($request);

        return view("default", [
            'file_path' => $this->view_path . "main_tickets",
            'stack_css' => 'message_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'allTickets' => $allTickets[0],
            'allMessages' => $allTickets[1],
            'data' => $allTickets[2]
        ]);

    }

    public function sendMessage(Request $request)
    {
        $messages = [
            'author.required' => 'L\'auteur est requis',
            'ticket.required' => 'Le destinataire est requis',
            'message.required' => 'Le message est requis',
            'message.max' => 'Le message ne doit pas dépasser 255 caractères'
        ];

        $data = $request->validate([
            'ticket' => 'required',
            'account' => 'required',
            'message' => 'required|string|max:255'
        ]);

        $client = new Client();
        $body = [
            'account' => $data['account'],
            'content' => $data['message'],
            'ticket' => $data['ticket']
        ];

        try{
            $response = $client->post(env('API_URL') . 'tmessage',[
                'headers' => [
                    "Authorization" => session('token')
                ],
                'json' => $body
            ]);

            $response = json_decode($response->getBody()->getContents(),true);
            return response()->json($response,200);

        }catch (\Exception $e){
            error_log($e->getMessage());
            Log::log('error',$e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ],500);
        }

        return response()->json($response,200);
    }

}
