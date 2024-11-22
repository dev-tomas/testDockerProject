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
        @foreach ($sales as $sale)
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
                <td>{{ date('Y', strtotime($sale->date)) }}</td>
                <td>{{ date('m', strtotime($sale->date)) }}</td>
                <td>05</td>
                <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                @if ($sale->detail->count() == 1)
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @else
                        <td>Venta de {{ $sale->detail[0]->product->description }}</td>
                    @endif
                @else
                    @php
                        $ip = 0;
                        $is = 0;
                    @endphp
                    @foreach ($sale->detail as $d)
                        @if ($d->product->operation_type == 2)
                            @php
                                $is = 1;
                            @endphp
                        @elseif($d->product->operation_type == 1)
                            @php
                                $ip = 1
                            @endphp
                        @endif
                    @endforeach

                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @elseif ($is == 1 && $ip == 0)
                        <td>Venta de Servicios</td>
                    @elseif($ip == 1 && $is == 0)
                        <td>Venta de Productos</td>
                    @elseif($is == 1 && $ip == 1)
                        <td>Venta de Productos y/o Servicios</td>
                    @else
                        <td>Venta de Productos y/o Servicios</td>
                    @endif
                @endif
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
        @endforeach
       @foreach ($creditnotes as $credit)
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
               <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
               <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
               <td>05</td>
               <td>05{{ date('m', strtotime($credit->date_issue)) }}{{ $voucher }}</td>
               <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
               <td>NOTA DE CREDITO</td>
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
       @endforeach

{{--        @foreach ($debitNotes as $debit)--}}
{{--            @php--}}
{{--                $setMovements = (int) $movements;--}}
{{--                $setVoucher = (int) $voucher;--}}

{{--                $repeatMovement = 10 - strlen($setMovements);--}}
{{--                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;--}}

{{--                $repeatVoucher = 6 - strlen($setVoucher);--}}
{{--                $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;--}}
{{--            @endphp--}}
{{--            <tr>--}}
{{--                <td>{{ $movements }}</td>--}}
{{--                <td>{{ date('Y', strtotime($debit->date_issue)) }}</td>--}}
{{--                <td>{{ date('m', strtotime($debit->date_issue)) }}</td>--}}
{{--                <td>05</td>--}}
{{--                <td>05{{ date('m', strtotime($debit->date_issue)) }}{{ $voucher }}</td>--}}
{{--                <td>{{ date('d/m/Y', strtotime($debit->date_issue)) }}</td>--}}
{{--                <td>NOTA DE DÉBITO</td>--}}
{{--                <td>038</td>--}}
{{--            </tr>--}}
{{--            @php--}}
{{--                $setMovements = (int) $movements + 1;--}}
{{--                $setVoucher = (int) $voucher + 1;--}}

{{--                $repeatMovement = 10 - strlen($setMovements);--}}
{{--                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;--}}

{{--                $repeatVoucher = 6 - strlen($setVoucher);--}}
{{--                $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;--}}
{{--            @endphp--}}
{{--        @endforeach --}}
    </tbody>
</table>