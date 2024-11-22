<table>
    <thead>
        <tr>
            <th>FECHA</th>
            <th>FECHA DE VENCIMIENTO</th>
            <th>TIPO</th>
            <th>SERIE</th>
            <th>NUMERO</th>
            <th>DOC. ENTIDAD</th>
            <th>RUC</th>
            <th>DENOMINACION</th>
            <th>MONEDA</th>
            <th>T/C</th>
            <th>GRAVADA</th>
            <th>EXONERADA</th>
            <th>INAFECTA</th>
            <th>ISC</th>
            <th>IGV</th>
            <th>OTROS</th>
            <th>IMPUESTO BOLSAS</th>
            <th>TOTAL</th>
            <th>TOTAL PENDIENTE</th>
            <th>OBSERVACIONES</th>
            <th>ACEPTADO POR SUNAT</th>
            <th>ESTADO SUNAT DESCRIPCION</th>
            <th>SUNAT RESPONSE CODE</th>
            <th>ANULADO</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumTaxed = 0;
            $sumExonerated = 0;
            $sumUnaffected = 0;
            $sumISC = 0;
            $sumIGV = 0;
            $sumOthers = 0;
            $sumICBPER = 0;
            $sumTotal = 0;
            $sumTotalPending = 0;
        @endphp
        @foreach ($sales as $sale)
            @php
                $saleTaxed = $sale->taxed;
                $saleExonerated = $sale->exonerated;
                $saleUnaffected = $sale->unaffected;
                $saleIgv = $sale->igv;
                $saleIcbper = $sale->icbper;
                $saleTotal = $sale->total;
                $saleOthercharge = $sale->othercharge;
                $salePending = ($sale->credito ? $sale->credito->debt : "0.00");

                if ($sale->low_communication_id != null || $sale->credit_note_id != null) {
                    $saleTaxed = 0;
                    $saleExonerated = 0;
                    $saleUnaffected = 0;
                    $saleIgv = 0;
                    $saleIcbper = 0;
                    $saleTotal = 0;
                    $saleOthercharge = 0;
                    $salePending = 0;
                }

                $sumTaxed = $saleTaxed + $sumTaxed;
                $sumExonerated = $saleExonerated + $sumExonerated;
                $sumUnaffected = $saleUnaffected + $sumUnaffected;
                $sumIGV = $saleIgv + $sumIGV;
                $sumOthers = $saleOthercharge + $sumOthers;
                $sumICBPER = $saleIcbper + $sumICBPER;
                $sumTotal = $saleTotal + $sumTotal;
                $sumTotalPending = $salePending + $sumTotalPending;
            @endphp
            <tr>
                <td>{{ $sale->date }}</td>
                <td>{{ $sale->expiration }}</td>
                <td>{{ $sale->type_voucher->code }}</td>
                <td>{{ $sale->serialnumber }}</td>
                <td>{{ $sale->correlative }}</td>
                <td>{{ $sale->customer->document_type->code }}</td>
                <td>{{ $sale->customer->document }}</td>
                <td>{{ $sale->customer->description }}</td>
                <td>{{ $sale->coin->description }}</td>
                <td>{{ $sale->change_type }}</td>
                <td>{{ $saleTaxed }}</td>
                <td>{{ $saleExonerated }}</td>
                <td>{{ $saleUnaffected }}</td>
                <td> {{-- ISC --}}</td>
                <td>{{ $saleIgv }}</td>
                <td>{{ $saleOthercharge }}</td>
                <td>{{ $sale->icbper == null ? '-' : $saleIcbper }}</td>
                <td>{{ $saleTotal }}</td>
                <td>{{ $salePending }}</td>
                <td>{{ $sale->observation }}</td>
                <td>{{ $sale->status_sunat == 1 ? 'SI' : 'NO' }}</td>
                <td>{{ $sale->sunat_code == null ? '-' : $sale->sunat_code->description }}</td>
                <td>{{ $sale->sunat_code == null ? '-' : $sale->sunat_code->code }}</td>
                <td>{{ $sale->low_communication_id != null ? 'SI' : '-' }}</td>
            </tr>
        @endforeach
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $sumTaxed }}</td>
            <td>{{ $sumExonerated }}</td>
            <td>{{ $sumUnaffected }}</td>
            <td></td>
            <td>{{ $sumIGV }}</td>
            <td>{{ $sumOthers }}</td>
            <td>{{ $sumICBPER }}</td>
            <td>{{ $sumTotal }}</td>
            <td>{{ $sumTotalPending }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>