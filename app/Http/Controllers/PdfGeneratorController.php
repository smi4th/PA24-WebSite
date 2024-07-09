<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use \Stripe\Stripe;

class PdfGeneratorController extends Controller
{
    public function generateReceipt($basket,$account,$request)
    {
        //create a pdf file with the receipt of the basket
        //name the pdf with the basket uuid

        $housing = $basket->HOUSING;
        $services = $basket->SERVICES;
        $bedrooms = $basket->BEDROOMS;
        $equipments = $basket->EQUIPMENTS;

        $total = 0;
        $totalTVA = 0;

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

        $taxesTVA = 0;
        try{
            $client = new Client();
            $response = $client->get(env('API_URL') . 'taxes?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $taxes = json_decode($response->getBody()->getContents());
            $taxes = $taxes->data;

            foreach ($taxes as $tax) {
                if ($tax->name == "TVA"){
                    $taxesTVA = $tax->value;
                    break;
                }
            }


        }catch (\Exception $e){
            error_log($e->getMessage());
            $taxesTVA = 20;
        }
        $taxesTVA = (int)$taxesTVA;

        $totalTVA = (int)($total * ($taxesTVA/100)) + $total;

        $pdf = Pdf::loadView('template_pdf.receipt', [
            'housing' => $housing,
            'services' => $services,
            'bedrooms' => $bedrooms,
            'equipments' => $equipments,
            'total' => $total,
            'totaltaxes' => $totalTVA,
            'account' => $account
        ]);

        $name = 'receipt_'.$basket->uuid.'.pdf';

        Storage::disk('wasabi')->put('receipts/'.$name, $pdf->output());

        //METTRE ICI LE CODE POUR ENVOYER L'INFORMATION A L'API POUR L'ENREGISTREMENT DU RECU

        return $pdf->download($name);
    }

    public function generateInterventionForm($data,$request)
    {
        try {
            //dd($data);
            $pdf = Pdf::loadView('template_pdf.intervention_form', [
                'customer' => $data['customer'],
                'startTime' => $data['startTime'],
                'duration' => $data['duration'],
                'price' => $data['price'],
                'comment' => $data['comment']
            ]);

            $name = 'intervention_' . $data['basket'] . '.pdf';

            Storage::disk('wasabi')->put('interventions/' . $name, $pdf->output());
        }catch (\Exception $e){
            error_log($e->getMessage());
            return false;
        }

        return $pdf->download($name);
    }
}
