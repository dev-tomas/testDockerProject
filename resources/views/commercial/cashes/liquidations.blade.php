@extends('layouts.azia')
@section('css')
    <style>
    .table-responsive,
.dataTables_scrollBody {
    overflow: visible !important;
}

.table-responsive-disabled .dataTables_scrollBody {
    overflow: hidden !important;
}</style>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">CIERRE DE CAJA</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('cashes.index') }}" class="btn btn-rounded btn-primary-custom">Ver Cajas</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id='frm_movements' style="width: 100%">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Buscar por Caja</label>
                                    <select name="cash" id="cash" class="form-control">
                                        <option value="">Todas</option>
                                        @foreach ($cashes as $cash)
                                            <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Filtrar por Fechas:</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-hover mg-b-0" id="tbl_data"  style="width: 100%;">
                                <thead>
                                    <th>CAJA</th>
                                    <th>VENDEDOR</th>
                                    <th>FECHA DE CIERRE</th>
                                    <th>TOTAL</th>
                                    <th>*</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                            <button class="btn btn-danger-custom pull-right" id="btnClose">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <iframe frameborder="0" width="100%;" height="700px;" id="frame_pdf">

                            </iframe>
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
            "order": [[ 4, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial/cashes/cierres/dt',
                'type' : 'get',
                'data': function(d) {
                    d.cash = $('#cash').val();
                    let rangeDates = $('#filter_date').val();
                    var arrayDates = rangeDates.split(" ");
                    var dateSpecificOne =  arrayDates[0].split("/");
                    var dateSpecificTwo =  arrayDates[2].split("/");

                    d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                    d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                }
            },
            'columns': [
                {
                    data: 'cash',
                },
                {
                    data: 'user',
                },
                {
                    data: 'date'
                },
                {
                    data: 'total'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find("td:eq('4')").html('<button type="button" class="btn btn-danger btn-sm pdf" id="'+aData['id']+'">PDF</button>');
            }
        });

        $('#cash').change(function() {
            tbl_data.ajax.reload();
        })
        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
        })

        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        })

        $('body').on('click', '.pdf', function() {
            let id = $(this).attr('id');
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial/cashes/cierres/pdf/' + id);
            $('#mdl_preview').modal('show');
        });

        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
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
                "daysOfWeek": [
                    "Dom",
                    "Lu",
                    "Mar",
                    "Mie",
                    "Ju",
                    "Vi",
                    "Sab",
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Deciembre"
                ],
                "firstDay": 0
            },
            "startDate": moment().subtract(7, 'days'),
            "endDate": moment().add(1, 'days'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@stop
