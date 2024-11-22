<table>
    <thead>
        <th>#</th>
        <th>CAJA</th>
        <th>MOVIMIENTO</th>
        <th>MONTO</th>
        <th>OBSERVACION</th>
    </thead>
    <tbody>
        @foreach($movements as $movement)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $movement->cash->name }}</td>
                <td>{{ $movement->movement }}</td>
                <td>{{ $movement->amount }}</td>
                <td>{{ $movement->observation == null ? '-' : $movement->observation }}</td>
            </tr>
        @endforeach
    </tbody>
</table>