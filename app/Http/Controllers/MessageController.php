<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Monolog\Handler\ErrorLogHandler;
use Illuminate\Support\Facades\Hash;
use App\Events\MessageSent;
use GuzzleHttp\Exception\RequestException;

class MessageController extends Controller
{
    var string $view_path = "message.";
    private function getAllUsers()
    {
        //renvoie la liste des utilisateurs
        $client = new Client();
        $token = session('token');
        return $client->get(env("API_URL") . 'account?all=true', [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
    }

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
    private function getUserMessages($uuid)
    {
        //renvoie les messages de l'utilisateur
        $client = new Client();
        $token = session('token');
        if (empty($token)) {
            return response()->json(['error' => 'token is required'], 400);
        }
        //dd(env("API_URL"), $token, $uuid);
        $messages =  $client->get(env("API_URL") . 'message?account=' . $uuid . '&author=' . $uuid, [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
        return $messages;
        $messages = json_decode($messages->getBody()->getContents());
        //dd($messages);
        
        
    }
    function index()
    {
        $listUsers = json_decode($this->getAllUsers()->getBody()->getContents());
        $infoUser = json_decode($this->getInfoprofile()->getBody()->getContents());
        
        $uuid = $infoUser->data[0]->uuid;
        //dd($uuid);
        //dd($this->getUserMessages($uuid));
        $messages = json_decode($this->getUserMessages($uuid)->getBody()->getContents());
        //dd($messages);
        usort($messages->data, function ($a, $b) {
            return strtotime($a->creation_date) - strtotime($b->creation_date);
        });

        return view("default", [
            'file_path' => $this->view_path . "main_message",
            'stack_css' => 'main_profile.css',
            'connected' => true,
            'profile' => false,
            'light' => false,
            'users' => $listUsers,
            'data' => $infoUser,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient' => 'required',
            'message' => 'required'
        ]);

        $recipient = $request->recipient;
        $info = $this->getInfoprofile();
        $client = new Client();
        $token = session('token');
        $body = [
            'author' => json_decode($info->getBody()->getContents())->data[0]->uuid,
            'account' => $recipient,
            'content' => $request->message,
            'imgPath' => "NULL"
        ];
        $message = [
            'author_uuid' => $body['author'],
            'recipient_account' => $body['account'],
            'message_content' => $body['content'],
            'image_path' => $body['imgPath']
        ];
        event(new MessageSent($message));
        try {
            $response = $client->post(env("API_URL") . 'message', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
                'json' => $body
            ]);
            //$responseData = json_decode($response->getBody()->getContents(), true);
            
            return response()->json(['message' => 'Message sent successfully!', ], 200);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return response()->json(json_decode($responseBodyAsString), $response->getStatusCode());
        }
    }
}
