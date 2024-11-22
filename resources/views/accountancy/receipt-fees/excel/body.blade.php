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
            <th>Asd_cProvCanc</th>
            <th>Asd_cOperaTC</th>
            <th>Asd_cTipoMoneda</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
            <tr>
                <td>{{ $d['movimiento'] }}</td>
                <td>{{ $d['anio'] }}</td>
                <td>{{ $d['periodo'] }}</td>
                <td>{{ $d['tipo_libro'] }}</td>
                <td>{{ $d['voucher'] }}</td>
                <td>{{ $d['cuenta'] }}</td>
                <td>{{ $d['item'] }}</td>
                <td>{{ $d['glosa'] }}</td>
                <td>{{ $d['debe'] }}</td>
                <td>{{ $d['haber'] }}</td>
                <td>{{ $d['tc'] }}</td>
                <td>{{ $d['debe_ext'] }}</td>
                <td>{{ $d['haber_ext'] }}</td>
                <td>{{ $d['cos_ccodigo'] }}</td>
                <td>{{ $d['tipo_entidad'] }}</td>
                <td>{{ $d['cod_entidad'] }}</td>
                <td>{{ $d['as_tipo_doc'] }}</td>
                <td>{{ $d['fecha'] }}</td>
                <td>{{ $d['serie_doc'] }}</td>
                <td>{{ $d['num_doc'] }}</td>
                <td>{{ $d['fec_ven'] }}</td>
                <td>{{ $d['prov_canc'] }}</td>
                <td>{{ $d['opera_tc'] }}</td>
                <td>{{ $d['tipo_moneda'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>