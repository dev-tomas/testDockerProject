<table>
    <thead>
        <tr>
            <th>FECHA</th>
            <th>SERIE</th>
            <th>CORRELATIVE</th>
            <th>DOCUMENTO</th>
            <th>DENOMINACION</th>
            <th>MONEDA</th>
            <th>DESCRIPCION</th>
            <th>BASE IMPONIBLE</th>
            <th>RETENCION</th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipts as $receipt)
            <tr>
                <td>{{ date('d-m-Y', strtotime($receipt->date)) }}</td>
                <td>{{ $receipt->shopping_serie }}</td>
                <td>{{ $receipt->shopping_correlative }}</td>
                <td>{{ $receipt->provider->document }}</td>
                <td>{{ $receipt->provider->description }}</td>
                <td>{{ $receipt->coin->symbol }}</td>
                <td>{{ $receipt->detail[0]->product->description }}</td>
                <td>{{ $receipt->subtotal }}</td>
                <td>{{ $receipt->total_retention }}</td>
                <td>{{ $receipt->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>