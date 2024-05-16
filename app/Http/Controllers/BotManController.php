<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Incoming\Answer;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }
        //DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        $config = [];
        $botman = app('botman');

        $botman->hears('{message}', function ($botman, $message) { 
            //renvoyer un json avec 'bonjour, "message"'
            $botman->reply('Bonjour, '.$message);
            return response()->json(['message' => $message]);
        });

        $botman->listen();
    }
}