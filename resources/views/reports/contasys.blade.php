@extends('layouts.azia')

@section('content')
    <div class="container-fluid">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Ventas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="since">Desde</label>
                                <input type="text" class="form-control datepicker-2" id="since" name="since" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="until">Hasta</label>
                                <input type="text" class="form-control datepicker-2" id="until" name="until" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-3" style="display: none;">
                            <div class="form-group">
                                <label for="voucher_type">Tipo de Documento</label>
                                <select name="document_type" id="voucher_type" class="form-control">
                                    <option value="0">Todos</option>
                                    <option value="1">FACTURA</option>
                                    <option value="2">BOLETA</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <button type="button" id="filter" class="btn btn-primary-custom">FILTRAR DATOS</button>
                            </div>

                            <div class="form-group pull-right">
                                <button class="btn btn-secondary-custom" id="download"><i class="fa fa-download"></i></button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="table">
                                <table id="tbl_data" class="dt-bootstrap4">
                                    <thead>
                                    <th>FECHA</th>
                                    <th>SERIE.</th>
                                    <th>NUM.</th>
                                    <th>T. DOC.</th>
                                    <th>DENOMINACIÓN</th>
                                    <th>TOTAL ONEROSA</th>
                                    </thead>

                                    <tbody>

                                    </tbody>
                                </table>
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
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': '/commercial/dt/sales/report',
                'type' : 'get',
                'data': function(d) {
                    d.since = $('#since').val();
                    d.until = $('#until').val();
                    d.voucher_type = $('#voucher_type').val();
                }
            },
            'columns': [
                {
                    data: 'date'
                },
                {
                    data: 'serialnumber'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'tp_description'
                },
                {
                    data: 'c_description'
                },
                {
                    data: 'total'
                },
            ],
        });

        $('#filter').click(function() {
            tbl_data.ajax.reload();
        });

        $('#download').click(function() {
            let until = 0;
            let since = 0;
            if($('#since').val() != 0) {
                since = $('#since').val();
            }

            if($('#until').val() != 0) {
                until = $('#until').val();
            }
            window.open('/report/sales/download/' + since + '/' + until + '/' + $('#voucher_type').val(), '_blank')
        });

        $('.datepicker-2').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
    </script>
@stop
