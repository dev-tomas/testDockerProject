@extends('layouts.azia-unlogin')
@section('content')
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom"></nav>
        <div class="content-wrapper">
            @if(config('adminlte.layout') == 'top-nav')
                <div class="container">
            @endif
                <section class="content">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="text-center">
                                <b>SUBIR COTIZACIÓN</b>
                            </h3>
                            <div class="text-center">
                                <small>Introduce tu RUC como proveedor y sube tu cotización en formato PDF</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-6">
                                    <fieldset>
                                        <form id="frm_send_providerQuotation" action="{{ route('store.quotation') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="ruc" value="{{ $ruc }}">
                                            <input type="hidden" name="signed" value="{{ $signed }}">
                                            <input type="hidden" name="requirement" value="{{ $requirement }}">
                                            <div class="form-group">
                                                <label>RUC Proveedor</label>
                                                <input type="text" name="provider" id="provider" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Subir Cotización</label>
                                                <input type="file" name="filed" id="filed" class="form-control" accept=".pdf" required>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary-custom">Enviar</button>
                                            </div>
                                        </form>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @if(config('adminlte.layout') == 'top-nav')
            </div>
            @endif
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
@stop
@section('script_admin')
    {{-- <script>
        $("#frm_send_providerQuotation").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe de completar todos los campos.');
            } else {
                e.preventDefault();
                // let data = $('#frm_send_providerQuotation').serialize();
                let formData = new FormData();
                formData.append('filed', $("#filed")[0].files[0]); 

                $.ajax({
                    url: '/logistic/purchase/quotation/set/store?ruc=' + '{{ $ruc }}' + '&provider=' + $("#provider").val() + '&signed='+'{{ $signed }}',
                    type: 'post',
                    data: formData + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    success: function(response) {
                        if(response == true) {
                            toastr.success('Se envió satisfactoriamente su cotizacion');
                            setTimeout(function(){
                                cerrar();
                            }, 8000);
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            }
        });

        function cerrar() {        
            window.close();
        }
    </script> --}}
@stop