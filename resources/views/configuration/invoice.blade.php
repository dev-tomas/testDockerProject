@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active"><a href="#certificate" data-toggle="pill" class="nav-link" role="tab">Certificado Digital</a></li>
                    <li class="nav-item"><a href="#credential" data-toggle="pill" role="tab" class="nav-link">Credencial SUNAT</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="certificate" role="tabpanel">
                        <form method="post" enctype="multipart/form-data" action="{{route("convertAndCertificate")}}">
                                @csrf
                            <div class="col-12"><br></div>
                            <div class="col-12">
                                @if($exists === true)
                                    <div class="alert alert-warning">
                                        <h5>Aviso!</h5>
                                        Ya tienes un certificado subido que vence: <b>{{Auth::user()->headquarter->client->expiration_certificate}}.</b>
                                        <br>
                                        Si subes otro certificado, reemplazará al anterior.
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Certiticado Digital</label>
                                    <input type="file" class="form-control" name="certificate" id="certificate" required>
                                </div>
                                <div class="form-group">
                                    <label for="password_certificate">Password</label>
                                    <input type="password" class="form-control" name="password_certificate" required>
                                </div>
                                <div class="form-group">
                                    <label for="expiration">Fecha de Vencimiento</label>
                                    <input type="text" class="form-control date" name="expiration" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom">SUBIR CERTIFICADO</button>
                            </div>
                        </form>
                    </div>


                    <div class="tab-pane fade" id="credential" role="tabpanel">
                        <div class="col-12"><br></div>
                        <form method="put" id="frm_sol">
                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <h3>Credencial Sunat</h3>
                                    </div>
                                    <label for="" class="col-1 col-sm-1 control-label">Usuario</label>
                                    <div class="col-11 col-sm-4">
                                        <input type="text" class="form-control" name="user" value="{{Auth::user()->headquarter->client->usuario_sol}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-1 col-sm-1 control-label">Clave</label>
                                    <div class="col-11 col-sm-4">
                                        <input type="text" class="form-control" name="password" value="{{Auth::user()->headquarter->client->clave_sol}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="button btn btn-primary-custom" id="save"><i class="fa fa-save"></i> GRABAR</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" role="tabpanel" id="serial">
3
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        $('.date').datepicker();
        $('#frm_sol').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios.");
            }else {
                e.preventDefault();
                let data = $('#frm_sol').serialize();
                $.ajax({
                    url: '{{route("saveDataSol")}}',
                    type: 'put',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response === true) {
                            toastr.success('Se actualizaron satisfactoriamente los datos.');
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
            }
        });
    </script>


    @if(Session::has('success'))
        <script>
            toastr.success('Se registró correctamente el certificado.');
        </script>
    @endif

    @if(Session::has('error'))
        <script>
            toastr.error('Oucrrió un error cuando intentaba grabar el certificado.');
        </script>
    @endif
@stop
