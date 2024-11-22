@extends('layouts.azia')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Escoger</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table" id="tbl_data">
                            <thead>
                                <th>Nombres</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                                <th>Opciones</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                    <th>Opciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var tbl_data = $("#tbl_data").DataTable({
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': "{!! route('acl.roles.dataTable') !!}",
                'type': 'get'
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' }
            ]
        });
    </script>
@stop