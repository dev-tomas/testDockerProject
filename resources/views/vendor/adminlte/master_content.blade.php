<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SOFACT</title>
    <link rel="shortcut icon" href="{{ asset('vendor/adminlte3/gyo/img/sofact.ico') }}">
    <link rel="icon" href="{{ asset('vendor/adminlte3/gyo/img/sofact.ico') }}" type="image/x-icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/fonts/ionicons.min.css')}}">

    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/default/OverlayScrollbars.min.css')}}">

    <link href="{{asset('vendor/adminlte3/gyo/plugins/fonts/fonts.google.css')}}" rel="stylesheet">

    <link href="{{asset('vendor/adminlte3/gyo/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/toastr/toastr.min.css')}}">

    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/jconfirm/jquery-confirm.min.css')}}">

    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/daterangepicker/daterangepicker.css')}}">

    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/datatables/css/jquery.dataTables.min.css')}}">
    @if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
        <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.css')}}">
    @endif

    {{-- <!--<link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/dist/css/adminlte.min.css')}}">-->
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/dist/css/adminlte2.css')}}"> --}}
    <link href="{{ asset('css/custom.min.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <style>input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button {-webkit-appearance: none;margin: 0;}input[type=number] { -moz-appearance:textfield;}</style>
    @yield('css')
    <style>
        fieldset
        {
            border: 1px solid #ddd !important;
            margin: 0;
            xmin-width: 0;
            padding: 10px;
            position: relative;
            border-radius: 30px;
            padding-left:10px!important;
        }

        textarea {
            resize: none;
        }

        .menu-open {
            display: block !important;
        }

        .color-gray{
            background-color: #ababab;
        }

        .form-control {
            border-radius: 30px;
        }

        .btn-light{
            background-color: #aaaaaa;
        }
    </style>
</head>
<body class="nav-sm">
    <div class="container body">
        <div class="main_container">
            @yield('body')
        </div>
    </div>

</body>
<script src="{{asset('vendor/adminlte3/gyo/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('vendor/adminlte3/gyo/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE -->
<script src="{{asset('vendor/adminlte3/gyo/dist/js/adminlte.js')}}"></script>


<script src="{{asset('vendor/adminlte3/gyo/default/jquery.overlayScrollbars.min.js')}}"></script>


<script src="{{asset('vendor/adminlte3/gyo/plugins/toastr/toastr.min.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/validator/validator.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/jconfirm/jquery-confirm.min.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
<script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/datatables/js/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('vendor/adminlte3/gyo/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('vendor/adminlte3/gyo/plugins/daterangepicker/daterangepicker.js')}}"></script>

<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
<script src="{{asset('js/smartresize.js')}}"></script>
<script src="{{asset('js/custom.min.js')}}"></script>

@if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
    <script src="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.min.js')}}"></script>
@endif

<!-- OPTIONAL SCRIPTS -->
{{-- <!--<script src="{{asset('vendor/adminlte3/gyo/dist/js/demo.js')}}"></script> --}}
{{-- <script src="{{asset('vendor/adminlte3/gyo/dist/js/pages/dashboard3.js')}}"></script>--> --}}

<script>
    $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true
    });
</script>
<script>
    $('#changeLocal').click(function () {
        $('#mdl-help').modal('show');
    });

    $('#frm_changeLocal').on('submit', function(e) {
        e.preventDefault();
        let data = $('#frm_changeLocal').serialize();
        $.ajax({
            url: '/change-local',
            type: 'put',
            data: data + '&_token=' + '{{ csrf_token() }}',
            dataType: 'json',
            beforeSend: function() {
                $('#btnChangeLocal').attr('disabled');
            },
            complete: function() {

            },
            success: function(response) {
                if(response == true) {
                    toastr.success('Se cambió satisfactoriamente de local');
                    $('#mdl-help').modal('hide');
                    location.reload();
                } else {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            },
            error: function(response) {
                console.log(response.responseText);
toastr.error('Ocurrio un error');
                $('#btnChangeLocal').removeAttr('disabled');
            }
        });
    });
</script>
@toastr_render()
@yield('script_admin')
<script>
    $(document).on('hidden.bs.modal', function (event) {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });
</script>
</html>
