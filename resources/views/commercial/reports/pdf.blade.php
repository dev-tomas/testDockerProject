@php
    setlocale(LC_TIME, 'es_ES');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        table tr {
            width: 100%;
        }
        table th, table td {
            box-sizing: border-box;
            padding: 5px;
        }
        table thead th {
            background: #000;
            color: #fff;
            text-align: center;
        }
        table tbody tr:nth-child(odd) {
            background: #eee;
        }
        .center {
            text-align: center;
        }
        .cont-logo {
            width: 50%;
            margin-bottom: 2em;
            color: #666;
        }
        .cont-info {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="cont-logo">
        <img src="{{asset('images/') . $clientInfo->logo  }}" height="100px" style="text-align:center" border="0">
        <div class="">
            <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
        </div>
    </div>
    <table>
        <thead style="color: #fff; background: #000;">
            <tr>
                <th scope="col">FECHA</th>
                <th scope="col">LOCAL</th>
                <th scope="col">VENDEDOR</th>
                <th scope="col">CLIENTE</th>
                <th scope="col">PRODUCTO</th>
                <th scope="col">VENTA TOTAL</th>
                <th scope="col">TOTAL PENDIENTE</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $totalPending = 0;
            @endphp
            @foreach($saless as $item)
                @php
                    $total = (float) $item['total'] + (float) $total;
                    $totalPending = (float) $item['credit'] + (float) $totalPending;
                @endphp

                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['headquarter'] }}</td>
                    <td>{{ $item['user'] }}</td>
                    <td>{{ $item['customer'] }}</td>
                    <td>{{ $item['product'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['credit'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5"><strong>TOTAL</strong></td>
                <td>{{ number_format($total, 2, '.', '') }}</td>
                <td>{{ number_format($totalPending, 2, '.', '') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>