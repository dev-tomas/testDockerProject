@extends('layouts.azia')
@section('css')
    <style>
        .hide {display: none}.show{display: block;}
    </style>
@stop
@section('content')
    <div class="row" style="padding: 2em 0; overflow: hidden;">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2>REPORTE DE COMPRAS</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row"></div>
                    <div class="row">
                        <div class="col-12">
                            {{-- <h4 class="text-center"><strong>Seleccione un Rango de Fechas.</strong></h4>
                            <p class="text-center">Selecciona un rango de fechas para mostrar todos los bonos de incio que se hayan creado en esas fechas, recuerda. No olvides seleccionar bien la fecha de cierre.</p> --}}
                            <form id="generateReport" method="POST">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Local</label>
                                                @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                                                    <select name="headquarter" id="headquarter"  class="form-control">
                                                        <option value="" selected>Todos</option>
                                                        @foreach ($headquarters as $h)
                                                            <option value="{{ $h->id }}" >{{ $h->description }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select name="headquarter" id="headquarter"  class="form-control" disabled>
                                                        @foreach ($headquarters as $h)
                                                            <option value="{{ $h->id }}" {{ auth()->user()->headquarter_id == $h->id ? 'selected' : '' }}>{{ $h->description }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label for="">Rango de Fechas</label>
                                                <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Proveedores</label>
                                                <select name="provider" id="provider"  class="form-control">
                                                    <option value="">Todos los Proveedores</option>
                                                    @foreach ($providers as $p)
                                                        <option value="{{ $p->id }}">{{ $p->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Producto</label>
                                                <select name="product" id="product" class="form-control">
                                                    <option value="">Todos</option>
                                                    @foreach ($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Filtro por Estado</label>
                                                <select name="filter_status" id="filter_status" class="form-control">
                                                    <option value="1">Todo los Estados</option>
                                                    <option value="2">Pendientes</option>
                                                    <option value="3">Pagados</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label for="condition">Forma de pago</label>
                                                <select name="condition" id="condition" class="form-control">
                                                    <option value="">TODAS LAS FORMAS DE PAGO</option>
                                                    <option value="EFECTIVO">EFECTIVO</option>
                                                    <option value="DEPOSITO EN CUENTA">DEPOSITO EN CUENTA</option>
                                                    <option value="CREDITO">CREDITO</option>
                                                    <option value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                                    <option value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                {{-- <div class="row my-4">
                                    <div class="col-12">
                                        <button class="btn btn-primary-custom" type="button" id="btnOA">Opciones Avanzadas</button>
                                    </div>
                                </div> --}}
                                <div class="row mb-2 mt-5">
                                    {{-- <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <button type="button" id="generate" class="btn btn-block btn-dark-custom">Generar Gráfico</button>
                                        </div>
                                    </div> --}}
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <button type="button" id="generateTable" class="btn btn-block btn-gray-custom">Generar Tabla</button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <button type="button" id="generatePDF" class="btn btn-block btn-danger-custom">PDF</button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <button type="button" id="generateExcel" class="btn btn-block btn-secondary-custom">Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer"></div>

            <div class="row" id="tableReport">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">FECHA</th>
                                            <th scope="col">LOCAL</th>
                                            <th scope="col">DOCUMENTO</th>
                                            <th scope="col">PROVEEDOR</th>
                                            <th scope="col">PRODUCTO</th>
                                            <th scope="col">TOTAL</th>
                                            <th scope="col">TOTAL PENDIENTE</th>
                                          </tr>
                                    </thead>
                                    <tbody id="rTableBody">
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
    <script src="{{asset('js/smartresize.js')}}"></script>
    <script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    <script>

        $('#btnOA').click(function() {
            if (!$('#oa').hasClass("show")) {
                $('#oa').removeClass('hide');
                $('#oa').addClass('show');
            } else {
                $('#oa').removeClass('show');
                $('#oa').addClass('hide');
                $('#product').val('');
            }
        });

        $('#generateExcel').click(function(e) {
            e.preventDefault();
            let data = $('#generateReport').serialize(); 
            window.open('/logistic.reports.purchases.generate.excel? ' + data, '_blank');
        });
        $('#generatePDF').click(function(e) {
            e.preventDefault();
            let data = $('#generateReport').serialize(); 
            window.open('/logistic.reports.purchases.generate.pdf? ' + data, '_blank');
        });
        

        $(function () {
            let fechas, totales;

            $('#generateTable').click(function() {
               let data = $('#generateReport').serialize(); 

               $.ajax({
                    url: '/logistic.report.purchases.get',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        $('#rTableBody').html('');
                        let tr;
                        $.each(response.detail, function(i, item) {
                            let tr = `
                                <tr>
                                    <td>${item.date}</td>
                                    <td>${item.headquarter}</td>
                                    <td>${item.document}</td>
                                    <td>${item.provider}</td>
                                    <td>${item.product}</td>
                                    <td>${item.total}</td>
                                    <td>${item.credit}</td>
                                </tr>
                            `;

                            $('#rTableBody').append(tr);
                        });
                        let tr2 = `
                                <tr>
                                    <td colspan="4"></td>
                                    <td><strong>TOTAL</strong></td>
                                    <td>${response.total}</td>
                                    <td>${response.totalCredit}</td>
                                </tr>
                            `;
                        $('#rTableBody').append(tr2);
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            });
        });
    </script>
@endsection