<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>RESUMEN CAJA CHICA</title>
    <style>
        /* .tblproducts{position: relative} */
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        div.breakNow { page-break-after: always;page-break-before: always;}
        .id {
            width: 65%;
            display: inline-block;
            padding: 8px;
            float: left;
            border: 1px solid #666;
            border-radius:10px;
        }
        .ic{
            width: 150px;
            height: 150px;
            display: inline-block;
            margin-left: 20px;
            float: left;
            border: 1px solid #666;
            border-radius: 10px;
        }
        .it {text-align: left;}
        footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 50px;
            background-color: #fff;
            color: #333;
            text-align: center;
            line-height: 35px;
            font-size: .7em;
            z-index: 9999;
        }

        body {
            position: relative;
            margin: 0;
            padding: 0;
        }

        .legal {
            display: block;
            margin: 0;
            padding: 0;
            color: red;
            font-size: 4em;
            position: absolute;
            width: 100%;
            left: 0%;
            top: 25%;
            transform: rotate(-30deg);
        }

        .content-qr {
            width: 150px;
            max-width: 150px;
            height: 150px;
            max-height: 150px;
            position: relative;
        }
        .content-qr img {
            width: 150px;
            height: 150px;
            display: block;
        }
        .nobreak {page-break-inside: avoid;}
    </style>
</head>
<body class="white-bg">
<table width="100%" height="250px" border="0" aling="center" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td style="height: 130px; width: 45%; max-width: 45%;" valign="middle">
            <img src="{{asset('images/') . $clientInfo->logo  }}" height="55px;" width="auto">
            <div>
                <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
            </div>
        </td>

        <td style="height: 130px; width: 45%, padding:0" valign="middle">
            <div class="tabla_borde" align="center" style="height: 120px">
                <table width="100%" border="0" cellpadding="6" cellspacing="0">
                    <tbody>
                        <tr>
                            <td align="center" valign="center">
                                <span style="font-size:15px" text-align="center">
                                    CUENTAS POR PAGAR
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<br>
<div class="tabla_borde">
    <table width="100%" border="0" cellpadding="6" cellspacing="0" class="tblproducts">
        <tbody>
        <tr>
            <td align="center" class="bold">Client</td>
            <td align="center" class="bold">Emision</td>
            <td align="center" class="bold">DOC. VENTA</td>
            <td align="center" class="bold">M.</td>
            <td align="center" class="bold">Monto</td>
            <td align="center" class="bold">Vencimiento</td>
            <td align="center" class="bold">Dias</td>
            <td align="center" class="bold">Deuda</td>
            <td align="center" class="bold">Estado</td>
        </tr>
        @foreach($credits as $credit)
        @php
            $fechaEmision = \Carbon\Carbon::parse($credit->date);
            $fechaExpiracion = \Carbon\Carbon::parse($credit->expiration);

            $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
        @endphp
            <tr class="border_top">
                <td align="center">{{ $credit->provider->description }}</td>
                <td align="center">{{ date('d-m-Y', strtotime($credit->date)) }}</td>
                <td align="center">{{ $credit->shopping->serial . ' - ' . $credit->shopping->correlative }}</td>
                <td align="center">{{ $credit->shopping->coin->symbol }}</td>
                <td align="center">{{ $credit->total }}</td>
                <td align="center">{{ date('d-m-Y', strtotime($credit->expiration)) }}</td>
                <td align="center">{{ $diasDiferencia }}</td>
                <td align="center">{{ $credit->debt }}</td>
                <td align="center">{{ $credit->status == 1 ? 'Cancelado' : 'Pendiente' }}</td>
            </tr>
            @foreach ($credit->payment as $qd)
                @if ($loop->first)
                    <tr class="border_top">
                        <td align="center" class="bold">Forma de Pago</td>
                        <td align="center" class="bold">Fecha de Expiración</td>
                        <td align="center" class="bold">Banco</td>
                        <td align="center" class="bold">Operacion</td>
                        <td align="center" class="bold">Montos Abonados</td>
                    </tr>
                @endif
                <tr class="border_top">
                    <td align="center">{{ $qd->payment_type }}</td>
                    <td align="center">{{ date('d-m-Y', strtotime($qd->expiration)) }}</td>
                    <td align="center">{{ $qd->bank }}</td>
                    <td align="center">{{ $qd->operation_bank }}</td>
                    <td align="center">{{ $qd->payment }}</td>
                </tr>
                @if ($loop->last)
                    <tr>
                        <td align="center">&nbsp;</td>
                        <td align="center">&nbsp;</td>
                        <td align="center">&nbsp;</td>
                        <td align="center">&nbsp;</td>
                        <td align="center">&nbsp;</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>
</body></html>
