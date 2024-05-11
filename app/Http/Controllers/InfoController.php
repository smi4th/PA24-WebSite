<?php

namespace App\Http\Controllers;

class InfoController extends Controller {
    public function showConfidentialite(){
        return view('info.confidentialite');
    }
    public function showCookies(){
        return view('info.cookie');
    }
    public function showMentionsLegales(){
        return view('info.mentions_legales');
    }
}