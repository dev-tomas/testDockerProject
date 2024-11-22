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
            background: #92D050;
            color: #fff;
            text-align: center;
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
    <title>COMPRA {{ $purchase->shopping_serie }}-{{ $purchase->shopping_correlative }}</title>
</head>
<body>
    <div class="cont-logo">
        <img src="{{public_path('images/') . $clientInfo->logo  }}" height="150px" style="text-align:center" border="0">
        <div class="">
            <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
        </div>
    </div>
    <h2 style="text-align: center">COMPRA {{ $purchase->shopping_serie }}-{{ $purchase->shopping_correlative }}</h2>
    <table width="100%">
        <tr>
            <td width="25%">
                <strong>PROVEEDOR:</strong> <br>
                {{ $purchase->provider->document }} {{ $purchase->provider->description }}
            </td>
            <td width="25%">
                <strong>FECHA:</strong> <br>
                {{ date('d-m-Y', strtotime($purchase->date)) }}
            </td>
            <td width="25%">
                <strong>MONEDA:</strong> <br>
                {{ $purchase->coin->description }}
            </td>
        </tr>
        <tr>
            <td width="25%">
                <strong>TIPO DE CAMBIO:</strong> <br>
                {{ $purchase->exchange_rate }}
            </td>
            <td width="25%">
                <strong>DOCUMENTO:</strong> <br>
                {{ $purchase->typeVoucher->description }} {{ $purchase->shopping_serie }}-{{ $purchase->shopping_correlative }}
            </td>
            <td width="25%">
                <strong>FORMA DE PAGO:</strong> <br>
                {{ $purchase->payment_type }}
            </td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th width="40%">Producto/Servicio</th>
                <th width="10%">Cantidad</th>
                <th width="10%">Pre. Uni.</th>
                <th width="10%">Val. Uni.</th>
                <th width="10%">Subtotal</th>
                <th width="10%">IGV</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody class="list_productos">
        @foreach ($purchase->detail as $d)
            <tr>
                <td>
                    {!! $d->product->description !!}
                </td>
                <td>
                    {{ $d->quantity }}
                </td>
                <td>
                    {{ $d->unit_price }}
                </td>
                <td align="right">
                    {{ $d->unit_value }}
                </td>
                <td align="right">
                    {{ $d->subtotal }}
                </td>
                <td align="right">
                    {{ number_format($d->subtotal * 0.18,2,'.','') }}
                </td>
                <td align="right">
                    {{ $d->total }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table width="100%">
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Descuento</strong></td>
            <td width="12%" align="right">{{ $purchase->discount }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Subtotal</strong></td>
            <td width="12%" align="right">{{ $purchase->subtotal }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Exonerada</strong></td>
            <td width="12%" align="right">{{ $purchase->exonerated }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Inafecta</strong></td>
            <td width="12%" align="right">{{ $purchase->unaffected }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Gravada</strong></td>
            <td width="12%" align="right">{{ $purchase->taxed }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>IGV</strong></td>
            <td width="12%" align="right">{{ $purchase->igv }}</td>
        </tr>
        <tr>
            <td width="75%"></td>
            <td width="12%" align="right"><strong>Total</strong></td>
            <td width="12%" align="right">{{ $purchase->total }}</td>
        </tr>
    </table>
</body>
</html>