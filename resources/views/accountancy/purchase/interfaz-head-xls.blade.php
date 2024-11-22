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
        @foreach ($book as $row)
            <tr>
                <td>{{ $row['movement'] }}</td>
                <td>{{ $row['year'] }}</td>
                <td>{{ $row['period'] }}</td>
                <td>{{ $row['book_type'] }}</td>
                <td>{{ $row['book_type'] }}{{ $row['period'] }}{{ $row['voucher'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['glosa'] }}</td>
                <td>{{ $row['coin'] }}</td>
                <td></td> 
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>