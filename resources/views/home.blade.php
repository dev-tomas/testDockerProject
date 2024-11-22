@extends('layouts.azia')
@section('css')
    <style>
        .select-clear {
            outline: none;
            border: none;
            background: transparent;
        }
    </style>
@endsection
@section('content')


<!-- #============================ Notificaciones =================================================== -->  
  <!-- Modal de la notificación -->
   {{-- <div class="modal fade" id="notificacionModal" tabindex="-1" role="dialog" aria-labelledby="notificacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificacionModalLabel">¡Comunicado General!</h5>
          </button>
        </div>
        <div class="modal-body">
          <p style="text-align: justify;">
            Este sábado 23 de septiembre a partir de las 18:15 p.m., nuestro servidor estará en mantenimiento por aproximadamente 24 horas. Durante este tiempo, nuestros servicios no estarán disponibles.</br>
            Lamentamos los inconvenientes y agradecemos su comprensión mientras trabajamos para mejorar nuestros sistemas.
            </br></br>Gracias por su paciencia.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
    $('#notificacionModal').modal('show');
  });
  </script>

  <style> -->
    <!-- /* ================== Estilo del modal ================== */
    #notificacionModal .modal-header {
        justify-content: center;
        
    }
    #notificacionModal .modal-content h5{
        font-size: 23px;
        color: #a2c700;
        justify-content: center;
        
    }
    #notificacionModal .modal-content{
        border-radius: 10px;
        border: 1px solid #000000;
        font-size: 17px;
        
    }
    #notificacionModal .modal-footer{
        justify-content: center;
    }

    #notificacionModal .modal-footer .btn-secondary{
        background-color: #545454;
        border-radius: 5px;
        font-size: 16px;
    }
  </style>  --}}
<!-- ===================================================================================================== -->


<div class="row">
    <div class="col-12">
        <div class="row no-gutters">
            <h5>{{ auth()->user()->headquarter->client->trade_name }}</h5>
        </div>
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card card-primary shadow-lg mb-5 bg-white">
                    <div class="card-header">
                        <h5 class="card-title pull-left">INGRESOS</h5> {{-- <span class="pull-right">Últimos 365 días</span> --}}
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-12 col-md-6 col-lg-4">
                                <h6><b>S/. {{ number_format ( $paidLastMonth, 2,"." ,"," ) }}</b></h6>
                                <h6><small>Facturas Pagadas Mes Actual</small></h6>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <h6><b>S/. {{ number_format ( $defeated, 2,"." ,"," ) }}</b></h6>
                                <h6><small>Facturas Vencidas</small></h6>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <h6><b>S/. {{ number_format ( $pend, 2,"." ,"," ) }}</b></h6>
                                <h6><small>Facturas Pendientes</small></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <canvas id="income" style="height:250px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row"><div class="col-12"><br></div></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-primary shadow-lg mb-5 bg-white">
                    <div class="card-header">
                        <h5 class="card-title pull-left">COMPRAS</h5>
                        <select name="period_shopping" id="period_shopping" class="pull-right select-clear">
                            <option value="current_month">Mes Actual</option>
                            <option value="last_month">Mes Anterior</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h5><b id="totalSpending">S/. {{ number_format ( $totalSpending['total'], 2,"." ,"," ) }}</b></h5>
                                <h6>Últimos 30 días</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <canvas id="spending" style="height:250px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row"><div class="col-12"><br></div></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-primary shadow-lg mb-5 bg-white">
                    <div class="card-header">
                        <h5 class="card-title pull-left">VENTAS</h5> <span class="pull-right"></span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h5><b>S/. {{ number_format ( $totalLastMonth['total'], 2,"." ,"," ) }}</b></h5>
                                <h6>Mes Actual</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <canvas id="lineChart" ></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row"><div class="col-12"><br></div></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-primary shadow-lg mb-5 bg-white">
                    <div class="card-header">
                        <h5 class="card-title pull-left">Producto más Vendido</h5> <span class="pull-right"></span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($top10Products as $product)
                                            <tr>
                                                <td>{{ $product['description'] }}</td>
                                                <td align="center">{{ $product['total_sold']}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row"><div class="col-12"><br></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('script_admin')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '/home/report',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    fechas = response['dates'];
                    totales = response['total'];
                    graphSM(fechas, totales);
                },
                error: function(response) {
                    // console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });
            $.ajax({
                url: '/home/income',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    totales = response['totals'];
                    graphIncome(totales);
                },
                error: function(response) {
                    // console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });

            getExpending();
        });

        function getExpending() {
            $.ajax({
                url: '/home/spending',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}' + '&period=' + $('#period_shopping').val(),
                dataType: 'json',
                success: function(response) {
                    totales = response['totals'];
                    $('#totalSpending').text('S/. ' + response['totalSpending']);
                    graphSpending(totales);
                },
                error: function(response) {
                    // console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        }

        $('#period_shopping').change(function() {
            getExpending()
        })

        function graphSM(fechas, totales) {
            let ctxLine = $('#lineChart');
            var myLineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    datasets: [{
                        backgroundColor: 'rgba(163, 195, 24, 1)',
                        borderColor: 'rgba(163, 195, 24, 0.5)',
                        label: 'Reportel del mes',
                        data: totales,
                        fill: false,
                    }],
                    labels: fechas
                },
                options: {
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Día'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Valor en Soles'
                            }
                        }]
                    }
                }
            });
        }
        function graphIncome(totales) {
            let income = $('#income');
            var chartIncome = new Chart(income, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: totales,
                        backgroundColor: [
                            'rgba(163, 195, 24, 1)',
                            'rgba(163, 195, 24, 0.2)',
                            'rgb(149, 165, 166)',
                        ],
                    }],
                    labels: [
                        'Facturas Pagadas 30 dias',
                        'Facturas Vencidas',
                        'Facturas Pendientes',
                    ]
                },
                options: []
            });
        }
        function graphSpending(totales) {
            let income = $('#spending');
            var chartIncome = new Chart(income, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: totales,
                        backgroundColor: [
                            'rgba(163, 195, 24, 1)',
                            'rgba(163, 195, 24, 0.2)',
                            'rgb(149, 165, 166)',
                        ],
                    }],
                    labels: [
                        'Mercaderia',
                        'Gastos',
                        'Activo Fijo',
                    ]
                },
                options: []
            });
        }
    </script>
@stop