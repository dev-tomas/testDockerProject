<table>
    <tbody>
        <tr>
            <td colspan="15"><strong>FORMATO 12.1: "REGISTRO DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS- DETALLE DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS"</strong></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr><td><strong>PERIODO: &nbsp; {{  }}</strong></td></tr>
        <tr><td><strong>RUC:</strong> &nbsp; {{ auth()->user()->headquarter->client->document }}</td></tr>
        <tr><td colspan="15"><strong>APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL: </strong> &nbsp; {{ auth()->user()->headquarter->client->trade_name }}</td></tr>
        <tr><td colspan="15"><strong>ESTABLECIMIENTO (1): </strong> &nbsp; {{ $warehouse->headquarter->address }}</td></tr>
        <tr><td><strong>CÓDIGO DE LA EXISTENCIA: </strong> &nbsp; {{ $product->code }}</td></tr>
        <tr><td><strong>TIPO (TABLA 5): </strong> &nbsp; 01</td></tr>
        <tr><td><strong>DESCRIPCIÓN: </strong> &nbsp; {{ $product->description }}</td></tr>
        <tr><td colspan="15"><strong>CÓDIGO DE LA UNIDAD DE MEDIDA (TABLA 6): </strong> &nbsp; {{ $product->ot->code }}</td></tr>
        <!--<tr><td><strong>MÉTODO DE VALUACIÓN: </strong>&nbsp;  1</td></tr>-->
        <tr>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" colspan="4">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO <br> DOCUMENTO INTERNO O SIMILAR</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" rowspan="2">TIPO DE <br> OPERACIÓN</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" rowspan="2" class="text-center">ENTRADAS</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" rowspan="2" class="text-center">SALIDA</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" rowspan="2" class="text-center">SALDO</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">FECHA</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">TIPO</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">SERIE</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">NÚMERO</td>
        </tr>
        @foreach ($kardexs as $item)
            <tr>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['date'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['type_document'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['document_serie'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['document_correlative'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['operation'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['entry'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['output'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['balance'] }}</td>          
        @endforeach
    </tbody>
</table>