<table>
    <tr>
        <td colspan="3" align="center">
            REPORTE DE INGRESOS DIARIOS
        </td>
        <td><strong>[{{ date('d-m-Y') }}]</strong></td>
    </tr>
    <tr>
        <td>EMISION</td>
        <td>NRO. DOC.</td>
        <td>TIPO DOC.</td>
        <td>CLIENTE</td>
        <td>MONEDA</td>
        <td>EFECTIVO</td>
        <td>DEPOSITO</td>
        <td>CREDITO</td>
        <td>TARJETA</td>
        <td>PRODUCTO</td>
        <td>U.M</td>
        <td>CANT.</td>
        <td>P.U</td>
    </tr>
    @foreach($data['details'] as $item)
        @if(isset($item['detail']))
            <tr>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['date'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['document'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['type_voucher'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['customer'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['coin'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['cash'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['deposito'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['credito'] }}</td>
                <td rowspan="{{ count($item['detail']) }}">{{ $item['tarjeta'] }}</td>
                <td>{{ $item['detail'][0]['product'] }}</td>
                <td>{{ $item['detail'][0]['operation'] }}</td>
                <td>{{ $item['detail'][0]['quantity'] }}</td>
                <td>{{ $item['detail'][0]['price'] }}</td>
            </tr>
            @if (count($item['detail']) > 1)
                @for ($i = 1; $i <= count($item['detail']) - 1; $i++)
                    <tr>
                        <td>{{ $item['detail'][$i]['product'] }}</td>
                        <td>{{ $item['detail'][$i]['operation'] }}</td>
                        <td>{{ $item['detail'][$i]['quantity'] }}</td>
                        <td>{{ $item['detail'][$i]['price'] }}</td>
                    </tr>
                @endfor
            @endif
        @endif
    @endforeach
    <tr>
        <td colspan="5" align="right"><strong>TOTAL EN SOLES</strong></td>
        <td>S/ {{ $data['cash_pen'] }}</td>
        <td>S/ {{ $data['deposito_pen'] }}</td>
        <td>S/ {{ $data['credito_pen'] }}</td>
        <td>S/ {{ $data['tarjeta_pen'] }}</td>
        <td colspan="2" align="right"><strong>TOTAL CANTIDAD</strong></td>
        <td>{{ $data['total_quantity'] }}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="5" align="right"><strong>TOTAL DÓLARES</strong></td>
        <td>$ {{ $data['cash_usd'] }}</td>
        <td>$ {{ $data['deposito_usd'] }}</td>
        <td>$ {{ $data['tarjeta_usd'] }}</td>
        <td>$ {{ $data['credito_usd'] }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>