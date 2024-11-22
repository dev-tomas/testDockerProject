<table>
    <thead>
    <tr>
        <th>Ase_cNummov</th> {{--- A --}}
        <th>Pan_cAnio</th>{{--- B --}}
        <th>Per_cPeriodo</th>{{--- C --}}
        <th>Lib_cTipoLibro</th>{{--- D --}}
        <th>Ase_nVoucher</th>{{--- E --}}
        <th>Pla_cCuentaContable</th>{{--- F --}}
        <th>Asd_nItem</th>{{--- G --}}
        <th>Asd_cGlosa</th>{{--- H --}}
        <th>Asd_nDebeSoles</th>{{--- I --}}
        <th>Asd_nHaberSoles</th>{{--- J --}}
        <th>Asd_nTipoCambio</th>{{--- K --}}
        <th>Asd_nDebeMonExt</th>{{--- L --}}
        <th>Asd_nHaberMonExt</th>{{--- M --}}
        <th>Cos_cCodigo</th>{{--- N --}}
        <th>Ten_cTipoEntidad</th>{{--- O --}}
        <th>Ent_cCodEntidad</th>{{--- P --}}
        <th>Asd_cTipoDoc</th>{{--- Q --}}
        <th>Asd_dFecDoc</th>{{--- R --}}
        <th>Asd_cSerieDoc</th>{{--- S --}}
        <th>Asd_cNumDoc</th>{{--- T --}}
        <th>Asd_dFecVen</th>{{--- U --}}
        <th>Asd_cTipoDocRef</th>{{--- V --}}
        <th>Asd_dFecDocRef</th>{{--- W --}}
        <th>Asd_cSerieDocRef</th>{{--- X  --}}
        <th>Asd_cNumDocRef</th>{{--- Y --}}
        <th>Asd_cRetencion</th>{{--- Z --}}
        <th>Asd_cProvCanc</th>{{--- AA --}}
        <th>Asd_cOperaTC</th>{{--- AB --}}
        <th>Asd_cTipoMoneda</th>{{--- AC --}}
        <th>Tra_cCodigo</th>{{--- AD --}}
        <th>Asd_cFormaPago</th>{{--- AE --}}
    </tr>
    </thead>
    <tbody>
    @for ($i = 0; $i < count($sd); $i++)
        @php
            $countRow = 1;
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
            <td>{{ $sd[$i]['account'] }}</td>
            <td>{{ $countRow++ }}</td>
            {{-- <td>{{ $sd[$i]['glosa'] }}</td> --}}
            <td>PAGO PROVEEDORES</td>
            <td>0.00</td> {{-- Gravada --}}
            <td>{{ number_format($sd[$i]['total_soles'], 2, '.', '') }}</td>
            <td>{{ $sd[$i]['tc'] }}</td>
            <td>000</td>
            <td>{{ $sd[$i]['total_dolares'] }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ date('d/m/Y', strtotime($sd[$i]['date'])) }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $sd[$i]['coin'] == '038' ? 'SCV' : 'VEN' }}</td>
            <td>{{ $sd[$i]['coin'] }}</td>
            <td></td>
            <td></td>
        </tr>
        @for ($x = 0; $x < count($sd[$i]['details']); $x++)
            <tr>
                <td>{{ $movements }}</td>
                <td>{{ date('Y', strtotime($sd[$i]['date'])) }}</td>
                <td>{{ date('m', strtotime($sd[$i]['date'])) }}</td>
                <td>04</td>
                <td>04{{ date('m', strtotime($sd[$i]['date'])) }}{{ $voucher }}</td>
                <td>{{ $sd[$i]['details'][$x]['paymentAccount'] }}</td>
                <td>{{ $countRow++ }}</td>
                @if ($sd[$i]['type'] == 1)
                    <td>PAGO PROVEEDORES</td>
                @else
                    {{-- <td>PAGO {{ $sd[$i]['details'][$x]['comprobante'] }}  {{ $sd[$i]['details'][$x]['serie'] }} - {{ $sd[$i]['details'][$x]['correlative']  }}</td> --}}
                    <td>PAGO PROVEEDORES</td>
                @endif
                <td>{{ number_format($sd[$i]['details'][$x]['total_soles'], 2, '.', '') }}</td>
                <td>0.00</td>
                <td>{{ $sd[$i]['details'][$x]['tc'] }}</td>
                <td>{{ $sd[$i]['details'][$x]['total_dolares'] }}</td>
                <td>0</td>
                <td>{{ $sd[$i]['details'][$x]['center'] }}</td>
                <td>P</td>
                <td>{{ $sd[$i]['details'][$x]['customer'] }}</td>
                <td>{{ $sd[$i]['details'][$x]['typeDocument'] }}</td>
                <td>{{ date('d/m/Y', strtotime($sd[$i]['date'])) }}</td>
                <td>{{ $sd[$i]['details'][$x]['serie'] }}</td>
                <td>{{ str_pad($sd[$i]['details'][$x]['correlative'], 8, '0', STR_PAD_LEFT) }}</td>
                <td>{{ date('d/m/Y', strtotime($sd[$i]['date'])) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>P</td>
                <td>{{ $sd[$i]['details'][$x]['opera'] }}</td>
                <td>{{ $sd[$i]['details'][$x]['coin'] }}</td>
                <td>{{ $sd[$i]['transaction'] }}</td>
                <td></td>
            </tr>
        @endfor
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