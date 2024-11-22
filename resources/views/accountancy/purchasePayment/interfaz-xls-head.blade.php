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
        @for ($i = 0; $i < count($sd); $i++)
            @php 
                $setMovements = (int) $movements;
                $setVoucher = (int) $voucher;
        
                $repeatMovement = 10 - strlen($setMovements);
                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;

                $repeatVoucher = 6 - strlen($setVoucher);
                $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;
            @endphp
            <tr>
                <td>{{ $movements }}</td>
                <td>{{ date('Y', strtotime($sd[$i]['date'])) }}</td>
                <td>{{ date('m', strtotime($sd[$i]['date'])) }}</td>
                <td>04</td>
                <td>04{{ date('m', strtotime($sd[$i]['date'])) }}{{ $voucher }}</td>
                <td>{{ date('d/m/Y', strtotime($sd[$i]['date'])) }}</td>
                <td>PAGO DE PROVEEDORES</td>
                <td>{{ $sd[$i]['coin'] }}</td>
            </tr>
            @php
                $setMovements = (int) $movements + 1;
                $setVoucher = (int) $voucher + 1;
                
                $repeatMovement = 10 - strlen($setMovements);
                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;

                $repeatVoucher = 6 - strlen($setVoucher);
                $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;
            @endphp
        @endfor
    </tbody>
</table>