@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">Reporte Diario</h3>
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
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Filtro por Fechas</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
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
                                                <th>TIPO DOC.</th>
                                                <th>CLIENTE</th>
                                                <th>MONEDA</th>
                                                <th>EFECTIVO</th>
                                                <th>DEPOSITO</th>
                                                <th>CREDITO</th>
                                                <th>TARJETA</th>
                                                <th>PRODUCTO</th>
                                                <th>U.M</th>
                                                <th>CANT.</th>
                                                <th>P.U</th>
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
                url: '/reporte/diario/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    $('#previewTable tbody').html('');
                    $.each(response.details, function(i, item) {
                        let tr = `
                            <tr>
                                <td rowspan="${item.detail.length}">${item.date}</td>
                                <td rowspan="${item.detail.length}">${item.document}</td>
                                <td rowspan="${item.detail.length}">${item.type_voucher}</td>
                                <td rowspan="${item.detail.length}">${item.customer}</td>
                                <td rowspan="${item.detail.length}">${item.coin}</td>
                                <td rowspan="${item.detail.length}">${item.cash}</td>
                                <td rowspan="${item.detail.length}">${item.deposito}</td>
                                <td rowspan="${item.detail.length}">${item.credito}</td>
                                <td rowspan="${item.detail.length}">${item.tarjeta}</td>
                                <td>${item.detail[0].product}</td>
                                <td>${item.detail[0].operation}</td>
                                <td>${item.detail[0].quantity}</td>
                                <td>${item.detail[0].price}</td>
                            </tr>
                        `;

                        if (item.detail.length > 1) {
                            for (let i = 1; i <= item.detail.length - 1; i++) {
                                tr += `<tr>
                                    <td>${item.detail[i].product}</td>
                                    <td>${item.detail[i].operation}</td>
                                    <td>${item.detail[i].quantity}</td>
                                    <td>${item.detail[i].price}</td>
                                </tr>`;
                            }
                        }

                        $('#previewTable tbody').append(tr);
                    });

                    let trFoot = `
                        <tr>
                            <td colspan="5" align="right"><strong>TOTAL EN SOLES</strong></td>
                            <td>S/ ${response.cash_pen}</td>
                            <td>S/ ${response.deposito_pen}</td>
                            <td>S/ ${response.credito_pen}</td>
                            <td>S/ ${response.tarjeta_pen}</td>
                            <td colspan="2" align="right"><strong>TOTAL CANTIDAD</strong></td>
                            <td>${response.total_quantity}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><strong>TOTAL DÓLARES</strong></td>
                            <td>$ ${response.cash_usd}</td>
                            <td>$ ${response.deposito_usd}</td>
                            <td>$ ${response.tarjeta_usd}</td>
                            <td>$ ${response.credito_usd}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    `;

                    $('#previewTable tbody').append(trFoot);

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

            window.open(`/reporte/diario/excel?${data}`, '_blank');
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
