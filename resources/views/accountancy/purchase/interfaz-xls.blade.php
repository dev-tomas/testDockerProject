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
            <th>Asd_d FecDoc</th>
            <th>Asd_cSerieDoc</th>
            <th>Asd_cNumDoc</th>
            <th>Asd_dFecVen</th>
            <th>Asd_cTipoDocRef</th>
            <th>Asd_dFecDocRef</th>
            <th>Asd_cSerieDocRef</th>
            <th>Asd_cNumDocRef</th>
            <th>Asd_nMontoInafecto</th>
            <th>Asd_cBaseImp</th>
            <th>Asd_cRetencion</th>
            <th>Asd_dFechaSpot</th>
            <th>Asd_cNumSpot</th>
            <th>Asd_cProvCanc</th>
            <th>Asd_cOperaTC</th>
            <th>Asd_cTipoMoneda</th>
            <th>Asd_cComprobante</th>
            <th>Id_Exoneracion</th>
            <th>Id_Tipo_Renta</th>
            <th>Id_Modalidad</th>
            <th>Id_Aduana</th>
            <th>Id_Clasific_Servicio</th>
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
                <td>{{ $row['account'] }}</td>
                <td>{{ $row['item'] }}</td>
                <td>{{ $row['glosa'] }}</td>
                <td>{{ $row['debe_soles'] }}</td> {{-- Gravada --}}
                <td>{{ $row['haber_soles'] }}</td>
                <td>{{ $row['tipo_cambio'] }}</td>
                <td>{{ $row['debe_dolares'] }}</td>
                <td>{{ $row['haber_dolares'] }}</td>
                <td>{{ $row['cos_codigo'] }}</td> {{-- centro de costo --}}
                <td>{{ $row['tipo_entidad'] }}</td>
                <td>{{ $row['cod_entidad'] }}</td>
                <td>{{ $row['doc_type'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['serie'] }}</td>
                <td>{{ $row['correlative'] }}</td>
                <td>{{ $row['expiration'] }}</td>
                <td>{{ $row['doc_ref_type'] }}</td>
                <td>{{ $row['doc_ref_date'] }}</td>
                <td>{{ $row['doc_ref_serie'] }}</td>
                <td>{{ $row['doc_ref_correlative'] }}</td>
                <td>{{ $row['mount_unaffected'] }}</td>
                <td>{{ $row['base_imp'] }}</td>
                <td>{{ $row['retention'] }}</td>
                <td>{{ $row['date_spot'] }}</td>
                <td>{{ $row['num_spot'] }}</td>
                <td>{{ $row['prov_canc'] }}</td>
                <td>{{ $row['opera_tc'] }}</td>
                <td>{{ $row['coin'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
