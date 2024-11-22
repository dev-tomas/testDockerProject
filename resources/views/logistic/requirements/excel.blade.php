<table>
    <thead>
        <tr>
            <th>NUMERO</th>
            <th>FECHA</th>
            <th>AREA</th>
            <th>SOLICITADO POR</th>
            <th>APROBADO POR</th>
            <th>NECESIDAD</th>
            <th>ALMACEN</th>
            <th>TOTAL</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requirements as $r)
            <tr>
                <td>{{ $r->serie . ' - ' . $r->correlative }}</td>
                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y') }}</td>
                <td>{{ $r->center->center }}</td>
                <td>{{ $r->requested }}</td>
                <td>{{ $r->authorized }}</td>
                <td>
                    @if ($r->type_requirement == 1)
                        INVENTARIO
                    @elseif($r->type_requirement == 2)
                        EQUIPAMIENTO
                    @endif
                </td>
                <td>{{ $r->warehouse->description }}</td>
                <td>{{ $r->total }}</td>
                <td>
                    @if ($r->status == 1)
                        PROCEDE
                    @elseif($r->status == 2)
                        REVISIÓN
                    @elseif($r->status == 3)
                        NO PROCEDE
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>