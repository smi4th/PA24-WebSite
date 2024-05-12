<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public function index()
    {
        return view('profile.main_profile');
    }

    private function getBasket(Request $request)
    {
        $client = new Client();
        $token = $request->session()->get('token');

        try{

            $response = $client->getAsync(env('API_URL') . 'account?token='.$token, [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ]
            ])->wait();
            $account = json_decode($response->getBody()->getContents());
            $account = $account->data[0]->uuid;

            $response = $client->getAsync(env('API_URL') . 'basket?account='.$account, [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ]
            ])->wait();
            $basket = json_decode($response->getBody()->getContents());
            $basket = $basket->baskets;
            foreach ($basket as $item) {
                if ($item->paid == 0){
                    return $item;
                }
            }
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Your basket is empty']);

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile',302,[],false)->withErrors(['error' => 'An error occured while fetching your basket']);
        }
        return redirect('/profile',302,[],false)->withErrors(['error' => 'Your basket is empty']);
    }

    public function checkout(Request $request)
    {

        $all_items = $this->getBasket($request);
        $lines_items = [];

        $housing = $all_items->HOUSING;
        $services = $all_items->SERVICES;
        $bedrooms = $all_items->BEDROOMS;
        $equipments = $all_items->EQUIPMENTS;
        //dd($all_items);
        if (empty($housing) && empty($services) && empty($bedrooms) && empty($equipments)){
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Your basket is empty']);
        }


        foreach ($housing as $house) {
            $lines_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $house->description,
                    ],
                    'unit_amount' => (int)$house->price*100,
                ],
                'quantity' => 1,
            ];
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
            }
            $lines_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $bedroom->description,
                    ],
                    'unit_amount' => (int)$bedroom->price*100,
                ],
                'quantity' => 1,
            ];
        }


        foreach ($services as $service) {
            $lines_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $service->description,
                    ],
                    'unit_amount' => (int)$service->price*100,
                ],
                'quantity' => 1,
            ];
        }

        foreach ($equipments as $equipment) {
            $lines_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $equipment->description,
                    ],
                    'unit_amount' => (int)$equipment->price*100,
                ],
                'quantity' => $equipment->numberTotal,
            ];
        }
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        Stripe::setApiVersion('2024-04-10');

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $lines_items,
            'payment_method_types' => [
                'card',
            ],
            'billing_address_collection'=> 'required',
            'mode' => 'payment',
            'success_url' => "http://localhost:8000/basketPayment/success",
            'cancel_url' => route('cancel'),
        ]);

        return redirect()->away($session->url);
    }
    public function webhook(Request $request)
    {
        error_log('webhook');
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $endpoint_secret = 'whsec_c8aaaf0e33f54bdf01922fb260680523cfb4e0c0264a1d57ea910e5864498fa3';
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }
        http_response_code(200);
    }

    public function cancel()
    {
        return redirect('/profile',302,[],false)->withErrors(['error' => 'Payment canceled']);
    }

    public function success(Request $request)
    {
        return redirect('/profile',302,[],false)->with('success', 'Payment success');


        $client = new Client();
        try {
            $response = $client->get(env('API_URL') . 'account?token='.$request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
            $accountUuid = $account->data[0]->uuid;

            $basket = $this->getBasket($request);
            $basketUuid = $basket->uuid;

            $response = $client->put(env('API_URL') . 'basket?uuid='.$basketUuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    'paid' => 1
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile',302,[],false)->withErrors(['error' => 'An error occured while updating your basket']);
        }
        return redirect('/profile',302,[],false)->with('success', 'Payment success');
    }
}
