<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        @page {
            font-family: Arial;
            font-size: 13px;
            margin: 0cm 0cm;
        }
        body {
            margin: 1.5cm;
            font-family: Arial;
            color: #666;
        }
        .table {
            width: 100%;
            border-spacing: 0;
        }
        .table tr {
            width: 100%;
            border-bottom: 1px solid #666;
        }
        .table thead {
            border-bottom: 1px solid #666;
        }
        .table td {
            box-sizing: border-box;
            padding: 5px;
            border-bottom: 1px solid #666;
        }

        .table thead th {
            color: #666;
            text-align: center;
        }
        .table {
            border: 1px solid #666;
            border-radius: 10px;
        }
        .table tbody tr {
            border-bottom: 1px solid #666;
        }
        .center {
            text-align: center;
        }
        .cont-logo {
            width: 100%;
            margin-bottom: 2em;
            text-align: center;
        }
        .cont-info {
            width: 100%;
            /* text-align: center; */
        }  
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 7cm;
            text-align: center;
            line-height: 30px;
        }
        .h {width: 100%;
            border-spacing: 0;}
        .tfin td{
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <table class="h">
        <tr>
            <td width="200px" class="center">
                <img src="{{asset('images/') . $clientInfo->logo  }}" height="60px" style="text-align:center" border="0">
                <br>{{ $clientInfo->trade_name }}
                <br>RUC: {{ $clientInfo->document }}
            </td>
            <td class="center"><h2><strong>Kardex</strong></h2>
                <h3>{{ $warehouse->description }} - {{ $product->description }}</h3></td>
            <td class="center">Fecha: {{ $from }} {{ $to }}</td>
        </tr>
    </table>
    <table class="table" border="0">
        <thead >
            <tr>
                <th width="30px">#</th>
                <th>Fecha - Hora</th>
                <th>Tipo Transacción</th>
                <th>Descripción</th>
                <th>Número</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kardexs as $kardex)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $kardex->created_at }}</td>
                    <td>{{ $kardex->type_transaction }}</td>
                    <td>{{ $kardex->description }}</td>
                    <td class="center">{{ $kardex->number == null ? '-' : $kardex->number }}</td>
                    <td class="center">{{ $kardex->entry == null ? '-' : $kardex->entry }}</td>
                    <td class="center">{{ $kardex->output == null ? '-' : $kardex->output }}</td>
                    <td class="center">{{ $kardex->balance }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>