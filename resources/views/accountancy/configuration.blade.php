@extends('layouts.azia')
@section('css')
    <style>.prepare,.delete,.p_enabled,.p_disabled{display: none;}</style>
    @can('pservicios.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
    @can('pservicios.delete')
        <style>.delete{display: inline-block;}</style>
    @endcan
    @can('pservicios.disable')
        <style>.p_enabled,.p_disabled{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">CONFIGURACION CUENTAS</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12"><br></div>
                    </div>
                    <form id="conf">
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta Proveedores</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_providers" value="{{ $ap }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta IGV</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_igv" value="{{ $ai }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta Recargo al Consumo</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_recharge" value="{{ $ac }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta ICBPER</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_icbper" value="{{ $ab }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta Honorarios</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_honorarios" value="{{ $ahonorarios }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta Retencion</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_retencion" value="{{ $aretencion }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta por Regularizar-Ventas</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_rel_sales" value="{{ $arelsales }}"></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-md-2">Cuenta por Regularizar-Compras</div>
                            <div class="col-12 col-md-2"><input type="text" class="form-control" name="cta_rel_purchases" value="{{ $arelpurchases }}"></div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary-custom">Grabar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        $('#conf').submit(function (e) {
            e.preventDefault();

            let data = $(this).serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'post',
                url: '/accounting.configuration.store',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if(response == true) {
                        toastr.success('Se agregó satisfactoriamente');
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
