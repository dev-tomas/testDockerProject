<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>RETENCIÓN - {{ $retention->correlative }}</title>
    <style>
        .tblproducts{position: relative}
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
    </style>
</head>
<body class="white-bg">
<table width="100%" style="page-break-inside: auto; ">
    <tbody><tr>
        <td style="padding:30px; !important;">
            <table width="100%" height="250px" border="0" aling="center" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="height: 130px; width: 45%" align="center" valign="middle">
                        <span>
                            <img src="{{asset('images/') . $clientInfo->logo  }}" height="100%;" style="text-align:center" border="0">
                            <div>
                                <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
                            </div>
                        </span>
                    </td>

                    <td style="height: 130px; width: 45%; padding:0" valign="middle">
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
                                        RETENCIÓN
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:1.35em;font-weight: bold;">
                                        <span>N°. :{{ $retention->serial_number }} - {{ $retention->correlative }}</span>
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
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td style="width:33%; padding-right:0; padding-top: 10px; padding-bottom: 10px;">
                            <div style="height: auto;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <strong>Razón Social: </strong> {{$retention->customer->description}})) }}
                                        </td>
                                        <td>
                                            <strong>RUC: </strong> {{$retention->customer->document}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Fecha Emisión: </strong> {{ date('d-m-Y', strtotime($retention->communication_date))}}
                                        </td>
                                        <td>
                                            <strong>Dirección: </strong> {{$retention->customer->address}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Régimen: </strong> {{$retention->regime->code}}
                                        </td>
                                        <td>
                                            <strong>Tasa: </strong> {{$retention->regime->description}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Tipo Moneda: </strong> {{$retention->coin}}
                                        </td>
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
                <table width="100%" border="0" cellpadding="7" cellspacing="0" class="tblproducts">
                    <tbody>
                    <tr>
                        <td align="center" class="bold">TIPO</td>
                        <td align="center" class="bold">NÚMERO</td>
                        <td align="center" class="bold">FECHA</td>
                        <td align="center" class="bold">MONEDA</td>
                        <td align="center" class="bold">TOTAL</td>
                        <td align="center" class="bold">TOTAL RETENIDO</td>
                        <td align="center" class="bold">TOTAL PAGADO</td>
                    </tr>
                    @foreach($retention['detail'] as $r)
                        <tr class="border_top">
                            <td align="center">{{$r->sale->type_voucher->description}}</td>
                            <td align="center">{{$r->sale->serialnumber}} - {{$r->sale->correlative}}</td>
                            <td align="center">{{date('d-m-Y', strtotime($r->sale->issue))}}</td>
                            <td align="center">{{$r->coin}}</td>
                            <td align="center">{{$r->no_retention}}</td>
                            <td align="center">{{$r->retained_amount}}</td>
                            <td align="center">{{$r->amount_paid}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tbody><tr>
        <td width="50%" valign="top">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tbody>
                <tr>
                    <td colspan="4">
                        <br>
                        <br>
                        <span style="font-family:Tahoma, Geneva, sans-serif; font-size:12px" text-align="center"><strong>Observaciones</strong></span>
                        <br>
                        <br>
                        {{ $retention->observation }}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td width="50%" valign="top">
            <br>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                <tbody>
                <tr class="border_bottom">
                    <td align="right"><strong>TOTAL: </strong></td>
                    <td width="10" align="right"><strong>S./</strong></td>
                    <td width="120" align="right">
                        <span>
                            {{ $retention->amount }}
                        </span>
                    </td>
                </tr>
                <tr class="border_bottom">
                    <td align="right"><strong>TOTAL RETENIDO: </strong></td>
                    <td width="10" align="right"><strong>S./</strong></td>
                    <td width="120" align="right">
                        <span>
                            {{ $retention->retained_amount }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td align="right">
                        <strong>TOTAL PAGADO: </strong>
                    </td>
                    <td width="10" align="right"><strong>S./</strong></td>
                    <td width="120" align="right">
                        <span>
                            {{ $retention->amount_paid }}
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
    <tbody>
    <tr>
        <td>Resumen</td>
        <td>{{$hash}}</td>
    </tr>
    </tbody>
</table>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>

</body></html>
{{-- {{dd()}} --}}
