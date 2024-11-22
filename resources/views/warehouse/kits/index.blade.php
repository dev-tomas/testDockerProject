@extends('layouts.azia')
@section('css')
    <style>.prepare,.delete{display: none;}</style>
    @can('categorias.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
    @can('categorias.delete')
        <style>.delete{display: inline-block;}</style>
    @endcan
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title"><b>COMBOS</b></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <button class="btn btn btn-primary-custom openModalProduct" id="openModalCategory">Nuevo Combo</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group ">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Buscar Combo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                                    <thead>
                                        <th>Descripción</th>
                                        <th>Producto Venta</th>
                                        <th>Items</th>
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
    </div>

<form role="form" data-toggle="validator" id="frm_kits">
    <div id="mdl_add_kit" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="font-size: 1.5em !important;">NUEVO COMBO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="kit_id" name="kit_id">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">Nombre</label>
                                <input type="text" class="form-control" name="description" id="description">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="product_sale">Producto Venta</label>
                                <select name="product_sale" id="product_sale" class="product-sale form-control" style="width: 100%" required>
                                    <option value="">- Seleccione un Producto -</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->internalcode }} - {{ $product->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table" id="table-details">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>*</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="200px">
                                            <select name="product_detail[]" style="width: 100%;" class="form-control product_detail" required>
                                                @foreach($productsNotKits as $productsNotKit)
                                                    <option value="{{ $productsNotKit->id }}">{{ $productsNotKit->internalcode }}-{{ $productsNotKit->description }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="20px"><input type="number" name="quantity[]" step="0.01" class="form-control" required></td>
                                        <td width="10px"><button class="btn btn-danger removeline" type="button"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" id="addProduct" type="button">Agregar Producto</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>
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
            'processing': false,
            'serverSide': true,
            'searching': false,
            'ajax': {
                'url': '/warehouse/kits/dt',
                'type' : 'get',
                "data": function (d) {
                    d.search = $('#search').val();
                }
            },
            'columns': [
                {data: 'description'},
                {data: 'product_sale'},
                {data: 'count_items'},
                {data: 'id'},
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = "<div class='btn-group'>";
                button += "<button type='button' class='btn btn-secondary dropdown-toggle dropdown-button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> Opciones";
                button += "</button>";
                button += "<div class='dropdown-menu' x-placement='bottom-start' style='position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;'>";
                button += "<a class='dropdown-item prepare'>Editar</a>";
                button += "</div>";
                button += "</div>";

                $(nRow).find('td:eq(3)').html(button);
            }
        });

        $('#search').keyup(function () {
            tbl_data.ajax.reload();
        })

        $('.product-sale').select2({
            dropdownParent: $('#mdl_add_kit')
        });

        $('#tbl_data').on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#kit_id").val(data['id']);


            $.post('/warehouse/kits/prepare',
                'kit=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#kit_id').val(response['id']);
                    $('#description').val(response['description']);
                    $('#product_sale').val(response.product_id).trigger('change');
                    $('#table-details tbody').html('')
                    let productsNotKits = @json($productsNotKits);
                    console.log(productsNotKits)

                    $.each(response.details, function (index, item) {
                        let tr = `
                                    <tr>
                                        <td width="200px">
                                            <select name="product_detail[]" style="width: 100%;" class="form-control product_detail" required>
                                    `;
                                                $.each(productsNotKits, function(idx, el) {
                                                    tr += `<option value="${el.id}" ${item.product_id == el.id ? 'selected' : ''}>${el.internalcode}-${el.description}</option>`;
                                                });
                        tr += `
                                            </select>
                                        </td>
                                        <td width="20px"><input type="number" value="${item.quantity}" name="quantity[]" step="0.01" class="form-control" required></td>
                                        <td width="5px"><button class="btn btn-danger removeline" type="button"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                        `;

                        $('#table-details tbody').append(tr);
                        iniselect2()
                    });

                    $('#mdl_add_kit').modal('show');
                }, 'json');
        });

        $('#frm_kits').validator().on('submit', function(e) {

            e.preventDefault();
            let data = $(this).serialize();

            $.ajax({
                url: '/warehouse/kits/store',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                beforeSend: function() {
                    $('#save').attr('disabled');
                },
                complete: function() {

                },
                success: function(response) {
                    if(response == true) {
                        toastr.success('Se grabó satisfactoriamente la marca');
                        $("#tbl_data").DataTable().ajax.reload();
                        $('#mdl_add_kit').modal('hide');
                    } else {
                        toastr.error(response.responseText);
                    }
                },
                error: function(response) {
                    toastr.error(response.responseText);
                    $('#save').removeAttr('disabled');
                }
            });
        });

        $('#addProduct').click(function () {
            let tr = `
                        <tr>
                            <td width="200px">
                                <select name="product_detail[]" style="width: 100%;" class="form-control product_detail" required>
                                    @foreach($productsNotKits as $productsNotKit)
                                        <option value="{{ $productsNotKit->id }}">{{ $productsNotKit->internalcode }}-{{ $productsNotKit->description }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td width="20px"><input type="number" name="quantity[]" step="0.01" class="form-control" required></td>
                            <td width="5px"><button class="btn btn-danger removeline" type="button"><i class="fa fa-minus"></i></button></td>
                        </tr>
            `;

            $('#table-details tbody').append(tr);
            iniselect2()
        })

        $('body').on('click', '.removeline', function () {
            $(this).parent().parent().remove();
        });

        $('body').on('click', '.generate', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();

            $.post('/warehouse/kits/prepare',
                'Combo=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#Combo').val(response['id']);
                    $('#Combo_name_add').val(response['description']);
                    $('#Combo_product_add').val(response.product_sale);
                    $('#table-add-items tbody').html('')
                    
                    $.each(response.details, function (index, item) {
                        let tr = `
                                    <tr>
                                        <td class="item-description" width="200px">${item.product}</td>
                                        <td width="20px"><input type="number" value="${item.quantity}" class="form-control needed-quantity form-gray" readonly></td>
                                        <td width="20px"><input type="number" value="${item.stock}" class="form-control needed-stock form-gray" readonly></td>
                                    </tr>
                        `;

                        $('#table-add-items tbody').append(tr);
                    });
                }, 'json');

            $('#addInventoryMdl').modal('show')
        });

        $('#frm_generate_add').submit(function (e) {
            e.preventDefault()

            let data  = $(this).serialize()

            let status = validateQuantities();

            if (status == false) {
                return false;
            }

            $.confirm({
                icon: "fa fa-warning",
                theme: "modern",
                animation: "scale",
                type: "orange",
                draggable: false,
                title: "¿Esta seguro de generar este Combo?",
                content: '',
                buttons: {
                    Confirmar: {
                        text: "Confirmar",
                        btnClass: "btn btn-green",
                        action: function () {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                }
                            });
                            $.ajax({
                                type: 'post',
                                url: '/warehouse/kits/generate',
                                data: data,
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se generó correctamene el Combo;')
                                        $('#addInventoryMdl').modal('hide')
                                    } else {
                                        toastr.error('Ocurrió un error!');
                                    }
                                },
                                error: function(response) {
                                    toastr.error('Ocurrió un error!');
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: "Cancelar",
                        btnClass: "btn btn-red"
                    }
                }
            });
        })

        function validateQuantities() {
            let quantity = $('#quantity_generate').val();
            let status = true;

            $('#table-add-items tbody tr').each(function(idx, tr) {
                let quantityNeeded = $(tr).find('.needed-quantity').val()
                let stock = $(tr).find('.needed-stock').val()
                let product = $(tr).find('.item-description').text()

                let quantityRequested = parseFloat(quantityNeeded) * parseFloat(quantity)

                if (parseFloat(stock) < parseFloat(quantityRequested)) {
                    status = false;
                    toastr.warning(`No cuenta con el stock suficiente de ${product}`)
                }
            })

            return status;
        }

        function iniselect2() {
            $('.product_detail').select2({
                dropdownParent: $('#mdl_add_kit')
            });
        }

        iniselect2()

        $('#openModalCategory').on('click', function() {
            $('#mdl_add_kit').modal('show');
        });

        $('#mdl_add_kit').on('hidden.bs.modal', function (event) {
            $('#kit_id').val('');
            $('#description').val('');
            $('#product_sale').val('').trigger('change');
            $('#table-details tbody').html('');
            let tr = `
                        <tr>
                            <td width="200px">
                                <select name="product_detail[]" style="width: 100%;" class="form-control product_detail" required>
                                    @foreach($productsNotKits as $productsNotKit)
                                        <option value="{{ $productsNotKit->id }}">{{ $productsNotKit->internalcode }}-{{ $productsNotKit->description }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td width="20px"><input type="number" name="quantity[]" step="0.01" class="form-control" required></td>
                            <td width="5px"><button class="btn btn-danger removeline" type="button"><i class="fa fa-minus"></i></button></td>
                        </tr>
            `;

            $('#table-details tbody').append(tr);
            iniselect2()
        });
        
        $('#addInventoryMdl').on('hidden.bs.modal', function (event) {
            $('#kit').val('');
            $('#kit_name_add').val('');
            $('#kit_product_add').val('');
            $('#table-add-items tbody').html('');
        });
    </script>
@stop