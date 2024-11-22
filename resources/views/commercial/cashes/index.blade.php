@extends('layouts.azia')
@section('css')
    <style>.delete,.convert,.send {display: none;}</style>
    @can('cotizaciones.delete')
        <style>.delete{display: block;}</style>
    @endcan
    @can('cotizaciones.convert')
        <style>.convert{display: block;}</style>
    @endcan
    @can('cotizaciones.send')
        <style>.send{display: block;}</style>
    @endcan
    <style>
    .table-responsive,
.dataTables_scrollBody {
    overflow: visible !important;
}

.table-responsive-disabled .dataTables_scrollBody {
    overflow: hidden !important;
}</style>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">CAJAS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('superadmin'))
                                <button type="button" id="btnNewCash" class="btn btn-rounded btn-primary-custom pull-left mg-r-15">
                                    Nueva Caja
                                </button>
                                <a href="{{ route('movement.index') }}" class="btn btn-rounded btn-primary-custom">Movimientos de Caja</a>
                            @endif
                            <a href="{{ route('cashes.closings') }}" class="btn btn-rounded btn-primary-custom">Cierres de Caja</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Caja</label>
                                <input type="text" id="filter_name" name="filter_name" class="form-control" placeholder="Ingresar Entidad">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar por Estado</label>
                                <select name="filter_status" id="filter_status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Abiertas</option>
                                    <option value="0">Cerradas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                <thead>
                                    <th>NOMBRE</th>
                                    <th>USUARIO</th>
                                    <th>ESTADO</th>
                                    <th>MONTO</th>
                                    <th>OPCIONES</th>
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
    <div id="mdlnewCash" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frm_newCash">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" name="code" id="code" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Nombre de Caja</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Cofre #1" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Cuenta Contable</label>
                                    <input type="text" class="form-control" name="account" id="account" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Moneda</label>
                                    <select name="cash_coin" id="cash_coin" class="form-control" required>
                                        <option value="">Seleccione Moneda</option>
                                        @foreach ($coins as $coin)
                                            <option value="{{ $coin->id }}">{{ $coin->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <select name="user" id="user" class="form-control" required>
                                        <option value="">Seleccione Usuario</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Local</label>
                                    <input type="text" class="form-control" value="{{ $currentHeadquarter->description }}" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <button type="submit" id="save" class="btn btn-rounded btn-primary-custom">Grabar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_open_cash" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Abrir Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="from_open_cash">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Cajero</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                    <input type="hidden" name="ci" id="ci">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Monto de Apertura</label>
                                    <input type="text" class="form-control" name="open_amount" id="open_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <button type="submit" id="saveOpen" class="btn btn-rounded btn-primary-custom">Abrir Caja</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_close_cash" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="from_close_cash">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Cajero</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                    <input type="hidden" name="ci" id="cic">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Monto de Cierre</label>
                                    <input type="text" class="form-control" name="close_amount" id="open_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observacion</label>
                                    <input type="text" class="form-control" name="observation" id="observation">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <button type="submit" id="saveClose" class="btn btn-rounded btn-primary-custom">Cerrar Caja</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_movement_cash" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Moviemiento de Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="from_movement_cash">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select name="type_movement" id="type_movement" class="form-control" required>
                                        <option value="">Seleccione un Tipo de Movimiento</option>
                                        <option value="INGRESO">INGRESO</option>
                                        <option value="SALIDA">SALIDA</option>
                                    </select>
                                    <input type="hidden" name="ci" id="cim">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Movimiento</label>
                                    <input type="text" class="form-control" name="movement_observation" id="movement_observation">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Monto</label>
                                    <input type="text" class="form-control" name="movement_amount" id="movement_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <button type="submit" id="saveClose" class="btn btn-rounded btn-primary-custom">Agregar Movimiento de Caja</button>
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
            "order": [[ 0, "asc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial/cashes/dt',
                'type' : 'get',
                'data': function(d) {
                    d.filter_name = $('#filter_name').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            'columns': [
                {
                    data: 'name',
                },
                {
                  data: 'user.name'
                },
                {
                    data: 'status',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '0') {
                                data = '<span class="badge badge-danger">CERRADA</span>';
                            } else if (data == '1') {
                                data = '<span class="badge badge-success">ABIERTO</span>';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'coin.symbol'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                    if (aData['status'] == 1) {
                        button += '<a class="dropdown-item close text-red" href="#">Cerrar Caja</a>';
                        button += '<a class="dropdown-item movement" href="#">Agregar Movimiento</a>';
                    } else {
                        button += '<a class="dropdown-item open" href="#">Abrir Caja</a>';
                        button += '<a class="dropdown-item edit" href="#">Editar Caja</a>';
                    }
                    button += '</div>';
                    button += '</div>';
                $(nRow).find("td:eq(4)").html(button);
            }
        });

        $('#btnNewCash').click(function() {
            clearData();
            $('#mdlnewCash').modal('show');
        });

        $('#frm_newCash').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_newCash').serialize();
                
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de crear esta caja?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    url: '/commercial/cashes/store',
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
                                            $('#mdlnewCash').modal('hide');
                                            toastr.success('Se creó satisfactoriamente la caja');
                                            $('#frm_newCash #edit-cash-id').remove();
                                            $("#tbl_data").DataTable().ajax.reload();
                                            clearData();
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
        });

        $('body').on('click', '.edit', function(e) {
           e.preventDefault();
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $('#code').val(data['code']);
            $('#name').val(data['name']);
            $('#account').val(data['account']);
            $('#cash_coin').val(data['coin_id']);
            $('#user').val(data['user_id']);
            $('#frm_newCash').append(`<input type="hidden" id="edit-cash-id" name="cashid" value="${data['id']}" >`)

            $('#mdlnewCash').modal('show');
        });

        function clearData() {
            $('#cash_name').val('');
            $('#cash_ip').val('');
            $('#cash_coin').val('');
            $('#open_amount').val('');
            $('#user').val('');
            $('#ci').val('');
            $('#cic').val('');
            $('#observation').val('');
            $('#type_movement').val('');
            $('#movement_observation').val('');
            $('#movement_amount').val('');
        }

        $('#mdlnewCash').on('hidden.bs.modal', function (event) {
            $('#code').val('');
            $('#name').val('');
            $('#account').val('');
            $('#cash_coin').val('');
            $('#user').val('');
            $('#frm_newCash #edit-cash-id').remove();
        })

        $('body').on('click', '.open', function() {
            clearData();
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#ci').val(data['id']);
            
            $('#mdl_open_cash').modal('show');
        });

        $('#from_open_cash').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#from_open_cash').serialize();
                
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de abrir esta caja?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    url: '/commercial/cashes/open',
                                    type: 'post',
                                    data: data + '&_token=' + '{{ csrf_token() }}',
                                    dataType: 'json',
                                    beforeSend: function() {
                                        $('#saveOpen').attr('disabled');
                                    },
                                    complete: function() {

                                    },
                                    success: function(response) {
                                        if(response == true) {
                                            $('#mdl_open_cash').modal('hide');
                                            toastr.success('Se abrió satisfactoriamente la caja');
                                            $("#tbl_data").DataTable().ajax.reload();
                                            clearData();
                                        } else if(response == -9) {
                                            toastr.warning('Ya tienes una caja abierta.');
                                        } else {
                                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                                        }
                                    },
                                    error: function(response) {
                                        console.log(response.responseText);
                                        toastr.error('Ocurrio un error');
                                        $('#saveOpen').removeAttr('disabled');
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
        });

        $('body').on('click', '.close', function() {
            clearData();
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#cic').val(data['id']);
            
            $('#mdl_close_cash').modal('show');
        });

        $('#from_close_cash').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#from_close_cash').serialize();
                
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de cerrar esta caja?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    url: '/commercial/cashes/close',
                                    type: 'post',
                                    data: data + '&_token=' + '{{ csrf_token() }}',
                                    dataType: 'json',
                                    beforeSend: function() {
                                        $('#saveClose').attr('disabled');
                                    },
                                    complete: function() {

                                    },
                                    success: function(response) {
                                        if(response == true) {
                                            $('#mdl_close_cash').modal('hide');
                                            toastr.success('Se cerró satisfactoriamente la caja');
                                            $("#tbl_data").DataTable().ajax.reload();
                                            clearData();
                                        } else {
                                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                                        }
                                    },
                                    error: function(response) {
                                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                                        $('#saveClose').removeAttr('disabled');
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
        });

        $('body').on('click', '.movement', function() {
            clearData();
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#cim').val(data['id']);
            
            $('#mdl_movement_cash').modal('show');
        });

        $('#from_movement_cash').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#from_movement_cash').serialize();
                
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de crear este movimiento?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    url: '/commercial/cashes/store/movement',
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
                                            $('#mdl_movement_cash').modal('hide');
                                            toastr.success('Se creó satisfactoriamente el movimiento');
                                            $("#tbl_data").DataTable().ajax.reload();
                                            clearData();
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
        });

        /**** PDF FUNCTIONS ***/

        /**
         * Download Pdf
         **/
        $('body').on('click', '.btnPdf', function() {
            let id = $(this).parent().parent().attr('id');
            window.open('/commercial.quotations.download.pdf/' + id);
        });

        /**
         * Print
         */
        $('body').on('click', '.print', function() {
            //let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            let id = $(this).parent().parent().attr('id');
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial.quotations.show.pdf/' + id);

            $('#mdl_preview').modal('show');
        });


        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            window.open('/commercial.quotations.show.pdf/' + id, '_blank');

        })

        /**
         *btnPrint
         **/
        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        })

        
        /**
         *btnClose
         **/
        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        })

        $('#filter_date').daterangepicker({
            startDate: new Date(2019, 1, 1),
            endDate: moment().startOf('hour').add(32, 'hour'),
             locale: {
              format: 'DD/MM/YYYY'
            }
        });

        
        $('#filter_name').on('keyup', function() {
            tbl_data.ajax.reload();
        });

        $('#filter_status').on('change', function() {
            tbl_data.ajax.reload();
        });

        $('#btnQuotation').click(function() {
            window.location.href = '/commercial.quotations.create';
        });
    </script>
@stop
