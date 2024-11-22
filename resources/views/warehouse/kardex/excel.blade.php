<table>
    <tbody>
        <tr>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">FECHA</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">TIPO</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">SERIE</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center">NÚMERO</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" class="text-center">ENTRADAS</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" class="text-center">SALIDA</td>
            <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;" align="center" class="text-center">SALDO</td>
        </tr>
        @foreach ($kardexs as $item)
            <tr>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['date'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['type_document'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['document_serie'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['document_correlative'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['entry'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['output'] }}</td>
                <td style="border: 1px solid #000000;border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; line-height: 15;">{{ $item['balance'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>