<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>venta</title>
    <style>
        *{font-family: Arial, Helvetica, sans-serif}
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
    </style>
</head>
<body class="white-bg">
    <table>
        <tr>
            <td>
                Estimado {{ $sale->customer->description }}, le recordamos que tiene pendiente de pago de la {{ $sale->type_voucher->description }} {{ $sale->serialnumber }}-{{ $sale->correlative }},
                por el monto de {{ $sale->coin->symbol }} {{ $sale->credito->debt ?? '' }}; la cual, venció el día {{ date('d-m-Y', strtotime($sale->expiration)) }}. 
                Por favor evite reportes a las centrales de riesgo. Nuestras cuentas bancarias para pago son las siguientes:
            </td>
        </tr>
        <tr><td><br><br></td></tr> 
        <tr>
            <td>
                <table border="0">
                    <tr>
                        <td><strong>BANCO:</strong></td>
                        <td><strong>MONEDA:</strong></td>
                        <td><strong>NUMERO DE CUENTA</strong></td>
                        <td><strong>CCI:</strong></td>
                    </tr>
                    @foreach ($sale->client->accountsBank as $acount)
                        <tr>
                            <td>{{ $acount->bank_name }}</td>
                            <td>{{ $acount->coins->description }}</td>
                            <td>{{ $acount->number }}</td>
                            <td>{{ $acount->cci }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr><td><br><br></td></tr>
        <tr>
            <td>Adjuntamos los documentos de la venta mencionada</td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>Atentamente <br></td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td>{{ $sale->client->document }} - {{ $sale->client->trade_name }}</td>
        </tr>
        <tr><td><br></td></tr>
    </table>
</body>
</html>
