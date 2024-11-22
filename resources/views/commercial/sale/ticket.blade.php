<html width="100%">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_sale.css')}}">
    <style>
        * {font-size: 10px;}
        @page { size: 7.4cm 35cm;}
        body {
            position: relative;
            text-align: left;
            margin: 0 6px;
        }

        .legal {
            display: block;
            margin: 0;
            padding: 0;
            color: red;
            font-size: 2em;
            position: absolute;
            width: 100%;
            left: 0%;
            top: 25%;
            transform: rotate(-30deg);
        }
        table tbody tr td {
            font-size: 11px;
        }
        .pnp {
            margin-bottom: 5px;
        }
        .pnp p {
            margin-bottom: 5px;
        }
        .link-scomp {
            color: #000;
        }
        .table-invoice head tr th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
    </style>
</head>
<body class="white-bg" width="100%">
@if ($clientInfo->production == 0)
    <p class="legal">SIN VALOR LEGAL</p>
@endif
<div style="text-align: center" class="pnp">
    <img src="{{public_path('images/' . $clientInfo->logo)  }}" style="width: 150px;height:auto;text-align:center;display:block;">
    <br>
    <p><strong>{{ $clientInfo->trade_name }}</strong></p>
    <p><strong>{{ Auth::user()->headquarter->address }}</strong></p>
    <br>
    <p><strong>R.U.C {{Auth::user()->headquarter->client->document}}</strong></p>
    <p><strong>{{ $sale->type_voucher->description }} ELECTRÓNICA</strong></p>
    <p><strong>{{ $sale->serialnumber }}-{{ $sale->correlative }}</strong></p>
    <br>
</div>
<div class="pnp">
    <table width="100%">
        <tr>
            <td style="padding: 5px 0 !important;">Fecha Emisión:</td>
            <td style="padding: 5px 0 !important;">{{ date('d/m/Y', strtotime($sale->issue)) }} {{ date('H:i', strtotime($sale->created_at)) }}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Vencimiento:</td>
            <td style="padding: 5px 0 !important;">{{ date('d/m/Y', strtotime($sale->expiration)) }} <br></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Tipo de Moneda:</td>
            <td style="padding: 5px 0 !important;"><strong>{{ $sale->coin->description }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">TC:</td>
            <td style="padding: 5px 0 !important;">{{ $sale->change_type ? number_format($sale->change_type, 3) : '1.000' }}</td>

            
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Medio de Pago:</td>
            <td style="padding: 5px 0 !important;">
                <strong>@if ($sale->condition_payment == 'CREDITO')
                        {{ $sale->condition_payment }} A {{ $sale->credit_time }} DIAS
                    @else
                        {{ $sale->condition_payment == null ? '-' : $sale->condition_payment }}
                    @endif {{ $sale->other_condition != null ? $sale->other_condition : '-' }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Señores:</td>
            <td style="padding: 5px 0 !important;">{{ $sale->customer->description }} <br></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Nro. de Documento:</td>
            <td style="padding: 5px 0 !important;">{{ $sale->customer->document }} <br></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Dirección:</td>
            <td style="padding: 5px 0 !important;">{{ $sale->customer->address }} <br></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">IGV:</td>
            <td style="padding: 5px 0 !important;">{{ $igv->value }}% <br></td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Vendedor:</td>
            <td style="padding: 5px 0 !important;">{{ \Str::upper($sale->user->name) }}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0 !important;">Almacén:</td>
            <td style="padding: 5px 0 !important;"><strong>{{ \Str::upper($sale->headquarter->description) }}</strong></td>
        </tr>
    </table>
</div>
<br>
<table width="100%" class="table-invoice">
    <thead>
        <tr>
            <th  style="padding: 10px 0; border-top: 2px solid #000 !important;border-bottom: 2px solid #000 !important;">DESCRIPCIÓN</th>
            <th  style="padding: 10px 0; border-top: 2px solid #000 !important;border-bottom: 2px solid #000 !important;">CANT.</th>
            <th  style="padding: 10px 0; border-top: 2px solid #000 !important;border-bottom: 2px solid #000 !important;">P. UNIT</th>
            <th  style="padding: 10px 0; border-top: 2px solid #000 !important;border-bottom: 2px solid #000 !important;">IMPORTE</th>
        </tr>
    </thead>
    <tbody>
    @foreach($sale->detail as $qd)
        <tr>
            <td style="padding: 5px 0;" colspan="4"><strong>{{ $qd->product->internalcode }} {{ $qd->product->description }}</strong></td>
        </tr>
        <tr>
            <td>{{ $qd->product->measure_id != null ? $qd->product->measure->description : '-' }}</td>
            <td><strong>{{ $qd->quantity }}</strong></td>
            <td align="left">{{ number_format($qd->price, 2, '.', '') }}</td>
            <td align="right">{{ number_format($qd->total, 2, '.', '') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
<p style="text-align: center;">TOTAL DESPACHADO: <strong>{{ $sale->detail->sum('quantity') }} UNI.</strong></p>
<hr style="border-top: 1px solid #000; margin: .5em 0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        @if ($sale->discount > 0.00)
            <tr>
                <td>DESCUENTO (-):</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->discount }}</td>
            </tr>
        @endif
        @if ($sale->exonerated > 0.00)
            <tr>
                <td>EXONERADA:</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->exonerated }}</td>
            </tr>
        @endif
        @if ($sale->unaffected > 0.00)
            <tr>
                <td align="left">INAFECTO</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->unaffected }}</td>
            </tr>
        @endif
        <tr>
            <td>GRAVADA</td>
            <td width="10" align="right">{{ $sale->coin->symbol }}</td>
            <td width="60" align="right">{{ $sale->taxed }}</td>
        </tr>

        <tr>
            <td>IGV</td>
            <td width="10" align="right">{{ $sale->coin->symbol }}</td>
            <td width="60" align="right">{{ $sale->igv}}</td>
        </tr>
        @if ($sale->free > 0.00)
            <tr>
                <td>GRATUITA</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->free }}</td>
            </tr>
        @endif
        <tr>
            <td>RECARGO AL CONSUMO</td>
            <td width="10" align="right">{{ $sale->coin->symbol }}</td>
            <td width="60" align="right">{{ $sale->recharge}}</td>
        </tr>
        <tr>
            <td>IMPORTE TOTAL</td>
            <td width="10" align="right">{{ $sale->coin->symbol }}</td>
            <td width="60" align="right">{{ $sale->total }}</td>
        </tr>
        @if ($sale->total_paying != null || $sale->total_paying > 0.00)    
            <tr >
                <td align="right">TOTAL A COBRAR</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->total_paying }}</td>
            </tr>
            <tr >
                <td align="right">VUELTO</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->balance }}</td>
            </tr>
        @endif
    </tbody>
</table>
<hr style="border-top: 1px solid #000; margin: .5em 0;">
<div class="pnp">
    <p><strong>SON: </strong>{{ \Str::upper($leyenda) }} {{ $sale->coin->description }}</p>
</div>
<hr style="border-top: 1px solid #000; margin: .5em 0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>{{ $sale->condition_payment}}</td>
            <td width="10" align="right">{{ $sale->coin->symbol }}</td>
            <td width="60" align="right">{{ $sale->condition_payment_amount }}</td>
        </tr>
        @if ($sale->other_condition != null)
            <tr>
                <td>{{ $sale->other_condition}}</td>
                <td width="10" align="right">{{ $sale->coin->symbol }}</td>
                <td width="60" align="right">{{ $sale->other_condition_mount }}</td>
            </tr>
        @endif
    </tbody>
</table>
<p><strong>Observaciones:</strong><br>{{ $sale->observation }}</p>
@if ($sale->condition_payment == 'CREDITO' && $sale->payments->count() > 0)
    <hr style="border-top: 1px solid #000; margin: .5em 0;">
    <div style="font-size: 12px"><strong>CUOTAS: </strong></div>
    <br>
    <table style="width: 100%">
        <tbody>
            <tr style="font-size: 10px;">
                <th>#</th>
                <th>CUOTA</th>
                <th>TOTAL</th>
            </tr>
            @foreach($sale->payments as $payment)
                <tr>                            
                    <td align="center"><strong>{{ $loop->iteration }}</td>
                    <td align="center">{{ date('d-m-Y', strtotime($payment->date)) }}</td>
                    <td align="right">{{ $sale->coin->symbol }} {{ number_format($payment->mount,2,'.',' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
@endif
<hr>
<div class="pnp" style="text-align: center;">
    <p style="margin-bottom: 0;">Representacion impresa del comprobante electrónico, para ver el documento o descargarlo visita:</p>
    <a href="{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}" target="_blank" class="link-scomp"><strong>{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}</strong></a>
    <p>O puedes verificarla en el portal de la Sunat utilizando su clave sol.</p>
</div>
<br>
<p style="text-align: center">{{ $clientInfo->pdf_footer }}</p>
<br>
@if($hash !== null)
    <div class="pnp" style="text-align: center">
        <p><strong>Resumen</strong></p>
        <p>{{ $hash }}</p>
    </div>
@endif

@if($qrCode !== null)
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
        <tbody>
        <tr>
            <td width="100px" height="100px" align="center">
                <img src="{{ $qr }}" alt="Qr Image" width="100px">
            </td>
        </tr>
        </tbody>
    </table>
@endif
</body>
</html>
