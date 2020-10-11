<?php

namespace App\Http\Controllers;

use App\Models\PlayerTotals;
use App\Models\Roster;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function export(Request $request) {
        $type = $request->type;
        $name = $request->player;
        $teamCode = $request->team;
        $pos = $request->position;
        $nationality = $request->country;
        $data = collect();

        $query = Roster::when($name, function ($query, $name) {
                return $query->where('name', $name);
            })
            ->when($teamCode, function ($query, $teamCode) {
                return $query->where('team_code', $teamCode);
            })
            ->when($pos, function ($query, $pos) {
                return $query->where('pos', $pos);
            })
            ->when($nationality, function ($query, $nationality) {
                return $query->where('nationality', $nationality);
            });

        switch ($type) {
            case 'playerstats':
                $players = $query->get();

                $data = $this->getPlayerStatData($players);

                break;
            case 'players';
                $data = $query->select('team_code', 'number', 'name', 'pos', 'height', 'weight', 'dob',
                        'nationality', 'years_exp', 'college')
                    ->get();

                break;
        }

        if ($data->isEmpty()) {
            exit("Error: No data found");
        }

        return $data;
    }

    private function getPlayerStatData($players)
    {
        $data = collect();

        $playerTotals = new PlayerTotals;

        foreach ($players as $player) {
            $totals = $playerTotals->calculateTotals($player->playerTotals);

            $datum = collect($player)
                ->union($player->playerTotals)
                ->union($totals)
                ->forget(['id', 'player_id', 'player_totals']);

            $data->push($datum);
        }

        return $data;
    }
}
