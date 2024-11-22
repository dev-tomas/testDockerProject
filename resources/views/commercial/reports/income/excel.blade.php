<table>
    <tr>
        <td colspan="4" align="center">
            REPORTE DE COBRANZAS  Y CANJES
        </td>
        <td><strong>[{{ date('d-m-Y H:i:s') }}]</strong></td>
    </tr>
    <tr>
        <td>EMISION</td>
        <td>NRO. DOC.</td>
        <td>TIPO DE PAGO.</td>
        <td>CLIENTE</td>
        <td>TOTAL</td>
        <td>MONEDA</td>
        <td colspan="6">ESTADO</td>
    </tr>
    @foreach($data as $item)
        <tr>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['date'] }}</td>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['document'] }}</td>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['condition'] }}</td>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['customer'] }}</td>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['total'] }}</td>
            <td rowspan="{{ count($item['detail']) + 2 }}">{{ $item['coin'] }}</td>
            <td colspan="6" style="color: {{ $item['status'] == 'ANULADO' || $item['status'] == 'ANULADO NC' ? '#FF0000' : ($item['status'] == 'PENDIENTE' ? '#FF8174' : '#92D050') }}" align="right"><strong>{{ $item['status'] }}</strong></td>
        </tr>
        <tr class="bg-primary text-white">
            <td STYLE="background: #595959; color: #ffffff" align="center">NRO. DOC</td>
            <td STYLE="background: #595959; color: #ffffff" align="center">FECHA PAGO</td>
            <td STYLE="background: #595959; color: #ffffff" align="center">MEDIO PAGO</td>
            <td STYLE="background: #595959; color: #ffffff" align="center">DEUDA</td>
            <td STYLE="background: #595959; color: #ffffff" align="center">PAGADO</td>
            <td STYLE="background: #595959; color: #ffffff" align="center">SALDO</td>
        </tr>
        @foreach($item['detail'] as $detail)
            <tr>
                <td align="center">{{ $detail['doc'] }}</td>
                <td align="center">{{ $detail['payment_date'] }}</td>
                <td align="center">{{ $detail['payment'] }}</td>
                <td align="center">{{ $detail['debt'] }}</td>
                <td align="center">{{ $detail['paid'] }}</td>
                <td align="center">{{ $detail['balance'] }}</td>
            </tr>
        @endforeach
    @endforeach
</table>