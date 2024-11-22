@extends('layouts.azia')
@section('css')
    <style>
        .select2-container{
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
        }
        .table tbody tr td { padding-left: 0;padding-right: 0; }
    </style>
@endsection
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_add_requirement">
        <div>
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="text-center">
                            <strong>NUEVO REQUERIMIENTO</strong>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="warehouse">Almacén</label>
                                    <select name="warehouse" id="warehouse" class="form-control select2" required>
                                        <option value="">Seleccione Almacén</option>  
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->description }}</option>
                                        @endforeach   
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Solicitado</label>
                                    <input type="text" class="form-control" name="requested" value="{{ auth()->user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Tipo de Requerimiento</label>
                                    <select name="typerequirement" id="" class="form-control" required>
                                        <option value="">Seleccione Tipo de Requerimiento</option>
                                        <option value="1">Inventario</option>
                                        <option value="2">Equipamento</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4"></div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Autorizado</label>
                                    <input type="text" class="form-control" name="authorized" disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Centro de Costos</label>
                                    <select name="centercost" id="centercost" class="form-control" required>
                                        <option value="">Seleccione Centro de Costos</option>
                                        @foreach ($costsCenter as $cc)
                                            <option value="{{ $cc->id }}">{{ $cc->center }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table" id="tbl_products" style="width: 100%">
                                            <thead class="text-center">
                                                <th width="280px">Categoría</th>
                                                <th max-width="200px">Productos/Servicios</th>
                                                <th width="130px">Cant.</th>
                                                <th width="130px">Stock</th>
                                                <th width="320px">Observaciones</th>
                                                <th width="100px">*</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <select name="category[]" id="category" class="form-control category" required>
                                                                    <option value="">Seleccione Categoría</option>
                                                                    @if($categories->count() > 0)
                                                                        @foreach($categories as $c)
                                                                            <option value="{{$c->id}}">{{$c->description}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group" style="width: 100%;">
                                                            <select style="width: 80%;" class="form-control select2 product" name="product[]" id="product" required>
                                                                <option value="">Seleccionar Producto</option>
                                                                @if($products->count() > 0)
                                                                    @foreach($products as $p)
                                                                        <option value="{{$p->id}}" p-code="{{ $p->internalcode }}" p-stock="{{$p->stock}}" p-price="{{$p->price}}" p-otype='{{ $p->operation_type }}'>{{$p->description}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="number" name="quantity[]" class="form-control" required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" id="stock" class="form-control stock" disabled>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" name="observation[]" class="form-control" >
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary-custom" id="btnAddProduct">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row mb-3 mt-3">
                            <div class="col-6 col-md-8"></div>
                            <div class="col-3 col-md-2">
                                <button class="btn btn-danger-custom btn-block">Cancelar</button>
                            </div>
                            <div class="col-3 col-md-2">
                                <button class="btn btn-primary-custom btn-block" id="btnAddRequirement">Crear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
@section('script_admin')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });


        function getdsds() {
            var products = "";
            $.post('/requirements/products', '_token=' +  '{{ csrf_token() }}', function(response) {
                for (var i = 0; i < response.length; i++) {
                    products += '<option value="' + response[i].id + '" p-code="'+ response[i].internalcode +'"  p-price="' + response[i].price + '" p-mesure="' + response[i].measure + '" p-stock="'+response[i].stock +'">';
                    products += response[i].description + '</option>';
                }
            }, 'json');

            var categories = "";
            $.post('/requirements/categories', '_token=' +  '{{ csrf_token() }}', function(response) {
                for (var i = 0; i < response.length; i++) {
                    categories += '<option value="' + response[i].id + '">';
                    categories += response[i].description + '</option>';
                }

                console.log(response);
            }, 'json');
        }        
        var products = "";
        $.post('/requirements/products', '_token=' +  '{{ csrf_token() }}', function(response) {
            for (var i = 0; i < response.length; i++) {
                products += '<option value="' + response[i].id + '"  p-code="'+ response[i].internalcode +'"  p-price="' + response[i].price + '" p-mesure="' + response[i].measure + '" p-stock="'+response[i].stock +'">';
                products += response[i].description + '</option>';
            }
        }, 'json');

        var categories = "";
        $.post('/requirements/categories', '_token=' +  '{{ csrf_token() }}', function(response) {
            for (var i = 0; i < response.length; i++) {
                categories += '<option value="' + response[i].id + '">';
                categories += response[i].description + '</option>';
            }

            console.log(response);
        }, 'json');

        $('#btnAddProduct').on('click', function() {
            getdsds();
            let data = `
                        <tr>
                            <td>
                                <div class="input-group">
                                    <select name="category[]" id="category" class="form-control category" required>
                                        <option value="">Seleccione Categoría</option>
                                        `
                                        + categories +
                                        `
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="input-group"  style="width: 100%;">
                                    <select style="width: 80%;" class="form-control select_2 product" name="product[]" id="product" required>
                                        <option value="">Seleccionar Producto</option>
                                        `
                                        + products +
                                        `
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="number" name="quantity[]" class="form-control" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" id="stock" class="form-control stock"  disabled>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" name="observation[]" class="form-control" >
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
            `;
            $('#tbl_products tbody').append(data);

            $('.select_2').select2();
        });
        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
        });

        $('body').on('change', '.product', function() {
            let tr = $(this).parent().parent().parent();
            tr.find('.stock').val($('option:selected', this).attr('p-stock'));
            tr.find('.umedida').val($('option:selected', this).attr('p-mesure'));
            tr.find('.code').val($('option:selected',this).attr('p-code'));
        });

        $('#frm_add_requirement').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr["warning"]("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frm_add_requirement').serialize();

                if($('#tbl_products tbody tr').length == 0) {
                    toastr["error"]("Debe seleccionar algún producto o servicio");
                    return false;
                }else{
                    $.confirm({
                        icon: 'fa fa-question',
                        theme: 'modern',
                        animation: 'scale',
                        type: 'green',
                        title: '¿Está seguro de crear este requerimiento?',
                        content: '',
                        buttons: {
                            Confirmar: {
                                text: 'Confirmar',
                                btnClass: 'btn-green',
                                action: function(){
                                    $.ajax({
                                        url: '/requirements/store',
                                        type: 'post',
                                        data: data + '&_token=' + '{{ csrf_token() }}',
                                        dataType: 'json',
                                        beforeSend: function() {
                                            $('#btnAddRequirement').attr('disabled');
                                        },
                                        complete: function() {},
                                        success: function(response) {
                                            console.log(response['response']);
                                            if(response['response'] == true) {
                                                toastr.success('Se grabó satisfactoriamente el requerimiento');
                                                window.location="/requirements";
                                            } else {
                                                console.log(response.responseText);
toastr.error('Ocurrio un error');
                                                console.log(response);
                                            }
                                        },
                                        error: function(response) {
                                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                                            $('#btnGrabarCliente').removeAttr('disabled');
                                        }
                                    });
                                }
                            },
                            Cancelar: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function(){
                                }
                            }
                        }
                    });
                }
            }
        });

        function clearDataProduct() {
            $('#category').val('');
            $('#measure').val('');
            $('#brand').val('');
            $('#code').val('');
            $('#internalcode').val('');
            $('#pdescription').val('');
            $('#product_id').val('');
            $('#quantity').val('');
            $('#cost').val('');
            $('#price').val('');
            $('#type').val('');
            $('#coin').val('1');
        }

        $('body').on('click', '.openModalProduct', function() {
            clearDataProduct();
            $('#mdl_add_product').modal('show');
        });

        $("#mdl_add_product").on('hidden.bs.modal', function () {
            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                let option = '<option>Seleccionar Producto</option>';
                for (var i = 0; i < response.length; i++) {
                    option += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '">';
                    option += response[i].description + '</option>';
                }

                // $('.c_product').html('');
                $('.c_product').append(option);
            }, 'json');
        });

        $('#frm_product').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_product').serialize();
                $.ajax({
                    url: '/warehouse.product.save',
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
                            toastr.success('Se grabó satisfactoriamente el producto');
                            $("#tbl_data").DataTable().ajax.reload();
                            clearDataProduct();
                            $('#mdl_add_product').modal('hide');
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            }
        });

        $('.btnAddCategory').click(function() {
            $('#mdl_add_category').modal('show');
        });
        $('#btnAddCategory').click(function() {
            $.post('/warehouse.category.save',
                'description=' + $('#add_category').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        toastr.success('Categoría grabada satisfactoriamente.');
                        $.get('/warehouse.category.all', function(response) {
                            let option = '<option>SIN CATEGORÍA</option>';
                            for (var i = 0; i < response.length; i++) {
                                option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                            }

                            $('.category').html('');
                            $('.category').append(option);
                            $('#mdl_add_category').modal('hide');
                        }, 'json');
                    } else {
                        toastr.error('Ocurrió un error inesperado.');
                    }
            }, 'json');
        });
        $('body').on('click', '.btnAddBrand', function() {
            $('#mdl_add_brand').modal('show');
        });

        $('#saveBrand').click(function() {
            $.post('/logistic.brand.save',
                'add_brand=' + $('#add_brand').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        $('#add_brand').val('');
                        $.get('/logistic.brand.get', function(response) {
                            let option = '<option value="">SIN MARCA</option>';
                            for (var i = 0; i < response.length; i++) {
                                option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                            }

                            $('#brand').html('');
                            $('#brand').append(option);
                            $('#mdl_add_brand').modal('hide');
                        }, 'json');
                        toastr.success('Marca Grabada Satisfactoriamente');

                    } else {
                        toastr.error('Ocurrió un error inesperado');
                    }
            }, 'json');
        });
    </script>
@stop
