<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.min.css') }}">
    <title>Buscar Comprobante</title>
    <style>
        fieldset {
            border: 1px solid #4444;
            border-radius: 15px;
            padding: 15px;
        }
        input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button {-webkit-appearance: none;margin: 0;}input[type=number] { -moz-appearance:textfield;}

        .form-control {
            border-radius: 20px !important;
        }
        label{font-weight: 500;}
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mt-5 mb-5">
                <img src="{{asset('images/logo.png')}}" style="width: 150px;">
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <h2>COMPROBANTE</h2>
            </div>
        </div>
        <div class="row d-flex justify-content-center mt-5">
            <div class="col-7 text-center">
                <fieldset>
                    <h4>{{ $customer->description }}</h4>
                    @if ($typeVoucher->code == '07')
                        <p>NOTA DE CRÉDITO {{ $serie }} - {{ $sale->correlative }}</p>
                    @elseif($typeVoucher->code == '08')
                        <p>NOTA DE DEBITO {{ $serie }} - {{ $sale->correlative }}</p>
                    @else
                        <p>{{ $typeVoucher->description }} {{ $serie }} - {{ $sale->correlative }}</p>
                    @endif

                    <p><strong>Fecha de Emisión:</strong> {{ date('d-m-Y', strtotime($issue)) }}</p>
                    @if ($expiration != false)
                        <p><strong>Fecha de Vencimiento:</strong> {{ date('d-m-Y', strtotime($expiration)) }}</p>
                        @if (isset($sale->coin_id))
                            <p><strong>Total:</strong> {{ $sale->coin->symbol }} {{ $sale->total }}</p>
                        @else
                            <p><strong>Total:</strong> S/. {{ $sale->total }}</p>
                        @endif
                    @endif
                    

                    @if ($typeVoucher->code == '07' || $typeVoucher->code == '08' ||  $typeVoucher->code == "09")
                        <a href="/storage/pdf/{{ $ruc }}/{{ $typeVoucher->code }}-{{ $serie }}-{{ $sale->correlative }}.pdf" class="btn btn-primary-custom btn-block" target="_blank">Descargar PDF</a>
                    @else
                        <a href="/storage/pdf/{{ $ruc }}/{{ $serie }}-{{ $sale->correlative }}.pdf" class="btn btn-primary-custom btn-block" target="_blank">Descargar PDF</a>
                    @endif
                    <a href="/storage/xml/{{ $ruc }}/{{ $ruc }}-{{ $voucher }}-{{ $serie }}-{{ $sale->correlative }}.xml" class="btn btn-secondary-custom btn-block" download>Descargar XML</a>
                </fieldset>
                <div class="row d-flex justify-content-center mt-5 mb-5">
                    <a href="{{ route('buscar.comprobante') }}" class="btn btn-link">NUEVA BUSQUEDA</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>