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
                            <h3 class="card-title">PRODUCTOS/SERVICIOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar producto">
                        </div>
                        <div class="col-2">
                            <select name="type-product" id="type-product" class="form-control">
                                <option value="">Todos</option>
                                <option value="0">Activo Fijo</option>
                                <option value="1">Mercaderia</option>
                                <option value="2">Gasto</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12"><br></div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <form id="frm_table">
                                    <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                        <thead>
                                        <th width="120px">COD. BARRA</th>
                                        <th width="120px">CODIGO</th>
                                        <th width="250px">DESCRIPCIÓN PRODUCTO/SERVICIO</th>
                                        <th width="150px">MERCADERIA VENTA</th>
                                        <th width="150px">MERCADERIA COMPRA</th>
                                        <th width="150px">GASTO COMPRA</th>
                                        <th width="150px">ACTIVO FIJO COMPRA</th>
                                        <th width="200px">CENTRO DE COSTOS</th>
                                        <th width="100px">*</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "searching": false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/accounting.products-dt',
                'type' : 'get',
                'data': function(d) {
                    d.search2 = $('#search').val();
                    d.type = $('#type-product').val();
                }
            },
            'columns': [
                {data: 'code'},
                {data: 'internalcode'},
                {data: 'description'},
                {data: 'account'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
            ],
            'columnDefs': [{
                'targets': [0],
                'orderable':false,
            }],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let center = @json($centerCosts);
                let selCenter = '<option value="">Seleccione un Centro de Costos</option>';
                $.each(center, function(index, value){
                    let selected = aData['cost_center_id'] == value['id'] ? 'selected' : '';
                    selCenter += '<option value="' + value['id'] + '" ' + selected + '>' + value['center'] + '</option>';
                });
                let account = aData['account'] == null ? '' : aData['account'];
                let account_expense = aData['account_expense'] == null ? '' : aData['account_expense'];
                let account_active_fixed = aData['account_active_fixed'] == null ? '' : aData['account_active_fixed'];
                let account_stock_purchase = aData['account_stock_purchase'] == null ? '' : aData['account_stock_purchase'];
                $(nRow).find('td:eq(3)').html('<input name="account" type="text" class="form-control account" value="'+ account +'"/>');
                $(nRow).find('td:eq(4)').html('<input name="account_stock_purchase" type="text" class="form-control account_stock_purchase" value="'+ account_stock_purchase +'"/>');
                $(nRow).find('td:eq(5)').html('<input name="account_expense" type="text" class="form-control account_expense" value="'+ account_expense +'"/>');
                $(nRow).find('td:eq(6)').html('<input name="account_active_fixed" type="text" class="form-control account_active_fixed" value="'+ account_active_fixed +'"/>');
                $(nRow).find('td:eq(7)').html('<select name="centercost" class="form-control center">'+ selCenter +'</select>');
                $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-secondary-custom btn-rounded save" data-product="'+ aData['id'] +'"><i class="fa fa-save"></i>');
            }
        });
        $('#search').on('keyup', function() {
            $('#tbl_data').DataTable().ajax.reload();
        });
        $('#type-product').on('change', function() {
            $('#tbl_data').DataTable().ajax.reload();
        });

        $('body').on('click','.save', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $.ajax({
                type: 'post',
                url: '/accounting.products-store',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: $(this).data('product'),
                    account:$(this).parents('tr').find('input.account').val(),
                    account_expense:$(this).parents('tr').find('input.account_expense').val(),
                    account_active_fixed:$(this).parents('tr').find('input.account_active_fixed').val(),
                    account_stock_purchase:$(this).parents('tr').find('input.account_stock_purchase').val(),
                    centercost:  $(this).parents('tr').find('.center').val(),
                },
                dataType: 'json',
                success: function(response) {
                    if(response == true) {
                        toastr.success('Se agregó satisfactoriamente');
                        $("#tbl_data").DataTable().ajax.reload();
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
