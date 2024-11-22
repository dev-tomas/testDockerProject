<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>COTIZACIÓN - {{ $quotation->serial_number }}-{{ $quotation->correlative }}</title>
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
        .tblproducts{position: relative}
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        div.breakNow { page-break-after: always;page-break-before: always;}
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
        .mt5 {
            margin-top: 5px;
        }
    </style>
</head>
<body class="white-bg">
<table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
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
                                    COTIZACIÓN
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:1.35em;font-weight: bold;">
                                    <span>{{ $quotation->serial_number }}{{ date('Y') }} - {{ $quotation->correlative }}</span>
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
                <td style="width:50%;padding-left:0;">
                    <table border="0" border-radius="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td><strong>DE:</strong> {{Auth::user()->headquarter->client->trade_name}}</td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <strong>Contacto: </strong>{{Auth::user()->name}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Email: </strong> {{Auth::user()->email}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Teléfono: </strong> {{Auth::user()->phone}}
                                </td>
                            </tr>
                            {{-- <tr><td> </td></tr> --}}
                            {{-- <tr><td> </td></tr> --}}
                        </tbody>
                    </table>
                </td>
                <td style="width:50%;padding-right:0;">
                    <table border="0" border-radius="0" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                            <td><strong>PARA: </strong> {{ $quotation->customer->description }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Contacto: </strong> {{ $quotation->customer->contact }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Email: </strong> {{ $quotation->customer->email }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Teléfono: </strong> {{ $quotation->customer->phone }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="tabla_borde mt5">
    <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tbody>
            <tr>
                <td><strong>CONDICIONES COMERCIALES</strong></td>
            </tr>
            <tr>
                <td width='33%' align="left">
                    <strong>
                        Fecha Emisión:
                    </strong>  {{ date('d-m-Y', strtotime($quotation->issue)) }}
                </td>
                <td width='33%' align="left">
                    <strong>
                        Fecha Vencimiento:
                    </strong>  {{ date('d-m-Y', strtotime($quotation->expiration))}}
                </td>
                <td width='33%' align="left">
                    <strong>Condición:</strong> {{$quotation->condition}}
                </td>
            </tr>
            <tr>
                <td width='33%' align="left">
                    <strong>
                        Moneda:
                    </strong>  {{ $quotation->coin->description }}
                </td>
                <td width='33%' align="left">
                    <strong>
                        T/C:
                    </strong>  {{ $quotation->change_type ? number_format($quotation->change_type, 3) : '1.000' }}
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
            <td align="center" class="bold">UNID.</td>
            <td align="center" class="bold">DESCRIPCIÓN</td>
            {{-- <td align="center" class="bold">V/U</td>
            <td align="center" class="bold">P/U</td> --}}
            <td align="center" class="bold">IMPORTE</td>
        </tr>
        @php
            $i = 0;
        @endphp
        @foreach($quotation_detail as $qd)
            @php
                $i++;
            @endphp
            <tr class="border_top">
                <td align="center">{{ $qd->unity }}</td>
                <td align="center">{{ $qd->product->measure->description }}</td>
                <td align="left" width="300px"><span style="padding: 0 8px">{{ $qd->brand_id != null ? $qd->product->brand->description . ' - ' : '' . $qd->product->description }}</span><br>{{ $qd->detail }}</td>
                {{-- @if ($clientInfo->price_type == 0)
                    <td align="center" width="60px">{{$qd->product->coin->symbol." ".$qd->price}}</td>
                    <td align="center" width="60px">{{$qd->product->coin->symbol." ". sprintf('%.2f', ($qd->price * 1.18)) }}</td>
                @else
                    <td align="center" width="60px">{{$qd->product->coin->symbol." ". sprintf('%.2f', $qd->price + ($qd->price - ($qd->price * 1.18)))  }}</td>
                    <td align="center" width="60px">{{$qd->product->coin->symbol." ".$qd->price}}</td>
                @endif --}}
                <td align="center" width="60px">{{ number_format((float) $qd->price * (float) $qd->unity,2,'.',',') }}</td>
            </tr>
            {{ $i}}
            @php if( $i % 10 == 0 ){ echo '<div style="page-break-after: always;"></div>'; } @endphp
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
                        {{ $quotation->observation }}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td width="50%" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                <tbody>
                    @if ($quotation->exonerated > 0.00)
                        <tr class="border_bottom">
                            <td align="right" style="padding: 0;" ><strong>EXONERADA: </strong></td>
                            <td width="10"  style="padding: 0;" align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                            <td width="120"  style="padding: 0;" align="right">{{ $quotation->exonerated }}</td>
                        </tr>
                    @endif
                    @if ($quotation->unaffected > 0.00)
                        <tr class="border_bottom">
                            <td align="right" style="padding: 0;" ><strong>INAFECTA: </strong></td>
                            <td width="10" style="padding: 0;" align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                            <td width="120" style="padding: 0;" align="right">{{ $quotation->unaffected }}</td>
                        </tr>
                    @endif
                    <tr class="border_bottom">
                        <td align="right" style="padding: 0;"><strong>GRAVADA: </strong></td>
                        <td width="10" style="padding: 0;"  align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">
                            <span>
                                {{ $quotation->taxed }}
                            </span></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding: 0;"><strong>
                                I.G.V. {{ number_format($igv->value, 2, '.', ',') }}%: </strong>
                        </td>
                        <td width="10" style="padding: 0;" align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                        <td width="120" style="padding: 0;" align="right">
                            <span>
                                {{ number_format($quotation->igv, 2) }}
                            </span>
                        </td>
                    </tr>
                    @if ($quotation->icbper > 0.00)
                        <tr>
                            <td align="right" style="padding: 0;"><strong>
                                    ICBPER : </strong>
                            </td>
                            <td width="10" style="padding: 0;" align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                            <td width="120" style="padding: 0;" align="right">{{ $quotation->icbper}}</td>
                        </tr>
                    @endif
                    @if ($quotation->recharge > 0.00)
                        <tr>
                            <td align="right" style="padding: 0;"><strong>
                                RECARGO AL CONSUMO: </strong>
                            </td>
                            <td width="10" style="padding: 0;" align="right" style="padding: 0;"><strong>{{ $quotation->coin->symbol }} </strong></td>
                            <td width="120" style="padding: 0;" align="right" style="padding: 0;">
                                    <span>
                                        {{ $quotation->recharge}}
                                    </span>
                            </td>
                        </tr>
                    @endif
                    @if ($quotation->free > 0.00)
                        <tr class="border_bottom">
                            <td align="right" style="padding: 0;" ><strong>GRATUITA: </strong></td>
                            <td width="10" style="padding: 0;" align="right"><strong>{{ $quotation->coin->symbol }} </strong></td>
                            <td width="120" style="padding: 0;" align="right">{{ $quotation->free }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td align="right" style="padding: 0;">
                            <strong>TOTAL: </strong>
                        </td>
                        <td width="10" align="right" style="padding: 0;"><strong>{{ $quotation->coin->symbol }} </strong></td>
                        <td width="120" align="right" style="padding: 0;">
                            <span>
                                {{ number_format($quotation->total, 2, '.', ',') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<div class="tabla_borde nobreak mt5" style="padding: 10px; text-align:left; margin-bottom: 5px">
    <strong>Importe en letras: </strong>{{$leyenda}} {{ $quotation->coin->description }}
</div>
@if ($quotation->condition == 'CREDITO' && $quotation->payments != null)
    <div class="tabla_borde nobreak" style="padding: 5px; text-align:center; margin-bottom: 5px; font-size: 11px">
        <div style="font-size: 11px"><strong>PAGOS CUOTAS AL CREDITO: </strong></div>
        <table style="width: 100%">
            <tr>
                @if ($quotation->payments->count() > 0)
                    <td style="width: 33.33%;">
                        <table style="width: 100%">
                            <tr>                            
                                <td align="left"><strong>CUOTA</td>
                                <td align="left"><strong>FECHA</strong></td>
                                <td align="left"><strong>TOTAL</strong></td>
                            </tr>
                        </table>
                    </td>
                @endif
                @if ($quotation->payments->count() > 1)
                    <td style="width: 33.33%;">
                        <table style="width: 100%">
                            <tr>                            
                                <td align="left"><strong>CUOTA</td>
                                <td align="left"><strong>FECHA</strong></td>
                                <td align="left"><strong>TOTAL</strong></td>
                            </tr>
                        </table>
                    </td>
                @endif
                @if ($quotation->payments->count() > 2)
                    <td style="width: 33.33%;">
                        <table style="width: 100%">
                            <tr>                            
                                <td align="left"><strong>CUOTA</td>
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
                @foreach($quotation->payments as $payment)
                    @php
                        $i++;
                    @endphp
                    <td style="width: 33.33%;">
                        <table style="width: 100%">
                            <tr>                            
                                <td align="center"><strong>{{ $loop->iteration }}</td>
                                <td align="right">{{ date('d-m-Y', strtotime($payment->date)) }}</td>
                                <td align="center">{{ $quotation->coin->symbol }} {{ number_format($payment->mount,2,'.',' ') }}</td>
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
        </tbody>
    </table>
    @if($bankInfo)
        <table border="0.2" cellpadding="1" cellspacing="1" style="width: 100%;">
            <thead>
                <tr>
                    <th>BANCO</th>
                    <th>MONEDA</th>
                    <th>NÚMERO DE CUENTA</th>
                    <th>CCI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankInfo as $bi)
                    <tr>
                        <td>{{ $bi->bank_name }}</td>
                        <td>{{ $bi->coins->description }}</td>
                        <td>{{ $bi->number }}</td>
                        <td>{{ $bi->cci }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>
</body></html>
{{-- {{dd()}} --}}
