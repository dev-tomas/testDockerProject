<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<body>
<table border="1" width="100%">
    <thead>
    <tr style="text-align: center;">
        <th rowspan="3" style="text-align: center;">CÓDIGO <br>INTERNO</th>
        <th rowspan="3" style="text-align: center;">
            FECHA DE EMISION <br> DEL COMPROBANTE <br> DE PAGO O <br> DOCUMENTO
        </th>
        <th rowspan="3" style="text-align: center;">
            FECHA DE <br> VENCIMIENTO O <br> FECHA DE PAGO
        </th>
        <th colspan="3" style="text-align: center;">
            COMPROBANTE DE PAGO O DOCUMENTO
        </th>
        <th colspan="3" style="text-align: center;">INFORMACION DEL CLIENTE</th>
        <th rowspan="3" style="text-align: center;">VALOR FACTURADO DE <br> LA EXPORTACION</th>
        <th rowspan="3" style="text-align: center;">BASE IMPONIBLE DE LA <br> OPERACION GRAVADA</th>
        <th colspan="2" style="text-align: center;">IMPORTE TOTAL DE LA OPERACION EXONERADA <br> O INAFECTA</th>
        <th rowspan="3" style="text-align: center;">ISC</th>
        <th rowspan="3" style="text-align: center;">IGV Y/O IPM</th>
        <th rowspan="3" style="text-align: center;">OTROS TRIBUTOS Y <br> CARGOS QUE NO <br> FORMAN PARTE DE LA <br> BASE IMPONIBLE</th>
        <th rowspan="3" style="text-align: center;">IMPORTE TOTAL DEL COMPROBANTE DE PAGO</th>
        <th rowspan="3" style="text-align: center;">TIPO DE CAMBIO</th>
        <th colspan="4" style="text-align: center;">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA</th>
        <th rowspan="3" style="text-align: center;">MONEDA</th>
        <th rowspan="3" style="text-align: center;">ESTADO</th>
        <th rowspan="3" style="text-align: center;">EQUIVALENTE EN <br> DOLARES AMERICANOS</th>
        <th rowspan="3" style="text-align: center;">FECHA <br> VENCIMIENTO</th>
        <th rowspan="3" style="text-align: center;">CONDICION <br> CONTADO/CREDITO</th>
        <th rowspan="3" style="text-align: center;">CODIGO CENTRO DE <br> COSTOS</th>
        <th rowspan="3" style="text-align: center;">CODIGO CENTRO DE <br> COSTOS 2</th>
        <th rowspan="3" style="text-align: center;">CUENTA CONTABLE <br> BASE IMPONIBLE</th>
        <th rowspan="3" style="text-align: center;">CUENTA CONTABLE <br> OTROS TRIBUTOS Y <br> CARGOS</th>
        <th rowspan="3" style="text-align: center;">CUENTA CONTABLE <br> TOTAL</th>
        <th rowspan="3" style="text-align: center;">REGIMEN ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">PORCENTAJE REGIMEN <br> ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">IMPORTE REGIMEN <br> ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">SERIE <br> DOCUMENTO <br> REGIMEN <br> ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">NUMERO DOCUMENTO <br> REGIMEN ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">FECHA DOCUMENTO <br> REGIMEN <br> ESPECIAL</th>
        <th rowspan="3" style="text-align: center;">CODIGO <br> PRESUPUESTO</th>
        <th rowspan="3" style="text-align: center;">PORCENTAJE <br> I.G.V.</th>
        <th rowspan="3" style="text-align: center;">GLOSA</th>
        <th rowspan="3" style="text-align: center;">MEDIO DE PAGO</th>
        <th rowspan="3" style="text-align: center;">CONDICIÓN <br> DE <br> PERCEPCIÓN</th>
        <th rowspan="3" style="text-align: center;">IMPORTE PARA <br> CALCULO RÉGIMEN <br> ESPECIAL</th>
    </tr>
    <tr>
        <th rowspan="2" style="text-align: center;">TIPO</th>
        <th rowspan="2" style="text-align: center;">N° SERIE/N° SERIE MAQ REGIS</th>
        <th rowspan="2" style="text-align: center;">NUMERO</th>
        <th colspan="2" style="text-align: center;">DOCUMENTO DE IDENTIDAD</th>
        <th rowspan="2" style="text-align: center;">APELLIDOS Y NOMBRES, DENOMINACION O RAZON SOCIAL</th>
        <th rowspan="2" style="text-align: center;">EXONERADA</th>
        <th rowspan="2" style="text-align: center;">INAFECTA</th>
        <th rowspan="2" style="text-align: center;">FECHA</th>
        <th rowspan="2" style="text-align: center;">TIPO</th>
        <th rowspan="2" style="text-align: center;">SERIE</th>
        <th rowspan="2" style="text-align: center;">N DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
    </tr>
    <tr>
        <th style="text-align: center;">TIPO</th>
        <th style="text-align: center;">NUMERO</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $s)
        <tr>
            <td style="text-align: center;">{{$s->id}}</td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{date('d-m-Y', strtotime($s->credit_note->date_issue))}}
                @else
                    {{date('d-m-Y', strtotime($s->date))}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{date('d-m-Y', strtotime($s->credit_note->due_date))}}
                @else
                    {{date('d-m-Y', strtotime($s->date))}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->credit_note->type_voucher->code}}
                @else
                    {{$s->type_voucher->code}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->credit_note->serial_number}}
                @else
                    {{$s->serialnumber}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->credit_note->correlative}}
                @else
                    {{$s->correlative}}
                @endif
            </td>
            <td style="text-align: center;">{{$s->customer->document_type->code}}</td>
            <td style="text-align: center;">{{$s->customer->document}}</td>
            <td style="text-align: center;">{{$s->customer->description}}</td>
            <td style="text-align: center;">{{$s->subtotal}}</td>
            <td style="text-align: center;">0</td>
            <td style="text-align: center;">{{$s->exonerated}}</td>
            <td style="text-align: center;">{{$s->unaffected}}</td>
            <td style="text-align: center;">0</td>
            <td style="text-align: center;">{{$s->igv}}</td>
            <td style="text-align: center;">0</td>
            <td style="text-align: center;">{{$s->total}}</td>
            <td style="text-align: center;">
                @if($s->low_communication_id !== null)
                    ANULADO
                @else
                    OPEN
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{date('d-m-Y', strtotime($s->date))}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->type_voucher->code}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->serialnumber}}
                @endif
            </td>
            <td style="text-align: center;">
                @if($s->credit_note != null)
                    {{$s->correlative}}
                @endif
            </td>
            <td style="text-align: center;">PEN</td>
            <td style="text-align: center;">@if(isset($s->sunat_code))
                    @if($s->sunat_code->code == 0)
                        ACEPTADO
                    @else
                        RECHAZADO
                    @endif
                @else
                    NO ENVIADA
                @endif
            </td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
