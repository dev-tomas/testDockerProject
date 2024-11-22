<table>
    <thead>
        <tr>
            <th>Ase_cNummov</th>
            <th>Per_cPeriodo</th>
            <th>Ase_dFecha</th>
            <th>Ase_cGlosa</th>
            <th>Ase_cTipoMoneda</th>
            <th>CreditoFiscal</th>
            <th>MateriaConstruccion</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            @php
                $setMovements = (int) $movements;

                $repeatMovement = 10 - strlen($setMovements);
                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;
            @endphp
            <tr>
                <td>{{ $movements }}</td>
                @if($sale->typevoucher_id == 3 || $sale->typevoucher_id == 4)
                    <td>{{ date('m', strtotime($sale->date_issue)) }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date_issue)) }}</td>
                @elseif($sale->typevoucher_id == 5 || $sale->typevoucher_id == 6)
                    <td>{{ date('m', strtotime($sale->date_issue)) }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date_issue)) }}</td>
                @else
                    <td>{{ date('m', strtotime($sale->date)) }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                @endif
                @if($sale->typevoucher_id == 3 || $sale->typevoucher_id == 4)
                    <td>NOTA DE CRÉDITO</td>
                @elseif($sale->typevoucher_id == 5 || $sale->typevoucher_id == 6)
                    <td>NOTA DE DÉBITO</td>
                @elseif($sale->typevoucher_id == 1)
                    @if($sale->low_communication_id != null)
                        <td>ANULACION</td>
                    @else
                        @if ($sale->detail->count() == 1)
                            <td>Venta de {{ $sale->detail[0]->product->description }}</td>
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
                    @endif
                @elseif($sale->typevoucher_id == 2)
                    @if($sale->status == 3 || $sale->status == 0)
                        <td>ANULACION</td>
                    @else
                        @if ($sale->detail->count() == 1)
                            <td>Venta de {{ $sale->detail[0]->product->description }}</td>
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

                            @if($sale->low_communication_id != null || $sale->status == 0||$sale->status_condition=9)
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
                    @endif
                @endif
                <td>038</td>
                <td></td>
                <td></td>
            </tr>
            @php
                $setMovements = (int) $movements + 1;

                $repeatMovement = 10 - strlen($setMovements);
                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;
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
               <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
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
    </tbody>
</table>