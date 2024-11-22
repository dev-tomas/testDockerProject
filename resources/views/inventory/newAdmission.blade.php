@extends('layouts.azia')
@section('css')
    <style>
        #tbl_products tbody tr td {padding: 10px 2px;}
    </style>
@endsection
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_add_admission">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="text-center">
                            <strong>NUEVO INGRESO</strong>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Fecha de Ingreso</label>
                                    <input value="{{ $date }}" type="text" class="form-control datepicker" name="date" id="date" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <select name="provider" id="provider" class="form-control" required>
                                        <option value="">Seleccione Proveedor</option>
                                        @foreach ($providers as $cc)
                                            <option value="{{ $cc->id }}" {{ $cc->id == $shopping->provider_id ? 'selected' : '' }}>{{ $cc->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Guía de Ingreso</label>
                                    <input type="text" class="form-control" name="guide" required>
                                    <input type="hidden" name="sid" value="{{ $shopping->id }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Almacén</label>
                                    <select name="warehouse" id="warehouse" class="form-control select2" required>
                                        <option value="">Seleccione Almacén</option>  
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ $loop->iteration == 1 ? 'selected' : '' }}>{{ $wh->description }}</option>
                                        @endforeach   
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Responsable</label>
                                    <input type="text" class="form-control" name="requested" value="{{ auth()->user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4"></div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table" id="tbl_products">
                                            <thead class="text-center">
                                                <th width="280px">FACT COMPRA</th>
                                                <th width="130px">COD. PROD.</th>
                                                <th width="130px">U. MED</th>
                                                <th width="320px">CATEGORIA</th>
                                                <th width="320px">MARCA</th>
                                                <th width="320px">DESCRIPCIÓN</th>
                                                <th width="320px">SERIE</th>
                                                <th width="320px">LOTE</th>
                                                <th width="320px">F. VENC.</th>
                                                <th width="320px">GARANTIA</th>
                                                {{-- <th width="320px">STOCK</th> --}}
                                                <th width="320px">CANT. COMPRA</th>
                                                <th width="320px">CANT. ING.</th>
                                                <th width="320px">OBSERVACION</th>
                                                {{-- <th width="320px">NUEVO STOCK</th> --}}
                                            </thead>
                                            <tbody>
                                                @foreach ($shopping->detail as $sd)
                                                    @if ($sd->product->operation_type != 2)
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="hidden" value="{{$sd->unit_price}}" name="unit_price[]">
                                                                {{ $shopping->shopping_serie }}-{{ $shopping->shopping_correlative }}
                                                            </td>
                                                            <td>{{ $sd->product->internalcode }}</td>
                                                            <td class="text-center">{{ $sd->product->ot->code }}</td>
                                                            <td>{{ $sd->product->category_id != null ? $sd->product->category->description : '' }}</td>
                                                            <td>{{ $sd->product->brand_id != null ? $sd->product->brand->description : '' }}</td>
                                                            <td>{{ $sd->product->description }} <input type="hidden" value="{{ $sd->product_id }}" name="admission_pid[]"></td>
                                                            <td><input type="text" name="serie[]" class="form-control"></td>
                                                            <td><input type="text" name="lot[]" class="form-control"></td>
                                                            <td><input value="{{ $date }}" type="text" class="form-control datepicker" name="expiration[]" autocomplete="off" required></td>
                                                            <td><input type="text" name="warranty[]" class="form-control"></td>
                                                            {{-- <td class="text-center"><span class="old-stock">{{ $sd->product->stock->stock }}</span><input type="hidden" name="pstock[]" value="{{ $sd->product->stock->stock }}"></td> --}}
                                                            <td class="text-center">{{ $sd->quantity }}</td>
                                                            <td><input type="number" class="form-control qa" step="0.01" name="quantity_admission[]" value="{{ $sd->quantity }}"></td>
                                                            <td><input type="text" class="form-control" name="observation[]"></td>
                                                            {{-- <td class="text-center"><span class="new-stock"></span></td> --}}
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
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
                                <button class="btn btn-primary-custom btn-block" id="btnAddAdmission">Crear</button>
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
            $('.qa').keyup(function() {
                let tr = $(this).parent().parent();
                let qa = $(this).val() * 1;
                let oq = tr.find('.old-stock').text() * 1;

                let nq = qa + oq;
                console.log(nq);
                tr.find('.new-stock').text(nq);
            });

            $('#frm_add_admission').validator().on('submit', function(e) {
                if(e.isDefaultPrevented()) {
                    toastr.warning('Debe llenar todos los campos obligatorios');
                } else {
                    e.preventDefault();
                    let data = $('#frm_add_admission').serialize();
                    $.ajax({
                        url: '/inventory/admission/store',
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
                                toastr.success('Se grabó satisfactoriamente el ingreso');
                                window.location.href = '/inventory';
                            } else if(response == -9) {
                                toastr.success('No hay un correlativo configurado para ingresos.');
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
        });
    </script>
@stop
