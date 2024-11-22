@extends('layouts.azia')
@section('css')
<style>
    .hide {
        display: none
    }

    .show {
        display: block;
    }

    /* Estilos para las secciones */
    .section {
        background-color: #FFF;
        padding: 10px;
        border-radius: 3px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Estilos para los elementos */
    .item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        margin: 20px;
        border-bottom: 2px solid #A3C318;

    }

    .input {
        display: flex;
        justify-content: space-between;
        padding: 20px 0;
        margin: 20px;
        border-bottom: 2px solid #E0E0E0;

    }
    .input-label{
        color: #333;
        font-weight: bold;
        font-size: 18px;
    }

    .item-label {
        color: #333;
        font-weight: bold;
        font-size: 18px;
    }

    .item-value {
        color: #555;
        font-size: 18px;
    }

    /* Seccion de Graphics */
    .container-charts {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .graphics {
        max-width: 400px;
        margin-bottom: 250px;
    }

    .bar {
        display: flex;
        justify-content: space-between;
        max-width: 900px;
    }

    .explanation {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        /* Nueva propiedad para separar el texto de los gráficos */
        margin-right: 20px;
    }

    .explanation h2 {
        font-size: 1.2em;
        margin-bottom: 10px;
    }

    .explanation p {
        font-size: 1em;
    }

    /* Estilos para separar los gráficos */
    .graphics+.graphics {
        margin-top: 50px;
    }


</style>
@stop
@section('content')
<div class="row" style="padding: 2em 0; overflow: hidden;">
    <div class="col-12">
        <div class="card">
            <div class="card-header " style="background-color: #A3C318;">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 style="color:white">REPORTE DE FINANZAS</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="col-12">
                        <form id="generateReport" method="POST">
                            <fieldset>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Local</label>
                                            @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                                            <select name="headquarter" id="headquarter" class="form-control">
                                                <option value="" selected>Todos</option>
                                                @foreach ($headquarters as $h)
                                                <option value="{{ $h->id }}">{{ $h->description }}</option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select name="headquarter" id="headquarter" class="form-control" disabled>
                                                @foreach ($headquarters as $h)
                                                <option value="{{ $h->id }}" {{ auth()->user()->headquarter_id == $h->id ? 'selected' : '' }}>{{ $h->description }}</option>
                                                @endforeach
                                            </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="">Rango de Fechas</label>
                                            <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 ">
                                        <div class="form-group">
                                            <label>Aplicar Filtro</label>
                                            <button type="button" id="generate" class="btn btn-block btn-dark-custom">Filtro</button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <label>Descargar Excel</label>
                                            <button type="button" id="generateExcel" class="btn btn-block btn-secondary-custom">Excel</button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 pull-right">
                                        <div class="form-group">
                                            <label>Descargar pdf</label>
                                            <button type="button" id="generatePDF" class="btn btn-block btn-danger-custom">PDF</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>

            <div class="container col-12">
                <div class="section col-12 col-md-6">
                    <h2 class="section-heading text-center" id="filterdate" style="color:#A3C318;">ESTADO DE RESULTADOS </h2>
                    <div class="item">
                        <span class="item-label">Ingresos Brutos</span>
                        <span class="item-value" id="IngresoBruto">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Descuentos y devoluciones</span>
                        <span class="item-value" id="Descuento">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Ingresos Netos</span>
                        <span class="item-value" id="IngresoNeto">0.00</span>
                    </div>

                    <div class="item">
                        <span class="item-label">Costo de Ventas</span>
                        <span class="item-value" id="CostoVentas">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Utilidad Bruta</span>
                        <span class="item-value" id="UtilidadBruta">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Gastos</span>
                        <span class="item-value" id="Gastos">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Gasto de personal </span>
                        <!-- <span>(S/.</span><input class="item-value" type="number" style="text-align: right">) -->
                         <span class="item-value" contentEditable="true" id="GastosPersonal">0.00</span>
                    </div>
                    <div class="item">
                        <span class="item-label">Utilidad operativa</span>
                        <span class="item-value" id="UtilidadOperativa">0.00</span>
                    </div>
                </div>

                <div class="section col-12 col-md-6" id="graphReport">
                    <div class="chart" style="position: relative; width:100%; height: 300px">
                        <canvas id="areaChart" style="display: block;" class="chartjs-render-monitor"></canvas>
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
            "applyLabel": "Aceptar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Custom",
            "weekLabel": "W",
            "daysOfWeek": ["Dom", "Lun", "Mar", "Mie", "Ju", "Vi", "Sab"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            "firstDay": 0
        },
        "startDate": moment().startOf('month'),
        "endDate": moment().endOf('month'),
        "cancelClass": "btn-dark"
    }, function(start, end, label) {
        // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    
</script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
<script>
    $(document).ready(function() {
        // $('#oa').hide();
        let fechas, totales, today, month, sales;
        let data = $('#generateReport').serialize();
        $('#graphReport').show();
        $('.btn').addClass('btn-rounded');

        $.ajax({
            url: '/analytics.reports.generate',
            type: 'post',
            data: data + '&_token=' + '{{ csrf_token() }}',
            dataType: 'json',
            success: function(response) {
                fechas = response['dates'];
                totales = response['total'];
                formatAndSetText('IngresoBruto', response['sales']);
                //formatAndSetText('Descuento', response['discount']);
                $('#Descuento').text("(S/."+parseFloat(response['discount']).toFixed(2)+")");
                formatAndSetText('IngresoNeto', response['incomeneto']);
                formatAndSetText('CostoVentas', response['salescost']);
                formatAndSetText('UtilidadBruta', response['utilitybrute']);
                //formatAndSetText('Gastos', response['expenses']);
                $('#Gastos').text("(S/." + parseFloat(response['expenses']).toFixed(2) + ")");
                formatAndSetText('UtilidadOperativa', response['utilityoperative']);
                graph(fechas, totales);
            },
            error: function(response) {
                console.log(response.responseText);
                toastr.error('Ocurrio un error');
            }
        });

        $.ajax({
            url: '/analytics.reports.index',
            type: 'post',
            data: '&_token=' + '{{ csrf_token() }}',
            dataType: 'json',
            success: function(response) {
                today = response['today'];
                month = response['month'];
                sales = response['sales'];
                formatAndSetText('TotalDay', response['day']);
                formatAndSetText('TotalMes', response['mes']);
                formatAndSetText('IngresoBruto', response['sales']);
                //formatAndSetText('Descuento', response['discount']);
                $('#Descuento').text("(S/."+parseFloat(response['discount']).toFixed(2)+")");
                formatAndSetText('IngresoNeto', response['incomeneto']);
                formatAndSetText('CostoVentas', response['salescost']);
                formatAndSetText('UtilidadBruta', response['utilitybrute']);
                //formatAndSetText('Gastos', response['expenses']);
                $('#Gastos').text("(S/." + parseFloat(response['expenses']).toFixed(2) + ")");
                formatAndSetText('UtilidadOperativa', response['utilityoperative']);
                graphTM(today, month);
            },
            error: function(response) {
                console.log(response.responseText);
                toastr.error('Ocurrio un error');
            }
        });
    });

    function formatAndSetText(elementId, value) {
        var isNegative = parseFloat(value) < 0;
        var absoluteValue = Math.abs(parseFloat(value));

        var formattedValue = absoluteValue.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
        });

        var textWithParentheses = isNegative ? "(S/." + formattedValue + ")" : "S/." + formattedValue;
        $('#' + elementId).text(textWithParentheses);
    }

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
        window.open('/analytics.reports.generate.excel? ' + data, '_blank');
    });
    $('#generatePDF').click(function(e) {
        e.preventDefault();
        let data = $('#generateReport').serialize();
        window.open('/analytics.reports.generate.pdf? ' + data, '_blank');
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
                bezierCurve: true,
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
                bezierCurve: true,
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

    $(function() {
        let fechas, totales;

        $('#generate').click(function() {
            let data = $('#generateReport').serialize();
            $.ajax({
                url: '/analytics.reports.generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    fechas = response['dates'];
                    totales = response['total'];
                    formatAndSetText('IngresoBruto', response['sales']);
                    //formatAndSetText('Descuento', response['discount']);
                    $('#Descuento').text("(S/."+parseFloat(response['discount']).toFixed(2)+")");
                    formatAndSetText('IngresoNeto', response['incomeneto']);
                    formatAndSetText('CostoVentas', response['salescost']);
                    formatAndSetText('UtilidadBruta', response['utilitybrute']);
                    //formatAndSetText('Gastos', response['expenses']);
                    $('#Gastos').text("(S/." + parseFloat(response['expenses']).toFixed(2) + ")");
                    formatAndSetText('UtilidadOperativa', response['utilityoperative']);
                    graph(fechas, totales);

                    $('#graphReport').show();
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                    $('#save').removeAttr('disabled');
                }
            });
        });
    });

    function graph(fechas, totales) {
        var ticksStyle = {
            fontColor: '#fff',
            fontStyle: 'bold'
        }
        var mode = 'index'
        var intersect = true

        var $visitorsChart = $('#areaChart')
        var visitorsChart = new Chart($visitorsChart, {
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Ventas Totales',
                    type: 'line',
                    data: totales,
                    backgroundColor: 'transparent',
                    borderColor: '#A3C318',
                    pointBorderColor: '#A3C318',
                    pointBackgroundColor: '#A3C318',
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .4)',
                            zeroLineColor: 'transparent'
                        },
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: true,
                            color: 'rgba(0, 0, 0, .4)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    }

</script>
 {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
 <script>
    var ctx1 = document.getElementById('myChart1').getContext('2d');
    var UtilidadBruta = parseFloat(document.getElementById('UtilidadBruta').textContent);
    var CostoVenta = parseFloat(document.getElementById('CostoVentas').textContent);

    // Crea el primer gráfico
    var myChart1 = new Chart(ctx1, {
        type: 'bar', // Tipo de gráfico, como bar, line, pie, etc.
        data: {
            labels: ['Utilidad Bruta', 'Costo de ventas'], // Etiquetas en el eje x
            datasets: [{
                label: 'Ingresos Operacionales', // Etiqueta del conjunto de datos
                data: [UtilidadBruta, CostoVenta], // Valores de los datos
                backgroundColor: 'transparent', // Color de fondo de las barras
                borderColor: '#A3C318', // Color del borde de las barras
                borderWidth: 1, // Ancho del borde
                pointBorderColor: '#A3C318',
                pointBackgroundColor: '#A3C318',
            }]
        },
        options: {
            // Configuración adicional del gráfico
        }
    });

    // Obtén el contexto del lienzo para el segundo gráfico
    var ctx2 = document.getElementById('myChart2').getContext('2d');

    // Define las variables para el segundo gráfico


    // Crea el segundo gráfico
    var myChart2 = new Chart(ctx2, {
        type: 'line', // Tipo de gráfico, como bar, line, pie, etc.
        data: {
            labels: ['Etiqueta 1', 'Etiqueta 2'], // Etiquetas en el eje x
            datasets: [{
                label: 'Datos', // Etiqueta del conjunto de datos
                data: [UtilidadBruta, CostoVenta], // Valores de los datos
                backgroundColor: 'transparent', // Color de fondo de las barras
                borderColor: '#A3C318', // Color del borde de las barras
                borderWidth: 1, // Ancho del borde
                pointBorderColor: '#A3C318',
                pointBackgroundColor: '#A3C318',
            }]
        },
        options: {
            // Configuración adicional del gráfico
        }
    });

    // Obtén el contexto del lienzo para el tercer gráfico
    var ctx3 = document.getElementById('myChart3').getContext('2d');

    // Define las variables para el tercer gráfico


    // Crea el tercer gráfico
    var myChart3 = new Chart(ctx3, {
        type: 'pie', // Tipo de gráfico, como bar, line, pie, etc.
        data: {
            labels: ['Etiqueta 1', 'Etiqueta 2'], // Etiquetas en el gráfico circular
            datasets: [{
                label: 'Datos', // Etiqueta del conjunto de datos
                data: [UtilidadBruta, CostoVenta], // Valores de los datos
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'], // Color de fondo de las secciones
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'], // Color del borde de las secciones
                borderWidth: 1 // Ancho del borde
            }]
        },
        options: {
            // Configuración adicional del gráfico
        }
    });
</script>  --}}

@endsection