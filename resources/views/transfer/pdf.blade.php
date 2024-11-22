<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>TRANSFERENCIA</title>
    <style>
        table thead tr th,
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        tr > td {
            padding-bottom: 0px;
            padding-top: 0px;
        }
        .tblproducts{position: relative}
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        div.breakNow { page-break-after: always;page-break-before: always;}
        footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            background-color: #fff;
            color: #333;
            text-align: center;
            line-height: 35px;
            font-size: .7em;
        }
        .mt5 {
            margin-top: 5px;
        }
    </style>
</head>
<body class="white-bg">
<table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td style="height: 130px; width: 45%; max-width: 45%;" align="center" valign="middle">
                <span>
                    <img src="{{ asset("images/{$clientInfo->logo}") }}" class="logoGen">
                    <div>
                        <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
                    </div>
                </span>
        </td>

        <td style="height: 100px; width: 45%; padding:0" valign="middle">
            <div class="tabla_borde" align="center" style="height: 100px">
                <table width="100%" border="0" cellpadding="6" cellspacing="0">
                    <tbody>
                    <tr>
                        <td align="center">
                                    <span style="font-size:15px" text-align="center">
                                        R.U.C. {{Auth::user()->headquarter->client->document}}
                                    </span>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:1.35em;font-weight: bold;">
                            TRANSFERENCIA
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:1.35em;font-weight: bold;">
                            <span>{{ $transfer->serie }}{{ date('Y') }} - {{ $transfer->correlative }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<p style="text-align: left; margin: 0;"><strong>Fecha - Hora:</strong> {{ date('d-m-Y H:i', strtotime($transfer->created_at)) }}</p>
<p style="text-align: left; margin: 0;"><strong>Almacén de Origen:</strong> {{ $transfer->warehouseOrigin->description }}</p>
<p style="text-align: left; margin: 0;"><strong>Almacén de Destino:</strong> {{ $transfer->warehouseDestination->description }}</p>
<p style="text-align: left; margin: 0;"><strong>Responsable:</strong> {{ $transfer->responasble }}</p>
<p style="text-align: left; margin: 0;"><strong>Encargado de Almacén:</strong> {{ $transfer->warehouseOrigin->responsable }}</p>
<p style="text-align: left; margin: 0;"><strong>Motivo:</strong> {{ $transfer->motive }}</p>

<div class="tabla_borde mt5">
    <table width="100%" border="0" cellpadding="6" cellspacing="0" class="tblproducts">
        <thead>
            <tr>
                <th>COD</th>
                <th>UM</th>
                <th>DESCRIPCION</th>
                <th>CANT.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfer->detail as $detail)
                <tr>
                    <td align="center">{{ $detail->product->code }}</td>
                    <td align="center">{{ $detail->product->ot->code }}</td>
                    <td>{{ $detail->product->description }}</td>
                    <td align="center">{{ $detail->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td align="right" colspan="3"><b>Total de Productos:</b></td>
                <td align="center">{{ $transfer->detail->sum('quantity') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
<br><br><br><br><br><br><br><br><br><br>
<table width="100%">
    <tr>
        <td align="center">
            ________________________________ <br>
            Responsable de entrega
        </td>
        <td align="center">
            ________________________________ <br>
            Recepción
        </td>
    </tr>
</table>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>
</body></html>
{{-- {{dd()}} --}}
