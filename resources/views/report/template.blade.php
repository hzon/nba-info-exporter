<html>
<head>
    <style type="text/css">
        body {
            font: 16px Roboto, Arial, Helvetica, Sans-serif;
        }
        td, th {
            padding: 4px 8px;
        }
        th {
            background: #eee;
            font-weight: 500;
        }
        tr:nth-child(odd) {
            background: #f4f4f4;
        }
    </style>
</head>
<body>
    @if ($data->isEmpty())
        <span>Sorry, no matching data was found</span>
    @else
        <table>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
            @foreach ($data as $datum)
                <tr>
                    @foreach($datum->toArray() as $key => $stat)
                        <td>{{ $stat }}</td>
                    @endforeach
                </tr>
            @endforeach

        </table>
    @endif

</body>
</html>
