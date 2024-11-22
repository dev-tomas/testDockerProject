{{-- {{ dd($products->product) }} --}}
<table>
    <thead style="color: #fff; background: #000;">
        <tr>
            <th>CODIGO</th>
            <th>CODIGO INTERNO</th>
            <th>DESCRIPCION</th>
            <th>MONEDA</th>
            <th>COSTO</th>
            @foreach($pricesList as $pl)
                <th>{{ $pl->description }}</th>
            @endforeach
            <th>UNIDAD</th>
            <th>STOCK</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
    @foreach($products as $p)
        <tr>
            <td>{{ $p->product->code }}</td>
            <td>{{ $p->product->internalcode }}</td>
            <td>{{ $p->product->description }}</td>
            <td>{{ $p->product->coin->description }}</td>
            <td>{{ $p->product->cost }}</td>
            @foreach($pricesList as $pl)
                @if(isset($p->product->product_price_list[$loop->index]))
                    <td>{{ $p->product->product_price_list[$loop->index]->price }}</td>
                @else
                    <td></td>
                @endif
            @endforeach
            <td>{{ $p->product->ot->description }}</td>
            <td>{{ $p->stock }}</td>
            <td>{{ $p->product->status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>