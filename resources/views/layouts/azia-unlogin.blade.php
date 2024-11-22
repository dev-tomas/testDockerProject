<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/typicons.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/ionicons.css') }}"> --}}
    <link rel="stylesheet" href="{{asset('css/themify-icons.css')}}">
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
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.css')}}">
    <link rel="stylesheet" href="{{ asset('css/jquery.ml-keyboard.css') }}">
    @yield('css')
</head>
<body>
    <div class="az-header">
        <div class="container">
            <div class="az-header-left">
                <a href="{{ route('home') }}" class="az-logo"><img src="{{ asset('images/sofactlogo.svg') }}" height="50px"></a>
            </div>          
        </div>
    </div>
    
    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Hola!</h2>
                        <p class="az-dashboard-text"></p>
                    </div>
                </div>
  
                @yield('content')
            </div>
        </div>
    </div>
    <div class="az-footer ht-40">
        <div class="container ht-100p pd-t-0-f">
            <span>&copy; {{ date('Y') }} SOFACT</span>
        </div>
    </div>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{asset('vendor/adminlte3/gyo/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
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
    <script src="{{asset('vendor/adminlte3/gyo/plugins/select2/select2.min.js')}}"></script>
    <script src="{{ asset('js/jquery.ml-keyboard.min.js') }}"></script>
    <script src="{{asset('js/smartresize.js')}}"></script>
    <script src="{{ asset('js/azia.js') }}"></script>
    @toastr_render()
  </body>
</html>
