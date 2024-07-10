<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use http\Client\Response;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\ErrorLogHandler;
use Illuminate\Support\Facades\Hash;
use App\Events\MessageSent;
use GuzzleHttp\Exception\RequestException;

class MessageController extends Controller
{
    var string $view_path = "message.";

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

    function getAllContact($token,$accountUUID)
    {
        $client = new Client();
        $contacts = [];
        $infoAccounts = [];

        try{
            $response = $client->get(env('API_URL') . "message?author=" . $accountUUID . "&account=".$accountUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $token

                ]
            ]);

            $allMessages = json_decode($response->getBody()->getContents());

            foreach($allMessages->data as $message){
                if (!in_array($message->account,$contacts) && !in_array($message->author,$contacts)) {

                    if ($message->author == $accountUUID) {
                        $contacts[] = $message->account;

                    } else if ($message->account == $accountUUID) {
                        $contacts[] = $message->author;
                    }
                }
            }

            //dd($contacts);

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages"
            ]);
        }

        try{
            foreach ($contacts as $contact){
                $response = $client->get(env('API_URL') . "account?uuid=" . $contact,[
                    'headers' => [
                        "Authorization" => "Bearer ". $token
                    ]
                ]);

                $data = json_decode($response->getBody()->getContents());
                $infoAccounts[] = $data->data[0];
            }
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages"
            ]);
        }

        return $infoAccounts;
    }

    function getAllMessageOfAccount($token,$accountUUID,$receiverUUID)
    {
        $client = new Client();
        $messages = [];

        try{
            $response = $client->get(env('API_URL') . "message?author=" . $accountUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $token

                ]
            ]);

            $allMessages = json_decode($response->getBody()->getContents());

            foreach($allMessages->data as $message){
                if($message->author == $accountUUID && $message->account == $receiverUUID){
                    $messages[] = $message;
                }
            }

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages"
            ]);
        }

        try{
            $response = $client->get(env('API_URL') . "message?author=" . $receiverUUID,[
                'headers' => [
                    "Authorization" => "Bearer ". $token
                ]
            ]);

            $allMessages = json_decode($response->getBody()->getContents());
            foreach($allMessages->data as $message){
                if($message->author == $receiverUUID && $message->account == $accountUUID){
                    $messages[] = $message;
                }
            }


        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Erreur lors du chargement des messages"
            ]);
        }


        return $messages;

    }

    function getAllMessages(Request $request)
    {
        $infoProfile = $this->getInfoprofile($request);

        $accountUUID = $infoProfile->data[0]->uuid;

        $listUsers = $this->getAllContact($request->session()->get('token'),$accountUUID);

        $allMessages = [];

        foreach ($listUsers as $user){
            $allMessages[] = $this->getAllMessageOfAccount($request->session()->get('token'),$accountUUID,$user->uuid);
        }

        return [$allMessages,$listUsers,$infoProfile];
    }

    function index(Request $request)
    {
        $allMessages = $this->getAllMessages($request);

        return view("default", [
            'file_path' => $this->view_path . "main_message",
            'stack_css' => 'message_profile',
            'connected' => true,
            'profile' => true,
            'light' => false,
            'messages' => $allMessages[0],
            'users' => $allMessages[1],
            'data' => $allMessages[2]
        ]);

    }

    public function sendMessage(Request $request)
    {
        $messages = [
            'author.required' => 'L\'auteur est requis',
            'account.required' => 'Le destinataire est requis',
            'message.required' => 'Le message est requis',
            'message.max' => 'Le message ne doit pas dépasser 255 caractères'
        ];

        $data = $request->validate([
            'author' => 'required',
            'account' => 'required',
            'message' => 'required|string|max:255'
        ]);

        $client = new Client();
        $body = [
            'author' => $data['author'],
            'account' => $data['account'],
            'content' => $data['message'],
            'imgPath' => "NULL"
        ];

        try{
            $response = $client->post(env('API_URL') . 'message',[
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
