<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Doc. Venta</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Vencimiento</th>
            <th>Deuda</th>
        </tr>
    </thead>
    <tbody>
        @foreach($credits as $c)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date('d-m-Y', strtotime($c->date)) }}</td>
                <td>{{ $c->provider->description }}</td>
                <td>{{ $c->shopping->serial . ' - ' . $c->shopping->correlative }}</td>
                <td>{{ $c->total }}</td>
                <td>{{ $c->status == '1' ? 'Cancelado' : 'Pendiente' }}</td>
                <td>{{ date('d-m-Y', strtotime($c->expiration)) }}</td>
                <td>{{ $c->debt }}</td>
            </tr>
        @endforeach
    </tbody>
</table>