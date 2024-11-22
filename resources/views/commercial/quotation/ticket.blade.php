<html width="100%">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_sale.css')}}">
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
        <div style="text-align: center" class="pnp">
            <img src="{{public_path('images/' . $clientInfo->logo)  }}" style="width: 70px;height:auto;text-align:center;display:block;">
            <br>
            <p><strong>{{ $clientInfo->trade_name }}</strong></p>
            <p><strong>{{ Auth::user()->headquarter->address }}</strong></p>
            <br>
            <p><strong>R.U.C {{Auth::user()->headquarter->client->document}}</strong></p>
            <p><strong>COTIZACION</strong></p>
            <p><strong>{{ $quotation->serial_number }}-{{ $quotation->correlative }}</strong></p>
            <br>
        </div>
        <div class="pnp">
            <table width="100%">
                <tr>
                    <td style="padding: 5px 0 !important;">Fecha Emisión:</td>
                    <td style="padding: 5px 0 !important;">{{ date('d/m/Y', strtotime($quotation->date)) }} {{ date('H:i', strtotime($quotation->created_at)) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Vencimiento:</td>
                    <td style="padding: 5px 0 !important;">{{ date('d/m/Y', strtotime($quotation->expiration)) }} <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Tipo de Moneda:</td>
                    <td style="padding: 5px 0 !important;"><strong>{{ $quotation->coin->description }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">TC:</td>
                    <td style="padding: 5px 0 !important;">{{ $quotation->change_type ? number_format($quotation->change_type, 3) : '1.000' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Medio de Pago:</td>
                    <td style="padding: 5px 0 !important;"><strong>@if ($quotation->condition_payment == 'CREDITO')
                                {{ $quotation->condition_payment }} A {{ $quotation->credit_time }} DIAS
                            @else
                                {{ $quotation->condition_payment == null ? '-' : $quotation->condition_payment }}
                            @endif {{ $quotation->other_condition != null ? $quotation->other_condition : '' }}</strong> <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Señores:</td>
                    <td style="padding: 5px 0 !important;">{{ $quotation->customer->description }} <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Nro. de Documento:</td>
                    <td style="padding: 5px 0 !important;">{{ $quotation->customer->document }} <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Dirección:</td>
                    <td style="padding: 5px 0 !important;">{{ $quotation->customer->address }} <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">IGV:</td>
                    <td style="padding: 5px 0 !important;">{{ $igv->value }}% <br></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Vendedor:</td>
                    <td style="padding: 5px 0 !important;">{{ \Str::upper($quotation->user->name) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0 !important;">Correo:</td>
                    <td style="padding: 5px 0 !important;">{{Auth::user()->email}}</td>
                </tr>
                 {{-- <tr>
                    <td style="padding: 5px 0 !important;">Almacén:</td>
                    <td style="padding: 5px 0 !important;"><strong>{{ \Str::upper($quotation->headquarter->description) }}</strong></td>
                </tr>  --}}
            </table>
        </div>
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
            @foreach($quotation_detail as $qd)
                <tr>
                    <td style="padding: 5px 0;" colspan="4"><strong>{{ $qd->product->internalcode }} {{ $qd->product->description }}</strong></td>
                </tr>
                <tr>
                    <td>{{ $qd->product->measure_id != null ? $qd->product->measure->description : '-' }}</td>
                    <td><strong>{{ $qd->unity }}</strong></td>
                    <td align="left">{{ number_format($qd->price, 2, '.', '') }}</td>
                    <td align="right">{{ number_format($qd->total, 2, '.', '') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br>
        <p style="text-align: center;">TOTAL DESPACHADO: <strong>{{ $quotation_detail->sum('unity') }} UNI.</strong></p>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            @if ($quotation->exonerated > 0.00)
                <tr>
                    <td>EXONERADA:</td>
                    <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                    <td width="60" align="right">{{ $quotation->exonerated }}</td>
                </tr>
            @endif
            @if ($quotation->unaffected > 0.00)
                <tr>
                    <td align="left">INAFECTO</td>
                    <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                    <td width="60" align="right">{{ $quotation->unaffected }}</td>
                </tr>
            @endif
            <tr>
                <td>GRAVADA</td>
                <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                <td width="60" align="right">{{ $quotation->taxed }}</td>
            </tr>

            <tr>
                <td>IGV</td>
                <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                <td width="60" align="right">{{ $quotation->igv}}</td>
            </tr>
            @if ($quotation->free > 0.00)
                <tr>
                    <td>GRATUITA</td>
                    <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                    <td width="60" align="right">{{ $quotation->free }}</td>
                </tr>
            @endif
            <tr>
                <td>RECARGO AL CONSUMO</td>
                <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                <td width="60" align="right">{{ $quotation->recharge}}</td>
            </tr>
            <tr>
                <td>IMPORTE TOTAL</td>
                <td width="10" align="right">{{ $quotation->coin->symbol }}</td>
                <td width="60" align="right">{{ $quotation->total }}</td>
            </tr>
            </tbody>
        </table>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <p><strong>SON: </strong>{{ \Str::upper($leyenda) }} {{ $quotation->coin->description }}</p>
        </div>
        @if ($quotation->observation == "|")
            <hr style="border-top: 1px solid #000; margin: .5em 0;">
            <div style="font-size: 12px">
                <strong>OBSERVACIONES: </strong> {{ $quotation->observation }}
            </div>
            <br>
        @endif
        <div class="pnp tabla_borde nobreak">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="nobreak">
                <tbody>
                    <tr>
                        <th class="bold" align="left" >CUENTAS BANCARIAS.</th>
                    </tr>
                    <tr>
                        <td><span class="bold">TITULAR DE LA CUENTA:</span> {{ $clientInfo->business_name }}</td>
                    </tr>
                    <tr>
                        <td ><span class="bold">RUC:</span> {{ $clientInfo->document }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            @if($bankInfo)
                <table  cellpadding="1" cellspacing="1" style="width: 100%;">
                    <tbody>
                        @foreach($bankInfo as $bi)
                            <tr>
                                <th align="left"><span class="bold" >{{ $bi->bank_name }} - {{ $bi->coins->description }} </span></th><br>
                            </tr>
                            <tr>
                                <td>CUENTA: {{ $bi->number }}</td>
                            </tr>
                            @if ($bi->cci != null)
                                <tr><td>CCI: {{ $bi->cci }}</td></tr>
                            @endif
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </body>
</html>
