@extends('layouts.azia')
@section('css')
    <style>.delete,.duplicate{display: none}</style>
    @can('transfers.delete')
        <style>.delete{display: inline-block;}</style>
    @endcan
    @can('transfers.duplicate')
        <style>.duplicate{display: inline-block;}</style>
    @endcan
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
                            <h3 class="card-title">TRANSFERENCIA DE PRODUCTOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @can('transfers.create')
                                <button type="button" id="newTransfer" class="btn btn-primary-custom">Nueva Transferencia</button>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <form id="frm_table">
                                <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                    <thead>
                                        <th>COD. TRANF</th>
                                        <th>FECHA - HORA</th>
                                        <th>ALM. ORIGEN</th>
                                        <th>ALM. DESTINO</th>
                                        <th>PDF</th>
                                        <th></th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </form>
                        </div>
                    </div>
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
                                            <select  class="form-control" name="warehouseOrigin" id="warehouseOrigin" required>
                                                <option value="">Selecciones un Almacen de Origen</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table-responsive">
                                            <thead class="thead-light">
                                            <tr>
                                                <th width="360px">Producto</th>
                                                <th width="100px">Stock Actual</th>
                                                <th width="100px">Cantidad a Transferir</th>
                                                <th width="50px">*</th>
                                            </tr>
                                            </thead>
                                            <tbody id="transferRowProduct">

                                            </tbody>
                                        </table>
                                        <button class="btn btn-primary-custom" type="button" id="addRowProductTransfer">Agregar Producto</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Almacen de Destino</label>
                                            <select  class="form-control" name="warehouseDestination" id="warehouseDestination" required>
                                                <option value="">Seleccione un Almacen de Destino</option>
                                            </select>
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
                                        <button type="submit" id="btnTransfer" class="btn btn-secondary-custom">
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
            "order": [[ 1, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/transfer/dt',
                'type' : 'get',
            },
            'columns': [
                {
                    data: 'serie',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            data = row.serie + row.correlative;
                        }
                        return data;
                    }
                },
                {
                    data: 'date',
                },
                {
                    data: 'wod',
                },
                {
                    data: 'wdd',
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData['status'] == 9) {
                    $(nRow).css({
                        'text-decoration': 'line-through',
                        'text-decoration-color': 'gray',
                        'color': 'gray'
                    });
                }

                $(nRow).find('td:eq(1)').text(moment(aData['date']).format('DD-MM-YYYY hh:mm'))
                $(nRow).find('td:eq(4)').html('<a href="/transfer/show-pdf/' + aData['id'] + '" class="btn btn-danger" target="_blank">PDF</a>');

                let button = '<div class="btn-group">';
                button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                button += '</button>';
                button += '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                if (aData['status'] == 9) {
                    button += '<a class="dropdown-item text-danger duplicate" href="#">Duplicar</a>';
                } else {
                    button += '<a class="dropdown-item text-danger delete" href="#">Anular</a>';
                }

                button += '</div>';
                button += '</div>';

                $(nRow).find("td:eq(5)").html(button);
            }
        });

        $('body').on('click', '.delete', function(e) {
            e.preventDefault();
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de anular esta transferencia?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/transfer/disabled',
                                data: {transfer: data['id']},
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se anuló satisfactoriamente la transferencia.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba anular la transferencia.');
                                    }

                                    tbl_data.ajax.reload();
                                },
                                error: function(response) {
                                    toastr.error('Ocurrio un error');
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn btn-red'
                    }
                }
            });
        })

        async function getProductsByWarehouse(warehouseOrigin) {
            let options = '';
            try {
                const response = await $.get('/transfer/get-products-by-warehouse', { warehouse: warehouseOrigin });
                options += '<option value="">Selecciona un Producto</option>';
                $.each(response, function(key, value) {
                    options += `<option value="${value.id}" p-stock="${value.stock}">${value.code} - ${value.description}</option>`;
                });
            } catch (error) {
                console.error('Error al obtener los productos del almacén:', error);
            }

            return options;
        }

        $('#check_all').change(function () {
            $('.select').prop("checked",$(this).prop("checked"));
        });

        $('.generateGuide').click(function () {
            let data = $('#frm_table').serialize();

            if (data == '' ) {
                toastr.warning('Debe de seleccionar al menos una transferencia.');
            } else {
                window.location = '/reference-guide/create/from-transfer?'+ data; 
            }
        });
        $('#newTransfer').click(function() {
            $('#mdl_add_transfer').modal('show');
        });

        $('body').on('change','#product', function() {
            // $('#transfer-currentStock').text($('#product').attr('p-stock'));
            $('#transfer-currentStock').text($('option:selected', this).attr('p-stock'));
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
            e.preventDefault();

            let status = validateTransfer()

            if (status == false) {
                return;
            }

            let data = $('#frm_add_transfer').serialize();
            $.ajax({
                type: 'post',
                url: '/transfer/store',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
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
                    toastr.error('Ocurrio un error');
                }
            });
        });

        function validateTransfer() {
            let status = true;

            $('#transferRowProduct tr').each(function (i, e) {
                let stock = $(e).find('.current-stock').val()
                let quantity = $(e).find('.stock-transfer').val()

                if (parseFloat(stock) < parseFloat(quantity)) {
                    status = false;
                    toastr.warning('Cantidad insuficiente. No se puede realizar la transferencia.');
                }
            });

            return status;
        }

        $('#addRowProductTransfer').click(async function () {
            let options = await getProductsByWarehouse($('#warehouseOrigin').val());

            let row = `
                    <tr data-product="">
                        <td style="padding: 0" width="360px">
                            <select name="product[]" style="width: 421px" class="form-control c_product" required>
                                ${options}
                            </select>
                        </td>
                        <td style="padding: 0" width="100px"><input type="text" readonly class="form-control current-stock"></td>
                        <td style="padding: 0" width="100px"><input type="number" name="transer[]" class="form-control stock-transfer" ></td>
                        <td style="padding: 0" width="50px"><button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button></td>
                    </tr>
            `;
            $('#transferRowProduct').append(row);
            $('.c_product').select2({
                dropdownParent: $('#mdl_add_transfer'),
            });
        });

        $('.c_product').select2({
            dropdownParent: $('#mdl_add_transfer'),
        });

        $('body').on('click', '.remove', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        })

        $('body').on('change','.c_product', function() {
            let product = $('option:selected', this).val();
            $(this).parent().parent().data('product', product);
            $(this).parent().parent().find('.current-stock').val($('option:selected', this).attr('p-stock'));
        });

        function clearData() {
            $('#warehouseOrigin').val(''); 
            $('#warehouseDestination').val(''); 
            $('#transfer-newStock').val(''); 
            $('#transMotivo').val(''); 
        }

        $('#warehouseOrigin').change(function(e) {
            e.preventDefault();

            $('#transferRowProduct').html('');
            getWarehousesDestination($('#warehouseOrigin').val())
        });

        function getWarehousesDestination(warehouseOrigin) {
            $.ajax({
                type: 'get',
                url: '/transfer/get-warehouse-transfer',
                data: { warehouse_origin: warehouseOrigin},
                dataType: 'json',
                success: function(response) {
                    $('#warehouseDestination').html('')
                    let option = `<option value="">Seleccione un Almacen de Destino</option>`
                    $.each(response, function(i,e) {
                        option += `<option value="${e.id}">${e.description}</option>`;
                    });

                    $('#warehouseDestination').append(option);
                },
                error: function(response) {
                    toastr.error('Ocurrio un error');
                }
            });
        }

        $('body').on('click', '.duplicate', function(e) {
            e.preventDefault();
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'post',
                url: '/transfer/get-data-duplicate',
                data: {transfer: data['id']},
                dataType: 'json',
                success: async function(response) {
                    $('#warehouseOrigin').val(response['warehouse_origin']);
                    await getWarehousesDestination(response['warehouse_origin']);
                    $('#transferRowProduct').html('')

                    await $.each(response.detail, async function(i, e) {
                        let products = await getProductsForTransfer(response['warehouse_origin']);
                        let optionsProduct = await getDuplicaProduct(products, e.product)
                        let row = `
                                <tr>
                                    <td style="padding: 0" width="360px">
                                        <select name="product[]" style="width: 421px" class="form-control c_product" required>
                                            ${optionsProduct}
                                        </select>
                                    </td>
                                    <td style="padding: 0" width="100px"><input type="text" readonly value="${e.stock}" class="form-control current-stock"></td>
                                    <td style="padding: 0" width="100px"><input type="number" name="transer[]" value="${e.quantity}" class="form-control stock-transfer" ></td>
                                    <td style="padding: 0" width="50px"><button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button></td>
                                </tr>
                        `;

                        $('#transferRowProduct').append(row);
                        $('.c_product').select2({
                            dropdownParent: $('#mdl_add_transfer'),
                        });
                    });

                    $('#warehouseDestination').val(response['warehouse_destination'])
                    console.log(response['warehouse_destination'])

                    $('#mdl_add_transfer').modal('show');
                },
                error: function(response) {
                    toastr.error('Ocurrio un error');
                }
            });
        })

        async function getProductsForTransfer(warehouseOrigin) {
            try {
                return await $.get('/transfer/get-products-by-warehouse', {warehouse: warehouseOrigin});

            } catch (error) {
                console.error('Error al obtener los productos del almacén:', error);
            }
        }

        async function getDuplicaProduct(products, product) {
            var options = '';
            $.each(products, function(i, e) {
                options += `<option value="${e.id}" ${e.id == product ? 'selected' : ''} p-stock="${e.stock}">${e.code} - ${e.description}</option>`;
            });

            return options;
        }
    </script>
@stop
