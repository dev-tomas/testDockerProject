<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>VENTA - {{ $sale->serialnumber }}-{{ $sale->correlative }}</title>
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
    </style>
</head>
    <body class="white-bg">
    @if ($clientInfo->production == 0)
        <p class="legal">SIN VALOR LEGAL</p>
    @endif
    <table width="100%" height="250px" border="0" aling="center" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="height: 130px; width: 45%; max-width: 45%;" align="center" valign="middle">
                    <span>
                        <img src="{{public_path('images/') . $clientInfo->logo  }}" height="150px" style="text-align:center" border="0">
                        <div>
                            <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
                        </div>
                        <p>{{ $clientInfo->pdf_header }}</p>
                    </span>
                </td>

                <td style="height: 130px; width: 45%; padding:0" valign="middle">
                    <div class="tabla_borde" align="center" style="height: 120px">
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
                                        @if ($sale->typevoucher_id == 1)
                                            FACTURA ELECTRÓNICA
                                        @elseif($sale->typevoucher_id == 2)
                                            BOLETA DE VENTA <span style="display: block">ELECTRÓNICA</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:1.35em;font-weight: bold;">
                                        <span>{{ $sale->serialnumber }}-{{ $sale->correlative }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="tabla_borde">
        <table style="width:100%">
            <tbody>
                <tr>
                    <td style="width:67%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                        <div>
                            <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td><strong>DATOS DEL CLIENTE</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>RUC:</strong></td>
                                        <td>{{ $sale->customer->document }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>DENOMINACIÓN:</strong></td>
                                        <td>{{ $sale->customer->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>DIRECCIÓN: </strong>
                                        </td>
                                        <td>
                                            {{ $sale->customer->address }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td style="width:33%; padding-right:0; padding-top: 10px; padding-bottom: 10px;">
                        <div style="height: auto;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>FECHA DE EMISIÓN: </strong> {{ date('d-m-Y', strtotime($sale->issue)) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>FECHA DE EXP.: </strong> {{ $sale->payments->count() > 0 ? date('d-m-Y', strtotime($sale->payments[$sale->payments->count() - 1]->date)) : date('d-m-Y', strtotime($sale->expiration)) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>CONDICIÓN:</strong> {{ $sale->condition_payment == null ? '-' : $sale->condition_payment }}
                                            <br>
                                            {{ $sale->other_condition != null ? $sale->other_condition : '-' }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>O/C:</strong> {{$sale->order == null ? '-' : $sale->order }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><strong>MONEDA:</strong> {{ $sale->coin->description }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><strong>T/C:</strong> {{ $sale->change_type }}</td>
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
    <div class="tabla_borde" style="margin-top: 5px;">
        <table width="100%" border="0" cellpadding="4" cellspacing="0" class="tblproducts">
            <thead>
                <tr>
                    <th align="center" width="50px">CANT.</th>
                    <th align="center" width="50px">UND.</th>
                    <th align="center" class="bold">DESCRIPCIÓN</th>
                    <th align="center" class="bold">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->detail as $qd)
                    <tr style="width: 100%;">
                        <td align="center">{{ $qd->quantity }}</td>
                        <td align="center">{{ $qd->product->measure_id != null ? $qd->product->measure->description : '-' }}</td>
                        <td align="center" width="300px"><span>{{ $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description }}</span><br>{{ $qd->detail }}</td>
                        <td align="center" width="60px">{{ number_format((float) $qd->price * (float) $qd->quantity,2,'.',',') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="50%" valign="top">
                    <table width="100%" border="0" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr>
                                <td colspan="4">
                                    <span style="font-family:Tahoma, Geneva, sans-serif; font-size:12px" text-align="center"><strong>Observaciones</strong></span>
                                    {{ $sale->observation }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td width="50%" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                        <tbody>
                            @if ($sale->discount > 0.00)
                                <tr class="border_bottom">
                                    <td align="right" style="padding: 0;"><strong>DESCUENTO (-): </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->discount }}</td>
                                </tr>
                            @endif
                            @if ($sale->exonerated > 0.00)
                                <tr class="border_bottom">
                                    <td align="right" style="padding: 0;"><strong>EXONERADA: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->exonerated }}</td>
                                </tr>
                            @endif
                            @if ($sale->unaffected > 0.00)
                                <tr class="border_bottom">
                                    <td align="right" style="padding: 0;"><strong>INAFECTA: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->unaffected }}</td>
                                </tr>
                            @endif
                            <tr class="border_bottom">
                                <td align="right" style="padding: 0;"><strong>GRAVADA: </strong></td>
                                <td width="10" style="padding: 0;"align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                <td width="120" style="padding: 0;" align="right">{{ $sale->taxed }}</td>
                            </tr>
                            <tr>
                                <td align="right" style="padding: 0;"><strong>I.G.V. {{$igv->value}}%: </strong></td>
                                <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                <td width="120" style="padding: 0;" align="right">{{ $sale->igv}}</td>
                            </tr>
                            @if ($sale->free > 0.00)
                                <tr class="border_bottom">
                                    <td align="right" style="padding: 0;"><strong>GRATUITA: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->free }}</td>
                                </tr>
                            @endif
                            @if ($sale->icbper > 0.00)
                                <tr>
                                    <td align="right" style="padding: 0;"><strong>
                                            ICBPER : </strong>
                                    </td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->icbper}}</td>
                                </tr>
                            @endif
                            @if ($sale->recharge > 0.00)
                                <tr>
                                    <td align="right" style="padding: 0;"><strong>
                                        RECARGO AL CONSUMO: </strong>
                                    </td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="120" style="padding: 0;" align="right">{{ $sale->recharge}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td align="right" style="padding: 0;"><strong>TOTAL: </strong></td>
                                <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                <td width="120" style="padding: 0;" align="right">{{ $sale->total }}</td>
                            </tr>
                            @if ($sale->total_paying != null || $sale->total_paying > 0.00)
                                <tr >
                                    <td align="right" style="padding: 0;"><strong>IMPORTE PAGADO: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="60" style="padding: 0;" align="right">{{ $sale->total_paying }}</td>
                                </tr>
                                <tr >
                                    <td align="right" style="padding: 0;"><strong>VUELTO: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="60" style="padding: 0;" align="right">{{ $sale->balance == null ? "0.00" : number_format($sale->balance, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="border_bottom">
                                <td align="right" style="padding: 0;"><strong>{{ $sale->condition_payment}}: </strong></td>
                                <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                <td width="60" style="padding: 0;" align="right">{{ number_format($sale->condition_payment_amount, 2) }}</td>
                            </tr>
                            @if ($sale->other_condition != null)
                                <tr class="border_bottom">
                                    <td align="right" style="padding: 0;"><strong>{{ $sale->other_condition}}: </strong></td>
                                    <td width="10" style="padding: 0;" align="right"><strong>{{ $sale->coin->symbol }} </strong></td>
                                    <td width="60" style="padding: 0;" align="right">{{ $sale->other_condition_mount }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="tabla_borde nobreak" style="padding: 10px; text-align:center; margin-bottom: 5px">
        <strong>Importe en letras: </strong>{{$leyenda}} {{ $sale->coin->description }}
    </div>
    @if ($sale->condition_payment == 'CREDITO' && $sale->payments->count() > 0)
        <div class="tabla_borde nobreak" style="padding: 5px; text-align:center; margin-bottom: 5px; font-size: 11px">
            <div style="font-size: 11px"><strong>PAGOS CUOTAS AL CREDITO: </strong></div>
            <table style="width: 100%">
                <tr>
                    @if ($sale->payments->count() > 0)
                        <td style="width: 33.33%;">
                            <table style="width: 100%">
                                <tr>
                                    <td align="left"><strong>CUOTA</strong></td>
                                    <td align="left"><strong>FECHA</strong></td>
                                    <td align="left"><strong>TOTAL</strong></td>
                                </tr>
                            </table>
                        </td>
                    @endif
                    @if ($sale->payments->count() > 1)
                        <td style="width: 33.33%;">
                            <table style="width: 100%">
                                <tr>
                                    <td align="left"><strong>CUOTA</strong></td>
                                    <td align="left"><strong>FECHA</strong></td>
                                    <td align="left"><strong>TOTAL</strong></td>
                                </tr>
                            </table>
                        </td>
                    @endif
                    @if ($sale->payments->count() > 2)
                        <td style="width: 33.33%;">
                            <table style="width: 100%">
                                <tr>
                                    <td align="left"><strong>CUOTA</strong></td>
                                    <td align="left"><strong>FECHA</strong></td>
                                    <td align="left"><strong>TOTAL</strong></td>
                                </tr>
                            </table>
                        </td>
                    @endif
                </tr>
                <tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach($sale->payments as $payment)
                        @php
                            $i++;
                        @endphp
                        <td style="width: 33.33%;">
                            <table style="width: 100%">
                                <tr>
                                    <td align="center"><strong>{{ $loop->iteration }}</td>
                                    <td align="right">{{ date('d-m-Y', strtotime($payment->date)) }}</td>
                                    <td align="center">{{ $sale->coin->symbol }} {{ number_format($payment->mount,2,'.',' ') }}</td>
                                </tr>
                            </table>
                        </td>
                        @if($i == 3)
                            </tr><tr>
                            @php
                                $i = 0;
                            @endphp
                        @endif
                    @endforeach
                </tr>
            </table>
        </div>
    @endif
    @if ($sale->condition_payment == 'CREDITO')
        <div class="tabla_borde nobreak">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="nobreak">
                <tbody>
                    <tr>
                        <td colspan="4" class="bold">CUENTAS BANCARIAS.</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="bold">TITULAR DE LA CUENTA:</span> {{ $clientInfo->business_name }}</td>
                        <td colspan="2"><span class="bold">RUC:</span> {{ $clientInfo->document }}</td>
                    </tr>
                    @foreach ($bankInfo as $bi)
                        <tr>
                            <td><span class="bold">BANCO:</span><br> {{ $bi->bank_name }}</td>

                            <td><span class="bold">MONEDA:</span><br> {{ $bi->coins->description }}</td>

                            <td><span class="bold">NUMERO DE CUENTA</span><br> {{ $bi->number }}</td>

                            <td><span class="bold">CCI:</span><br> {{ $bi->cci }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div class="nobreak cont-idc" style="margin-top: 5px;">
        <div class="it">
            <div style="width: 75%; display: inline-block; float: left; border: 1px solid #666; border-radius: 20px; padding: 10px;">
                <p style="margin-bottom: 0;">Representacion impresa del comprobante electrónico, para ver el documento o descargarlo visita:</p>
                <a href="{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}" target="_blank"><strong>{{ route('buscar.comprobante', Auth::user()->headquarter->client->document) }}</strong></a>
                <p>O puedes verificarla en el portal de la Sunat utilizando su clave sol.</p>
                <p style="text-align: center">{{ $clientInfo->pdf_footer }}</p>
            </div>
            @if($qrCode !== null)
                <table width="10%" border="0" cellpadding="0" cellspacing="0" style="float: left; padding: 0;margin: 0;">
                    <tr>
                        <td class="content-qr" align="center" style="padding: 0;margin: 0;">
                            <img src="{{ $qr }}" alt="Qr Image" width="100px">
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    </div>
        <footer>
            {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
        </footer>
    </body>
</html>
