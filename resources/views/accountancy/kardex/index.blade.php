@extends('layouts.azia')
@section('css')
    <style>
        label {font-weight: 700 !important;}
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 col-md-12 text-center">
                            <h3 class="card-title">KARDEX VALORIZADO</h3>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="form_kardex">
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-2">
                                <div class="form-group">
                                    <label>Almacén:</label>
                                    <select name="warehouse" id="warehouse" class="form-control" required>
                                        <option value="">Seleccione un Almacén</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $w == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 col-lg-2">
                                <div class="form-group">
                                    <label for="">Producto:</label>
                                    <select name="product" id="product" class="form-control select_2" required>
                                        <option value="">Seleccione un Producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ $p == $product->id ? 'selected' : '' }}>{{ $product->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 col-lg-2">
                                <div class="form-group">
                                    <label for="">Filtrar por Fechas:</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas" required>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group text-center align-items-center">
                                    <button type="button" id="kForm" class="btn btn-primary-custom mt-4"><i class="fa fa-search"></i> Buscar</button>
                                    <button type="submit" id="kExcel" class="btn btn-secondary-custom mt-4">Excel</button>
                                    <button type="submit" id="kPDF" class="btn btn-secondary-custom mt-4">PDF</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="row">
                        <div class="table-responsive" id="table-kardex">
                            <table class="table table-bordered table-hover mg-b-0">
                                <thead>
                                    <tr>
                                        <th align="center" width="30px">#</th>
                                        <th align="center" width="150px">FECHA - HORA</th>
                                        <th align="center" width="150px">TIPO TRANSACCIÓN</th>
                                        <th align="center" width="100px">TIPO DOCUMENTO</th>
                                        <th align="center" width="150px">NÚMERO</th>
                                        <th align="center" colspan="3" class="text-center entries" width="100px">INGRESO</th>
                                        <th align="center" colspan="3" class="text-center exit" width="100px">SALIDA</th>
                                        <th align="center" colspan="3" class="text-center" width="100px">SALDO</th>
                                    </tr>
                                    <tr>
                                        <th colspan="5"></th>
                                        <th align="center" class="entries">CANTIDAD</th>
                                        <th align="center" class="entries">COSTO UNIT.</th>
                                        <th align="center" class="entries">COSTO TOTAL</th>
                                        <th align="center" class="exit">CANTIDAD</th>
                                        <th align="center" class="exit">COSTO UNIT.</th>
                                        <th align="center" class="exit">COSTO TOTAL</th>
                                        <th align="center">CANTIDAD</th>
                                        <th align="center">COSTO UNIT.</th>
                                        <th align="center">COSTO TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyKardex">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMovementMdl" tabindex="-1" aria-labelledby="addMovementLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="frm_addMovement">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMovementLabel">Agregar Movimiento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Responsable</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Almacén</label>
                                    <select name="movement_warehouse" id="movement_warehouse" class="form-control" required>
                                        <option value="">Seleccione un Almacén</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $w == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Producto:</label>
                                    <select name="movement_product" id="movement_product" class="form-control" style="width: 100%" required>
                                        <option value="">Seleccione un Producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ $p == $product->id ? 'selected' : '' }}>{{ $product->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Cantidad</label>
                                    <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Movimiento</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="1">Salida</option>
                                        <option value="2">Entrada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="description" id="description" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Grabar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
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
                "daysOfWeek": ["Dom","Lu","Mar","Mie","Ju","Vi","Sab",],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Deciembre"],
                "firstDay": 0
            },
            "startDate": moment().subtract(7, 'days'),
            "endDate": moment().add(1, 'days'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $(document).ready(function() {
            $('#table-kardex').hide();
        });

        $('#kForm').click(function() {
            $('#table-kardex').hide();
            let data = $('#form_kardex').serialize();
            $.ajax({
                url: '/kardex-valorizado/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    $('#tbodyKardex').html('');
                    let tr;
                    let cont = 0;
                    $.each(response, function(i, item) {
                        let tr = `
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.date}</td>
                                <td>${item.type_transaction} - ${item.operation}</td>
                                <td>${item.type_document}</td>
                                <td>${item.number}</td>
                                <td>${item.entry}</td>
                                <td>${item.cost_entry}</td>
                                <td>${item.entry_cost}</td>
                                <td>${item.output}</td>
                                <td>${item.cost_output}</td>
                                <td>${item.output_cost}</td>
                                <td>${item.balance}</td>
                                <td>${item.cost_balance}</td>
                                <td>${item.balance_cost}</td>
                            </tr>
                        `
                        $('#tbodyKardex').append(tr);
                    });

                    $('#table-kardex').show();
                },
                error: function(response) {
                    toastr.error('Ocurrio un error');
                }
            });
        });

        $('#kExcel').click(function(e) {
            e.preventDefault();
            let data = $('#form_kardex').serialize(); 
            window.open('/kardex-valorizado/generate/excel? ' + data, '_blank');
        });
        $('#kPDF').click(function(e) {
            e.preventDefault();
            let data = $('#form_kardex').serialize(); 
            window.open('/kardex-valorizado/generate/pdf? ' + data, '_blank');
        });
        $('.select_2').select2();

        $('#movement_product').select2({
            dropdownParent: $('#addMovementMdl')
        });

        $('#frm_addMovement').submit(function(e) {
            e.preventDefault();

            let data = $('#frm_addMovement').serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/kardex-valorizado/addmovement',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if(response == true) {
                        toastr.success('Agregó correctament el movimiento');
                        $('#addMovementMdl').modal('hide')
                    }  else {
                        toastr.error('Ocurrió un error!');
                    }
                },
                error: function(response) {
                    toastr.error('Ocurrió un error!');
                }
            });
        })

        $('#addMovement').click(function() {
            $('#addMovementMdl').modal('show')
        })

        $('#addMovementMdl').on('hidden.bs.modal', function (event) {
            $('#movement_warehouse').val('')
            $('#movement_product').val('').trigger('change')
            $('#quantity').val('')
            $('#type').val('1')
            $('#description').val('');
        });
    </script>
@stop
