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
            return redirect('/login', 302, [], true)->withErrors([
                "error" => "Error when get data"
            ]);
        }

    }
    public function logout(Request $request)
    {
        if ($request->session()->has('token')) {
            /*$client = new Client();
            $response = $client->post( env("API_URL") . '/logout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            if ($response->getStatusCode() === 200) {
                $request->session()->forget('token');
                $request->session()->flush();
                return redirect('/login', 302, [], true)->with('success', 'Logout success!');
            }*/
            $request->session()->forget('token');
            $request->session()->flush();
            return redirect('/login', 302, [], true)->with('success', 'Logout success!');
        }
    }

    public function register() : View
    {
        try{
            $client = new Client();
            $response = $client->get( env("API_URL") . 'account?all=true');
            $responseBody = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() !== 201) {
                return redirect('/register', 302, [], true)->withErrors([
                    "error" => "Error when get data"
                ]);
            }
            return view('auth.register', ['data'=>$responseBody]);
        }catch (GuzzleException $e) {
            return redirect('/register', 302, [], true)->withErrors([
                "error" => "Error when get data"
            ]);
        }
    }
    function checkRegister(RegisterRequest $request)
    {
        $dataInput = $request->validated();
        $client = new Client();
        $response = $client->post( env("API_URL") . '/register', [
            'form_params' => [
                'name' => $dataInput['name'],
                'email' => $dataInput['email'],
                'password' => $dataInput['password'],
                'password_confirmation' => $dataInput['password_confirmation'],
                'firstname' => $dataInput['firstname'],
                'lastname' => $dataInput['lastname'],
                'account_type' => $dataInput['account_type'],
            ]
        ]);
        $responseBody = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() === 200) {
            return redirect('/login', 302, [], true)->with('success', 'Register success!');
        }
        return to_route('auth.register')->withErrors([
            "name" => $responseBody['message'],
            "email" => $responseBody['message'],
            "password" => $responseBody['message'],
            "password_confirmation" => $responseBody['message'],
            "firstname" => $responseBody['message'],
            "lastname" => $responseBody['message'],
            "account_type" => $responseBody['message'],
        ])->onlyInput('name', 'email','firstname', 'lastname', 'account_type');
    }
}
