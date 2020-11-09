<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameLogs extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'game_id',
        'user_id',
        'playerOneName',
        'playerTwoName',
        'recorder',
        'playerOneLifeValue',
        'playerTwoLifeValue',
        'result'
    ];
    
}
