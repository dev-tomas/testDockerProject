<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    {{-- <title>VENTA - {{ $sale->serialnumber }}-{{ $sale->correlative }}</title> --}}
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
            top: 25%;
            transform: rotate(-30deg);
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

                    <td style="height: 130px; width: 45%, padding:0" valign="middle">
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
                                            COMPRA
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:1.35em;font-weight: bold;">
                                            <span>{{ $shopping->serial }}-{{ $shopping->correlative }}</span>
                                            <br>
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
                        <td style="width:67%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                            {{-- <div class="tabla_borde"> --}}
                            <div>
                                <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td><strong>DATOS DEL PROVEEDOR</strong></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>RUC:</strong>
                                            </td>
                                            <td>{{$shopping->provider->document}}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>DENOMINACIÓN:</strong>
                                            </td>
                                            <td>{{ $shopping->provider->description }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>DIRECCIÓN: </strong>
                                            </td>
                                            <td>
                                                {{ $shopping->provider->address }}
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
                                            <td><strong>DOCUMENTO DE COMPRA:</strong> <br> {{ $shopping->shopping_serie }} - {{ $shopping->shopping_correlative }}</td>
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
                <table width="100%" border="0" cellpadding="6" cellspacing="0" class="tblproducts">
                    <tbody>
                    <tr>
                        <td align="center" class="bold">CANT.</td>
                        <td align="center" class="bold">UND.</td>
                        <td align="center" class="bold">COD.</td>
                        <td align="center" class="bold">DESCRIPCIÓN</td>
                        <td align="center" class="bold">V/U</td>
                        <td align="center" class="bold">P/U</td>
                        <td align="center" class="bold">IMPORTE</td>
                    </tr>
                    @php
                        $i = 0;
                    @endphp
                    @foreach($shopping->detail as $qd)
                        @php
                            $i++;
                        @endphp
                        <tr class="border_top">
                            <td align="center">{{ $qd->quantity }}</td>
                            <td align="center">{{ $qd->product->ot->code }}</td>
                            <td align="center">{{ $qd->product->internalcode }}</td>
                            <td align="center" width="300px"><span>{{ $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description }}</span><br>{{ $qd->detail }}</td>
                            <td align="center" width="60px">{{ $shopping->coin->symbol }} {{ $qd->unit_value }}</td>
                            <td align="center" width="60px">{{ $shopping->coin->symbol }} {{ $qd->unit_price }}</td>
                            <td align="center" width="60px">{{ $shopping->coin->symbol }} {{ $qd->total }}</td>
                        </tr>
                        {{ $i}}
                        @php if( $i % 10 == 0 ){ echo '<div style="page-break-after: always;"></div>'; } @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr>
                    <td width="50%" valign="top">
                        <table width="100%" border="0" cellpadding="5" cellspacing="0">
                            
                        </table>
                    </td>
                    <td width="50%" valign="top">
                        <br>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                            <tbody>
                                <tr>
                                    <td align="right"><strong>
                                            I.G.V. {{$igv->value}}%: </strong>
                                    </td>
                                    <td width="10" align="right"><strong>{{ $shopping->coin->symbol }} </strong></td>
                                    <td width="120" align="right">
                                        <span>
                                            {{ $shopping->igv}}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <strong>TOTAL: </strong>
                                    </td>
                                    <td width="10" align="right"><strong>{{ $shopping->coin->symbol }} </strong></td>
                                    <td width="120" align="right">
                                        <span>
                                            {{ $shopping->total }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<footer>
</body></html>
