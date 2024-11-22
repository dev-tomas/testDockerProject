@php
    $fecha_unix = time();
    setlocale(LC_TIME, 'es_ES.utf8');
    $fecha_formateada = strftime('%d de %B del %Y', $fecha_unix);
@endphp
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>STOCK CLIENTES - {{ Str::upper($fecha_formateada) }}</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
        body{
            margin: 15px 10px;
            padding: 0;
            border-bottom: 5px solid #92D050;
        }

        table {
            border-collapse: collapse;
            border-width: 2px;
        }

        .table-head td {
            background: #92D050;
            color: #fff;
            font-weight: 700;
            border: 2px solid #ffffff;
            height: 45px;
        }
        .front-btn {
            text-decoration: none;
            color: #ffffff;
            font-weight: 700;
            display: block;
            background: #92D050;
            border-radius: 10px;
            height: 35px;
            line-height: 30px;
            margin: 10px 20px;
        }
        tbody tr > td {
            font-size: 14px;
        }
        thead tr > td {
            font-size: 15px;
        }

        portada {
            height: 100%;
        }

        portada {
            display: table;
            width: 100%;
        }

        .container-portada {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .container-portada img {
            display: inline-block;
            vertical-align: middle;
        }

        .container-portada h1 {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
    <body class="portada" style="text-align: center">
    <br><br><br><br><br><br><br><br><br><br>
        <img src="{{public_path('images') .'/'. $client->logo  }}" style="text-align: center" height="150" />
    <br><br><br><br>
        <h1 style="font-size: 4em">CATÁLOGO DE <br> PRODUCTOS</h1>
    </body>
    <body>
        <div id="home"></div>
        <img src="{{public_path('images') .'/'. $client->logo  }}" height="100">
        <h2 style="text-align: center; color: #92D050; margin-bottom: 2em;">ÍNDICE</h2>
        <table width="100%" style="margin-top: 4em;">
            @php
                $cont = 0;
            @endphp
            @while($cont < count($data))
                <tr>
                    <td align="center">
                        <a href="#{{ Str::title($data[$cont]['category']) }}" class="front-btn">{{ $data[$cont]['category'] }}</a>
                    </td>
                    @php
                        $cont++;
                    @endphp
                    @if(isset($data[$cont]['category']))
                        <td align="center">
                            <a href="#{{ Str::title($data[$cont]['category']) }}" class="front-btn">{{ $data[$cont]['category'] }}</a>
                        </td>
                    @else
                        <td>&nbsp;</td>
                    @endif
                </tr>
                @php
                    $cont++;
                @endphp
            @endwhile
        </table>
    </body>
    @foreach($data as $items)
        <body>
            <div id="{{ Str::title($items['category']) }}"></div>
            <table width="100%">
                <tr>
                    <td width="25%">
                        <img src="{{public_path('images') .'/'. $client->logo  }}" height="70">
                    </td>
                    <td width="50%" align="center">
                        <h2 style="text-align: center; color: #92D050; margin-bottom: 1em;">{{ $items['category'] }}</h2>
                    </td>
                    <td width="25%" align="center">
                        <a href="#home" class="front-btn">ÍNDICE</a>
                    </td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr class="table-head">
                        <td width="80px" width="150px" align="center">CODIGO</td>
                        <td width="120px" align="center">IMAGEN</td>
                        <td width="205px" align="center">PRODUCTO</td>
                        <td width="72px" align="center">COLOR</td>
                        <td width="72px" align="center">STOCK FINAL</td>
                        <td width="72px" align="center">P. CAJA <br> <span style="color:#3C3C3C">(MAS DE 50 PZAS.)</span></td>
{{--                        <td align="center">PRECIO POR CIENTO <br> <span style="color:#FF0000">(A PARTIR DE 50 PZAS.)</span></td>--}}
                        <td width="72px" align="center">P. MUESTRA <br> <span style="color:#3C3C3C">(MENOS DE 50 PZAS.)</span></td>
                        <td width="120px" align="center">DESCRIPCION</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items['products'] as $item)
                        <tr  style="height: 150px;page-break-inside:avoid;">
                            <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}"><strong>{{ $item['internalcode'] }}</strong></td>
                            <td align="center" valign="middle" style="border: 2px solid #000; text-align: center;" rowspan="{{ count($item['brands']) }}">
                                <img width="110px" src="{{ $item['image'] }}" style="object-fit: cover" alt="">
                            </td>
                            <td align="center" width="200px"  valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">{{ $item['description'] }}</td>
                            <td width="72px" align="center" @if(count($item['brands']) >= 3) height="18" @else height="{{ 100 / count($item['brands']) }}"  @endif valign="middle" style="border: 2px solid #000;background: {{ $item['brands'][0]['color'] }}; color: {{ $item['brands'][0]['text'] }}">{{ $item['brands'][0]['brand'] }}</td>
                            <td align="center" @if(count($item['brands']) >= 3) height="18" @else height="{{ 100 / count($item['brands']) }}"  @endif valign="middle" style="border: 2px solid #000;">{{ $item['brands'][0]['stock'] }}</td>
{{--                            @if(array_key_exists('precio_mayorista', $item['prices']))--}}
{{--                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">--}}
{{--                                    <strong>S/ {{ number_format($item['prices']['precio_mayorista'], 2, '.', ',') }}</strong>--}}
{{--                                </td>--}}
{{--                            @else--}}
{{--                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">--}}
{{--                                    &nbsp;--}}
{{--                                </td>--}}
{{--                            @endif--}}
                            @if(array_key_exists('precio_por_caja', $item['prices']))
                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                                    <strong>S/ {{ number_format($item['prices']['precio_por_caja'], 2, '.', ',') }}</strong>
                                </td>
                            @else
                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                                    &nbsp;
                                </td>
                            @endif
                            @if(array_key_exists('precio_por_unidad', $item['prices']))
                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                                    <strong>S/ {{ number_format($item['prices']['precio_por_unidad'], 2, '.', ',') }}</strong>
                                </td>
                            @else
                                <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                                    &nbsp;
                                </td>
                            @endif
                            <td valign="middle" align="center" width="12px" style="border: 2px solid #000;" rowspan="{{ count($item['brands'])}}">
                                <p>{{ $item['detail'] }}</p>
                            </td>
                        </tr>
                        @for($i = 1; $i < count($item['brands']); $i++)
                            <tr>
                                <td width="72px" align="center" valign="middle"  @if(count($item['brands']) >= 3) height="18" @else height="{{ 100 / count($item['brands']) }}"  @endif style="border: 2px solid #000;background: {{ $item['brands'][$i]['color'] }}; color: {{ $item['brands'][$i]['text'] }}">{{ $item['brands'][$i]['brand'] }}</td>
                                <td align="center" valign="middle"  @if(count($item['brands']) >= 3) height="18" @else height="{{ 100 / count($item['brands']) }}"  @endif style="border: 2px solid #000;">{{ $item['brands'][$i]['stock'] }}</td>
                            </tr>
                        @endfor
                    @endforeach
                </tbody>
            </table>
        </body>
    @endforeach
</html>