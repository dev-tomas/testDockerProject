@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">Reporte de Stock de Productos por Almacén</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-secondary-custom float-right" type="button" id="btnexcel">
                                <i class="fa fa-download"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formPreview">
                        <div class="row mb-3">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Filtro por Productos</label>
                                    <select id="product" class="form-control">
                                        <option value="">Todos los Productos</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Filtro por Categorías</label>
                                    <select id="filter_category" class="form-control">
                                        <option value="">Todas las Categorías</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <button class="btn btn-primary-custom float-right" type="submit" id="generatePreview">Generar</button>
                            </div>
                        </div>
                    </form>

                    <form id="frm_preview">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="previewTable">
                                        <thead>
                                        <tr>
                                            <th>PRODUCTO</th>
                                            <th>CODIGO</th>
                                            @foreach($warehouses as $warehouse)
                                                <th>{{ $warehouse->description }}</th>
                                            @endforeach
                                            <th>TOTAL STOCK</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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
        let warehouses = @json($warehouses);

        $('#formPreview').submit(function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/reporte/stock-almacen/generate',
                type: 'post',
                data:  {product: $('#product').val(), category: $('#filter_category').val()},
                dataType: 'json',
                success: function(response) {
                    $('#previewTable tbody').html('');
                    $.each(response, function(i, item) {
                        let tr = `
                            <tr>
                                <td>${item.product}</td>
                                <td align="center">${item.code}</td>
                        `;
                        $.each(warehouses, function(w, warehouse) {
                            let waa = removeAccents(warehouse.description).toLowerCase().replace(/\s/g, '');
                            if (response[i][waa] != undefined) {
                                tr += `<td align="center">${response[i][waa]} <br> ${item.operation}</td>`;
                            } else {
                                tr += `<td align="center"></td>`;
                            }
                        })
                        tr += `<td align="center">${item.total}</td>
                            </tr>
                        `;

                        $('#previewTable tbody').append(tr);
                    });

                    $('#previewTable tbody').show();
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });

        const removeAccents = (str) => {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        $('#btnexcel').click(function(e) {
            e.preventDefault();

            let product = $('#product').val();
            let category = $('#filter_category').val();

            window.open(`/reporte/stock-almacen/excel?product=${product}&category=${category}`, '_blank');
        });

        function titleCase(str) {
            return str
                .toLowerCase()
                .split(' ')
                .map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                })
                .join(' ');
        }

        function snakeCase(str) {
            str = str.replace(/[\w]([A-Z])/g, function(match) {
                return match[0] + '_' + match[1];
            }).replace(/\s+/g, '_').toLowerCase();

            return str.slice(0)
        }

        $('#product').select2();

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
                "daysOfWeek": ["Dom","Lun","Mar","Mie","Ju","Vi","Sab"],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                "firstDay": 0
            },
            "startDate": moment().startOf('month'),
            "endDate": moment().endOf('month'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@stop
