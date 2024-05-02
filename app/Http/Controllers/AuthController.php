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
        //vérfie si les données sont correctes
        dd($dataInput);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token'
        ];

        $body = '{
            "email" => $dataInput["email"],
            "password" => $dataInput["password"]
        }';

        $client = new Client();
        $response = $client->post( env("API_URL") . 'login',$headers,$body);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() === 201) {
            $request->session()->regenerate();
            $request->session()->put('token', $responseBody['token']);

            return redirect('/landing', 302, [], true)->with('success', $responseBody['message']);
        }
        return redirect('auth.login')->with([
           "email" => "Credentials wrong",
            "password" => "Credentials wrong"
        ])->onlyInput('email');

    }
    public function logout(Request $request)
    {
        if ($request->session()->has('token')) {
            $client = new Client();
            $response = $client->post( env("API_URL") . '/logout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            if ($response->getStatusCode() === 200) {
                $request->session()->forget('token');
                $request->session()->flush();
                return redirect('/login', 302, [], true)->with('success', 'Logout success!');
            }
        }
    }

    public function register() : View
    {
        $client = new Client();
        $response = $client->get( env("API_URL") . 'account?all=true');
        $responseBody = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() !== 200) {
            return redirect('/landing', 302, [], true)->withErrors([
                "error" => "Error when get data"
            ]);
        }
        return view('auth.register', ['data'=>$responseBody]);
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
