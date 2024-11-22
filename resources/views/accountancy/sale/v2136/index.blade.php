@extends('layouts.azia')
@section('content')
<input type="hidden" id="audocument" value="{{ auth()->user()->headquarter->client->document }}">
<input type="hidden" id="autydocument" value="{{ auth()->user()->headquarter->client->document_type->code }}">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">CONTABILIDAD VENTAS</h3>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                </div>
                <div class="card-body">
                    <form id="formPreview">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Número de Movimiento</label>
                                    <input type="number" id="movement" name="movement" class="form-control" placeholder="Ingresar Entidad">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Filtro por Fechas</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary-custom" type="submit" id="generatePreview">Generar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        $('#formPreview').submit(function(e) {
            e.preventDefault();

            let correlatives = $('#formPreview').serialize();
            
            window.open('/accounting/sale-2-13-6/generate? ' + correlatives, '_blank');

            location.reload();
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
    </script>
@stop
