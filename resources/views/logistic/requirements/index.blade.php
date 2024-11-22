@extends('layouts.azia')
@section('css')
    @cannot('requirement.edit')
        <style>
            .edit {
                pointer-events: none;
                cursor: default;
                text-decoration: none;
                color: black;
            }
        </style>
    @endcannot
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row  text-center">
                        <div class="col-md-12">
                            <h3 class="card-title">REQUERIMIENTOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            @can('requirement.create')
                                <button class="btn btn-primary-custom" id="addRequirements">
                                    Nuevo Requerimiento
                                </button>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('requirement.export')
                                <a class="btn btn-secondary-custom pull-right" href="{{route('requirement.export')}}">
                                    Descargar Excel
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Area</label>
                                <input type="text" id="denomination" class="form-control" placeholder="Ingresar Entidad">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Documento</label>
                                <input type="text" id="document" class="form-control" placeholder="Ingresar documento">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text" id="filter_date" class="form-control" placeholder="Seleccionar fechas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                    <th>NUMERO</th>
                                    <th>FECHA</th>
                                    <th>AREA</th>
                                    <th>RESPONSABLE</th>
                                    <th>NECESIDAD</th>
                                    <th>RESUMEN</th>
                                    <th>ESTADO</th>
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
    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-danger-custom pull-right" id="btnClose">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <iframe frameborder="0" width="100%;" height="700px;" id="frame_pdf">

                            </iframe>
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
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 1, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/requirements/dt',
                'type' : 'get',
                // 'data': function(d) {
                //     d.denomination = $('#denomination').val();
                //     d.document = $('#document').val();

                //     let rangeDates = $('#filter_date').val();
                //     var arrayDates = rangeDates.split(" ");
                //     var dateSpecificOne =  arrayDates[0].split("/");
                //     var dateSpecificTwo =  arrayDates[2].split("/");

                //     d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                //     d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                // }
            },
            'columns': [
                {
                    data: 'serie',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            data = '<a href="/requirements/edit/' + row.serie + '/' + row.correlative + '" class="edit link-rq">' + row.serie + '-' + row.correlative + '</a>'
                        }
                        return data;
                    }
                },
                {
                    data: 'create'
                },
                {
                    data: 'center'
                },
                {
                    data: 'requested'
                },
                {
                    data: 'type',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '1') {
                                data = 'Inventario';
                            } else if (data == '2') {
                                data = 'Equipamiento';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'total'
                },
                {
                    data: 'status',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '0') {
                                data = '-';
                            } else if (data == '1') {
                                data = '<span class="badge badge-success">PROCEDE</span>';
                            } else if (data == '2') {
                                data = '<span class="badge badge-warning">REVISIÓN</span>';
                            } else if (data == '3') {
                                data = '<span class="badge badge-danger">NO PROCEDE</span>';
                            } else {
                                data = '-';
                            }
                        }
                        return data;
                    }
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find("td:eq(5)").html('<button type="button" class="btn btn-danger-custom btn-sm print">PDF</button>');
            }
        });

        $('#addRequirements').on('click', function() {
            window.location.href = '{{route('addRequirements')}}';
        });

        /**
         * Print
         */
         $('#tbl_data').on('click', '.print', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $('.btnPrint').attr('id', data['id']);
            $('#frame_pdf').attr('src', '/requirements/pdf/' + data['id']);

            $('#mdl_preview').modal('show');
        });

        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        })

    </script>
@stop
