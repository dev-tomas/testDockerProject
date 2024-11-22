<table>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td width="100px"></td>
        <td width="100px"></td>
        <td width="250px"><img src="{{public_path('images') .'/'. $client->logo  }}" height="100"></td>
        <td width="50px">&nbsp;</td>
        <td width="250px">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    @php
        $cont = 0;
    @endphp
    @while($cont < count($data))
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="center"><strong><a href="sheet://'{{ $data[$cont]['category'] }}'!A1">{{ $data[$cont]['category'] }}</a></strong></td>
            <td>&nbsp;</td>
            @php
                $cont++;
            @endphp
            @if(isset($data[$cont]['category']))
                <td align="center"><strong><a href="sheet://'{{ $data[$cont]['category'] }}'!A1">{{ $data[$cont]['category'] }}</a></strong></td>
            @else
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        @php
            $cont++;
        @endphp
    @endwhile
</table>