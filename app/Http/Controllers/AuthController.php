<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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
        $client = new Client();
        $response = $client->post( env("API_URL") . '/login', [
            'form_params' => [
                'email' => $dataInput['email'],
                'password' => $dataInput['password'],
            ]
        ]);
        $responseBody = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() === 200) {
            $request->session()->regenerate();
            $request->session()->put('token', $responseBody['token']);

            return redirect('/landing', 302, [], true)->with('success', 'Login success!');
        }
        return to_route('auth.login')->withErrors([
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
}
