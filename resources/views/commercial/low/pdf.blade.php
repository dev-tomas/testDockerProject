<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>COMUNICACIÓN DE BAJA - {{ $low->correlative }}</title>
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
                    <td style="height: 130px; width: 45%; max-width: 45%;" align="center" valign="middle">
                        <span>
                            <img src="{{ asset("images/{$clientInfo->logo}") }}" class="logoGen">
                            <div>
                                <small>{{ $clientInfo->trade_name }} | {{ Auth::user()->headquarter->address }} | {{$clientInfo->phone }} | {{ $clientInfo->email }} | {{ $clientInfo->web }}</small>
                            </div>
                        </span>
                    </td>

                    <td style="height: 200px; width: 45%; padding:0" valign="middle">
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
                                        COMUNICACIÓN DE BAJA
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:1.35em;font-weight: bold;">
                                        <span>NÚMERO-{{ $low->correlative }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:1.35em;font-weight: bold;">
                                        <span>NÚMERO DE TICKET-{{ $low->ticket }}</span>
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
                                            <strong>FECHA DE GENERACIÓN: </strong> {{ date('d-m-Y', strtotime($low->generation_date)) }}
                                        </td>
                                        <td>
                                            <strong>FECHA DE COMUNICACIÓN: </strong> {{ date('d-m-Y', strtotime($low->communication_date))}}
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
                <table width="100%" border="0" cellpadding="6" cellspacing="0" class="tblproducts">
                    <tbody>
                    <tr>
                        <td align="center" class="bold">FECHA</td>
                        <td align="center" class="bold">TIPO DE DOCUMENTO</td>
                        <td align="center" class="bold">NRO. DE DOCUMENTP</td>
                        <td align="center" class="bold">MOTIVO</td>
                    </tr>
                    @foreach($low_detail as $ld)
                        <tr class="border_top">
                            <td align="center">{{ date('d-m-Y', strtotime($low->generation_date))  }}</td>
                            @if($ld->sale !== null)
                                <td align="center">{{ $ld->sale->type_voucher->description }}</td>
                                <td align="center">{{ $ld->sale->serialnumber }}-{{$ld->sale->correlative}}</td>
                            @elseif($ld->debit_note !== null)
                                <td align="center">{{ $ld->debit_note->type_voucher->description }}</td>
                                <td align="center">{{ $ld->debit_note->serial_number }}-{{$ld->debit_note->correlative}}</td>
                            @elseif($ld->credit_note !== null)
                                <td align="center">{{ $ld->credit_note->type_voucher->description }}</td>
                                <td align="center">{{ $ld->credit_note->serial_number }}-{{$ld->credit_note->correlative}}</td>
                            @endif()
                            <td align="center">{{ $ld->motive }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<footer>
    {{ $clientInfo->address }} - {{ $clientInfo->email }} - {{ $clientInfo->phone }}
</footer>

</body></html>
{{-- {{dd()}} --}}
