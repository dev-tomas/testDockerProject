@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header text-center">
                    <h3 class="card-title">DETALLE DE COMPRA</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label>Proveedor:</label>
                                <p>{{ $purchase->provider->description }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Fecha</label>
                                <p>{{ date('d-m-Y', strtotime($purchase->date)) }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Moneda</label>
                                <p>{{ $purchase->coin->description }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Tipo Cambio</label>
                                <p>{{ $purchase->exchange_rate }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label> Tipo Documento</label>
                                <p>{{ $purchase->voucher->description }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group" id="shoppingdocument">
                                <label>Documento</label>
                                <p>{{ $purchase->shopping_serie }} - {{ $purchase->shopping_correlative }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Tipo de Compra</label>
                                <p>{{ $purchase->shopping_type == '1' ? 'Inventario' : 'Equipamento' }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Tipo de Registro</label>
                                <p>{{ $purchase->shopping_type == '1' ? 'Físico' : 'Electrónico' }}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Forma de Pago</label>
                                <p>{{ $purchase->payment_type }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table id="tbl_items" class="table">
                                    <thead>
                                    <th width="40%">Producto/Servicio</th>
                                    <th width="10%">Cantidad</th>
                                    <th width="10%">Pre. Uni.</th>
                                    <th width="10%">Val. Uni.</th>
                                    <th width="10%">Subtotal</th>
                                    <th width="10%">IGV</th>
                                    <th width="10%">Total</th>
                                    </thead>
                                    <tbody class="list_productos">
                                        @foreach ($purchase->detail as $d)
                                            <tr>
                                                <td>
                                                    {!! $d->product->description !!}
                                                </td>
                                                <td>
                                                    {{ $d->quantity }}
                                                </td>
                                                <td>
                                                    {{ $d->unit_price }}
                                                </td>
                                                <td>
                                                    {{ $d->unit_value }}
                                                </td>
                                                <td>
                                                    {{ $d->subtotal }}
                                                </td>
                                                <td>
                                                    {{ $d->igv }}
                                                </td>
                                                <td>
                                                    {{ $d->total }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <br>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Descuento</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->discount }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Subtotal</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->subtotal }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Exonerada</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->exonerated }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Inafecta</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->unaffected }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Gravada</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->taxed }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>IGV</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->igv }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Total</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->total }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="/logistic.purchases" class="btn btn-primary-custom pull-right" id="btngoShopping">
                                IR A COMPRAS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        $('#QuoteGenerate').hide();

        $('#payment').change(function (){
            let method = $(this).val();

            if (method === 'CREDITO') {
                $('#QuoteGenerate').show();
            } else {
                $('#QuoteGenerate').hide();
            }
        });
        $('#frm_update_sc').on('submit', function(e) {
            e.preventDefault();
            let data = $('#frm_update_sc').serialize();
            $.ajax({
                url: '/logitic.purchase.update',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    if(response == true) {
                        $('#shoppingdocument').load(' #shoppingdocument');
                        toastr.success('Documento Actualizado');
                    } else {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });
        });
        $('#frm_update_pm').on('submit', function(e) {
            e.preventDefault();
            let data = $('#frm_update_pm').serialize();
            $.ajax({
                url: '/logistic.purchase/update-method-payment',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    if(response == true) {
                        $('#shoppingMethod').load(' #shoppingMethod');
                        toastr.success('Documento Actualizado');
                    } else {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });
        });
    </script>
@stop
