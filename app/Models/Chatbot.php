<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chatbot extends Model
{
    protected $connection = 'chatbot';
    protected $table = 'chatbot';

    protected $fillable = ['keyword', 'text'];
}
