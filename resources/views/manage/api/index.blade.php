@extends('layouts.azia')
@section('css')
    <style>
        .valid-ruc{ height: 33.9px; position: absolute; top: 25%;right: 10px; display: none; user-select: none;} .valid-ruc.active{display: block;} .cont-ruc {position: relative}
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="card-title">API</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <a class="btn btn-primary-custom" href="{{ route('manage.api.tokens') }}">
                                Tokens
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text" id="filter_date" class="form-control" placeholder="Seleccionar fechas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="150px">Fecha Hora</th>
                                        <th width="150px">IP</th>
                                        <th width="300px">Token</th>
                                        <th>Headers</th>
                                        <th>Payload</th>
                                    </tr>
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
            "order": [[ 1, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/manage/api/dt',
                'type' : 'get',
                'data': function(d) {
                    d.dates = $('#filter_date').val();
                }
            },
            'columns': [
                {
                    data: 'created_at',
                },
                {
                    data: 'host',
                },
                {
                    data: 'token',
                },
                {
                    data: 'headers',
                },
                {
                    data: 'payload',
                },
            ],
        });

        $('#frm_new_token').submit(function(e) {
           e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/manage/api/create',
                data: {
                  client_id: $('#company').val()
                },
                dataType: 'json',
                success: function(response) {
                    toastr.success('Token creado correctamente');
                    $('#newTokenModal').modal('hide');
                    $('#company').val('')
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });
        });

        $('#filter_date').daterangepicker({
            "minYear": 2000,
            "autoApply": false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "Desde",
                "toLabel": "Hasta",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": ["Dom","Lun","Mar","Mie","Ju","Vi","Sab"],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                "firstDay": 0
            },
            "startDate": moment().startOf('month'),
            "endDate": moment().endOf('month'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
        });
    </script>
@stop
