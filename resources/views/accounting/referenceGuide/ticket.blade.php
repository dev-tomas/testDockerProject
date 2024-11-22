<html width="100%">
    <head>
        <meta http-equiv="Content-Type"
              content="text/html; charset=UTF-8">
        <link rel="stylesheet"
              href="{{ asset('vendor/adminlte3/gyo/css/documents/style_sale.css')}}">
        <style>
            * {font-size : 10px;}
            @page { margin-top: 20px; margin-bottom: 0px; }
            body {
                position   : relative;
                text-align : left;
                margin     : 0 18px;
            }

            .legal {
                display   : block;
                margin    : 0;
                padding   : 0;
                color     : red;
                font-size : 2em;
                position  : absolute;
                width     : 100%;
                left      : 0%;
                top       : 25%;
                transform : rotate(-30deg);
            }

            table tbody tr td {
                font-size : 11px;
            }

            .pnp {
                margin-bottom : 5px;
            }

            .pnp p {
                margin  : 0;
                padding : 0;
            }

            .link-scomp {
                color : #000;
            }

            footer {
                position         : fixed;
                bottom           : -50px;
                left             : 0px;
                right            : 0px;
                height           : 50px;
                background-color : #fff;
                color            : #333;
                text-align       : center;
                line-height      : 35px;
                font-size        : .7em;
                z-index          : 9999;
            }
        </style>
    </head>
    <body class="white-bg"
          width="100%">
        @if ($clientInfo->production == 0)
            <p class="legal">SIN VALOR LEGAL</p>
        @endif
        <div style="text-align: center"
             class="pnp">
            <img src="{{ $clientInfo->logo_company }}"
                 style="height: auto; width: 90px;text-align:center;display:block;">
            <br> <br>
            @if ($clientInfo->show_bussines_name == 1)
                <p style="margin: 5px 0">
                    <strong style="font-size: 1.4em">{{ $clientInfo->business_name }}</strong>
                </p>
            @endif
            <p>
                <strong>{{ $clientInfo->trade_name }}</strong>
            </p>
            <p>
                <strong>RUC: {{$clientInfo->document}}</strong>
            </p>
            <p>{{ $guide->headquarter->address }}</p>
            <p>
                <strong>GUIA DE REMISION ELECTRONICA</strong>
            </p>
            <p>
                <strong>{{ $guide->serialnumber }}-{{ $guide->correlative }}</strong>
            </p>
        </div>
        <div class="pnp">
            <p>
                <strong>DATOS DEL DESTINATARIO:</strong> {{ $guide->receiver }}</p>
            <p>
                @if ($guide->receiver_type_document_id == 6)
                    <strong>VARIOS:</strong>
                @else
                    <strong>{{ $guide->docReceiver->description }}:</strong>
                @endif
                : {{ $guide->receiver_document }}</p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <p>
                <strong>DATOS DEL TRASLADO</strong></p>
            <p>
                <strong>FECHA DE EMISION</strong>
                : {{ date('d-m-Y', strtotime($guide->date)) }}</p>
            <p>
                <strong>FECHA DE TRASLADO</strong>
                : {{ date('d-m-Y', strtotime($guide->traslate)) }}</p>
            <p>
                <strong>MODALIDAD DE TRANSPORTE: </strong>
                @if ($guide->modality == 2)
                    TRANSPORTE PRIVADO
                @elseif($guide->modality == 1)
                    TRANSPORTE PUBLICO
                @endif
            </p>
            <p>
                <strong>PESO BRUTO TOTAL (KGM): </strong>
                {{ $guide->weight }}
            </p>
            @if ($guide->lumps != null)
                <p>
                    <strong>BULTOS: </strong>
                    {{ $guide->lumps }}
                </p>
            @endif
            <p style="text-transform: uppercase">
                <strong>MOTIVO DE TRASLADO: </strong>
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
                @elseif($guide->motive == 9)
                    Venta
                @elseif($guide->motive == 10)
                    Compra
                @endif
            </p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <p>
                <strong>DATOS DEL TRANSPORTE</strong></p>
            @if($guide->docTransport != null)
            <p><strong>TRANSPORTISTA</strong>
                @if ($guide->transport_type_document_id == 6)
                    VARIOS
                @else
                    <strong>{{ $guide->docTransport->description }}:</strong>
                @endif
                 - {{ $guide->transport_document }} - {{ $guide->transport_name }}</p>
            @endif
            @if($guide->vehicle)
            <p>
                <strong>VEHICULO</strong>
                : {{ $guide->vehicle }}</p>
            @endif
            @if($guide->docDriver)
                <p>
                    <strong>CONDUCTOR: </strong>
                    @if ($guide->driver_type_document_id == 6)
                        VARIOS
                    @else
                        <strong>{{ $guide->docDriver->description }}:</strong>
                    @endif
                     - {{ $guide->driver_document }} - {{ $guide->driver_name }}
                </p>
            @endif
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            {{-- <p> --}}
                {{-- <strong>PUNTO DE PARTIDA Y PUNTO DE LLEGADA</strong></p> --}}
            <p>
                <strong>DIRECCION DEL PUNTO DE PARTIDA</strong>
                :  {{$guide->ubigeo_start->department}} - {{$guide->ubigeo_start->province}} - {{$guide->ubigeo_start->district}} - {{ $guide->start_address }}</p>
                <p>
                <strong>DIRECCION DEL PUNTO DE LLEGADA</strong>
                : {{$guide->ubigeo_arrival->department}} - {{$guide->ubigeo_arrival->province}} - {{$guide->ubigeo_arrival->district}} - {{ $guide->arrival_address }}</p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <table style="width: 100%">
            <tbody>
                <tr style="font-size: 10px;">
                    <th>NRO</th>
                    <th align="left"><strong>[ CANT. ]</strong> DESCRIPCIÓN</th>
                    <th>U/M</th>
                </tr>
                @foreach($guide->detail as $qd)
                    <tr class="border_top">
                        <td align="center">{{ $loop->iteration }}</td>
                        <td align="left"><strong>[ {{ number_format($qd->quantity, $clientInfo->decimal_quantity, '.', '') }} ] </strong>{{ $qd->product->internalcode }} {{ $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description }} {{ $qd->detail }}</td>
                        <td align="center">{{ $qd->product->ot->code }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        @if ($guide->sale_id != null)
            <p>{{ $guide->sale->type_voucher->description }} {{ $guide->sale->serialnumber }}-{{ $guide->sale->correlative }}</p>
        @endif
        <b></b>
        <div class="pnp" style="text-align: center">
            <img src="{{ $qr }}" alt="Qr Image" width="90px">
        </div>
    </body>
</html>
