@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <div class="row">
                        <div class="col-12">
                            <h2>Nuevo Role</h2>
                        </div>
                        <div class="col-12">
                            <p>
                                {{-- La serie debe empezar con la letra F para FACTURAS y NOTAS asociadas,
                                B para BOLETAS DE VENTAS y sus NOTAS asociadas, R para comprobantes
                                de RETENCIÓN, P para comprobantes de PERCEPCIÓN. --}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'roles.store']) !!}
                        @include('roles.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        $('.delete_correlative').click(function() {
            let row = $(this).parent().parent().parent();
            let id = row.find('.correlative_id').val();
            $.ajax({
                url: '{{route("deleteCorrelative")}}',
                type: 'get',
                data: {
                    'correlative_id': id
                },
                dataType: 'json',
                success: function(response) {
                    if(response === true) {
                        row.remove();
                        toastr.success('Se eliminó correctamente.');
                    }
                }
            });
        });
    </script>
@stop
