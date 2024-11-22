<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>REQUERIMIENTO - {{ $requirement->serie }}-{{ $requirement->correlative }}</title>
    <style>
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
                                                   REQUERIMIENTO
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size:1.2em;">
                                                    <span>{{ $requirement->serie }} - {{ $requirement->correlative }}</span>
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
                                            <td><strong>SOLICITADO: </strong> {{ $requirement->requested}}</td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <strong>AREA: </strong> {{ $requirement->center->center }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>FECHA: </strong> {{ \Carbon\Carbon::parse($requirement->created_at)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>ALMACÉN </strong> {{ $requirement->warehouse->description }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width:50%;padding-right:0;">
                                <table border="0" border-radius="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                       
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
                        <td align="center" class="bold">ITEM</td>
                        <td align="center" class="bold">COD.</td>
                        <td align="center" class="bold">PRODUCTO/SERVICIO</td>
                        <td align="center" class="bold">CANT.</td>
                    </tr>
                        @foreach ($requirement->detail as $detail)
                            <tr class="border_top">
                                <td align="center">{{ $loop->iteration }}</td>
                                <td align="center">{{ $detail->product->internalcode }}</td>
                                <td align="center">{{ $detail->product->description }}</td>
                                <td align="center">{{ $detail->quantity }}</td>
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
    {{ $clientInfo->address }} - {{ $clientInfo->web }} - {{ $clientInfo->phone }}
</footer>
</body></html>