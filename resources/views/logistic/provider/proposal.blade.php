@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h1 class="text-center">PROPUESTAS DE PROVEEDOR</h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Requerimiento:</label>
                                <select id="slc_requirement" class="form-control">
                                    <option value="">Selecciona Requerimiento</option>
                                    @foreach ($requirements as $requirement)
                                        <option value="{{ $requirement->id }}">{{ $requirement->serie . '-' . $requirement->correlative }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-12">
            <div class="card card-default" id="pp">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label>Propuestas de Proveedores: <span id="requirement-label"></span></label>
                                <ul class="list-unstyled lp" id="list-requirement">
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <div class="col-xs-12 col-md-4">
                                    <div class="pdf-preview">
                                        <embed src="" type="application/pdf" class="ppdf" width="100%" height="600px" position="1" data-scale="1.6" />
                                    </div>
                                    <div class="comparator">
                                        {{-- <form id="frm_oc_create1" position='1'> --}}
                                            <div class="form-group">
                                                <input type="text" name="plazo" id="plazo" class="form-control plazo" placeholder="Plazo de Entrega" position="1" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="condicion" id="condicion" class="form-control condicion" placeholder="Condición" position="1" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="entrega" id="entrega" class="form-control entrega" placeholder="Entrega" position="1" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="inversion" id="inversion" class="form-control inversion" placeholder="Inversión" position="1" required>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox warning">
                                                    <input class="custom-control-input selection" type="radio" id="customCheckbox1" name="selection" value="1" position="1" prq="" required>
                                                    <label for="customCheckbox1" class="custom-control-label">Elegir</label>
                                                </div>
                                            </div>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="pdf-preview">
                                        <embed src="" type="application/pdf" class="ppdf" width="100%" height="600px" position="2" data-scale="1.6" />
                                    </div>
                                    <div class="comparator">
                                        {{-- <form id="frm_oc_create2" position='2'> --}}
                                            <div class="form-group">
                                                <input type="text" name="plazo" id="plazo" class="form-control plazo" placeholder="Plazo de Entrega" position="2" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="condicion" id="condicion" class="form-control condicion" placeholder="Condición" position="2" required>
                                            </div>
                                            <input type="hidden" name="rqid" class="rqid">
                                            <div class="form-group">
                                                <input type="text" name="entrega" id="entrega" class="form-control entrega" placeholder="Entrega" position="2" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="inversion" id="inversion" class="form-control inversion" placeholder="Inversión" position="2" required>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox warning">
                                                    <input class="custom-control-input selection" type="radio" id="customCheckbox2" name="selection" value="1" position="2" prq="" required>
                                                    <label for="customCheckbox2" class="custom-control-label">Elegir</label>
                                                </div>
                                            </div>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="pdf-preview">
                                        <embed src="" type="application/pdf" class="ppdf" width="100%" height="600px" position="3" data-scale="1.6" />
                                    </div>
                                    <div class="comparator">
                                        {{-- <form id="frm_oc_create3" position='3'> --}}
                                            <div class="form-group">
                                                <input type="text" name="plazo" id="plazo" class="form-control plazo" placeholder="Plazo de Entrega" position="3" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="condicion" id="condicion" class="form-control condicion" placeholder="Condición" position="3" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="entrega" id="entrega" class="form-control entrega" placeholder="Entrega" position="3" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="inversion" id="inversion" class="form-control inversion" placeholder="Inversión" position="3" required>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox warning">
                                                    <input class="custom-control-input selection" type="radio" id="customCheckbox3" name="selection" value="1" position="3" prq="" required>
                                                    <label for="customCheckbox3" class="custom-control-label">Elegir</label>
                                                </div>
                                            </div>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <button type="button" class="btn btn-primary-custom btnAddOC">Generar O/C</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        
    $('.ppdf').on('mouseover', function(){
        $(this).children('.photo').css({'transform': 'scale('+ $(this).attr('data-scale') +')'});
    }).on('mouseout', function(){
        $(this).children('.photo').css({'transform': 'scale(1)'});
    }).on('mousemove', function(e){
      $(this).children('.photo').css({'transform-origin': ((e.pageX - $(this).offset().left) / $(this).width()) * 100 + '% ' + ((e.pageY - $(this).offset().top) / $(this).height()) * 100 +'%'});
    }).each(function(){
      $(this)
        // add a photo container
        .append('<div class="photo"></div>')
        // some text just to show zoom level on current item in this example
        .append('<div class="txt"><div class="x">'+ $(this).attr('data-scale') +'x</div>ZOOM ON<br>HOVER</div>')
        // set up a background image for each tile based on data-image attribute
        .children('.photo').css({'background-image': 'url('+ $(this).attr('data-image') +')'});
    })


        $('#pp').hide();

        $("#slc_requirement").change(function() {
           let ir = $(this).val();

           $('.rqid').val(ir);

            $.ajax({
                url: '/logistic.providers.proposals.get?requirement=' + ir,
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    if(response != null) {
                        let proposals = ``;
                        for (let i = 0; i < response.length; i++) {
                            proposals += `
                                    <li>
                                        <span class="requirement-info" style="display:block;">` + response[i].document + `</span>
                                        <i class="fa fa-file-pdf-o text-danger" style="font-size:48px;" aria-hidden="true"></i>
                                        <input type="checkbox" class="rq" path="` + response[i].file + `" prq="` + response[i].id + `" style="margin-left: 5em;" position="">
                                    </li>
                            `;
                        }

                        $("#list-requirement").html('');
                        $("#list-requirement").append(proposals);
                        $('#pp').show();
                    } else {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            }); 
        });

        let path = '';
        let position = '';
        let count = 0;

        $('body').on('change', '.rq', function() {
            path = $(this).attr('path');

            let prqid = $(this).attr('prq');

            if ($(this).prop('checked')) {
                $('.ppdf').each(function() {
                    if ($(this).attr('src') == '') {
                        position = $(this).attr('position');
                    }                    
                });

                $('.ppdf').each(function() {
                    if ($(this).attr('position') == position) {
                        $(this).attr('src', path);
                    }
                });

                $('.selection').each(function() {
                    if ($(this).attr('position') == position) {
                        $(this).attr('prq', prqid);
                    }
                });

                $(this).attr('position', position);

                count++;
            } else {
                if (!$(this).prop('checked')) {
                    currentPosition = $(this).attr('position');

                    $('.selection').each(function() {
                        if ($(this).attr('position') == currentPosition) {
                            $(this).attr('prq', '');
                        }
                    });

                    $('.ppdf').each(function() {
                        if ($(this).attr('position') == currentPosition) {
                            let newPosition = '';
                            $(this).attr('src', newPosition);
                        }
                    });
                    clearDataPosition(currentPosition);
                }

                $(this).attr('position', '');
                count--;
            }

            if (count == 3) {
                $('.rq').each(function() {
                    if (!$(this).prop('checked')) {
                        $(this).attr('disabled', true);
                    }
                });
            } else if (count < 3) {
                $('.rq').each(function() {
                    if (!$(this).prop('checked')) {
                        $(this).removeAttr('disabled');
                    }
                });
            }
        });

        function clearDataPosition(position) {
            $('.plazo').each(function() {
                if ($(this).attr('position') == position) {
                    $(this).val('');
                }
            })
            $('.entrega').each(function() {
                if ($(this).attr('position') == position) {
                    $(this).val('');
                }
            })
            $('.condicion').each(function() {
                if ($(this).attr('position') == position) {
                    $(this).val('');
                }
            })
            $('.inversion').each(function() {
                if ($(this).attr('position') == position) {
                    $(this).val('');
                }
            })
        }

        function clearAllData() {
            $('.plazo').each(function() {
                $(this).val('');
            })
            $('.entrega').each(function() {
                $(this).val('');
            })
            $('.condicion').each(function() {
                $(this).val('');
            })
            $('.inversion').each(function() {
                $(this).val('');
            })
            $('.selection').each(function() {
                $(this).attr('prq', '');
            })
        }

        let infoPosition = '';
        let plazo, entrega, condicion, inversion, id;

        $('body').on('change', '.selection', function() {
            infoPosition = $(this).attr('position');
        });

        $('.btnAddOC').click(function(e) {
            getDataPosition(infoPosition);
            let requid = $('.rqid').val();
            if(plazo != '' || entrega != '' || condicion != '' || inversion != '') {
                $.ajax({
                    url: '/logistic.providers.proposals.generateoc?plazo=' + plazo + '&entrega=' + entrega + '&condicion=' + condicion + '&inversion=' + inversion + '&id=' + id + '&rquid=' + requid,
                    type: 'post',
                    data: '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('.btnAddOC').attr('disabled', true);
                    },
                    success: function(response) {
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente la orden de compra');
                            window.location = '/logistic.order.purchase'; 
                        } else if(response == -1) {
                            toastr.warning('Debe de completar todos los campos');
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                            console.log(response);
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('.btnAddOC').removeAttr('disabled');
                    }
                });
            } else {
                toastr.warning('Debe de completar todos los campos');
            }
        });

        function getDataPosition(position) {
            $('.plazo').each(function() {
                if ($(this).attr('position') == position) {
                    plazo = $(this).val();
                }
            })
            $('.entrega').each(function() {
                if ($(this).attr('position') == position) {
                    entrega = $(this).val();
                }
            })
            $('.condicion').each(function() {
                if ($(this).attr('position') == position) {
                    condicion = $(this).val();
                }
            })
            $('.inversion').each(function() {
                if ($(this).attr('position') == position) {
                    inversion = $(this).val();
                }
            })
            $('.selection').each(function() {
                if ($(this).attr('position') == position) {
                    id = $(this).attr('prq');
                }
            })
        }
    </script>
@stop
