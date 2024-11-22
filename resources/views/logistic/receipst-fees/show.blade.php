@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header text-center">
                    <h3 class="card-title">DETALLE DE RECIBO POR HONORARIOS</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label>Proveedor:</label>
                                <p>{{ $purchase->provider->description }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-xl-1">
                            <div class="form-group">
                                <label>Fecha</label>
                                <p>{{ date('d-m-Y', strtotime($purchase->date)) }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-xl-1">
                            <div class="form-group">
                                <label>Moneda</label>
                                <p>{{ $purchase->coin->description }}</p>
                            </div>
                        </div>
                        @if($purchase->exchange_rate)
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Tipo Cambio</label>
                                    <p>{{ $purchase->exchange_rate }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label> Tipo Documento</label>
                                <p>RECIBO POR HONORARIOS</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Documento</label>
                                <p>{{ $purchase->shopping_serie }} - {{ $purchase->shopping_correlative }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label>Aplica Retención</label>
                                <p>{{ $purchase->has_retention ? 'SI' : 'NO' }}</p>
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
                                    <label>Subtotal</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>{{ $purchase->subtotal }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-right">
                                    <label>Retención (8%)</label>
                                </div>
                                <div class="col-6 text-right">
                                    <p>(-) {{ $purchase->total_retention }}</p>
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
                            <a href="/recibos-honorarios" class="btn btn-primary-custom pull-right" id="btngoShopping">
                                IR A COMPRAS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop