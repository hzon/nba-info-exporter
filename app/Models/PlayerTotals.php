<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerTotals extends Model
{
    use HasFactory;

    protected $primaryKey = 'player_id';
    protected $keyType = 'string';

    public function calculateTotals($data)
    {
        $point3 = $data['3pt'];
        $point2 = $data['2pt'];
        $freeThrow = $data['free_throws'];
        $attemptedFG = $data['field_goals_attempted'];
        $fieldGoals = $data['field_goals'];
        $attemptedPoint3 = $data['3pt_attempted'];
        $attemptedPoint2 = $data['2pt_attempted'];
        $freeThrows = $data['free_throws'];
        $attemptedFT = $data['free_throws_attempted'];
        $offensiveReb = $data['offensive_rebounds'];
        $defensiveReb = $data['defensive_rebounds'];

        $totals = collect();
        $totals['total_points'] = $this->totalPoints($point3, $point2, $freeThrow);
        $totals['field_goals_pct'] = $this->pctFG($attemptedFG, $fieldGoals);
        $totals['3pt_pct'] = $this->pctPoint3($attemptedPoint3, $point3);
        $totals['2pt_pct'] = $this->pctPoint2($attemptedPoint2, $point2);
        $totals['free_throws_pct'] = $this->pctFreeThrows($attemptedFT, $freeThrows);
        $totals['total_rebounds'] = $this->totalRebounds($offensiveReb, $defensiveReb);

        return $totals;
    }

    private function totalPoints($point3, $point2, $freeThrow)
    {
        return ($point3 * 3) + ($point2 * 2) + $freeThrow;
    }

    private function pctFG($attemptedFG, $fieldGoals)
    {
        return $attemptedFG ? (round($fieldGoals / $attemptedFG, 2) * 100) . '%' : 0;
    }

    private function pctPoint3($attemptedPoint3, $point3)
    {
        return $attemptedPoint3 ? (round($point3 / $attemptedPoint3, 2) * 100) . '%' : 0;
    }

    private function pctPoint2($attemptedPoint2, $point2)
    {
        return $attemptedPoint2 ? (round($point2 / $attemptedPoint2, 2) * 100) . '%' : 0;
    }

    private function pctFreeThrows($attemptedFT, $freeThrows)
    {
        return $attemptedFT ? (round($freeThrows / $attemptedFT, 2) * 100) . '%' : 0;
    }

    private function totalRebounds($offensiveReb, $defensiveReb)
    {
        return $offensiveReb + $defensiveReb;
    }
}
