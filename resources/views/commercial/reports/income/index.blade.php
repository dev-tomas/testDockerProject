@extends('layouts.azia')

@section('css')
    <style>
        .table-gray-dark > td {
            background-color: #595959;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">Reporte de Ingresos</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-secondary-custom float-right" type="button" id="btnexcel">
                                <i class="fa fa-download"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formPreview">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Filtro por Fechas</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Filtro Locales</label>
                                    <select name="headquarter_filter" id="headquarter_filter" class="form-control">
                                        <option value="">Todos los Locales</option>
                                        @foreach($headquarters as $headquarter)
                                            <option value="{{ $headquarter->id }}">{{ $headquarter->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Filtro Cliente</label>
                                    <input type="text" class="form-control" name="customer_filter" id="customer_filter">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Filtro Estado</label>
                                    <select name="status_filter" id="status_filter" class="form-control">
                                        <option value="">Todos los Estados</option>
                                        <option value="1">Cancelados</option>
                                        <option value="0">Pendientes</option>
                                        <option value="2">Anulados</option>
                                        <option value="3">Anulados NC</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary-custom" type="submit" id="generatePreview">Generar</button>
                            </div>
                        </div>
                    </form>

                    <form id="frm_preview">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="previewTable">
                                        <thead>
                                        <tr>
                                            <th>EMISION</th>
                                            <th>NRO. DOC.</th>
                                            <th>TIPO DE PAGO.</th>
                                            <th>CLIENTE</th>
                                            <th>TOTAL</th>
                                            <th>MONEDA</th>
                                            <th colspan="6">ESTADO</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
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
            let data = $('#formPreview').serialize();
            $.ajax({
                url: '/reporte/ingresos/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    $('#previewTable tbody').html('');
                    $.each(response, function(i, item) {
                        let tr = `
                            <tr>
                                <td rowspan="${item.detail.length + 2}">${item.date}</td>
                                <td rowspan="${item.detail.length + 2}">${item.document}</td>
                                <td rowspan="${item.detail.length + 2}">${item.condition}</td>
                                <td rowspan="${item.detail.length + 2}">${item.customer}</td>
                                <td rowspan="${item.detail.length + 2}">${item.total}</td>
                                <td rowspan="${item.detail.length + 2}">${item.coin}</td>
                                <td colspan="6" class="${item.status == 'ANULADO' || item.status == 'ANULADO NC' ? 'text-danger' : item.status == 'PENDIENTE' ? 'text-warning' : 'text-success' }" align="right">${item.status}</td>
                            </tr>
                        `;

                            tr += `<tr class="table-gray-dark text-white">
                                    <td align="center">NRO. DOC</td>
                                    <td align="center">FECHA PAGO</td>
                                    <td align="center">MEDIO PAGO</td>
                                    <td align="center">DEUDA</td>
                                    <td align="center">PAGADO</td>
                                    <td align="center">SALDO</td>
                                </tr>`;
                            $.each(item.detail, function(d, detail) {
                                tr += `<tr>
                                    <td align="center">${detail.doc}</td>
                                    <td align="center">${detail.payment_date}</td>
                                    <td align="center">${detail.payment}</td>
                                    <td align="center">${detail.debt}</td>
                                    <td align="center">${detail.paid}</td>
                                    <td align="center">${detail.balance}</td>
                                </tr>`;
                            });

                        $('#previewTable tbody').append(tr);
                    });

                    $('#previewTable tbody').show();
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });

        $('#btnexcel').click(function(e) {
            e.preventDefault();
            let data = $('#formPreview').serialize();

            window.open(`/reporte/ingresos/excel?${data}`, '_blank');
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
