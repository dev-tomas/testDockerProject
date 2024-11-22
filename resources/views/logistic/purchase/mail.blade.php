<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>SOLICITUD DE COTIZACION</title>
    <style>
        *{font-family: Arial, Helvetica, sans-serif}
        table tbody tr{
            padding-bottom: 0;
            padding-top: 0;
        }
    </style>
</head>
<body class="white-bg">
    {{-- {{ dd($clientInfo) }} --}}
    <table>
        <tr>
            <td>Estimado (a), {{ $provider }}</td>
        </tr>
        <tr><td><br></td></tr>
        <tr><td><br></td></tr>
        <tr>
            <td>Sírvase cotizarnos, según el archivo adjunto y adjunte su propuesta económica en el siguiente link: <br></td>
        </tr>
        <tr>
            <td><a href="{{ $url }}">Ingresar Propuesta</a></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>Atentamente <br></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr><td><br></td></tr>
        <tr><td>{{auth()->user()->name}}</td></tr>
        <tr><td><strong>Asesor Comercial</strong></td></tr>
        <tr>
            <td><img src="{{asset('images') . '/' . $clientInfo->logo  }}" style="width: 100px;height:auto;text-align:center;display:block;"></td>
        </tr>
        <tr><td>{{auth()->user()->email}} - {{ auth()->user()->phone }}  <br> {{ $clientInfo->trade_name }} - RUC: {{ $clientInfo->document }}</td></tr>
    </table>
</body>
</html>
