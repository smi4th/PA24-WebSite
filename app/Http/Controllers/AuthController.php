<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function login() : View
    {
        return view('auth.login');
    }
    public function checkLogin(LoginRequest $request)
    {
        $dataInput = $request->validated();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token'
        ];
        $body = [
            'email' => $dataInput['email'],
            'password' => $dataInput['password']
        ];

        try{
            $client = new Client();
            $response = $client->post(env("API_URL") . 'login', [
                'headers' => $headers,
                'json' => $body
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 201) {
                $request->session()->regenerate();
                $request->session()->put('token', $responseBody['token']);
                $request->session()->put('auth', true);

                return redirect('/', 302, [], true)->with('success', 'Login success!');
            }
            return redirect('/login', 302, [], true)->withErrors([
                "email" => $responseBody['message'],
                "password" => $responseBody['message']
            ])->onlyInput('email');

        } catch (GuzzleException $e) {
            error_log($e->getMessage());
            return redirect('/login', 302, [], false)->withErrors([
                "error" => "Error when login please try again!"
            ]);
        }

    }
    public function logout(Request $request)
    {
        if ($request->session()->has('token')) {
            try{
                $client = new Client();
                $request->session()->flush();
                $response = $client->delete( env("API_URL") . 'login', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $request->session()->get('token')
                    ]
                ]);
                if ($response->getStatusCode() === 200) {
                    //repasser le secure à true
                    return redirect('/login', 302, [], false)->with('success', 'Logout success!');
                }
                return redirect('/', 302, [], false)->withErrors([
                    "error" => "Error when logout"
                ]);

            }catch (GuzzleException $e) {
                return redirect('/', 302, [], false)->withErrors([
                    "error" => "Error when logout"
                ]);
            }
        }
    }

    public function register()
    {
        try{
            $client = new Client();
            $response = $client->get(env("API_URL") . 'account_type?private=false');
            $responseBody = json_decode($response->getBody()->getContents(), true);

            //dd($responseBody);
            if ($response->getStatusCode() !== 200) {
                return view('auth.login')->withErrors([
                    "error" => "Error when load register page "
                ]);
            }
            $data = $responseBody['data'];
            return view('auth.register', ['data'=>$data]);

        }catch (GuzzleException $e) {
            return view('auth.login')->withErrors([
                "error" => "Error when load register page "
            ]);
        }
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
    public function deleteAccount(){
        $response = $this->getInfoprofile();
        $uuid = json_decode($response->getBody()->getContents(), true)['data'][0]['uuid'];
        $token = session('token');
        try{
            $this->logout(request());
            $client = new Client();
            $response = $client->delete(env("API_URL") . 'account?uuid=' . $uuid, [
                'headers' => [
                    'authorization' => 'Bearer ' . $token,
                ]
            ]);
            if ($response->getStatusCode() === 200) {
                return redirect('/login', 302, [], false)->with('success', 'Delete success!');
            }
            return redirect('/login', 302, [], true)->withErrors([
                "error" => "Error when delete account"
            ]);
        }catch (GuzzleException $e) {
            return redirect('/login', 302, [], true)->withErrors([
                "error" => "Error when delete account"
            ]);
        }
    }
    function checkRegister(RegisterRequest $request)
    {
        try{
            $dataInput = $request->validated();
            $client = new Client();
            $body = [
                'username' => $dataInput['username'],
                'email' => $dataInput['email'],
                'password' => $dataInput['password'],
                'first_name' => $dataInput['firstname'],
                'last_name' => $dataInput['lastname'],
                'account_type' => $dataInput['account_type'],
                'imgPath' => 'NULL'
            ];

            $response = $client->post( env("API_URL") . 'account', [
                'json' => $body
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 201) {
                return redirect('/login', 302, [], false)->with('success', 'Register success!');
            }

            return redirect('/register',302,[],false)->withErrors([
                "name" => $responseBody['message'],
                "email" => $responseBody['message'],
                "password" => $responseBody['message'],
                "password_confirmation" => $responseBody['message'],
                "firstname" => $responseBody['message'],
                "lastname" => $responseBody['message'],
                "account_type" => $responseBody['message'],
            ])->onlyInput('username', 'email','firstname', 'lastname', 'account_type');

        }catch (GuzzleException $e) {
            error_log($e->getMessage());
            return redirect('/register',302,[],false)->withErrors([
                "error" => "Error when register"
            ])->onlyInput('username', 'email','firstname', 'lastname', 'account_type');
        }
    }
}
