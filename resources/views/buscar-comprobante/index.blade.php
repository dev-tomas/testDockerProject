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
                <h2>BUSCAR COMPROBANTE</h2>
            </div>
        </div>
        <div class="row d-flex justify-content-center mt-5">
            <div class="col-7">
                <fieldset>
                    <form action="{{ route('buscar-comprobante.find') }}" method="get">
                        {{-- @csrf --}}
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>RUC:</label>
                                    <input type="number" class="form-control" name="ruc" value="{{ $ruc }}" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>TIPO DE COMPROBANTE:</label>
                                    <select required="required" name="voucher" id="voucher" class="form-control" required>
                                        <option value="">Selecciona un tipo de Comprobante</option>
                                        <option value="01">01 - FACTURA ELECTRÓNICA</option>
                                        <option value="03">03 - BOLETA DE VENTA ELECTRÓNICA</option>
                                        <option value="07">07 - NOTA DE CRÉDITO ELECTRÓNICA</option>
                                        <option value="08">08 - NOTA DE DÉBITO ELECTRÓNICA</option>
                                        <option value="09">09 - GUÍA DE REMISIÓN REMITENTE ELECTRÓNICA</option>
                                        {{-- <option value="20">20 - COMPROBANTE DE RETENCIÓN ELECTRÓNICA</option>
                                        <option value="40">40 - COMPROBANTE DE PERCEPCIÓN ELECTRÓNICA</option>
                                        <option value="09">09 - GUÍA DE REMISIÓN REMITENTE ELECTRÓNICA</option>
                                        <option value="14">14 - RECIBO DE SERVICIOS PÚBLICOS ELECTRÓNICO</option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>SERIE:</label>
                                    <input type="text" class="form-control" name="serie" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>NÚMERO</label>
                                    <input type="text" pattern="\d*" class="form-control" name="number" maxlength="8" required>
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>TOTAL</label>
                                    <input type="number" class="form-control" step="0.01" name="total" id="total" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary-custom btn-block">BUSCAR</button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#voucher').change(function() {
            if ($(this).val() == 9) {
                $('#total').removeAttr('required');
                $('#total').attr('disabled', true);
            } else {
                $('#total').attr('required', true);
                $('#total').attr('disabled', false);
            }
        })
    </script>
</body>
</html>