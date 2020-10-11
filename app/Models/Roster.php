<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use HasFactory;

    protected $table = 'roster';
    protected $keyType = 'string';

    public function playerTotals()
    {
        return $this->hasOne(PlayerTotals::class, 'player_id');
    }
}
