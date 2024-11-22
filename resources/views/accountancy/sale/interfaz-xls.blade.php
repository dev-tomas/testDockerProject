<table>
    <thead>
        <tr>
            <th>Ase_cNummov</th>
            <th>Pan_cAnio</th>
            <th>Per_cPeriodo</th>
            <th>Lib_cTipoLibro</th>
            <th>Ase_nVoucher</th>
            <th>Pla_cCuentaContable</th>
            <th>Asd_nItem</th>
            <th>Asd_cGlosa</th>
            <th>Asd_nDebeSoles</th>
            <th>Asd_nHaberSoles</th>
            <th>Asd_nTipoCambio</th>
            <th>Asd_nDebeMonExt</th>
            <th>Asd_nHaberMonExt</th>
            <th>Cos_cCodigo</th>
            <th>Ten_cTipoEntidad</th>
            <th>Ent_cCodEntidad</th>
            <th>Asd_cTipoDoc</th>
            <th>Asd_dFecDoc</th>
            <th>Asd_cSerieDoc</th>
            <th>Asd_cNumDoc</th>
            <th>Asd_dFecVen</th>
            <th>Asd_nMontoInafecto</th>
            <th>Asd_cBaseImp</th>
            <th>Asd_cProvCanc</th>
            <th>Asd_cOperaTC</th>
            <th>Asd_cTipoMoneda</th>
            <th>Asd_cTipoDocRef</th>
            <th>Asd_dFecDocRef</th>
            <th>Asd_cSerieDocRef</th>
            <th>Asd_cNumDocRef</th>
            <th>Id_Exoneracion</th>
            <th>Id_Tipo_Renta</th>
            <th>Id_Modalidad</th>
            <th>Id_Aduana</th>
            <th>Id_Clasific_Servicio</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
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
                <td>{{ date('Y', strtotime($sale->date)) }}</td>
                <td>{{ date('m', strtotime($sale->date)) }}</td>
                <td>05</td>
                <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                    <td>1212101</td>
                @else
                    <td>1212102</td>
                @endif
                <td>{{ $countRow++ }}</td>
                @if($sale->low_communication_id != null || $sale->status == 0 || $sale->status == 0)
                    <td>ANULADA</td>
                @else
                    @php
                        $ip = 0;
                        $is = 0;
                    @endphp
                    @foreach ($sale->detailo as $d)
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
                    @if ($sale->detailo->count() == 1)
                        <td>Venta de {{ $sale->detailo[0]->product->description }}</td>
                    @elseif ($is == 1 && $ip == 0)
                        <td>Venta de Servicios</td>
                    @elseif($ip == 1 && $is == 0)
                        <td>Venta de Productos</td>
                    @elseif($is == 1 && $ip == 1)
                        <td>Venta de Productos y/o Servicios</td>
                    @else
                        <td>Venta de Productos</td>
                    @endif
                @endif
                @if($sale->low_communication_id != null || $sale->status == 3||$sale->status_condition=9)
                {{dd($sale->total)}}    
                <td>0.00</td>
                @else
                    <td>{{ $sale->total }}</td>
                @endif
                <td>0.00</td> 
                <td>000</td>
                <td>000</td>
                <td>000</td>
                <td>{{ $sale->detailo[0]->product->cost_center_id != null ? $sale->detailo[0]->product->centerCost->code : '' }}</td>
                <td>C</td>
                <td>{{ $sale->customer->code }}</td>
                <td>{{ $sale->type_voucher->code }}</td>
                <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                <td>{{ $sale->serialnumber }}</td>
                @php
                    $repeatCorrelative = 8 - strlen($sale->correlative);
                    $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$sale->correlative;
                @endphp
                <td>{{ $correlative }}</td>
                @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                    <td></td>
                @else
                    <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td>
                @endif
                <td>000</td>
                <td></td>
                <td>P</td>
                <td>SCV</td>
                <td>038</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @if ($sale->igv > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($sale->date)) }}</td>
                    <td>{{ date('m', strtotime($sale->date)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ai->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @else
                        @if ($sale->detailo->count() == 1)
                            <td>Venta de {{ $sale->detailo[0]->product->description }}</td>
                        @elseif ($is == 1 && $ip == 0)
                            <td>Venta de Servicios</td>
                        @elseif($ip == 1 && $is == 0)
                            <td>Venta de Productos</td>
                        @elseif($is == 1 && $ip == 1)
                            <td>Venta de Productos y/o Servicios</td>
                        @else
                            <td>Venta de Productos</td>
                        @endif
                    @endif
                    <td>0.00</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else
                        <td>{{ $sale->igv }}</td>
                    @endif
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                    <td>{{ $sale->serialnumber }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($sale->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$sale->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                        <td></td>
                    @else
                        <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td>
                    @endif
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td>SCV</td>
                    <td>038</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
            @if ($sale->recharge > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($sale->date)) }}</td>
                    <td>{{ date('m', strtotime($sale->date)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ar->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @else
                        @if ($sale->detailo->count() == 1)
                            <td>Venta de {{ $sale->detailo[0]->product->description }}</td>
                        @elseif ($is == 1 && $ip == 0)
                            <td>Venta de Servicios</td>
                        @elseif($ip == 1 && $is == 0)
                            <td>Venta de Productos</td>
                        @elseif($is == 1 && $ip == 1)
                            <td>Venta de Productos y/o Servicios</td>
                        @else
                            <td>Venta de Productos</td>
                        @endif
                    @endif
                    <td>0.00</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else
                        <td>{{ $sale->recharge }}</td>
                    @endif
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                    <td>{{ $sale->serialnumber }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($sale->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$sale->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                        <td></td>
                    @else
                        <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td>
                    @endif
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td>SCV</td>
                    <td>038</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
            @if ($sale->icbper > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($sale->date)) }}</td>
                    <td>{{ date('m', strtotime($sale->date)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ab->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @else
                        @if ($sale->detailo->count() == 1)
                            <td>Venta de {{ $sale->detailo[0]->product->description }}</td>
                        @elseif ($is == 1 && $ip == 0)
                            <td>Venta de Servicios</td>
                        @elseif($ip == 1 && $is == 0)
                            <td>Venta de Productos</td>
                        @elseif($is == 1 && $ip == 1)
                            <td>Venta de Productos y/o Servicios</td>
                        @else
                            <td>Venta de Productos</td>
                        @endif
                    @endif
                    <td>0.00</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else
                        <td>{{ $sale->icbper }}</td>
                    @endif
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                    <td>{{ $sale->serialnumber }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($sale->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$sale->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                        <td></td>
                    @else
                        <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td>
                    @endif
                    <td>000</td>
                    <td></td>
                    <td></td>
                    <td>SCV</td>
                    <td>038</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
            @foreach ($sale->detailo as $detail)    
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($sale->date)) }}</td>
                    <td>{{ date('m', strtotime($sale->date)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $detail->product->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>ANULADA</td>
                    @else
                        <td>{{ $detail->product->description }}</td>
                    @endif
                    <td>0.00</td>
                    @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else
{{--                         <td>{{  number_format((float) $detail->price * (float) $detail->quantity, 2) }}</td> --}}
                        @if ($detail->product->type_igv_id == 8 || $detail->product->type_igv_id == 9)
                            <td>{{  number_format($detail->total, 2, '.', '') }}</td>
                        @else
                            <td>{{  number_format($detail->subtotal, 2, '.', '') }}</td>
                        @endif
                    @endif
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td>{{ $detail->product->cost_center_id != null ? $detail->product->centerCost->code : ''}}</td>
                    <td>C</td>
                    <td>{{ $sale->customer->code }}</td>
                    <td>{{ $sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                    <td>{{ $sale->serialnumber }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($sale->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$sale->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO')
                        <td></td>
                    @else
                        <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td>
                    @endif
                    @if($detail->product->type_igv_id == 1)
                        <td>000</td>
                        <td>002</td>
                    @elseif($detail->product->type_igv_id == 9)
                        <td>1</td>
                        <td>999</td>
                    @elseif($detail->product->type_igv_id == 8)
                        <td>1</td>
                        <td>998</td>
                    @else
                        <td>1</td>
                        <td>021</td>
                    @endif
                    <td></td>
                    <td>SCV</td>
                    <td>038</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
            @php
                $setMovements = (int) $movements + 1;
                $setVoucher = (int) $voucher + 1;

                $repeatMovement = 10 - strlen($setMovements);
                $movements = str_repeat('0',($repeatMovement >=0) ? $repeatMovement : 0).$setMovements;

                $repeatVoucher = 6 - strlen($setVoucher);
                $voucher = str_repeat('0',($repeatVoucher >=0) ? $repeatVoucher : 0).$setVoucher;
                $credit = $sale->credit_note;
            @endphp
        @endforeach
        @foreach ($creditnotes as $credit)
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
                <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
                <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
                <td>05</td>
                <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                @if ($credit->sale->condition_payment == 'EFECTIVO' || $credit->sale->condition_payment == 'DEPOSITO EN CUENTA' || $credit->sale->condition_payment == 'TARJETA DE CREDITO' || $credit->sale->condition_payment == 'TARJETA DE DEBITO')
                    <td>1212101</td>
                @else
                    <td>1212102</td>
                @endif
                <td>{{ $countRow++ }}</td>
                <td>NOTA DE CREDITO</td>
                <td>0.00</td>
                <td>{{ $credit->total }}</td>
                <td>000</td>
                <td>000</td>
                <td>000</td>
                <td>{{ $credit->detail[0]->product->cost_center_id != null ? $credit->detail[0]->product->centerCost->code : '' }}</td>
                <td>C</td>
                <td>{{ $credit->customer->code }}</td>
                <td>{{ $credit->type_voucher->code }}</td>
                <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
                <td>{{ $credit->serial_number }}</td>
                @php
                    $repeatCorrelative = 8 - strlen($credit->correlative);
                    $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$credit->correlative;
                @endphp
                <td>{{ $correlative }}</td>
                <td></td>
                <td>000</td>
                <td></td>
                <td>P</td>
                <td>SCV</td>
                <td>038</td>
                <td>{{ $credit->sale->type_voucher->code }}</td>
                <td>{{ date('d/m/Y', strtotime($credit->sale->date)) }}</td>
                <td>{{ $credit->sale->serialnumber }}</td>
                <td>{{ $credit->sale->correlative }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @if ($credit->igv > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ai->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    <td>NOTA DE CREDITO</td>
                    {{-- @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else --}}
                    <td>{{ $credit->igv }}</td>
                    <td>0.00</td>
                    {{-- @endif --}}
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td>C</td>
                    <td>{{ $credit->customer->code }}</td>
                    <td>{{ $credit->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ $credit->serial_number }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($credit->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$credit->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    {{-- @if ($credit->sale->condition_payment == 'EFECTIVO' || $credit->sale->condition_payment == 'DEPOSITO EN CUENTA' || $credit->sale->condition_payment == 'TARJETA DE CREDITO' || $credit->sale->condition_payment == 'TARJETA DE DEBITO') --}}
                    <td></td>
                    {{-- @else --}}
                    {{-- <td>{{ date('d/m/Y', strtotime($credit->due_date)) }}</td> --}}
                    {{-- @endif --}}
                    <td>000</td>
                    <td></td>
                    <td>P</td>
                    <td>SCV</td>
                    <td>038</td>
                    <td>{{ $credit->sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->sale->date)) }}</td>
                    <td>{{ $credit->sale->serialnumber }}</td>
                    <td>{{ $credit->sale->correlative }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
            @if ($credit->recharge > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ai->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    <td>NOTA DE CREDITO</td>
                    {{-- @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else --}}
                    <td>{{ $credit->recharge }}</td>
                    <td>0.00</td>
                    {{-- @endif --}}
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td>C</td>
                    <td>{{ $credit->customer->code }}</td>
                    <td>{{ $credit->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ $credit->serial_number }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($credit->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$credit->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    {{-- @if ($credit->sale->condition_payment == 'EFECTIVO' || $credit->sale->condition_payment == 'DEPOSITO EN CUENTA' || $credit->sale->condition_payment == 'TARJETA DE CREDITO' || $credit->sale->condition_payment == 'TARJETA DE DEBITO') --}}
                    <td></td>
                    {{-- @else --}}
                    {{-- <td>{{ date('d/m/Y', strtotime($credit->due_date)) }}</td> --}}
                    {{-- @endif --}}
                    <td>000</td>
                    <td></td>
                    <td>P</td>
                    <td>SCV</td>
                    <td>038</td>
                    <td>{{ $credit->sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->sale->date)) }}</td>
                    <td>{{ $credit->sale->serialnumber }}</td>
                    <td>{{ $credit->sale->correlative }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
            @if ($credit->icbper > 0.00)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($sale->date)) }}{{ $voucher }}</td>
                    <td>{{ $ab->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    <td>NOTA DE CREDITO</td>
                    {{-- @if($sale->low_communication_id != null || $sale->status == 0)
                        <td>0.00</td>
                    @else --}}
                    <td>{{ $credit->icbper }}</td>
                    <td>0.00</td>
                    {{-- @endif --}}
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td></td>
                    <td>C</td>
                    <td>{{ $credit->customer->code }}</td>
                    <td>{{ $credit->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ $credit->serial_number }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($credit->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$credit->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    {{-- @if ($credit->sale->condition_payment == 'EFECTIVO' || $credit->sale->condition_payment == 'DEPOSITO EN CUENTA' || $credit->sale->condition_payment == 'TARJETA DE CREDITO' || $credit->sale->condition_payment == 'TARJETA DE DEBITO') --}}
                    <td></td>
                    {{-- @else --}}
                    {{-- <td>{{ date('d/m/Y', strtotime($credit->due_date)) }}</td> --}}
                    {{-- @endif --}}
                    <td>000</td>
                    <td></td>
                    <td>P</td>
                    <td>SCV</td>
                    <td>038</td>
                    <td>{{ $credit->sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->sale->date)) }}</td>
                    <td>{{ $credit->sale->serialnumber }}</td>
                    <td>{{ $credit->sale->correlative }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif

            {{--Detalle--}}
            @foreach ($credit->detail as $detail)
                <tr>
                    <td>{{ $movements }}</td>
                    <td>{{ date('Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ date('m', strtotime($credit->date_issue)) }}</td>
                    <td>05</td>
                    <td>05{{ date('m', strtotime($credit->date_issue)) }}{{ $voucher }}</td>
                    <td>{{ $detail->product->account }}</td>
                    <td>{{ $countRow++ }}</td>
                    <td>NOTA DE CREDITO</td>
                    {{-- <td>{{  number_format((float)$detail->price * (float) $detail->quantity, 2) }}</td> --}}
                    @if ($detail->product->type_igv_id == 8 || $detail->product->type_igv_id == 9)
                        <td>{{  number_format($detail->total, 2, '.', '') }}</td>
                    @else
                        <td>{{  number_format($detail->subtotal, 2, '.', '') }}</td>
                    @endif
                    <td>0.00</td>
                    <td>000</td>
                    <td>000</td>
                    <td>000</td>
                    <td>{{ $detail->product->cost_center_id != null ? $detail->product->centerCost->code : ''}}</td>
                    <td>C</td>
                    <td>{{ $credit->customer->code }}</td>
                    <td>{{ $credit->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->date_issue)) }}</td>
                    <td>{{ $credit->serial_number }}</td>
                    @php
                        $repeatCorrelative = 8 - strlen($credit->correlative);
                        $correlative = str_repeat('0',($repeatCorrelative >=0) ? $repeatCorrelative : 0).$credit->correlative;
                    @endphp
                    <td>{{ $correlative }}</td>
                    {{-- @if ($sale->condition_payment == 'EFECTIVO' || $sale->condition_payment == 'DEPOSITO EN CUENTA' || $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO') --}}
                    <td></td>
                    {{-- @else --}}
                    {{-- <td>{{ date('d/m/Y', strtotime($sale->expiration)) }}</td> --}}
                    {{-- @endif --}}
                    @if($detail->product->type_igv_id == 1)
                        <td>000</td>
                        <td>002</td>
                    @elseif($detail->product->type_igv_id == 9)
                        <td>1</td>
                        <td>999</td>
                    @elseif($detail->product->type_igv_id == 8)
                        <td>1</td>
                        <td>998</td>
                    @else
                        <td>1</td>
                        <td>021</td>
                    @endif
                    <td></td>
                    <td>SCV</td>
                    <td>038</td>
                    <td>{{ $credit->sale->type_voucher->code }}</td>
                    <td>{{ date('d/m/Y', strtotime($credit->sale->date)) }}</td>
                    <td>{{ $credit->sale->serialnumber }}</td>
                    <td>{{ $credit->sale->correlative }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
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