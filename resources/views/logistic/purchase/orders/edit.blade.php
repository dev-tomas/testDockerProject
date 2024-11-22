@extends('layouts.azia')
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_add_requirement">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="text-center">
                            <strong>EDITAR ORDEN DE COMPRA</strong>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-1">
                                <div class="form-group">
                                    <label for="warehouse">O/C</label>
                                    <input type="text" class="form-control" value="{{ $order->serie . ' - ' . $order->correlative }}" required disabled>
                                    <input type="hidden" name="opid" value="{{ $order->id }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="warehouse">Plazo de Entrega</label>
                                    <input type="text" name="delivery_term" id="delivery_term" class="form-control" value="{{ $order->delivery_term }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Condición</label>
                                    <input type="text" class="form-control" name="condition" id="condition" value="{{ $order->condition }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Entrega</label>
                                    <input type="text" class="form-control" name="delivery" id="delivery" value="{{ $order->delivery }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Inversión</label>
                                    <input type="text" class="form-control" name="investment" id="investment" value="{{ $order->investment }}" required>
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table" id="tbl_products" style="width: 100%">
                                        <thead class="text-center">
                                            <th>Código</th>
                                            <th>Productos/Servicios</th>
                                            <th width="120px">Cant.</th>
                                            <th width="120px">U. MED</th>
                                            <th>Observaciones</th>
                                            <th width="100px">Opciones</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->detail as $d)
                                                <input type="hidden" name="dpoid[]" value="{{ $d->id }}">
                                                <tr>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" name="code[]" class="form-control code" value="{{ $d->product->code }}" required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select name="product[]" id="product" class="form-control select2 product" required>
                                                                <option value="">Seleccione Produto/Servicio</option>
                                                                @foreach($products as $p)
                                                                    <option value="{{$p->id}}" p-price="{{$p->price}}" p-stock='{{ $p->stock }}' p-mesure="{{ $p->measure }}" p-code="{{ $p->code }}" {{ $d->product_id == $p->id ? "selected" : "" }}>{{$p->description}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" name="quantity[]" class="form-control" required value="{{ $d->quantity }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" id="umedida" class="form-control umedida" required disabled value="{{ $d->product->measure->description }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" name="observation[]" class="form-control" required value="{{ $d->observation }}">
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
                                <button class="btn btn-danger-custom btn-block cancel">Cancelar</button>
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
            $('.select2').select2();
        });

        $('.cancel').click(function() {
            window.location = '/logistic.order.purchase'; 
        });
        var products = "";
        $.post('/requirements/products', '_token=' +  '{{ csrf_token() }}', function(response) {
            for (var i = 0; i < response.length; i++) {
                products += '<option value="' + response[i].id + '" p-price="' + response[i].price + '" p-mesure="' + response[i].measure + '" p-stock="'+response[i].stock +'" p-code="' + response[i].code +'"" >';
                products += response[i].description + '</option>';
            }
        }, 'json');

        $('#btnAddProduct').on('click', function() {
            let data = `
                        <tr>
                            <td>
                                <div class="form-group">
                                    <input type="text" name="code[]" class="form-control code" required>
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
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" name="quantity[]" class="form-control" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" id="umedida" class="form-control umedida" required disabled>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" name="observation[]" class="form-control" required>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
            `;
            $('#tbl_products tbody').append(data);

            $('.select2').select2();
        });
        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
        });

        $('body').on('change', '.product', function() {
            let tr = $(this).parent().parent().parent();
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
                            Confirmar: function () {
                                $.ajax({
                                    url: '/logistic.order.purchase.update',
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
                                            toastr.success('Se actualizó satisfactoriamente la orden de compra');
                                            window.location="/logistic.order.purchase";
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
                            },
                            Cancelar: function () {}
                        }
                    });
                }
            }
        });
    </script>
@stop
