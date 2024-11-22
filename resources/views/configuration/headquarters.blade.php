@extends('layouts.azia')
@section('css')
    <style>.edit{display: none;}</style>
    @can('localserie.edit')
        <style>.edit{display: block;}</style>
    @endcan
    <style>
        td, td div {font-size: 11.5px !important;}
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">LOCALES</h3>
                        </div>
                        <div class="col-12">
                            @can('localserie.create')
                                <a href="{{route('addHeadquarter')}}" class="btn btn-primary-custom">NUEVO LOCAL</a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%; font-size: 10px !important;">
                                <thead class="thead-dark ">
                                    <th width="15px">Código</th>
                                    <th width="100px">Descripción</th>
                                    <th width="20px">Ubigeo (INEI)</th>
                                    <th width="280px">Dirección Exacta</th>
                                    <th width="50px">Departamento</th>
                                    <th width="50px">Provincia</th>
                                    <th width="50px">Distrito</th>
                                    <th width="290px">Series</th>
                                    <th width="10px"></th>
                                </thead>
                                <tbody style="font-size: 10px !important;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            // 'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'pageLength' : 5,
            'lengthMenu': false,
            "scrollX": true,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "asc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'bLengthChange' : false,
            'ajax': {
                'url': '{{route('dt_headquarters')}}',
                'type' : 'get',
                'data': function(d) {

                }
            },
            'columns': [
                {data: 'code'},
                {data: 'description'},
                {data: 'ubigeos.code'},
                {data: 'address'},
                {data: 'ubigeos.department'},
                {data: 'ubigeos.province'},
                {data: 'ubigeos.district'},
                {
                    data: null,
                    orderable: false,
                },
                {data: null, orderable: false},
            ],
            'fnRowCallback': function( nRow, aData) {
                let content = '';
                if(aData['correlatives'] != undefined) {
                    $.each(aData['correlatives'], function(key, value) {
                        if (value['type_voucher'].visible == 1) {
                            content += '<div class="row">';
                            content += '<div class="col-9 text-left">' + value['type_voucher'].description;
                            if (value.contingency == 1 ) {
                                content += '<strong> [CONTIGENCIA]</strong>';
                            }
                            content += '</div>';
                            content += '<div class="col-3 text-right">' + value.serialnumber;
                            content += '</div>';
                            content += '</div>';
                        }
                        
                    });
                } else {
                    content = '';
                }

                let sunatcode = '<div>'+ aData['description'] +'</div><div><strong>Código Sunat:</strong><br> '+ aData['sunat_code'] +'</div>';

                let sizes = '';
                if(aData['client'].invoice_size == 0) {
                    sizes += '<div><strong>FACTURAS Y NOTAS:</strong> A4</div>';
                } else {
                    sizes += '<div><strong>FACTURAS Y NOTAS:</strong> TICKET</div>';
                }

                if(aData['client'].retention_size == 0) {
                    sizes += '<div><strong>RETENCIONES:</strong> A4</div>';
                } else {
                    sizes += '<div><strong>RETENCIONES:</strong> TICKET</div>';
                }

                if(aData['client'].ticket_size == 0) {
                    sizes += '<div><strong>BOLETA:</strong> A4</div>';
                } else {
                    sizes += '<div><strong>BOLETA:</strong> TICKET</div>';
                }

                if(aData['client'].perception_size == 0) {
                    sizes += '<div><strong>PERCEPCIÓN:</strong> A4</div>';
                } else {
                    sizes += '<div><strong>PERCEPCIÓN:</strong> TICKET</div>';
                }

                let boton = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                OPCIONES
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a class="dropdown-item edit" href="#">Editar</a>
                            </div>
                `;

                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"> Opciones';
                button += '<span class="caret"></span>'
                button += '</button>';
                button += '<ul style="text-align:center;" class="dropdown-menu" x-placement="bottom-start">';
                button += '<li><a href="#" class="edit">Editar</a></li>';
                button += '</ul>';
                button += '</div>';

                $(nRow).find('td:eq(1)').html(sunatcode);
                $(nRow).find('td:eq(7)').html(content);
                // $(nRow).find('td:eq(7)').html(sizes);
                $(nRow).find('td:eq(8)').html('<button type="button" class="edit btn btn-redondos btn-primary-custom btn-sm" title="Editar"><i class="fa fa-edit"></i></button>');
            }
        });

        $('body').on('click', '.edit', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/configuration/headquarters/edit/' + data['id'];
        });
    </script>
@stop
