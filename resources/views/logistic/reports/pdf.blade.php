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
    <div class="cont-info">
        <p><strong>Proveedor:</strong> {{ $provider != '' ? $provider->description : 'Todos los Proveedores' }}</p>
        <p><strong>Producto:</strong> {{ $product != '' ? $product->description : 'Todos los Productos' }}</p>
        <p><strong>Fecha:</strong> {{ $desde }} {{ $hasta }}</p>
    </div>
    <table>
        <thead style="color: #fff; background: #000;">
            <tr>
                <th scope="col">FECHA</th>
                <th scope="col">LOCAL</th>
                <th scope="col">DOCUMENTO</th>
                <th scope="col">PROVEEDOR</th>
                <th scope="col">PRODUCTO</th>
                <th scope="col">TOTAL</th>
                <th scope="col">TOTAL PENDIENTE</th>
              </tr>
        </thead>
        <tbody>
            @foreach($shoppings as $s)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($s->date)) }}</td>
                    <td>{{ $s->headquarter->description }}</td>
                    <td>{{ $s->typeVoucher->description }} - {{ $s->shopping_serie }}-{{ $s->shopping_correlative }}</td>
                    <td>{{ $s->provider->description }}</td>
                    <td>{{ $s->detail[0]->product->description }}</td>
                    <td>{{ $s->total }}</td>
                    <td>{{ $s->credit ? $s->credit->debt : 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>