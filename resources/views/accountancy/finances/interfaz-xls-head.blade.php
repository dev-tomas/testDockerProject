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
                <td>02</td>
                <td>02{{ date('m', strtotime($sd[$i]['date'])) }}{{ $voucher }}</td>
                <td>{{ date('d/m/Y', strtotime($sd[$i]['date'])) }}</td>
                <td>COBRO DE DOCUMENTOS - DIA {{ date('d', strtotime($sd[$i]['date'])) }}</td>
                <td>038</td>
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
        @foreach($notes as $note)
            @if ($note['type'] == 1)
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
                    <td>{{ date('Y', strtotime($note['date'])) }}</td>
                    <td>{{ date('m', strtotime($note['date'])) }}</td>
                    <td>02</td>
                    <td>02{{ date('m', strtotime($note['date'])) }}{{ $voucher }}</td>
                    <td>{{ date('d/m/Y', strtotime($note['date'])) }}</td>
                    <td>{{ $note['type_document'] }}</td>
                    <td>038</td>
                </tr>
                @php
                    $setMovements = (int) $movements + 1;
                    $setVoucher = (int) $voucher + 1;
                    
                    $repeatMovement = 10 - strlen($setMovements);
                    $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;

                    $repeatVoucher = 6 - strlen($setVoucher);
                    $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;
                @endphp
            @endif
        @endforeach
    </tbody>
</table>