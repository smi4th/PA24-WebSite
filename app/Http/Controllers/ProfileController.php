<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Monolog\Handler\ErrorLogHandler;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    var string $view_path = "profile.";

    function index()
    {
        #renvoie la vue du profil
        return view("default", [
            'file_path' => $this->view_path . "main_profile",
            'stack_css' => 'main_profile.css',
            'connected' => true,
            'profile' => true,
            'light' => false
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
    public function showProfile()
    {
        //renvoie la vue du profil avec les infos du compte
        $responseBody = "";
        try {
            $client = new Client();
            //recuperer username en session
            $token = session('token');
            $response = $this->getInfoprofile();

            if ($response->getStatusCode() == 200) {
                $responseBody = json_decode($response->getBody()->getContents(), true);
            } else {
                return redirect('/login', 302, [], true)->withErrors([
                    "error" => "Error when get data: " . $response->getBody()->getContents()
                ]);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            dd($e->getMessage());
        }

        // renvoyer les infos du compte
        return view('profile.main_profile', ['data' => $responseBody]);
    }
    public function editProfile($inputName)
    {
        //renvoie la vue de modification du profil
        if ($inputName == "password") {
            $nom = "Mot de passe";
        }
        if ($inputName == "email") {
            $nom = "Email";
        }
        if ($inputName == "username") {
            $nom = "Nom d'utilisateur";
        }
        if ($inputName == "name") {
            $nom = "Nom";
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
            $infosAcc = $this->getInfoprofile();
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
        $request->validate([
            'profile_image' => 'required|image|max:2048',
        ]);

        $fileName = time() . '.' . $request->profile_image->extension();
        $request->profile_image->move(public_path('assets/images/pfp'), $fileName);

        $client = new Client();
        $token = session('token');
        $infosAcc = $this->getInfoprofile();
        $uuid = json_decode($infosAcc->getBody()->getContents(), true)['data'][0]['uuid'];
        $body = ['imgPath' => $fileName];
        $response = $client->put(env("API_URL") . 'account?uuid=' . $uuid, [
            'headers' => ['authorization' => 'Bearer ' . $token],
            'json' => $body
        ]);
        if ($response->getStatusCode() !== 200) {
            return redirect('/profile', 302, [], false)->withErrors([
                "error" => "Error when update profile: " . $response->getBody()->getContents()
            ]);
        }
        return redirect('/profile', 302, [], false)->with('success', 'Profile picture updated!');
    }
}
