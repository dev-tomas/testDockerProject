@php
    $fecha_unix = time();
    setlocale(LC_TIME, 'es_ES.utf8');
    $fecha_formateada = strftime('%d de %B del %Y', $fecha_unix);
 @endphp
<table>
    <tr>
        <td width="100px"></td>
        <td colspan="2"><img src="{{public_path('images') .'/'. $client->logo  }}" height="100"></td>
        <td colspan="4" align="center"><strong style="color: blue; font-size: 20px">STOCK CLIENTES - {{ $fecha_formateada }}</strong></td>
        <td colspan="2" align="right"><p style="color: #FF0000">LOS PRECIOS INCLUYEN IGV</p></td>
        <td align="right"><strong><a href="sheet://'PRODUCTOS'!A1">REGRESAR</a></strong></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td width="100px"></td>
        <td width="150px" align="center">CODIGO</td>
        <td align="center">IMAGEN</td>
        <td align="center">PRODUCTO</td>
        <td align="center">COLOR</td>
        <td align="center">STOCK FINAL</td>
        <td align="center">PRECIO POR MILLAR <br> <span style="color:#FF0000">(A PARTIR DE 500 PZAS.)</span></td>
        <td align="center">PRECIO POR CIENTO <br> <span style="color:#FF0000">(A PARTIR DE 50 PZAS.)</span></td>
        <td align="center">PRECIO POR MUESTRA <br> <span style="color:#FF0000">(MENOS DE 50 PZAS.)</span></td>
        <td align="center">DESCRIPCION</td>
    </tr>
    @foreach($data as $item)
        <tr>
            <td width="100px" rowspan="{{ count($item['brands']) }}"></td>
            <td align="center" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}"><strong>{{ $item['internalcode'] }}</strong></td>
            <td align="center" valign="middle" style="border: 2px solid #000; text-align: center;" rowspan="{{ count($item['brands']) }}">
                <img height="100" src="{{ $item['image'] }}" style="object-fit: cover" alt="">
            </td>
            <td align="center" width="200px" @if(count($item['brands']) < 6) height="90"  @endif valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">{{ $item['description'] }}</td>
            <td align="center" @if(count($item['brands']) > 4) height="20" @else height="{{ 100 / count($item['brands']) }}"  @endif valign="middle" style="border: 2px solid #000;background: {{ $item['brands'][0]['color'] }}; color: {{ $item['brands'][0]['text'] }}">{{ $item['brands'][0]['brand'] }}</td>
            <td align="center" @if(count($item['brands']) > 4) height="20" @else height="{{ 100 / count($item['brands']) }}"  @endif valign="middle" style="border: 2px solid #000;">{{ $item['brands'][0]['stock'] }}</td>
            @if(array_key_exists('precio_mayorista', $item['prices']))
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    <strong>S/ {{ number_format($item['prices']['precio_mayorista'], 2, '.', ',') }}</strong>
                </td>
            @else
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    &nbsp;
                </td>
            @endif
            @if(array_key_exists('precio_por_caja', $item['prices']))
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    <strong>S/ {{ number_format($item['prices']['precio_por_caja'], 2, '.', ',') }}</strong>
                </td>
            @else
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    &nbsp;
                </td>
            @endif
            @if(array_key_exists('precio_por_unidad', $item['prices']))
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    <strong>S/ {{ number_format($item['prices']['precio_por_unidad'], 2, '.', ',') }}</strong>
                </td>
            @else
                <td align="right" valign="middle" style="border: 2px solid #000;" rowspan="{{ count($item['brands']) }}">
                    &nbsp;
                </td>
            @endif
            <td valign="middle" width="300px" style="border: 2px solid #000;white-space: pre-wrap;" rowspan="{{ count($item['brands'])}}">{{ $item['detail'] }}</td>
        </tr>
        @for($i = 1; $i < count($item['brands']); $i++)
            <tr>
                <td align="center" valign="middle"  @if(count($item['brands']) > 4) height="20" @else height="{{ 100 / count($item['brands']) }}"  @endif style="border: 2px solid #000;background: {{ $item['brands'][$i]['color'] }}; color: {{ $item['brands'][$i]['text'] }}">{{ $item['brands'][$i]['brand'] }}</td>
                <td align="center" valign="middle"  @if(count($item['brands']) > 4) height="20" @else height="{{ 100 / count($item['brands']) }}"  @endif style="border: 2px solid #000;">{{ $item['brands'][$i]['stock'] }}</td>
            </tr>
        @endfor

    @endforeach
</table>