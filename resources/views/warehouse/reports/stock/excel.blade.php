@php
    function eliminar_tildes($cadena)
        {
            //Ahora reemplazamos las letras
            $cadena = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
                array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
                $cadena
            );

            $cadena = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
                array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
                $cadena );

            $cadena = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
                array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
                $cadena );

            $cadena = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
                array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
                $cadena );

            $cadena = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
                array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
                $cadena );

            $cadena = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'),
                array('n', 'N', 'c', 'C'),
                $cadena
            );

            return $cadena;
        }
@endphp
<table>
    <tr>
        <td colspan="4" align="center">
            REPORTE DE STOCK DE PRODUCTOS POR ALMACEN
        </td>
        <td><strong>[{{ date('d-m-Y H:i:s') }}]</strong></td>
    </tr>
    <tr>
        <td>PRODUCTO</td>
        <td>CODIGO</td>
        @foreach($warehouses as $warehouse)
            <td>{{ Str::upper($warehouse->description) }}</td>
        @endforeach
        <td>TOTAL STOCK</td>
    </tr>
    @php
        $cont = 0;
    @endphp
    @foreach($data as $item)
        <tr>
            <td>{{ $item['product'] }}</td>
            <td align="center">{{ $item['code'] }}</td>
            @foreach($warehouses as $warehouse)
                <td align="center">{{ $data[$cont][str_replace(' ', '', strtolower(eliminar_tildes($warehouse->description)))] }} <br> {{ $item['operation'] }}</td>
            @endforeach
            <td align="center">{{ $item['total'] }}</td>
        </tr>
        @php
            $cont++;
        @endphp
    @endforeach
</table>