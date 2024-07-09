<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Stripe\Stripe;
use function Laravel\Prompts\error;

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
            return false;

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Erreur lors du chargement du panier']);
        }
        return redirect('/profile',302,[],false)->withErrors(['error' => 'Panier vide']);
    }

    public function checkout(Request $request)
    {

        $all_items = $this->getBasket($request);
        if ($all_items == false){
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Panier vide']);
        }

        $lines_items = [];

        $total = 0;
        //dd($all_items);

        $housing = $all_items->HOUSING;
        $services = $all_items->SERVICES;
        $bedrooms = $all_items->BEDROOMS;
        $equipments = $all_items->EQUIPMENTS;
        //dd($all_items);
        if (empty($housing) && empty($services) && empty($bedrooms) && empty($equipments)){
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Panier vide']);
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
            $total += $house->price;
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
            $total += $bedroom->price;
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
            $total += $service->price;
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
            $total += $equipment->price;
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
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Erreur lors du chargement des taxes']);
        }

        $lines_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'TVA',
                ],
                'unit_amount' => (int)($total * $taxesTVA/100)*100,
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        Stripe::setApiVersion('2024-04-10');

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $lines_items,
            'payment_method_types' => [
                'card',
            ],
            'billing_address_collection'=> 'required',
            'mode' => 'payment',
            'success_url' => "http://localhost:8000/basketPayment/success",//env('APP_URL')."/basketPayment/success",
            'cancel_url' => route('cancel'),
        ]);

        return redirect()->away($session->url);
    }

    public function subscription(Request $request)
    {
        /*
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        \Stripe\Stripe::setApiVersion('2024-04-10');

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        try {
            $client = new Client();
            $response = $client->get(env('API_URL') . 'subscription?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);

            $subscriptions = json_decode($response->getBody()->getContents());
            $subscriptions = $subscriptions->data;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return redirect('/profile', 302, [], false)->withErrors(['error' => 'An error occured while fetching subscriptions']);
        }

        $line_items = [];
        foreach ($subscriptions as $subscription) {
            if (strtolower($subscription->imgPath) == null) {
                $subscription->imgPath = 'https://via.placeholder.com/150';
            }

            try {
                $product = \Stripe\Product::retrieve($subscription->uuid);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $product = null;
            }

            if ($product) {
                // Optionally, delete the old prices if needed
                $prices = \Stripe\Price::all(['product' => $subscription->uuid]);
                foreach ($prices as $price) {
                    \Stripe\Price::update($price->id, ['active' => false]);
                }
            } else {
                $product = \Stripe\Product::create([
                    'id' => $subscription->uuid,
                    'name' => $subscription->description,
                    'images' => [$subscription->imgPath],
                    'metadata' => [
                        'duration' => $subscription->duration,
                        'ads' => $subscription->ads ? 'Oui' : 'Non',
                        'VIP' => $subscription->VIP ? 'Oui' : 'Non',
                    ]
                ]);
            }

            // Create different prices for the product
            $price_tiers = [
                [
                    'unit_amount' => (int)$subscription->price * 100,
                    'currency' => 'eur',
                    'recurring' => ['interval' => 'month'],
                    'metadata' => ['tier' => 'Standard']
                ],
                [
                    'unit_amount' => (int)($subscription->price * 1.5) * 100,
                    'currency' => 'eur',
                    'recurring' => ['interval' => 'month'],
                    'metadata' => ['tier' => 'Premium']
                ],
                [
                    'unit_amount' => (int)($subscription->price * 2) * 100,
                    'currency' => 'eur',
                    'recurring' => ['interval' => 'month'],
                    'metadata' => ['tier' => 'VIP']
                ]
            ];

            foreach ($price_tiers as $tier) {
                $price = \Stripe\Price::create([
                    'product' => $subscription->uuid,
                    'unit_amount' => $tier['unit_amount'],
                    'currency' => $tier['currency'],
                    'recurring' => $tier['recurring'],
                    'metadata' => $tier['metadata']
                ]);

                $line_items[] = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
            }
        }

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'subscription',
            'success_url' => url('/basketPayment/successSubscription'),
            'cancel_url' => route('cancel'),
        ]);

        return redirect()->away($session->url);
        */
        return view("default",[
            'file_path' => 'subscription',
            'stack_css' => 'subscription',
            'connected' => true,
            'profile' => false,
            'light' => false
        ]);
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

            case 'checkout.session.completed':
                $paymentIntent = $event->data->object;
                return $this->successSubscription($request);

            default:
                error_log($event->type);
                http_response_code(400);
                exit();
        }
        http_response_code(200);
    }

    public function cancel()
    {
        error_log('cancel');
        return redirect('/profile',302,[],false)->withErrors(['error' => 'Paiement annuler']);
    }

    public function successSubscription(Request $request)
    {
        return redirect('/profile',302,[],false)->with('success', 'Subscription success');
    }

    public function successPayment(Request $request)
    {
        //call an other controller
        //return app()->call('App\Http\Controllers\StripePaymentController@success', ['request' => $request]);
        $basket = $this->getBasket($request);
        try {
            $client = new Client();
            $response = $client->get(env('API_URL') . 'account?token='.$request->session()->get('token'), [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);
            $account = json_decode($response->getBody()->getContents());
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Erreur pendant le paiement']);
        }

        $pdf = (new PdfGeneratorController())->generateReceipt($basket,$account,$request);

        try{
            $client = new Client();
            $response = $client->put(env('API_URL') . 'basket?uuid='.$basket->uuid, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    'paid' => '1'
                ]
            ]);

        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/profile',302,[],false)->withErrors(['error' => 'Erreur pendant la mise à jour du panier']);
        }

        return redirect('/profile',302,[],false)->with('success', 'Paiement effectué');

    }
}
