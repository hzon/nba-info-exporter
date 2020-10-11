<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerTotals extends Model
{
    use HasFactory;

    protected $primaryKey = 'player_id';
    protected $keyType = 'string';

    public function roster()
    {
        return $this->belongsTo(Roster::class, 'player_id');
    }
}
