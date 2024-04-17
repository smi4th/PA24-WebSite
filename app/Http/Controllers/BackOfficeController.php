<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BackOfficeController extends Controller
{

    var string $view_path = "backoffice.";
    /*
     *  il tape dans le fichier views/main_backoffice.blade.php au lieu de views/backoffice/index.blade.php
     *
     */
    public function index(): View
    {
        error_log("index");
        return view('main_backoffice', [
            'file_path' => $this->view_path . "index",
            'stack_css' => 'styles_index'
        ]);
    }

    public function statistics(): View
    {
        error_log("statistics");
        return view('main_backoffice', [
            'file_path' => $this->view_path . "statistics",
            'stack_css' => 'styles_statistics'
        ]);
    }

    public function suggests(): View
    {
        return view('backoffice.suggests');
    }

    public function travelers(): View
    {
        return view('backoffice.travelers');
    }

    public function prestations(): View
    {
        return view('backoffice.prestations');
    }

    public function prestationsCompanies(): View
    {
        return view('backoffice.prestations-companies');
    }

    public function providers(): View
    {
        return view('backoffice.donors');
    }

    public function supports(): View
    {
        return view('backoffice.supports');
    }

    public function permissions(): View
    {
        return view('backoffice.permissions');
    }

    public function settings(): View
    {
        return view('backoffice.settings');
    }

    public function __invoke() : View
    {
        return view('backoffice');
    }

}
