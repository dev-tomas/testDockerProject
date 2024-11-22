<table>
    <thead>
    <tr>
        <th>Ase_cNummov</th>
        <th>Pan_cAnio</th>
        <th>Per_cPeriodo</th>
        <th>Lib_cTipoLibro</th>
        <th>Ase_nVoucher</th>
        <th>Ase_dFecha</th>
        <th>Ase_cGlosa</th>
        <th>Ase_cTipoMoneda</th>
        <th>CreditoFiscal</th>
        <th>MateriaConstruccion</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
            <tr>
                <td>{{ $d['movement'] }}</td>
                <td>{{ $d['anio'] }}</td>
                <td>{{ $d['periodo'] }}</td>
                <td>{{ $d['tipo_libro'] }}</td>
                <td>{{ $d['voucher'] }}</td>
                <td>{{ $d['fecha'] }}</td>
                <td>{{ $d['glosa'] }}</td>
                <td>{{ $d['tipo_moneda'] }}</td>
                <td>{{ $d['credito_fiscal'] }}</td>
                <td>{{ $d['material_construccion'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>