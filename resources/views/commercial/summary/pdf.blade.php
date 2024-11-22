<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_summary.css')}}">
    <title>RESUMEN DIARIO DE BOLETAS DEVENTA </title>
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

        .no-margin{
            margin: 0
        }
    </style>
</head>
<body class="white-bg">
@if ($clientInfo->production == 0)
    <p class="legal">SIN VALOR LEGAL</p>
@endif
<table width="525">
    <tbody>
    <tr>
        <td>
            <table width="100%" height="250px" border="0" aling="center" cellpadding="0" cellspacing="0">
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

                    <td style="height: 130px; width: 45%; padding:0" valign="middle">
                        <div class="tabla_borde" align="center" style="height: 120px">
                            <table width="100%" border="0" cellpadding="6" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" style="font-size:1.35em;font-weight: bold;">
                                        RESUMEN DIARIO DE <br>
                                        BOLETAS DE VENTA
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                            <span style="font-size:15px" text-align="center">
                                                R.U.C. {{Auth::user()->headquarter->client->document}}
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
                <table style="width: 80%;">
                    <tbody>
                    <tr>
                        <td style="width:100%; padding-right:0; padding-top: 10px; padding-bottom: 10px;">
                            {{-- <div class="tabla_borde" style="height: auto;"> --}}
                            <div style="height: auto;">
                                <table>
                                    <tbody>
                                    {{-- <tr>
                                        <td><span style="color: #fff;">FECHA</span></td>
                                    </tr> --}}
                                    <tr>
                                        <td>
                                            <strong>FECHA DE EMISIÓN DEL RESUMEN: </strong> {{ date('d-m-Y', strtotime($summary->date_issues)) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>FECHA DE GENERACIÓN.: </strong> {{ date('d-m-Y', strtotime($summary->date_generation))}}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><strong>MONEDA:</strong> SOLES</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="tabla_borde">
                <table width="525px" border="0" cellpadding="5" cellspacing="0">
                    <tbody>
                    <tr>
                        <td align="center" class="bold">Documento</td>
                        <td align="center" class="bold">Condición</td>
                        <td align="center" class="bold">Impuestos</td>
                        <td align="center" class="bold">Totales</td>
                        <td align="center" class="bold">Imp. Total</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach($summary->detail as $sd)
                        @php
                            $i++;
                        @endphp
                        <tr class="border_top">
                            <td>
                                @if($sd->sale->credit_note !== null)
                                    <p class="no-margin"><strong>{{$sd->sale->credit_note->type_voucher->description}}</strong> {{$sd->sale->serialnumber}} - {{$sd->sale->correlative}}</p>
                                    <p><strong>DOC. REF.</strong> {{$sd->sale->type_voucher->description}} {{$sd->sale->serialnumber}} - {{$sd->sale->correlative}}</p>
                                @else
                                    <p><strong>{{$sd->sale->type_voucher->description}}</strong> {{$sd->sale->serialnumber}} - {{$sd->sale->correlative}}</p>
                                @endif
                            </td>
                            <td align="center">@if($sd->sale->status === 1)<p class="no-margin">ADICIONAR</p>@else<p class="no-margin">ANULADO</p>@endif</p>
                            </td>
                            <td align="center"><p class="no-margin"><b>IGV</b> {{$sd->sale->igv}}</p></td>
                            <td align="center"><p class="no-margin"><strong>GRAVADA</strong> {{$sd->sale->taxed}}</p>
                                <p class="no-margin"><strong>EXONERADA</strong> {{$sd->sale->exonerated}}</p></td>
                            <td align="center">
                                <p class="no-margin">{{$sd->sale->total}}</p>
                            </td>
                        </tr>
                        {{ $i}}
                        @php if( $i % 10 == 0 ){ echo '<div style="page-break-after: always;"></div>'; } @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <!--<div>
                <blockquote>
                    <strong>Resumen:</strong>
                </blockquote>
            </div>-->
        </td>
    </tr>
    </tbody>
</table>

<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>
</body></html>
