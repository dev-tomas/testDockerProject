<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{public_path('vendor/adminlte3/gyo/css/documents/style_quotation.css')}}">
    <title>ORDEN DE COMPRA</title>
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
            <td>Estimado (a), {{ $provider->description }}</td>
        </tr>
        <tr>
            {{-- <td>Enviamos Orden de Compra</td> --}}
            {{-- <td>Estimado: {{$quotation->customer->description}}</td> --}}
        </tr>
        <tr><td><br></td></tr>
        <tr><td><br></td></tr>
        <tr>
            <td>Adjuntamos nuestra orden de compra según la cotización enviada.</td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>Atentamente <br></td>
        </tr>
        <tr><td><br></td></tr>
        <tr><td>{{auth()->user()->name}}</td></tr>
        @foreach (auth()->user()->roles as $item)
            <tr><td><strong>{{ $item->name }}</strong></td></tr>
        @endforeach
        <tr>
            <td><img src="{{asset('images') . '/' . $clientInfo->logo  }}" style="width: 100px;height:auto;text-align:center;display:block;"></td>
        </tr>
        <tr><td>{{auth()->user()->email}} - {{ auth()->user()->phone }}  <br> {{ $clientInfo->trade_name }} - RUC: {{ $clientInfo->document }}</td></tr>
    </table>
</body>
</html>
