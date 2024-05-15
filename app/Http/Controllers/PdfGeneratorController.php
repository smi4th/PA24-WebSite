<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorController extends Controller
{
    public function generateReceipt($basket,$account)
    {
        //create a pdf file with the receipt of the basket
        //name the pdf with the basket uuid

        $housing = $basket->HOUSING;
        $services = $basket->SERVICES;
        $bedrooms = $basket->BEDROOMS;
        $equipments = $basket->EQUIPMENTS;

        $total = 0;

        $account = $account->data[0];

        foreach ($services as $service) {
            $total += $service->price;
        }

        foreach ($equipments as $equipment) {
            $total += $equipment->price;
        }

        foreach ($bedrooms as $bedroom) {
            $find = false;
            foreach ($housing as $house) {
                if ($house->uuid == $bedroom->housing){
                    $find = true;
                    break;
                }
            }
            if ($find){
                continue;
            }else{
                $total += $bedroom->price;
            }
        }

        foreach ($housing as $house) {
            $total += $house->price;
        }


        $pdf = PDF::loadView('template_pdf.receipt', [
            'housing' => $housing,
            'services' => $services,
            'bedrooms' => $bedrooms,
            'equipments' => $equipments,
            'total' => $total,
            'account' => $account
        ]);
        $name = 'receipt_'.$basket->uuid.'.pdf';

        Storage::disk('receipts')->put($name, $pdf->output());

        //METTRE ICI LE CODE POUR ENVOYER L'INFORMATION A L'API POUR L'ENREGISTREMENT DU RECU

        return $pdf->download($name);
    }
}
