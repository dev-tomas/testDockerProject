<table>
    <thead>
        <tr>
            <th>NUMERO</th>
            <th>FECHA</th>
            <th>TIPO DE DOCUMENTO DE COMPRA</th>
            <th>DOCUMENTO DE COMPRA</th>
            <th>TIPO DE COMPRA</th>
            <th>TIPO DE REGISTRO</th>
            <th>RUC/DNI/ECT PROVEEDOR</th>
            <th>PROVEEDOR</th>
            <th>MONEDA</th>
            <th>IGV</th>
            <th>TOTAL</th>
            <th>TOTAL PENDIENTE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchases as $r)
            <tr>
                <td>{{ $r->serial . ' - ' . $r->correlative }}</td>
                <td>{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
                <td>{{ $r->voucher->code }}</td>
                <td>{{ $r->shopping_serie . ' - ' . $r->shopping_correlative }}</td>
                <td>
                    @if ($r->shopping_type == '1')
                        {{ 'INVENTARIO' }}
                    @elseif($r->shopping_type == '2')
                        {{ 'EQUIPAMIENTO' }}
                    @endif
                </td>
                <td>
                    @if ($r->shipping_register == '1')
                        {{ 'FISICO' }}
                    @elseif($r->shipping_register == '2')
                        {{ 'ELECTRONICO' }}
                    @endif
                </td>
                <td>{{ $r->provider->document }}</td>
                <td>{{ $r->provider->description }}</td>
                <td>{{ $r->coin->symbol }}</td>
                <td>{{ $r->igv }}</td>
                <td>{{ $r->total }}</td>
                <td>{{ $r->credit ? $r->credit->debt : '0' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>