<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use GuzzleHttp\Client;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public $connected = false, public $light, public $profile)
    {
        /**
         * $connected : savoir si on affiche le header connecté ou non avec photo de profil
         * $light : savoir si on affiche le header en mode light ou dark
         * $profile : si c'est le header de la page profil
         */
        //dd($connected,$light,$profile);
        $this->connected = $connected == null ? false : $connected;
        $this->light = $light == null ? false : $light;
        $this->profile = $profile == null ? false : $profile;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $client = new Client();
        $token = session('token');
        $response = $client->get(env("API_URL") . 'account?token=' . $token, [
            'headers' => [
                'authorization' => 'Bearer ' . $token,
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        $values =  ['connected' => $this->connected, 'light' => $this->light, 'profile' => $this->profile, 'data' => $data];
        //dd($values['data']['data'][0]['imgPath']);
        //die();
        return view('components.header',$values);
    }
}
