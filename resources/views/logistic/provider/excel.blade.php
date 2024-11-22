<table>
    <thead>
        <tr>
            <th style="width: 200px; max-width: 200px;">TIPO DE DOCUMENTO <br style="mso-data-placement:same-cell;" />
                6 = RUC<br style="mso-data-placement:same-cell;" />
                1 = DNI<br style="mso-data-placement:same-cell;" />
                - = VARIOS - VENTAS MENORES A S/.700.00 Y OTROS<br style="mso-data-placement:same-cell;" />
                4 = CARNET DE EXTRANJERÍA<br style="mso-data-placement:same-cell;" />
                7 = PASAPORTE<br style="mso-data-placement:same-cell;" />
                8 = VARIOS<br style="mso-data-placement:same-cell;" />
                A = CÉDULA DIPLOMATICA DE IDENTIDAD<br style="mso-data-placement:same-cell;" />
                0 = NO DOMICILIADO, <br style="mso-data-placement:same-cell;" /> SIN RUC (EXPORTACIÓN)</th>
            <th>NUMERO</th>
            <th>TIPO</th>
            <th>RAZÓN SOCIAL</th>
            <th>RAZÓN COMERCIAL</th>
            <th>DIRECCIÓN</th>
            <th>CORREO 1</th>
            <th>CORREO 2</th>
            <th>TELÉFONO</th>
            <th>DETRACCIÓN</th>
            <th>CÓDIGO</th>
            <th>CONTACTO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
            <tr>
                <td>{{ $customer->document_type->code }}</td>
                <td>{{ $customer->document }}</td>
                <td>P</td>
                <td>{{ $customer->description }}</td>
                <td>{{ $customer->tradename }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->secondary_email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->detraction }}</td>
                <td>{{ $customer->code }}</td>
                <td>{{ $customer->contact }}</td>
            </tr>
        @endforeach
    </tbody>
</table>