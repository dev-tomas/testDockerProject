@extends('layouts.azia')
@section('css')
    <style>.edit,.delete,.convert,.send {display: none;}</style>
    @can('cotizaciones.edit')
        <style>.edit{display: block;}</style>
    @endcan
    @can('cotizaciones.delete')
        <style>.delete{display: block;}</style>
    @endcan
    @can('cotizaciones.convert')
        <style>.convert{display: block;}</style>
    @endcan
    @can('cotizaciones.send')
        <style>.send{display: block;}</style>
    @endcan
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
                            <h3 class="card-title">CAJAS</h3>
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
                                    <select name="filter_cash" id="filter_cash" class="form-control">
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
                            <div class="col-12 col-md-3">
                                <div class="form-group mt-4">
                                    <button class="btn btn-secondary-custom">Ver</button>
                                    <button type="submit" id="kExcel" class="btn btn-secondary-custom">Excel</button>
                                    <button type="submit" id="kPDF" class="btn btn-danger-custom">PDF</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row" id="rowMovement">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-hover mg-b-0"  style="width: 100%;">
                                <thead>
                                    <th width="30px">#</th>
                                    <th>CAJA</th>
                                    <th>MOVIMIENTO</th>
                                    <th>MONTO</th>
                                    <th>OBSERVACION</th>
                                </thead>

                                <tbody id="tbodyCash">

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
        $(document).ready(function() {
            $('#rowMovement').hide();
        });
        $('#frm_movements').submit(function(e) {
            $('#rowMovement').hide();
            e.preventDefault();
            let data = $('#frm_movements').serialize(); 
            $.ajax({
                url: '/commercial/cashes/movements/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    $('#tbodyCash').html('');
                    let tr;
                    let cont = 0;
                    $.each(response, function(i, item) {
                        cont++;
                        let observation = response[i].observation == null ? '-' : response[i].observation;
                        tr = '<tr>';
                        tr += '<td>'+ cont + '</td>';
                        tr += '<td>'+ response[i].cash.name + '</td>';
                        tr += '<td>'+ response[i].movement + '</td>';
                        tr += '<td>'+ response[i].amount + '</td>';
                        tr += '<td>'+ observation + '</td>';
                        tr += '</tr>';
                        $('#tbodyCash').append(tr);
                    });

                    $('#rowMovement').show();
                    console.log(response);
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });
        });

        $('#kExcel').click(function(e) {
            e.preventDefault();
            let data = $('#frm_movements').serialize(); 
            window.open('/commercial/cashes/movements/generate/excel? ' + data, '_blank');
        });
        $('#kPDF').click(function(e) {
            e.preventDefault();
            let data = $('#frm_movements').serialize(); 
            window.open('/commercial/cashes/movements/generate/pdf? ' + data, '_blank');
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
