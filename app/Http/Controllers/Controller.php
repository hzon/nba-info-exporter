<?php

namespace App\Http\Controllers;

use App\Models\PlayerTotals;
use App\Models\Report;
use App\Models\Roster;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Primary method for data export
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|null|string|void
     */
    public function export(Request $request) {
        $type = $request['type'];
        $name = $request['player'];
        $teamCode = $request['team'];
        $pos = $request['position'];
        $nationality = $request['country'];
        $format = $request['format'];
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

        return $this->format($data, $format);
    }

    /**
     * Extract additional player info and store as collection
     *
     * @param $players
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Show data according to preferred format
     *
     * @param $data
     * @param $format
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|null|string|void
     */
    private function format($data, $format)
    {
        $result = null;

        switch ($format) {
            case 'xml':
                $result = Report::xml($data);
                break;
            case 'json':
                $result = Report::json($data);
                break;
            case 'csv':
                $result = Report::csv($data);
                break;
            default:
                $result = Report::html($data);
        }

        return $result;
    }
}
