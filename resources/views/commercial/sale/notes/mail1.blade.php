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
            <td>Estimado (a) {{ $cn->customer->contact }}</td>
        </tr>
        <tr>
            <td>Adjuntamos los documentos de la venta realizada</td>
        </tr>
        <tr><td><br></td></tr>
        <tr><td><br></td></tr>
        <tr>
            <td>Gracias por confiar en nosostros. <br></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>Atentamente <br></td>
        </tr>
        <tr><td><br></td></tr>
        <tr><td>{{auth()->user()->headquarter->client->trade_name}}</td></tr>
        <tr>
            <td><img src="{{asset('images') . '/' . $clientInfo->logo  }}" style="width: 100px;height:auto;text-align:center;display:block;"></td>
        </tr>
    </table>
</body>
</html>
