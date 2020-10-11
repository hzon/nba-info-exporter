<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LSS\Array2XML;

class Report extends Model
{
    use HasFactory;

    public static function xml($data)
    {
        header('Content-type: text/xml');

        // fix any keys starting with numbers
        $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $xmlData = [];

        foreach ($data->all() as $row) {
            $xmlRow = [];
            foreach ($row as $key => $value) {
                $key = preg_replace_callback('(\d)', function($matches) use ($keyMap) {
                    return $keyMap[$matches[0]] . '_';
                }, $key);
                $xmlRow[$key] = $value;
            }
            $xmlData[] = $xmlRow;
        }

        $xml = Array2XML::createXML('data', [
            'entry' => $xmlData
        ]);

        return $xml->saveXML();
    }

    public static function json($data)
    {
        header('Content-type: application/json');

        return json_encode($data->all());
    }

    public static function csv($data)
    {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv";');
        if (!$data->count()) {
            return;
        }
        $csv = [];

        // extract headings
        // replace underscores with space & ucfirst each word for a decent headings
        $headings = collect($data->get(0))->keys();
        $headings = $headings->map(function($item, $key) {
            return collect(explode('_', $item))
                ->map(function($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $csv[] = $headings->join(',');

        // format data
        foreach ($data as $dataRow) {
            $csv[] = implode(',', array_values($dataRow));
        }

        return implode("\n", $csv);
    }

    public static function html($data)
    {
        $headings = collect();

        if ($data->count()) {
            // extract headings
            // replace underscores with space & ucfirst each word for a decent heading
            $headings = collect($data->get(0))->keys();
            $headings = $headings->map(function($item, $key) {
                return collect(explode('_', $item))
                    ->map(function($item, $key) {
                        return ucfirst($item);
                    })
                    ->join(' ');
            });
        }

        return view('report.template', compact('headings', 'data'));
    }

}
