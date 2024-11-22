<table class="table">
    <thead style="color: #fff; background: #000;">
        <tr>
            <th>#</th>
            <th>CODIGO</th>
            <th>DESCRIPCION</th>
            <th>S. ACTUAL</th>
            <th>COSTO</th>
            <th>T. COSTO</th>
            <th>V. VENTA</th>
            <th>T. VENTA</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalCost = 0;
            $totalPrice = 0;
        @endphp
        @foreach($inventaries as $i)
            @php
                $totalCost = ((float) $i->cost * (float) $i->stock) + $totalCost;
                $totalPrice = ((float) $i->price * (float) $i->stock) + $totalPrice;
            @endphp
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $i->code }}</td>
                <td>{{ $i->product }}</td>
                <td class="center">{{ $i->stock }}</td>
                <td class="center">{{ $i->cost }}</td>
                <td class="center">{{ number_format(((float) $i->cost * (float) $i->stock),2,",",".") }}</td>
                <td class="center">{{ number_format(((float) $i->price),2,",",".") }}</td>
                <td class="center">{{ number_format(((float) $i->price * (float) $i->stock),2,",",".") }}</td>
            </tr>
        @endforeach
        <tr><td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td></tr>
        <tr>
            <td></td>
            <td colspan="2"><strong>TOTAL</strong></td>
            <td></td>
            <td></td>
            <td class="center">{{ number_format($totalCost,2,",",".") }}</td>
            <td></td>
            <td class="center">{{ number_format("$totalPrice",2,",",".") }}</td>
        </tr>
    </tbody>
</table>