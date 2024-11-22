@extends('layouts.azia')
@section('css')
    <style>.prepare{display: block;}</style>
    @can('correlativos.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div class="container-fluid">
        <div class="col-12">
            @can('correlativos.create')
                <form method="post" role="form" data-toggle="validator" id="frm_correlative">
                    <input type="hidden" id="correlative_id" name="correlative_id">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">
                                Correlativos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="typevoucher">Tipo de Comprobante *</label>
                                        <select name="typevoucher" id="typevoucher" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            @if($typevouchers->count() > 0)
                                                @foreach($typevouchers as $tp)
                                                    <option value="{{$tp->id}}">{{$tp->description}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="serialnumber">Serie *</label>
                                    <input type="text" name="serialnumber" id="serialnumber" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="correlative">Correlativo *</label>
                                    <input type="text" name="correlative" id="correlative" class="form-control" required>
                                </div>
                            </div>
                        </div>
    
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-danger-custom" id="btnCancel">
                                        <i class="fa fa-close"></i>
                                        CANCELAR
                                    </button>
                                    <button type="submit" class="btn btn-primary-custom" id="btnSave">
                                        <i class="fa fa-save"></i>
                                        GRABAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endcan

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Cotizaciones</h3>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table id="tbl_data" class="dt-bootstrap4">
                            <thead>
                                <th>TIPO COMPROBANTE</th>
                                <th>SERIE.</th>
                                <th>CORRELATIVO</th>
                                <th>OPCIONES</th>
                            </thead>

                            <tbody>

                            </tbody>

                            <tfoot>
                                <th>
                                    <select name="s_typevoucher" id="s_typevoucher" class="form-control" style="width: 180px;">
                                        @if($typevouchers->count() > 0)
                                                <option value="">Todos</option>
                                            @foreach($typevouchers as $tp)
                                                <option value="{{$tp->id}}">{{$tp->description}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </th>
                                <th>SERIE.</th>
                                <th>CORRELATIVO</th>
                                <th>OPCIONES</th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.dt.correlatives',
                'type' : 'get',
                'data': function(d){
                    d.typevoucher_id = $('#s_typevoucher').val();
                }
            },
            'columns': [
                {
                    data: 'tv_description'
                },
                {
                    data: 'serialnumber'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-secondary-custom btn-sm prepare">';
                button += '<i class="fa fa-edit"></i>';
                button += '</button>';
                $(nRow).find("td:eq(3)").html(button);
            }
        });

        tbl_data.on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#customer_id").val(data['id']);

            $.get('/commercial.correlative.prepare',
                'correlative_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#typevoucher').val(response['id']);
                    $('#serialnumber').val(response['serialnumber']);
                    $('#correlative').val(response['correlative']);
                }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        });

        $('#frm_correlative').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_correlative').serialize();

                $.ajax({
                    url: '/commercial.correlatives.create',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSave').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el correlativo');
                            $('#typevoucher').val('');
                            $('#serialnumber').val('');
                            $('#correlative').val('');
                            $("#tbl_data").DataTable().ajax.reload();
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#btnSave').removeAttr('disabled');
                    }
                });
            }
        });

        $('#s_typevoucher').on('change', function() {
            $("#tbl_data").DataTable().ajax.reload();
        });

        $('#btnCancel').on('click', function() {
            $('#typevoucher').val('');
            $('#serialnumber').val('');
            $('#correlative').val('');
            $('#correlative_id').val('');
        });
    </script>
@stop