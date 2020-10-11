<?php

namespace App\Http\Controllers;

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
        $data = null;

        switch ($type) {
            case 'playerstats':
                $data = Roster::with('playerTotals')
                    ->when($name, function ($query, $name) {
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
                    })
                    ->get();

                break;
            case 'players':
                $data = Roster::when($name, function ($query, $name) {
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
                    })
                    ->get();

                break;
        }

        // TODO: remove toArray() later
        if (empty($data->toArray())) {
            exit("Error: No data found");
        }

        return $data;
    }
}
