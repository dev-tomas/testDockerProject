<html width="100%">
    <head>
        <meta http-equiv="Content-Type"
              content="text/html; charset=UTF-8">
        <link rel="stylesheet"
              href="{{ asset('vendor/adminlte3/gyo/css/documents/style_sale.css')}}">
        <style>
            * {font-size : 10px;}
            @page { margin-top: 20px; margin-bottom: 0px; }
            body {
                position   : relative;
                text-align : left;
                margin     : 0 18px;
            }

            .legal {
                display   : block;
                margin    : 0;
                padding   : 0;
                color     : red;
                font-size : 2em;
                position  : absolute;
                width     : 100%;
                left      : 0%;
                top       : 25%;
                transform : rotate(-30deg);
            }

            table tbody tr td {
                font-size : 11px;
            }

            .pnp {
                margin-bottom : 5px;
            }

            .pnp p {
                margin  : 0;
                padding : 0;
            }

            .link-scomp {
                color : #000;
            }

            footer {
                position         : fixed;
                bottom           : -50px;
                left             : 0px;
                right            : 0px;
                height           : 50px;
                background-color : #fff;
                color            : #333;
                text-align       : center;
                line-height      : 35px;
                font-size        : .7em;
                z-index          : 9999;
            }
        </style>
    </head>
    <body class="white-bg"
          width="100%">
        <div style="text-align: center"
             class="pnp">
            <p><strong>{{ auth()->user()->headquarter->client->trade_name }}</strong></p>
            <br>
            <p><strong>DOMICILIO FISCAL<strong></p>
            <p>{{ auth()->user()->headquarter->client->address }}</p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp" style="text-align: center">
            <p><strong>CIERRE DE CAJA VENDEDOR</strong></p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <table>
                <tr>
                    <td><strong>Vendedor</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->user->name }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha</strong></td>
                    <td>: </td>
                    <td>{{ date('Y-m-d H:i:s', strtotime($liquidation->created_at)) }}</td>
                </tr>
                <tr>
                    <td><strong>Nro. Transac</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->transaction }}</td>
                </tr>
                <tr>
                    <td><strong>Num. Ticket Boleta Ini.</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->boleta_start }}</td>
                </tr>
                <tr>
                    <td><strong>Num. Ticket Boleta Fin.</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->boleta_end }}</td>
                </tr>
                <tr>
                    <td><strong>Num. Ticket Factura Ini.</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->factura_start }}</td>
                </tr>
                <tr>
                    <td><strong>Num. Ticket Factura Fin.</strong></td>
                    <td>: </td>
                    <td>{{ $liquidation->factura_end }}</td>
                </tr>
            </table>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp" style="text-align: center">
            <p><strong>RESUMEN DE VENTA</strong></p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <table width="100%">
                <tr>
                    <td><strong>Total Ticket Boleta</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->total_boleta }}</td>
                </tr>
                <tr>
                    <td><strong>Total Ticket Factura</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->total_factura }}</td>
                </tr>
                <tr>
                    <td><strong>Total General</strong></td>
                    <td>: </td>
                    <td align="right">{{ number_format((float) $liquidation->total_factura + (float) $liquidation->total_boleta, 2) }}</td>
                </tr>
            </table>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp" style="text-align: center">
            <p><strong>RESUMEN POR TIPOS DE PAGOS</strong></p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <table width="100%">
                <tr>
                    <td><strong>EFECTIVO</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->efectivo }}</td>
                </tr>
                <tr>
                    <td><strong>TARJETA CREDITO</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->tarjeta_credito }}</td>
                </tr>
                <tr>
                    <td><strong>TARJETA DEBITO</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->tarjeta_debito }}</td>
                </tr>
                <tr>
                    <td><strong>DEPOSITO EN CUENTA</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->deposito_cuenta }}</td>
                </tr>
                <tr>
                    <td><strong>EMITIDOS A CREDITO</strong></td>
                    <td>: </td>
                    <td align="right">{{ number_format($liquidation->total_credits, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td><strong>TOTAL GENERAL</strong></td>
                    <td>: </td>
                    <td align="right">{{ number_format((float) $liquidation->tarjeta_credito + (float) $liquidation->tarjeta_debito + (float) $liquidation->efectivo + (float) $liquidation->deposito_cuenta + (float) $liquidation->total_credits, 2) }}</td>
                </tr>
            </table>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp" style="text-align: center">
            <p><strong>CUADRE DE CAJA</strong></p>
        </div>
        <hr style="border-top: 1px solid #000; margin: .5em 0;">
        <div class="pnp">
            <table width="100%">
                <tr>
                    <td><strong>Monto de Apertura</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->opening_amount }}</td>
                </tr>
                <tr>
                    <td><strong>Ventas pagadas en Caja</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->paid_cash }}</td>
                </tr>
                <tr>
                    <td><strong>Ventas pagadas a Crédito</strong></td>
                    <td>: </td>
                    <td align="right">{{ number_format($liquidation->payment_credits,2,'.','') }}</td>
                </tr>
                <tr>
                    <td><strong>Salidas de Caja</strong></td>
                    <td>: </td>
                    <td align="right">({{ $liquidation->output }})</td>
                </tr>
                <tr>
                    <td><strong>Ingresos de Caja</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->entries }}</td>
                </tr>
                <tr>
                    <td><strong>Total General</strong></td>
                    <td>: </td>
                    <td align="right">{{ $liquidation->total }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>