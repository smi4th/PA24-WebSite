<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BackOfficeController extends Controller
{
    public function index(): View
    {
        return view('backoffice');
    }
}
