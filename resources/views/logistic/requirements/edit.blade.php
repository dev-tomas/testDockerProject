@extends('layouts.azia')
@section('css')
    <style>
        .custom-checkbox.success .custom-control-input:checked~.custom-control-label::before{
            background-color:#28a745;
        }
        .custom-checkbox.warning .custom-control-input:checked~.custom-control-label::before{
            background-color:#ffc107;
        }
        .custom-checkbox.danger .custom-control-input:checked~.custom-control-label::before{
            background-color:#dc3545;
        }
        /** focus shadow pinkish **/
        .custom-checkbox.success .custom-control-input:focus~.custom-control-label::before{
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(40, 167, 69, 0.25); 
        }
        .custom-checkbox.warning .custom-control-input:focus~.custom-control-label::before{
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(255, 193, 7, 0.25); 
        }
        .custom-checkbox.danger .custom-control-input:focus~.custom-control-label::before{
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(255, 0, 247, 0.25); 
        }
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
        <div class="container-fluid">
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
                                            <option value="{{ $wh->id }}" {{ $requirement->warehouse_id == $wh->id ? "selected" : "" }}>{{ $wh->description }}</option>
                                        @endforeach   
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Solicitado</label>
                                    <input type="text" class="form-control" name="requested" value="{{ $requirement->requested }}" required disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Tipo de Requerimiento</label>
                                    <select name="typerequirement" id="" class="form-control" required>
                                        <option value="">Seleccione Tipo de Requerimiento</option>
                                        <option value="1" {{ $requirement->type_requirement == 1 ? "selected" : "" }}>Inventario</option>
                                        <option value="2" {{ $requirement->type_requirement == 2 ? "selected" : "" }}>Equipamento</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4"></div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Autorizado</label>
                                    <input type="text" class="form-control" id="authorized" name="authorized" value="{{ $requirement->authorized == null ? auth()->user()->name : $requirement->authorized }}" required>
                                    <input type="hidden" name="rqid" value="{{ $requirement->id }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Centro de Costos</label>
                                    <select name="centercost" id="centercost" class="form-control" required>
                                        <option value="">Seleccione Centro de Costos</option>
                                        @foreach ($costsCenter as $cc)
                                            <option value="{{ $cc->id }}" {{ $requirement->centercost_id == $cc->id ? "selected" : "" }}>{{ $cc->center }}</option>
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
                                                <th width="320px">Productos/Servicios</th>
                                                <th width="130px">Cant.</th>
                                                <th width="130px">Stock</th>
                                                <th width="320px">Observaciones</th>
                                                <th width="100px">*</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($requirement->detail as $d)
                                                    <input type="hidden" name="drqid[]" value="{{ $d->id }}">
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="category[]" id="category" class="form-control select2" required>
                                                                    <option value="">Seleccione Categoría</option>
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}" {{ $d->category_id == $category->id ? "selected" : "" }}>{{ $category->description }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="product[]" id="product" class="form-control select2 product" required>
                                                                    <option value="">Seleccione Produto/Servicio</option>
                                                                    @foreach($products as $p)
                                                                        <option value="{{$p->id}}" p-price="{{$p->price}}" p-stock='{{ $p->stock }}' p-code="{{ $p->internalcode }}" {{ $d->product_id == $p->id ? "selected" : "" }}>{{$p->description}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="productprice[]" id="productprice" class="price" value="{{ $d->product->price }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="quantity[]" class="form-control" required value="{{ $d->quantity }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" id="stock" class="form-control stock"  disabled value="{{ $d->product->stock->stock }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" name="observation[]" class="form-control" value="{{ $d->observation }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                        <fieldset class="mt-4">
                            <div class="row">
                                <div class="col-12 col-md-4 pl-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox danger">
                                            <input class="custom-control-input primary" type="radio" id="customCheckbox1" name="status" value="3" {{ $requirement->status == '3' ? "checked" : "" }} requried>
                                            <label for="customCheckbox1" class="custom-control-label">No Procede</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox warning">
                                            <input class="custom-control-input" type="radio" id="customCheckbox2" name="status" value="2" {{ $requirement->status == '2' ? "checked" : "" }} requried>
                                            <label for="customCheckbox2" class="custom-control-label">Revisión</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox success">
                                            <input class="custom-control-input" type="radio" id="customCheckbox3" name="status" value="1" {{ $requirement->status == '1' ? "checked" : "" }} requried>
                                            <label for="customCheckbox3" class="custom-control-label">Procede</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row mb-3 mt-3">
                            <div class="col-6 col-md-8"></div>
                            <div class="col-3 col-md-2">
                                <button class="btn btn-danger-custom btn-block">Cancelar</button>
                            </div>
                            <div class="col-3 col-md-2">
                                <button class="btn btn-primary-custom btn-block" id="btnAddRequirement">Guardar</button>
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
            // $('.select2').select2();
        });

        
        var products = "";
        $.post('/requirements/products', '_token=' +  '{{ csrf_token() }}', function(response) {
            for (var i = 0; i < response.length; i++) {
                products += '<option value="' + response[i].id + '" p-price="' + response[i].price + '" p-mesure="' + response[i].measure + '" p-stock="'+response[i].stock +'" p-code="' + response[i].internalcode +'"" >';
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
            let data = `
                        <tr>
                            <td>
                                <div class="form-group">
                                    <select name="category[]" id="category" class="form-control select2" required>
                                        <option value="">Seleccione Categoría</option>
                                        `
                                        + categories +
                                        `
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select name="product[]" id="product" class="form-control select2 product" required>
                                        <option value="">Seleccione Produto/Servicio</option>
                                        `
                                        + products +
                                        `
                                    </select>
                                    <input type="hidden" name="productprice[]" id="productprice" class="price">
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
                                    <input type="text" name="observation[]" class="form-control">
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
            `;
            $('#tbl_products tbody').append(data);

            // $('.select2').select2();
        });
        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
        });

        $('body').on('change', '.product', function() {
            let tr = $(this).parent().parent().parent();
            tr.find('.stock').val($('option:selected', this).attr('p-stock'));
            tr.find('.umedida').val($('option:selected', this).attr('p-mesure'));
            tr.find('.code').val($('option:selected',this).attr('p-code'));
            $(this).parent().find('.price').val($('option:selected',this).attr('p-price'));
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
                        title: '¿Está seguro de actualizar este requerimiento?',
                        content: '',
                        buttons: {
                            Confirmar: {
                                text: 'Confirmar',
                                btnClass: 'btn-green',
                                action: function(){
                                    $.ajax({
                                        url: '/requirements/update',
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
                                            } else if(response['response'] == -2) { 
                                                toastr.warning('Debe de seleccionar una acción.');
                                            }else {
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
    </script>
@stop
