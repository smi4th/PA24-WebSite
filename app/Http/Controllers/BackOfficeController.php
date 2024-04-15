<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BackOfficeController extends Controller
{

    var string $file_path = "backoffice/";

    public function index(): View
    {
        return view('backoffice', [
            'file_path' => $this->file_path . "index",
            'stack_name' => 'styles_index'
        ]);
    }

    public function statistics(): View
    {
        return view('backoffice.statistics');
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
