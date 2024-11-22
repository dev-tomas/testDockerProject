<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>ORDEN DE COMPRA - {{ $order->serie }}-{{ $order->correlative }}</title>
    <style>
        .tblproducts{position: relative}
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
        div.breakNow { page-break-after: always;page-break-before: always;}
    </style>
</head>
<body class="white-bg">
<table width="100%" style="page-break-inside: auto; ">
    <tbody><tr>
        <td style="padding:30px; !important;">
                <table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td style="height: 100px; width: 45%" align="center" valign="middle">
                                <span>
                                    <img src="{{asset('images/') . $clientInfo->logo  }}" height="100%;" style="text-align:center" border="0">
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
                                                <td align="center" style="font-size:1.2em;font-weight: bold;">
                                                   ORDEN DE COMPRA
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size:1.2em;">
                                                    <span>{{ $order->serie }} - {{ $order->correlative }}</span>
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
                            <td style="width:50%;padding-left:0;">
                                <table border="0" border-radius="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>FECHA: </strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>TELEFONO: </strong>  {{$clientInfo->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <strong>EMAIL: </strong> {{ auth()->user()->email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <strong>TELEFONO: </strong> {{ auth()->user()->phone }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width:50%;padding-right:0;">
                                <table border="0" border-radius="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td><strong>PROVEEDOR: </strong> {{ $order->provider->description}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>RUC: </strong> {{ $order->provider->document}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>EMAIL: </strong> {{ $order->provider->email}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>TELEFONO: </strong> {{ $order->provider->phone}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>CONDICIÓN DE ENTREGRA: </strong> {{ $order->delivery_term}}</td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <strong>CONDICIÓN: </strong> {{ $order->condition }}
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td>
                                                <strong>ENTREGA: </strong> {{ $order->delivery }}
                                            </td>
                                        </tr> --}}
                                    </tbody>
                                </table>
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
                        <td align="center" class="bold">COD.</td>
                        <td align="center" class="bold">PRODUCTO/SERVICIO</td>
                        <td align="center" class="bold">CANT.</td>
                        {{-- <td align="center" class="bold">OBSERVACIONES</td> --}}
                    </tr>
                        @foreach ($order->detail as $detail)
                            <tr class="border_top">
                                <td align="center">{{ $detail->product->internalcode }}</td>
                                <td align="center">{{ $detail->product->description }}</td>
                                <td align="center">{{ $detail->quantity }}</td>
                                {{-- <td align="center" width="150px">{{ $detail->observation }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <div style="text-align: center">
            <strong>APROBADO POR:</strong>
            <br>
            {{ $order->requirement->authorized }}
        </div>
    </tr>
    </tbody>
</table>
</body></html>
