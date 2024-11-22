<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>GUIA DE REMISION REMITENTE - {{ $guide->serialnumber }} - {{ $guide->correlative }}</title>
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
        div.breakNow {
            page-break-before: always;
        }
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
        .tabla_borde {padding-left: 10px;padding-right: 10px;}
        .mt5 {
            margin-top: 5px;
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
        <td style="height: 130px; width: 45%" align="center" valign="middle">
            <span>
                <img src="{{ asset("images/{$clientInfo->logo}") }}" class="logoGen">
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
                                GUIA DE REMISION ELECTRONICA REMITENTE
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:1.35em;font-weight: bold;">
                                <span>{{ $guide->serialnumber }}-{{ $guide->correlative }}</span>
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
<div class="tabla_borde" style="margin-top: 5px;">
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:100%;padding-left:0;padding-top: 5px; padding-bottom: 5px;boder-radius:10px;">
                <div>
                    <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td style="width: 50%"><strong>DATOS DEL DESTINATARIO</strong></td>
                        </tr>
                        <tr>
                            <td style="width: 50%">
                                <strong>APELLIDO Y NOMBRE, DENOMINACION Y RAZON SOCIAL:</strong>
                            </td>
                            <td style="width: 50%">{{$guide->receiver}}</td>
                        </tr>
                        <tr>
                            <td style="width: 50%">
                                <strong>DOCUMENTO DE IDENTIDAD:</strong>
                            </td>
                            <td style="width: 50%">{{ $guide->receiver_document }}</td>
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
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:100%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                {{-- <div class="tabla_borde"> --}}
                <div>
                    <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td><strong>DATOS DEL TRASLADO</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 50%">
                                    <strong>Fecha de Emisión:</strong>
                                </td>
                                <td style="width: 50%">{{$guide->date}}</td>

                                <td style="width: 50%">
                                    <strong>Fecha de Traslado:</strong>
                                </td>
                                <td style="width: 50%">{{ $guide->traslate }}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%">
                                    <strong>Modalidad de Transporte: </strong>
                                </td>
                                <td style="width: 50%">
                                    @if ($guide->modality == '02')
                                        Transporte Privado
                                    @else
                                        Transporte Público
                                    @endif
                                </td>
                                <td style="width: 50%">
                                    <strong>Peso Bruto total (KGM): </strong>
                                </td>
                                <td style="width: 50%">
                                    {{ $guide->weight }}
                                </td >
                            </tr>
                            <tr>
                                <td style="width: 50%">
                                    <strong>Motivo de Traslado: </strong>
                                </td>
                                <td style="width: 50%" colspan="2">
                                    @if ($guide->motive == 1)
                                        Venta sujeta a confirmación de la misma empresa
                                    @elseif($guide->motive == 2)
                                        Traslado entre establecimientos
                                    @elseif($guide->motive == 3)
                                        Traslado de bienes para transformación
                                    @elseif($guide->motive == 4)
                                        Recojo de bienes
                                    @elseif($guide->motive == 5)
                                        Traslado por emisor itinerante
                                    @elseif($guide->motive == 6)
                                        Traslado zona primaria
                                    @elseif($guide->motive == 7)
                                        Venta con entrega a terceros
                                    @elseif($guide->motive == 8)
                                        Otras no incluida en los puntos anteriores.
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                @if ($guide->lumps != null)
                                    <td style="width: 50%">
                                        <strong>BULTOS: </strong>
                                    </td>
                                    <td style="width: 50%">
                                        {{ $guide->lumps }}
                                    </td >
                                @endif
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
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:100%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                {{-- <div class="tabla_borde"> --}}
                <div>
                    <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td><strong>DATOS DEL TRANSPORTE</strong></td>
                        </tr>
                        @if($guide->docTransport != null)
                            <tr>
                                <td style="width: 50%">
                                    <strong>TRANSPORTISTA:</strong>
                                </td>
                                <td>{{$guide->docTransport->description}} - {{ $guide->transport_document }} - {{ $guide->transport_name }}</td>
                            </tr>
                        @endif
                        @if($guide->vehicle)
                            <tr>
                                <td style="width: 50%">
                                    <strong>VEHICULO:</strong>
                                </td>
                                <td>{{ $guide->vehicle }}</td>
                            </tr>
                        @endif
                        @if($guide->docDriver)
                            <tr>
                                <td style="width: 50%">
                                    <strong>CONDUCTOR:</strong>
                                </td>
                                <td>{{$guide->docDriver->description}} - {{ $guide->driver_document }} - {{ $guide->driver_name }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="tabla_borde mt5">
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:100%;padding-left:0;padding-top: 10px; padding-bottom: 10px;boder-radius:10px;">
                {{-- <div class="tabla_borde"> --}}
                <div>
                    <table border="0" border-radius="5" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td colspan="2"><strong>DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 50%">
                                    <strong>Dirección del punto de partida:</strong>
                                </td>
                                <td style="width: 50%">
                                    {{$guide->ubigeo_start->department}} -
                                    {{$guide->ubigeo_start->province}} -
                                    {{$guide->ubigeo_start->district}} -
                                    {{ $guide->start_address }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%">
                                    <strong>Dirección del punto de llegada:</strong>
                                </td>
                                <td style="width: 50%">
                                    {{$guide->ubigeo_arrival->department}} -
                                    {{$guide->ubigeo_arrival->province}} -
                                    {{$guide->ubigeo_arrival->district}} -
                                    {{ $guide->arrival_address }}
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
<div class="tabla_borde mt5">
    <table width="100%" border="0" cellpadding="6" cellspacing="0">
        <tbody>
        <tr>
            <td align="center" class="bold">Nro..</td>
            <td align="center" class="bold">COD.</td>
            <td align="center" class="bold">DESCRIPCIÓN</td>
            <td align="center" class="bold">U/M</td>
            <td align="center" class="bold">CANTIDAD</td>
        </tr>
        @foreach($guide->detail as $qd)
            @if ($loop->iteration == 6)
                <div class="breakNow"></div>
            @endif
            <tr class="border_top">
                <td align="center">{{ $loop->iteration }}</td>
                <td align="center">{{ $qd->product->code }}</td>
                <td align="center" width="300px"><span>{{ $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description }}</span><br>{{ $qd->detail }}</td>
                <td align="center">{{ $qd->product->ot->code }}</td>
                <td align="center">{{ $qd->quantity }}</td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
@if ($guide->observations != null)
    <div class="" style="margin-top: 5px;">
        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:100%;padding-left:0;padding-top: 5px; padding-bottom: 5px;boder-radius:10px;">
                    <div>
                        <table border="0" border-radius="5" cellpadding="0" cellspacing="0" style="width:100%">
                            <tbody>
                            <tr>
                                <td>
                                    @if ($guide->observations != null)
                                        <label><strong>OBSERVACIONES</strong></label>
                                        <p>{{  $guide->observations }}</p>
                                    @endif
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
@endif
@if ($guide->sale_id != null)
    <div class="" style="margin-top: 5px;">
       {{ $guide->sale->type_voucher->description }} {{ $guide->sale->serialnumber }}-{{ $guide->sale->correlative }}
    </div>
@endif
<div class="nobreak" style="margin-top: 5px;">
    <table style="width:100%">
        <tbody>
        <tr>
            <td style="width:80%; border-radius:10px;font-size: 10px;">
                <p style="margin-bottom: 0; text-align: left;">Representacion impresa del comprobante electrónico, para ver el documento o descargarlo visita:</p>
                <p style="margin-bottom: 0; text-align: left;">O puedes verificarla en el portal de la Sunat utilizando su clave sol.</p>
            </td>
            @if($guide->qr_text != null)
                <td style="width:20%; padding-right:0; border-radius:10px; text-align: center;">
                    <img src="{{ $qr }}" alt="Qr Image" width="120px" style="display: block; margin-top: 15px">
                </td>
            @endif
        </tr>
        </tbody>
    </table>
</div>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>
</body></html>
