<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ProfileController extends Controller
{
    var string $view_path = "profile.";

    function index()
    {
        return view("default",[
            'file_path' => $this->view_path . "main_profile",
            'stack_css' => 'main_profile.css',
            'connected' => true,
            'profile' => true,
            'light' => false
        ]);
    }
}
