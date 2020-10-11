<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use HasFactory;

    protected $table = 'roster';
    protected $keyType = 'string';

    /**
     * Get a roster player's totals and stat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function playerTotals()
    {
        return $this->hasOne(PlayerTotals::class, 'player_id');
    }
}
