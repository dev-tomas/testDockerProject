<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SOFACT</title>
    <link rel="shortcut icon" href="{{ asset('vendor/adminlte3/gyo/img/sofact.ico') }}">
    <link rel="icon" href="{{ asset('vendor/adminlte3/gyo/img/sofact.ico') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Poppins:500,600|Roboto:400,500,600" rel="stylesheet">
    <link rel="stylesheeit" href="{{asset('vendor/adminlte3/gyo/css/main/material-icons.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/adminlte3/gyo/css/main/login.css')}}">
    <script src="https://kit.fontawesome.com/4e2b5b0f91.js" crossorigin="anonymous"></script>
</head>
<body class="hold-transition sidebar-mini">
@yield('body')

</body>
<footer></footer>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="{{asset('vendor/adminlte3/gyo/js/toggler.js')}}"></script>
<script src="{{asset('vendor/adminlte3/gyo/plugins/jquery/jquery.min.js')}}"></script>

<script>
    $('body').on('keyup', '#document', function() {
        if($(this).val().length == 11) {
            $.ajax({
                url: '/consult.ruc/' + $(this).val(),
                type: 'post',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    if(response != 'No se encontro el ruc') {
                        $('.valid-ruc').addClass('active');
                    } else {
                        $('.valid-ruc').removeClass('active');
                    }
                },
                error: function(response) {

                }
            });
        } else {
            $('.valid-ruc').removeClass('active');
        }
    });
</script>
</html>
