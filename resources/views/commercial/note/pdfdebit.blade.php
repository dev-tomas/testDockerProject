<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>NOTA DE DÉBITO - {{ $debit_note->serial_number }}-{{ $debit_note->correlative }}</title>
    <style>
        .tblproducts{position: relative}
        table thead tr th,
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        tr > td {
            padding-bottom: 0px;
            padding-top: 0px;
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
            width: 35%;
            display: inline-block;
            padding: 8px;
            float: left;
            /* border: 1px solid #666;
            border-radius: 10px; */
        }
        body {
            position: relative;
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
            top: 50%;
            transform: rotate(-30deg);
        }
        .mt5 {
            margin-top: 5px;
        }
    </style>
</head>
<body class="white-bg">
@if ($clientInfo->production == 0)
    <p class="legal">SIN VALOR LEGAL</p>
@endif
<table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td style="height: 130px; width: 45%; max-width: 45%;" align="center" valign="middle">
            <span>
                <img src="{{public_path('images/') . $clientInfo->logo  }}" height="150px" style="text-align:center" border="0">
                <div>
                    <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
                </div>
            </span>
        </td>

        <td style="height: 100px; width: 45%, padding:0" valign="middle">
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
                            NOTA DE DÉBITO ELECTRÓNICA
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:1.35em;font-weight: bold;">
                            <span>{{ $debit_note->serial_number }}-{{ $debit_note->correlative }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<div class="tabla_borde mt5">
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:67%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                {{-- <div class="tabla_borde"> --}}
                <div>
                    <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td><strong>DATOS DEL CLIENTE</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>RUC:</strong>
                            </td>
                            <td>{{$customerInfo->document}}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>DENOMINACIÓN:</strong>
                            </td>
                            <td>{{ $customerInfo->description }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>DIRECCIÓN: </strong>
                            </td>
                            <td>
                                {{ $customerInfo->address }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td style="width:33%; padding-right:0; padding-top: 10px; padding-bottom: 10px;">
                {{-- <div class="tabla_borde" style="height: auto;"> --}}
                <div style="height: auto;">
                    <table>
                        <tbody>
                        <tr>
                            <td><span style="color: #fff;">FECHA</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>FECHA DE EMISIÓN: </strong> {{ date('d-m-Y', strtotime($debit_note->date_issue)) }}
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>FECHA DE EXP.: </strong> {{ date('d-m-Y', strtotime($debit_note->due_date))}}
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>MONEDA:</strong> {{ $debit_note->sale->coin->description }}
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="tabla_borde mt5">
    <table width="100%" border="0" cellpadding="6" cellspacing="0" class="tblproducts">
        <tbody>
        <tr>
            <td align="center" class="bold">CANT.</td>
            <td align="center" class="bold">UND.</td>
            <td align="center" class="bold">DESCRIPCIÓN</td>
            <td align="center" class="bold">IMPORTE</td>
        </tr>
        @php
            $i = 0;
        @endphp
        @foreach($debit_note->detail as $qd)
            {{-- @php
                $i++;
            @endphp --}}
            <tr class="border_top">
                <td align="center">{{ $qd->quantity }}</td>
                <td align="center">{{ $qd->product->measure_id != null ? $qd->product->measure->description : '-' }}</td>
                <td align="center" width="300px"><span>{!! $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description !!}</span></td>
                <td align="center" width="60px">{{$qd->product->coin->symbol . number_format((float) $qd->price * (float) $qd->quantity,2,'.',',') }}</td>
            </tr>
            {{-- {{ $i}}
            @php if( $i % 10 == 0 ){ echo '<div style="page-break-after: always;"></div>'; } @endphp --}}
        @endforeach
        </tbody>
    </table>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="mt5">
    <tbody><tr>
        <td width="50%" valign="top">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tbody>
                <tr>
                    <td colspan="4">
                        <span style="font-family:Tahoma, Geneva, sans-serif; font-size:12px" text-align="center"><strong>Observaciones</strong></span>
                        {{ $debit_note->observation }}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td width="50%" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                <tbody>
                @if ($debit_note->exonerated > 0.00)
                    <tr class="border_bottom">
                        <td align="right" style="padding: 0;"><strong>EXONERADA: </strong></td>
                        <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">{{ number_format($debit_note->exonerated,2) }}</td>
                    </tr>
                @endif
                @if ($debit_note->unaffected > 0.00)
                    <tr class="border_bottom">
                        <td align="right" style="padding: 0;"><strong>INAFECTA: </strong></td>
                        <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">{{ number_format($debit_note->unaffected,2) }}</td>
                    </tr>
                @endif
                <tr class="border_bottom">
                    <td align="right" style="padding: 0;"><strong>GRAVADA: </strong></td>
                    <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                    <td width="120" style="padding: 0;" align="right">
                        <span>
                            {{ number_format($debit_note->taxed,2) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td align="right" style="padding: 0;"><strong>
                            I.G.V. {{$igv->value}}%: </strong>
                    </td>
                    <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                    <td width="120" style="padding: 0;" align="right">
                        <span>
                            {{ number_format($debit_note->igv, 2) }}
                        </span>
                    </td>
                </tr>
                
                @if ($debit_note->icbper > 0.00)
                    <tr>
                        <td align="right" style="padding: 0;"><strong>
                                ICBPER : </strong>
                        </td>
                        <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">{{ number_format($debit_note->icbper,2)}}</td>
                    </tr>
                @endif
                @if ($debit_note->recharge > 0.00)
                    <tr>
                        <td align="right" style="padding: 0;"><strong>
                                RECARGO AL CONSUMO: </strong>
                        </td>
                        <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">{{ number_format($debit_note->recharge,2)}}</td>
                    </tr>
                @endif
                <tr>
                    <td align="right" style="padding: 0;">
                        <strong>TOTAL: </strong>
                    </td>
                    <td width="10" style="padding: 0;" align="right"><strong>{{ $debit_note->sale->coin->symbol }} </strong></td>
                    <td width="120" style="padding: 0;" align="right">
                        <span>
                            {{ number_format($debit_note->total ,2)}}
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<div class="tabla_borde nobreak mt5" style="padding: 10px; text-align:center; margin-bottom: 5px">
    <strong>Importe en letras: </strong>{{$leyenda}} {{ $debit_note->sale->coin->description }}
</div>
<div class="tabla_borde nobreak" style="text-align: center;">
    <p><strong>MOTIVO DE EMISIÓN:</strong> {{ $debit_note->typeCreditNote->code }} - {{ $debit_note->typeCreditNote->description }}</p>
    <p><strong>DOCUMENTO RELACIONADO:</strong> {{ $debit_note->sale->type_voucher->description }} - {{ $debit_note->sale->serialnumber }}-{{ $debit_note->sale->correlative }}</p>
</div>
<div class="nobreak cont-idc mt5">
    <div class="id">
        <p style="margin-bottom: 0;">Representacion impresa del comprobante electrónico, para ver el documento o descargarlo visita:</p>
        <a href="{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}" target="_blank"><strong>{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}</strong></a>
        <p>O puedes verificarla en el portal de la Sunat utilizando su clave sol.</p>
        {{-- <p><strong>Resumen: {{$hash}}</strong></p> --}}
    </div>
    <div class="ic" style="">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            {{-- <tr>
                <td width="150px" height="150px" align="center">
                    <img style="width: 100%; display: block;" src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(200)->generate($qrCode)) }}" alt="Qr Image">
                </td>
            </tr> --}}
        </table>
    </div>
</div>
</body></html>
{{-- {{dd()}} --}}