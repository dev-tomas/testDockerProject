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
                            <h2>REPORTE DE VENTAS</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-3 col-12" style="height: 150px">
                            <!-- small box -->
                            <div class="small-box" style="padding: 10px; background: #A3C318">
                                <div class="inner text-white">
                                    <h3><span>$</span><span id="TotalDay"></span></h3>
                                    <p>Ventas del Día</p>
                                    <canvas id="salesDay" style="display: block;width: 357px;height: 121px;margin: 0 auto;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-12" style="height: 150px">
                            <!-- small box -->
                            <div class="small-box" style="padding: 10px; background: #838383">
                                <div class="inner text-white">
                                    <h3><span>$</span><span id="TotalMes">874</span></h3>
                                    <p>Venta Acumulada del mes</p>
                                    <canvas id="salesMonth" style="display: block;width: 357px;height: 121px;margin: 0 auto;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5"></div>
                    <div class="row mt-5">
                        <div class="col-12">
                            <h4 class="text-center"><strong>Seleccione un Rango de Fechas.</strong></h4>
                            <p class="text-center">Selecciona un rango de fechas para mostrar todos los bonos de incio que se hayan creado en esas fechas, recuerda. No olvides seleccionar bien la fecha de cierre.</p>
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
{{--                                        <div class="col-12 col-md-2">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label for="">Periodo:</label>--}}
{{--                                                <select name="dateby" id="dateby" class="form-control">--}}
{{--                                                    <option value="1">Días</option>--}}
{{--                                                    <option value="2">Meses</option>--}}
{{--                                                    <option value="3">Años</option>--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label for="">Rango de Fechas</label>
                                                <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Vendedores</label>
                                                <select name="seller" id="seller" class="form-control">
                                                    <option value="" selected>Todos</option>
                                                    @foreach ($sellers as $h)
                                                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label>Clientes</label>
                                                <select name="customer" id="customer" class="form-control">
                                                    <option value="" selected>Todos</option>
                                                    @foreach ($customers as $h)
                                                        <option value="{{ $h->id }}">{{ $h->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label for="">Filtro por Estado</label>
                                                <select id="filter_status" name="status" class="form-control">
                                                    <option value="1">Todo los Estados</option>
                                                    <option value="4">Pagados</option>
                                                    <option value="2">Pendientes</option>
                                                    <option value="3">Pendientes - Vencidos</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                                <label for="">Filtro por Forma de Pago</label>
                                                <select id="filter_payment" name="payment" class="form-control">
                                                    <option value="">Todas las Formas de Pago</option>
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
                                </div>
                                <fieldset class="mt-2 hide" id="oa">
                                    <div class="row">
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
                                    </div>
                                </fieldset> --}}
                                <div class="row mb-2 mt-5">
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <button type="button" id="generate" class="btn btn-block btn-dark-custom">Generar Gráfico</button>
                                        </div>
                                    </div>
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

            <div class="row" id="graphReport">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart" style="position: relative; width:100%; height: 300px">
                                <canvas id="areaChart" style="display: block;" width="764" height="250" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                            <th scope="col">VENDEDOR</th>
                                            <th scope="col">CLIENTE</th>
                                            <th scope="col">PRODUCTO</th>
                                            <th scope="col">VENTA TOTAL</th>
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
        $( document ).ready(function() {
            // $('#oa').hide();
            let fechas, totales, today,month;
            let data = $('#generateReport').serialize(); 
            $('#graphReport').show();
            $('#tableReport').hide();
            $('.btn').addClass('btn-rounded');

            $.ajax({
                url: '/commercial.reports.generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    fechas = response['dates'];
                    totales = response['total'];
                    graph(fechas, totales);
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });

            $.ajax({
                url: '/commercial.reports.index',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    today = response['today'];
                    month = response['month'];
                    $('#TotalDay').text(parseFloat(response['day']).toFixed(2));
                    $('#TotalMes').text(parseFloat(response['mes']).toFixed(2));
                    graphTM(today, month);
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });

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
            window.open('/commercial.reports.generate.excel? ' + data, '_blank');
        });
        $('#generatePDF').click(function(e) {
            e.preventDefault();
            let data = $('#generateReport').serialize(); 
            window.open('/commercial.reports.generate.pdf? ' + data, '_blank');
        });

        function graphTM(today, month) {
            new Chart(document.getElementById("salesDay"), {
                type: 'line',
                data: {
                    labels: today,
                    datasets: [{ 
                        data: today,
                        borderColor: "#FFFFFF",
                        backgroundColor: 'rgba(0,0,0, .20)',
                        fill: 'start',
                        lineTension: 0,           
                    }]
                },
                options: {
                    bezierCurve : true,
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.yLabel;
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                drawBorder: false,
                            },
                            display: false,
                        }],
                        xAxes: [{
                            gridLines: {
                                drawBorder: false,
                            },
                            display: false,
                        }]
                    }
                }
            });
            new Chart(document.getElementById("salesMonth"), {
                type: 'line',
                data: {
                    labels: month,
                    datasets: [{ 
                        data: month,
                        borderColor: "#000000",
                        // backgroundColor: 'rgba(52,152,219, .20)',
                        backgroundColor: 'rgba(0,0,0, .20)',
                        fill: 'start',
                        lineTension: 0,           
                    }]
                },
                options: {
                    bezierCurve : true,
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.yLabel;
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                drawBorder: false,
                            },
                            display: false,
                        }],
                        xAxes: [{
                            gridLines: {
                                drawBorder: false,
                            },
                            display: false,
                        }]
                    }
                }
            });
        }
        
        $(function () {
            let fechas, totales;

            $('#generate').click(function() {
               let data = $('#generateReport').serialize(); 

               $.ajax({
                    url: '/commercial.reports.generate',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    success: function(response) {
                        fechas = response['dates'];
                        totales = response['total'];
                        graph(fechas, totales);

                        $('#graphReport').show();
                        $('#tableReport').hide();
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            });
        });
        function graph(fechas,totales) {
            var ticksStyle = {
                fontColor: '#fff',
                fontStyle: 'bold'
            }
            var mode      = 'index'
            var intersect = true

            var $visitorsChart = $('#areaChart')
            var visitorsChart  = new Chart($visitorsChart, {
                data   : {
                    labels  : fechas,
                    datasets: [{
                        type                : 'line',
                        data                : totales,
                        backgroundColor     : 'transparent',
                        borderColor         : '#A3C318',
                        pointBorderColor    : '#A3C318',
                        pointBackgroundColor: '#A3C318',
                        fill                : false
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips           : {
                        mode     : mode,
                        intersect: intersect
                    },
                    hover              : {
                        mode     : mode,
                        intersect: intersect
                    },
                    legend             : {
                        display: false
                    },
                    scales             : {
                        yAxes: [{
                            gridLines: {
                                display      : true,
                                lineWidth    : '4px',
                                color        : 'rgba(0, 0, 0, .4)',
                                zeroLineColor: 'transparent'
                            },
                        }],
                        xAxes: [{
                            display  : true,
                            gridLines: {
                                display: true,
                                color        : 'rgba(0, 0, 0, .4)',
                                zeroLineColor: 'transparent'
                            },
                            ticks    : ticksStyle
                        }]
                    }
                }
            })
        }

        $(function () {
            let fechas, totales;

            $('#generateTable').click(function() {
               let data = $('#generateReport').serialize(); 

               $.ajax({
                    url: '/commercial.reports.generate.table',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    success: function(response) {
                        $('#rTableBody').html('');
                        let total = 0;
                        let totalPending = 0;

                        $.each(response.totals, function(i, e) {
                            total = parseFloat(total) + parseFloat(e.total);
                            totalPending = parseFloat(totalPending) + parseFloat(e.credit);

                            let tr = `
                                <tr>
                                    <td>${e.date}</td>
                                    <td>${e.headquarter}</td>
                                    <td>${e.user}</td>
                                    <td>${e.customer}</td>
                                    <td>${e.product}</td>
                                    <td>${e.total}</td>
                                    <td>${e.credit}</td>
                                </tr>
                            `;

                            $('#rTableBody').append(tr);
                        })

                        let tr = `
                                <tr>
                                    <td colspan="5" align="right"><strong>TOTAL</strong></td>
                                    <td>${parseFloat(total).toFixed(2)}</td>
                                    <td>${parseFloat(totalPending).toFixed(2)}</td>
                                </tr>
                            `;

                        $('#rTableBody').append(tr);

                        $('#graphReport').hide();
                        $('#tableReport').show();
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