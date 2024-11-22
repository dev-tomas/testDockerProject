@php
    use App\Http\Controllers\ChangeHeadquarterController as Headquarters;

    $headquarters = Headquarters::getHeadquarter();
    $headquarter = Headquarters::currentLocal();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name_not', 'BAUTIFAK') }}</title>
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/typicons.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/ionicons.css') }}"> --}}
    <link rel="stylesheet" href="{{asset('css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.min.css')}}">
    <link href="{{ asset('css/azia.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/font-awesome/css/font-awesome.min.css')}}">
    {{-- <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/fonts/ionicons.min.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/default/OverlayScrollbars.min.css')}}">
    <link href="{{asset('vendor/adminlte3/gyo/plugins/fonts/fonts.google.css')}}" rel="stylesheet">
    <link href="{{asset('vendor/adminlte3/gyo/plugins/datepicker/datepicker3.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/jconfirm/jquery-confirm.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/datatables/css/jquery.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/jquery.ml-keyboard.css') }}">
    @yield('css')

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/validator/validator.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.min.js')}}"></script>
    <!-- IMPLEMENTS FABRIZIO -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- END IMPLEMENTS FABRIZIO -->
</head>
<body class="az-body az-body-sidebar">
    @include('partials.sidebar')
    <div class="az-content az-content-dashboard-two">
        @include('partials.header')
        <div class="az-content-header d-block d-md-flex">
            <div>
                {{-- <h2 class="az-content-title tx-24 mg-b-5 mg-b-lg-8">Hola, bienvenido de nuevo!</h2> --}}
            </div>
            <div class="az-dashboard-header-right">
            </div>
        </div>
        <div class="az-content-body">
            @yield('content')
        </div>
        <div class="az-footer ht-40">
            <div class="container-fluid pd-t-0-f ht-100p">
                <span>&copy; {{ date('Y') }} BAUTIFAK</span>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdl-help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CAMBIAR DE LOCAL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="frm_changeLocal">
                        <div class="form-group">
                            <label>Local:</label>
                            <select name="headquarter" id="headquarter" class="form-control">
                                <option value="">Selecciona un Local</option>
                                @foreach ($headquarters as $h)
                                    <option value="{{ $h->id }}" {{ session()->get('headlocal') == $h->id ? 'selected' : '' }}>{{ $h->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary-custom" id="btnChangeLocal">Cambiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('vendor/adminlte3/gyo/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/default/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/jconfirm/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/datatables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
    <script src="{{ asset('js/jquery.ml-keyboard.min.js') }}"></script>
    <script src="{{asset('js/smartresize.js')}}"></script>
    <script src="{{ asset('js/azia.js') }}"></script>
    <script>
        $(function(){
            'use strict'
            $("input").keydown(function (e){
                var keyCode= e.which;

                if (keyCode == 13){
                    event.preventDefault();
                    return false;
                }
            });
            $("select").keydown(function (e){
                var keyCode= e.which;

                if (keyCode == 13){
                    event.preventDefault();
                    return false;
                }
            });

            $('.az-sidebar .with-sub').on('click', function(e){
                e.preventDefault();
                $(this).parent().toggleClass('show');
                $(this).parent().siblings().removeClass('show');
            });
            $(document).on('click touchstart', function(e){
                e.stopPropagation();
                if(!$(e.target).closest('.az-header-menu-icon').length) {
                    var sidebarTarg = $(e.target).closest('.az-sidebar').length;
                    if(!sidebarTarg) {
                        $('body').removeClass('az-sidebar-show');
                    }
                }
            });
            $('#azSidebarToggle').on('click', function(e){
                e.preventDefault();
                if(window.matchMedia('(min-width: 992px)').matches) {
                    $('body').toggleClass('az-sidebar-hide');
                } else {
                    $('body').toggleClass('az-sidebar-show');
                }
            });

            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            });

            var url = window.location.pathname,
                urlRegExp = new RegExp(url.replace(/\/$/,'') + "$");
                $('.az-sidebar-body .nav .nav-item .nav-sub .nav-sub-item a.nav-sub-link').each(function(){
                    if(urlRegExp.test(this.href.replace(/\/$/,''))){
                        $(this).parent().addClass('active');
                        $(this).parent().parent().parent().addClass('active');
                    }
                });
            // $('.btn').addClass('btn-rounded');
        });
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
    @yield('script_includes')
  </body>
</html>
