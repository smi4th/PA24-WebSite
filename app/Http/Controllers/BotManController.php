<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        $config = [
            
        ];
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create($config);


        $botman->hears('{message}', function ($botman, $message) {
            $message = request()->input('message');
            $responseMessage = 'Bonjour, ' . $message;
            $botman->reply($responseMessage);
        });

        $botman->listen();
    }
}
