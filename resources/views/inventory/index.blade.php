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
                        <div class="col-12 text-center">
                            <h3 class="card-title">INVENTARIO</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @can('ingresos')
                                <button class="btn btn-primary-custom mg-r-10" id="addAdmission">
                                    Ingresos
                                </button>
                            @endcan
                            <button id="iExcel" class="btn btn-secondary-custom pull-right">Excel</button>
                            <button id="iPDF" class="btn btn-secondary-custom mg-r-10 pull-right">PDF</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtrar Por Almacen:</label>
                                <select id="warehouse" class="form-control">
                                    <option value="">Todos los Almacenes</option>
                                    @foreach ($warehouses as $p)
                                        <option value="{{ $p->id }}">{{ $p->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtrar Por Producto:</label>
                                <div>
                                    <select id="product" class="form-control select_2">
                                        <option value="">Todos los Productos</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->internalcode }} - {{ $p->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text" id="filter_date" class="form-control" placeholder="Seleccionar fechas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                    <th>CODIGO</th>
                                    <th>DESCRIPCION</th>
                                    <th>CATEGORIA</th>
                                    <th>MARCA</th>
                                    <th>ALMACEN</th>
                                    <th>LUGAR</th>
                                    <th>U. MED</th>
                                    <th>FECHA DE INGRESO</th>
                                    <th>C. ING.</th>
                                    <th>STOCK</th>
                                    <th>ESTADO</th>
                                    <th>*</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_add_admision" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVO INGRESO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_add_admission">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> Seleccionar Compra *</label>
                                            <select name="shopping" id="shopping" class="form-control" required>
                                                <option value="">Selecciona una Compra</option>
                                                @foreach ($shoppings as $shopping)
                                                    <option data-si="{{ $shopping->id }}" value="{{ $shopping->shopping_serie }}/{{ $shopping->shopping_correlative }}">{{ $shopping->shopping_serie }} - {{ $shopping->shopping_correlative }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <button id="printShopping" disabled type="button" class="btn btn-dark-secondary mt-4"><i class="fa fa-print"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" id="send" class="btn btn-primary-custom">
                                            Agregar Ingreso
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_add_transfer" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">TRASLADO ENTRE ALMACENES</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_add_transfer">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Responsable</label>
                                            <p>{{ auth()->user()->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Almacen de Origen</label>
                                            <select name="warehouseOrigin" id="warehouseOrigin" class="form-control" required>
                                                <option value="">Selecciones un Almacen de Origen</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" {{ $wh->id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Producto</label>
                                            <p id="transfer-product"></p>
                                            <input type="hidden" name="inventaryproductId" id="inventaryproductId">
                                            <input type="hidden" name="ii" id="ii">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Stock Actual</label>
                                            <p id="transfer-currentStock"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Almacen de Destino</label>
                                            <select name="warehouseDestination" id="warehouseDestination" class="form-control" required>
                                                <option value="">Selecciones un Almance de Destino</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>Cantidad a Transferir</label>
                                            <input type="number" class="form-control" id="transfer-newStock" name="transfer_newStock" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Motivo</label>
                                            <input type="text" name="transMotivo" id="transMotivo" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" id="btnTransfer" class="btn btn-primary-custom">
                                            Transferir
                                        </button>
                                    </div>
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
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 3, "desc"],[0, 'desc']],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/inventory/dt_inventory',
                'type' : 'get',
                'data': function(d) {
                    d.product = $('#product').val();
                    d.warehouse = $('#warehouse').val();
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
                    data: 'code',
                },
                {
                    data: 'product',
                },
                {
                    data: 'category',
                },
                {
                    data: 'brand',
                },
                {
                    data: 'warehouse',
                },
                {
                    data: 'place',
                },
                {
                    data: 'ot',
                },
                {
                    data: 'date',
                },
                {
                    data: 'amount',
                },
                {
                    data: 'stock',
                },
                {
                    data: 'status',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '1') {
                                data = 'Activo';
                            } else {
                                data = 'Inactivo';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'pi',
                },
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-rounded btn-secondary-custom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                    if (aData['status'] == '1') {
                        button += '<a class="dropdown-item newTransfer" href="#">Traslado</a>';
                        button += '<a class="dropdown-item" href="/inventory/edit/'+ aData['serie'] +'/'+ aData['correlative'] +'">Editar</a>';
                    }
                    button += '<a class="dropdown-item" href="/kardex?product='+ aData['pi'] +'&warehouse='+ aData['wi'] +'">Detalle</a>';
                    button += '</div>';
                    button += '</div>';
                    if (aData['status'] == "0") {
                        $('td', nRow).css({'background-color': 'rgba(255, 234, 234, 0.42)', 'user-select' : 'none'});
                    } else {
                        $('td', nRow).css('background-color', 'none');
                    }

                    /*if(aData['stock'] - aData['minimum_stock']) {
                        $(nRow).addClass('table-danger');
                    } else if(aData['stock'] === aData['minimum_stock']) {
                        $(nRow).addClass('table-warning');
                    } else if(aData['stock'] > aData['minimum_stock']) {
                        $(nRow).addClass('table-success');
                    }*/

                $(nRow).find("td:eq(11)").html(button);
            }
        });

        $('#product').on('change', function() {
            tbl_data.ajax.reload();
        });
        $('#warehouse').on('change', function() {
            tbl_data.ajax.reload();
        });
        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
        });

        $('#addAdmission').click(function() {
            $('#mdl_add_admision').modal('show');
        });

        $("#frm_add_admission").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe seleccionar un archivo XML.');
            } else {
                e.preventDefault();
                let shopping = $('#shopping').val();

                window.location.href = '/inventory/' + shopping;
            }
        });

        $('#byStatus').change(function(){
            tbl_data.column($(this).data('column')).search($(this).val()).draw();
        });
        $('#byCompany').keyup(function(){
            tbl_data.column($(this).data('column')).search($(this).val()).draw() ;
        });

        $('body').on('click','.newTransfer', function() {
            clearData();
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let pid = data['pi'];
            let pdescription = data['product'];
            let stock = data['stock'];
            let ii = data['ii'];

            $('#inventaryproductId').val(pid);
            $('#transfer-product').text(pdescription);
            $('#transfer-currentStock').text(stock);
            $('#ii').val(ii);
            $('#mdl_add_transfer').modal('show');
        });

        $('#transfer-newStock').keyup(function () {
            let stock = $('#transfer-currentStock').text() * 1;
            let quantity = $(this).val();

            if (stock < quantity) {
                $('#btnTransfer').attr('disabled');
                toastr.warning('Cantidad insuficiente. No se puede realizar la transferencia.')
            } else {
                $('#btnTransfer').removeAttr('disabled');
            }
        });

        $("#frm_add_transfer").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe completar todos los campos.');
            } else {
                e.preventDefault();
                let stock = $('#transfer-currentStock').text() * 1;
                let quantity = $('#transfer-newStock').val();
                if (stock < quantity) {
                    $('#btnTransfer').attr('disabled');
                    toastr.warning('Cantidad insuficiente. No se puede realizar la transferencia.')
                } else {
                    let data = $('#frm_add_transfer').serialize();
                    $.ajax({
                        type: 'post',
                        url: '/transfer/store',
                        data: data + '&_token=' + '{{ csrf_token() }}',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response)
                            if(response === true) {
                                toastr.success('Se generó correctamente transferencia');
                                $('#mdl_add_transfer').modal('hide');
                                window.location.href = '/transfer/';
                                clearData();
                            } else {
                                toastr.error('Ocurrió un error!');
                            }
                        },
                        error: function(response) {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                            console.log(response.responseText)
                        }
                    });
                }
            }
        });

        function clearData() {
            $('#warehouseOrigin').val(''); 
            $('#warehouseDestination').val(''); 
            $('#transfer-newStock').val(''); 
            $('#transMotivo').val(''); 
        }

        let shoppingSlc = 0;
        $('#shopping').change(function () {
            $('#printShopping').removeAttr('disabled');
        });

        $('#printShopping').click(function() {
            let s = $('option:selected', $('#shopping')).attr('data-si');
            window.open('/inventory/generate/pdf/' + s + '/shopping', '_blank');
        });
        $('.select_2').select2();

        $('#iPDF').click(function(e) {
            e.preventDefault();
            window.open('/inventory/generate/pdf', '_blank');
        });
        $('#iExcel').click(function(e) {
            e.preventDefault();
            window.open('/inventory/generate/excel', '_blank');
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
                    "Lu",
                    "Mar",
                    "Mie",
                    "Ju",
                    "Vi",
                    "Sab",
                    "Dom"
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
            "startDate": moment().subtract(6, 'days'),
            "endDate": moment().add(1, 'days'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@stop
