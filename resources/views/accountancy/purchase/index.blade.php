@extends('layouts.azia')
@section('content')
<input type="hidden" id="audocument" value="{{ auth()->user()->headquarter->client->document }}">
<input type="hidden" id="autydocument" value="{{ auth()->user()->headquarter->client->document_type->code }}">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">CONTABILIDAD COMPRAS</h3>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                </div>
                <div class="card-body">
                    <form id="formPreview">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Número de Movimiento</label>
                                    <input type="number" id="movement" name="movement" class="form-control" placeholder="Ingresar Entidad">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Número de Voucher</label>
                                    <input type="number" id="voucher" name="voucher" class="form-control" placeholder="Ingresar voucher">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Filtro por Fechas</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary-custom" type="button" id="generatePreview">Generar</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mt-4">
                        <div class="col-12">
                            <form id="frm_preview">
                                <div class="table-responsive">
                                    <table class="table" id="previewTable">
                                        <thead>
                                            <th>RUC</th>
                                            <th>TD</th>
                                            <th>SERIE</th>
                                            <th>NUM DOC</th>
                                            <th>IGV</th>
                                            <th>TOTAL</th>
                                            <th>FECHA</th>
                                            <th>ENTIDAD</th>
                                            <th>T</th>
                                            <th>COD. E</th>
                                            <th>GLOSA</th>
                                            <th>CUENTA</th>
                                            <th>CENTRO DE COSTO</th>
                                            <th>TIPO DE OPERACION</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <button class="btn btn-secondary-custom" type="submit" id="executePreview">Ejecutar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#executePreview').hide();
        });
        
        $('body').on('keyup', '.ia', function () {
            var $this = $(this);
            var maxlength = 7;
            var value = $this.val();
            if (value && value.length >= maxlength) {
                $this.val(value.substr(0, maxlength));
            }
        });

        $('#generatePreview').click(function() {
            $('#table-kardex').hide();
            let data = $('#formPreview').serialize();
            $.ajax({
                url: '/accounting/purchase/preview/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    $('#previewTable tbody').html('');
                    $.each(response, function(i, item) {
                        let glosa = '';
                        let accounts = '';
                        let location = '';
                        $.each(item.details, function(d, el) {
                            glosa += `${el.glosa} <br>`;
                            accounts += `<input type="number" min="0" step="1" pattern="\d*" maxlength="7" class="form-control form-control-sm ia" name="account[]" value="${el.account}" required>
                                        <input type="hidden" name="shopping[]" value="${item.shopping}">
                                        <input type="hidden" name="type[]" value="${el.type}"> <br>`;
                            location += `${el.location} <br>`;
                        })
                        let tr = `
                            <tr rowspan="${item.details.length}">
                                <td>${item.document}</td>
                                <td>${item.type_voucher}</td>
                                <td>${item.serie}</td>
                                <td>${item.correlative}</td>
                                <td>${item.igv}</td>
                                <td>${item.total}</td>
                                <td>${item.date}</td>
                                <td>${item.provider}</td>
                                <td>${item.t}</td>
                                <td>${item.provider_code}</td>
                                <td>${glosa}</td>
                                <td>${accounts}</td>
                                <td>${location}</td>
                            </tr>
                        `;
                        

                            
                            // tr = '<tr>';
                            // tr += '<td>'+ response[i].provider.document + '</td>';
                            // tr += '<td>'+ response[i].voucher.code + '</td>';
                            // // tr += '<td>'+ response[i].type_transaction + '</td>';
                            // tr += '<td>'+ response[i].shopping_serie + '</td>';
                            // tr += '<td>'+ response[i].shopping_correlative + '</td>';
                            // tr += '<td>'+ response[i].igv + '</td>';
                            // tr += '<td>'+ response[i].total + '</td>';
                            // tr += '<td>'+ moment(response[i].date).format('DD/MM/YYYY') + '</td>';
                            // tr += '<td>'+ response[i].provider.description + '</td>';
                            // tr += '<td>P</td>';
                            // tr += '<td>'+ response[i].provider.code +'</td>';
                            // tr += '<td>';
                            
                            // if (isMercaderia == 1) {
                            //     tr += 'Compra de Mercaderia <br>';
                            // }
                            // if (isActivo == 1) {
                            //     tr += 'Compra de Activo Fijo <br>';
                            // }
                            // if (isGasto == 1) {
                            //     tr += 'Compra de Gasto <br>';
                            // }
                            // tr += '</td>';
                            
                            // tr += '<td>';
                            //     if (isMercaderia == 1) {
                            //         tr += '<input type="number" min="0" step="1" pattern="\d*" maxlength="7" class="form-control form-control-sm ia" name="account[]" required><input type="hidden" name="shopping[]" value="'+ response[i].id +'"><input type="hidden" name="type[]" value="1"> <br>';
                            //     }
                            //     if (isActivo == 1) {
                            //         tr += '<input type="number" min="0" step="1" pattern="\d*" maxlength="7" class="form-control form-control-sm ia" name="account[]" required><input type="hidden" name="shopping[]" value="'+ response[i].id +'"><input type="hidden" name="type[]" value="0"> <br>';
                            //     }
                            //     if (isGasto == 1) {
                            //         tr += '<input type="number" min="0" step="1" pattern="\d*" maxlength="7" class="form-control form-control-sm ia" name="account[]" required><input type="hidden" name="shopping[]" value="'+ response[i].id +'"><input type="hidden" name="type[]" value="2"> <br>';
                            //     }
                            // tr += '</td>';
                            // if (response[i].detail[0].warehouse != null) {
                            //     tr += '<td> <strong>Almacén</strong> '+  response[i].detail[0].warehouse.description +'</td>';
                                
                            // } else if (response[i].detail[0].center_cost != null) {
                            //     tr += '<td> <strong>Centro de Costo</strong> '+  response[i].detail[0].center_cost.center +'</td>';
                            // } else {
                            //     tr += '<td>-</td>'
                            // }
                            // tr += '</tr>';
                        
                        $('#previewTable tbody').append(tr);
                    });

                    $('#previewTable tbody').show();
                    if (response.length == 0) {
                        $('#executePreview').hide();
                        toastr.info('No se encontraron datos');
                    } else {
                        $('#executePreview').show();
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });

        $('#frm_preview').submit(function(e) {
            e.preventDefault();

            let correlatives = $('#formPreview').serialize();
            let accounts = $('#frm_preview').serialize();
            let accountsArr = $('#frm_preview').serializeArray();
            var pass = true;

            $(accountsArr).each(function (i, item) {
                if (item.name == "account[]") {
                    if(item.value.length < 7 ) {
                        console.log(item.value.length)
                        toastr.warning('Los números de cuenta deben contener 7 dígitos.');
                        pass = false;
                        console.log(pass);
                        return false;
                    }
                }
            });

            if (pass) {
                window.open('/accounting/purchase/generate? ' + correlatives + '&' + accounts, '_blank');

                location.reload();
            }
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
