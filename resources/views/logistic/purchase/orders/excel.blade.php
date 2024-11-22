<table>
        <thead>
            <tr>
                <th>NUMERO</th>
                <th>FECHA</th>
                <th>PLAZO DE ENTREGA</th>
                <th>CONDICION</th>
                <th>ENTREGA</th>
                <th>DOC. PROVEEDOR</th>
                <th>DENOMINACION PROVEEDOR</th>
                <th>INVERSION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $r)
                <tr>
                    <td>{{ $r->serie . ' - ' . $r->correlative }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y') }}</td>
                    <td>{{ $r->delivery_term }}</td>
                    <td>{{ $r->condition }}</td>
                    <td>{{ $r->delivery }}</td>
                    <td>{{ $r->provider->document }}</td>
                    <td>{{ $r->provider->description }}</td>
                    <td>{{ $r->investment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>