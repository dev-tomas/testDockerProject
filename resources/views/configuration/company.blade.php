@extends('layouts.azia')
@section('css')
    @if (auth()->user()->info->type_theme == 1)
        <style>
            .btn-white {color: #202124 !important;}
            .btn-white:active {outline: none;}
            .card-body h3, .title  {color: #202124 !important;}
        </style>
    @endif
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-body">
                    @if ($message = Session::get('error'))
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <h5><i class="fa fa-close"></i> Error!</h5>
                                    {{ $message }}
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                    	<div class="col-12 text-center">
                            <h3 class="card-title">CONFIGURACIÓN PRINCIPAL</h3>
                    	</div>
                    </div>
                    <div class="row">
	                    <div class="col-12">
	                    	<div class="accordion" id="accordionExample">
                                @can('empresa.emisor')
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    DATOS DE LA EMPRESA
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form method="post" id="frm_transmitter">
                                                    <div class="row">
                                                        <div class="col-12 col-md-3">
                                                            <div class="form-group">
                                                                <label>RUC :</label>
                                                                <input disabled="" type="text" class="form-control" value="{{Auth::user()->headquarter->client->document}}" >
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <div class="form-group">
                                                                <label>Razón Social o nombre Completo :</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->trade_name}}" name="trade_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Razón Comercial (OPCIONAL) :</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->business_name}}" name="business_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Email de esta empresa :</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->email}}" name="email">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Teléfonos :</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->phone}}" name="phone">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Página Web (OPCIONAL) :</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->web}}" name="web">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Dirección:</label>
                                                                <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->address}}" name="address">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <h4 class="title">Cuenta Bancaria</h4>
                                                        </div>
                                                        <div class="account_bank col-12">
                                                            @foreach($bank_accounts as $ba)
                                                                <div id="account-form-{{ $ba->id }}">
                                                                    <div class="row" >
                                                                        <div class="col-12 col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Moneda</label>
                                                                                <select class="form-control" name="bank_account_coin">
                                                                                    <option value="">Seleccionar</option>
                                                                                    @foreach($coins as $c)
                                                                                        @if($ba->coin_id == $c->id)
                                                                                            <option selected value="{{$c->id}}">{{$c->description}}</option>
                                                                                        @else
                                                                                            <option value="{{$c->id}}">{{$c->description}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-group">
                                                                                <label>Tipo de cuenta</label>
                                                                                <select class="form-control" name="bank_account_type">
                                                                                    <option value="">Seleccionar</option>
                                                                                    @foreach($bank_account_types as $bat)
                                                                                        @if($ba->bank_account_type_id == $bat->id)
                                                                                            <option selected value="{{$bat->id}}">{{$bat->description}}</option>
                                                                                        @else
                                                                                            <option value="{{$bat->id}}">{{$bat->description}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Nombre de Banco</label>
                                                                                <input type="text" class="form-control" name="bank_account_name" value="{{$ba->bank_name}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-group">
                                                                                <label>Titular</label>
                                                                                <input type="text" class="form-control" name="bank_account_headline" value="{{$ba->headline}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12 col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Número de Cuenta</label>
                                                                                <input type="text" class="form-control" name="bank_account_number" value="{{$ba->number}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-group">
                                                                                <label>CCI</label>
                                                                                <input type="text" class="form-control" name="bank_account_cci" value="{{$ba->cci}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Descripción adicinal(Opcional)</label>
                                                                                <input type="text" class="form-control" name="bank_account_observation" value="{{$ba->observation}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-group">
                                                                                <label>Cuenta Contable</label>
                                                                                <input type="text" class="form-control" name="bank_account_account" value="{{$ba->accounting_account}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <div class="form-group">
                                                                                <input type="hidden" class="account_id" name="bank_account_id" value="{{$ba->id}}">
                                                                                <button type="button" class="btn btn-danger-custom btn-block delete_permanent_account">ELIMINAR CUENTA</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="form-group">
                                                                                <button type="button" data-account-bank="{{ $ba->id }}" class="btn btn-primary-custom btn-block update_account_bank">GRABAR CUENTA</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-secondary-custom btn-block" type="button" id="add_account">CREAR CUENTA BANCARIA</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <h4 class="title">Medios de Pago</h4>
                                                        </div>
                                                    </div>
                                                    <div class="row ">
                                                        <div class="col-12 payment_methods">
                                                            @foreach ($payment_methods as $payment)
                                                                <div class="row">
                                                                    <div class="col-12 col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Nombre de Medio de Pago</label>
                                                                            <input type="text" value="{{ $payment->name }}" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Cuenta Contable</label>
                                                                            <input type="text" class="form-control" value="{{ $payment->account }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <button type="button" class="btn btn-danger-custom btn-block delete_permanent_payment" data-payment="{{ $payment->id }}">ELIMINAR METODO DE PAGO</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-secondary-custom btn-block" type="button" id="add_method">CREAR METODO DE PAGO</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-primary-custom">GRABAR</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('empresa.impresa')
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    PERSONALIZAR REPRESENTACIÓN IMPRESA
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form action="{{route('saveImage')}}" id="frm_image" enctype="multipart/form-data" method="post">
                                                    @csrf
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Logotipo para PDF</h4>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Logotipo en format .JPG para Facturas</label>
                                                                    <p>(320px por 80px) menos de 20KB</p>
                                                                    <input type="file" class="file" name="logo" accept="image/x-png,image/image/jpeg" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <img class="img-thumbnail" src="{{asset('images/' . Auth::user()->headquarter->client->logo)}}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <button class="btn btn-secondary-custom">SUBIR IMAGEN</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                                <form method="post" id="frm_personalize">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h4 class="title">Tamaño de archivos PDF para cada tipo de documento</h4>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN FACTURAS Y NOTAS ASOCIADAS</label>
                                                                <select name="invoice_size" id="invoice_size" class="form-control">
                                                                    @if(Auth::user()->headquarter->client->invoice_size == 0)
                                                                        <option value="0" selected> A4 </option>
                                                                        <option value="1"> TICKET </option>
                                                                    @else
                                                                        <option value="0"> A4 </option>
                                                                        <option value="1" selected> TICKET </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN BOLETAS Y NOTAS ASOCIADAS</label>
                                                                <select name="ticket_size" id="ticket_size" class="form-control">
                                                                    @if(Auth::user()->headquarter->client->ticket_size == 0)
                                                                        <option value="0" selected> A4 </option>
                                                                        <option value="1"> TICKET </option>
                                                                    @else
                                                                        <option value="0"> A4 </option>
                                                                        <option value="1" selected> TICKET </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN COTIZACIONES</label>
                                                                <select name="quotation_size" id="quotation_size" class="form-control">
                                                                    <option value="a4" {{ Auth::user()->headquarter->client->quotation_size == 'a4' ? 'selected' : '' }}> A4 </option>
                                                                    <option value="ticket" {{ Auth::user()->headquarter->client->quotation_size == 'ticket' ? 'selected' : '' }}> TICKET </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN GUIAS DE REMISION</label>
                                                                <select name="reference_guide_size" id="reference_guide_size" class="form-control">
                                                                    <option value="a4" {{ Auth::user()->headquarter->client->reference_guide_size == 'a4' ? 'selected' : '' }}> A4 </option>
                                                                    <option value="ticket" {{ Auth::user()->headquarter->client->reference_guide_size == 'ticket' ? 'selected' : '' }}> TICKET </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN RETENCIONES</label>
                                                                <select name="retention_size" id="retention_size" class="form-control">
                                                                    @if(Auth::user()->headquarter->client->retention_size == 0)
                                                                        <option value="0" selected> A4 </option>
                                                                        <option value="1"> TICKET </option>
                                                                    @else
                                                                        <option value="0"> A4 </option>
                                                                        <option value="1" selected> TICKET </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>FORMATO DE PDF EN PERCEPCIONES</label>
                                                                <select name="perception_size" id="perception_size" class="form-control">
                                                                    @if(Auth::user()->headquarter->client->perception_size == 0)
                                                                        <option value="0" selected> A4 </option>
                                                                        <option value="1"> TICKET </option>
                                                                    @else
                                                                        <option value="0"> A4 </option>
                                                                        <option value="1" selected> TICKET </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h4 class="title">Perzonalización de PDF</h4>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>INOFORMACION DE CABECERA</label>
                                                                <input type="text" maxlength="250" class="form-control" name="pdf_header" 
                                                                        value="{{ auth()->user()->headquarter->client->pdf_header }}">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>INOFORMACION DE PIE DE PAGINA</label>
                                                                <input type="text" maxlength="250" class="form-control" name="pdf_footer" 
                                                                        value="{{ auth()->user()->headquarter->client->pdf_footer }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <button class="btn btn-primary-custom">GRABAR</button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12"><br></div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('empresa.adicional')
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseT">
                                                    CONFIGURACIÓN ADICIONAL
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form method="post" id="frm_additional">
                                                    {{-- <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Precio unitario(con IGV) en productos y items</h4>
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Activar Precio Unitario(CON IGV), por defecto se usa el VALOR SIN IGV</label>
                                                                <div class="form-group">
                                                                    @if(Auth::user()->headquarter->client->price_type == 1)
                                                                        <input type="checkbox" checked value="1" name="price_type" id="price_type">
                                                                    @else
                                                                        <input type="checkbox" value="1" name="price_type" id="price_type">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset> --}}
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Tipo de Cambio</h4>
                                                            </div>
                                                            <div class="col-12">
                                                                <button class="btn btn-gray-custom mb-4" id="exchangeRate" type="button">
                                                                    Extraer Tipo de Cambio
                                                                </button>
                                                            </div>
                                                            <div class="col-12 col-md-6">
                                                                <label>Compra</label>
                                                                <input type="text" name="exchange_rate_pusrchase" id="exchange_rate_pusrchase" class="form-control" value="{{ auth()->user()->headquarter->client->exchange_rate_purchase }}">
                                                            </div>
                                                            <div class="col-12 col-md-6">
                                                                <label>Venta</label>
                                                                <input type="text" name="exchange_rate_sale" id="exchange_rate_sale" class="form-control" value="{{ auth()->user()->headquarter->client->exchange_rate_sale }}">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    {{-- <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Recargo al consumo</h4>
                                                            </div>
                                                            <div class="col-12 col-md-6">
                                                                <label>Recargo al consumo automático</label>
                                                                <div class="form-group">
                                                                    @if(Auth::user()->headquarter->client->automatic_consumption_surcharge == 1)
                                                                        <input type="checkbox" checked value="1" name="automatic_consumption_surcharge" id="automatic_consumption_surcharge">
                                                                    @else
                                                                        <input type="checkbox" value="1" name="automatic_consumption_surcharge" id="automatic_consumption_surcharge">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-6">
                                                                <label>Recargo al consumo automático porcentaje</label>
                                                                <input type="text" class="form-control" name="automatic_consumption_surcharge_price" id="automatic_consumption_surcharge_price" value="{{Auth::user()->headquarter->client->automatic_consumption_surcharge_price}}">
                                                            </div>
                                                        </div>
                                                    </fieldset> --}}
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Bienes o Servicios Región Selva (Automático)</h4>
                                                            </div>
                                                            <div class="col-6">
                                                                <label>¿Bienes Región Selva?</label>
                                                                <div class="form-group">
                                                                    @if(Auth::user()->headquarter->client->jungle_region_goods == 1)
                                                                        <input type="checkbox" checked value="1" name="jungle_region_goods" id="jungle_region_goods">
                                                                    @else
                                                                        <input type="checkbox" value="1" name="jungle_region_goods" id="jungle_region_goods">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label>Servicios Región Selva?</label>
                                                                <div class="form-group">
                                                                    @if(Auth::user()->headquarter->client->jungle_region_services == 1)
                                                                        <input type="checkbox" checked value="1" name="jungle_region_services" id="jungle_region_services">
                                                                    @else
                                                                        <input type="checkbox" value="1" name="jungle_region_services" id="jungle_region_services">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">Impuesto al consumo de Bolsas de Plástico</h4>
                                                            </div>
                                                            <div class="col-6">
                                                                <label>Activar Item de descripción automática</label>
                                                                <div class="form-group">
                                                                    @if(Auth::user()->headquarter->client->consumption_tax_plastic_bags == 1)
                                                                        <input type="checkbox" checked value="1" name="consumption_tax_plastic_bags" id="consumption_tax_plastic_bags">
                                                                    @else
                                                                        <input type="checkbox" value="1" name="consumption_tax_plastic_bags" id="consumption_tax_plastic_bags">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label> </label>
                                                                <div class="form-group">
                                                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                                                                    <input type="text" name="consumption_tax_plastic_bags_price" class="form-control" id="consumption_tax_plastic_bags_price" value="{{Auth::user()->headquarter->client->consumption_tax_plastic_bags_price}}">
                                                                    @else
                                                                        <input type="text" class="form-control" value="{{Auth::user()->headquarter->client->consumption_tax_plastic_bags_price}}">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">CONFIGURAR ENVIO DE NOTIFICACIONES DE COBRANZA</h4>
                                                            </div>
                                                            <div class="col-6">
                                                                <br>
                                                                @php
                                                                    $days = (array) unserialize(auth()->user()->headquarter->client->days_to_send_collections_notifications);
                                                                @endphp
                                                                <div class="form-group">
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(1, $days) ? 'checked' : '' }} value="1"> Lunes</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(2, $days) ? 'checked' : '' }} value="2"> Martes</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(3, $days) ? 'checked' : '' }} value="3"> Miercoles</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(4, $days) ? 'checked' : '' }} value="4"> Jueves</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(5, $days) ? 'checked' : '' }} value="5"> Viernes</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(6, $days) ? 'checked' : '' }} value="6"> Sábado</label>
                                                                    <label class="mx-2"><input type="checkbox" name="day[]" {{ in_array(7, $days) ? 'checked' : '' }} value="7"> Domingo</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label> </label>
                                                                <p>Configura los días en los que se enviará notificaciones de cobranza.</p>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h4 class="title text-center">TIPO DE CAJA Y CIERRES</h4>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Tipo de Caja y Cierre</label> <br>
                                                                    <input type="checkbox" name="cash_type" value="1" {{ auth()->user()->headquarter->client->cash_type == 1 ? 'checked' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label> </label>
                                                                <p>Si la opción esta activada se hará un cierre por usuario.</p>
                                                                <p>Si la opción esta desactivada se hará un cierre por local.</p>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    @if (auth()->user()->hasRole('superadmin') || session()->has('saou'))
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h4 class="title text-center">EMISIÓN CON FECHA ANTERIOR</h4>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label>Emisión fecha anterior</label>
                                                                    <div class="form-group">
                                                                        @if(Auth::user()->headquarter->client->issue_with_previous_data == 1)
                                                                            <input type="checkbox" checked value="1" name="issue_with_previous_data" id="issue_with_previous_data">
                                                                        @else
                                                                            <input type="checkbox" value="1" name="issue_with_previous_data" id="issue_with_previous_data">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label>Emisión con fecha anterior días</label>
                                                                    <div class="form-group">
                                                                        <input type="number" name="issue_with_previous_data_days" class="form-control" id="issue_with_previous_data_days" value="{{Auth::user()->headquarter->client->issue_with_previous_data_days}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="row"><div class="col-12"><br></div></div>
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h4 class="title text-center">BOLETAS</h4>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label><input type="radio" value="0" name="type_send_boletas" {{ auth()->user()->headquarter->client->type_send_boletas == 0 ? 'checked' : '' }}> Envío por Resumen Diario</label>
                                                                        <br>
                                                                        <label><input type="radio" value="1" name="type_send_boletas" {{ auth()->user()->headquarter->client->type_send_boletas == 1 ? 'checked' : '' }}> Envío Directo</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="row"><div class="col-12"><br></div></div>
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h4 class="title text-center">ACTIVO</h4>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label>Activo</label>
                                                                    <div class="form-group">
                                                                        @if(Auth::user()->headquarter->client->status == 1)
                                                                            <input type="checkbox" checked value="1" name="status" id="status">
                                                                        @else
                                                                            <input type="checkbox" value="1" name="status">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="row"><div class="col-12"><br></div></div>
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h4 class="title text-center">Pasar a Producción</h4>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label>Activo</label>
                                                                    <div class="form-group">
                                                                        <input type="checkbox" value="1" id="production" {{ Auth::user()->headquarter->client->production == 1 ? 'disabled' : '' }}>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p>Esta opción borra todos los comprobantes de prueba, quita la marca de Sin Valor Legal, mantiene los productos y clientes creados.</p>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    @endif
                                                    <div class="row"><div class="col-12"><br></div></div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-primary-custom">GRABAR</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @if (auth()->user()->ia == 1)
                                    <div class="card">
                                        <div class="card-header" id="headingApiSunat">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseApiSunat" aria-expanded="false" aria-controls="collapseApiSunat">
                                                    CONFIGURACIÓN DE CREDENCIALES API SUNAT
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseApiSunat" class="collapse" aria-labelledby="headingApiSunat" data-parent="#accordionExample">
                                            <div class="card-body" style="color: #000;">
                                                <h4>CONFIGURACIÓN DE CREDENCIALES API SUNAT</h4>
                                                @if (auth()->user()->headquarter->client->sunatCredentials == null)
                                                    <div class="alert alert-warning" role="alert">
                                                        Aún no tienes configuradas las <i>CREDENCIALES API SUNAT</i>, para enviar tus Guías de Remisión. Para generar tus credenciales.
                                                    </div>
                                                @endif
                                                <form id="sunatApiForm">
                                                    @php
                                                        $sunatClientId = auth()->user()->headquarter->client->sunatCredentials != null ? auth()->user()->headquarter->client->sunatCredentials->sunat_client_id : '';
                                                        $sunatClientSunat = auth()->user()->headquarter->client->sunatCredentials != null ? auth()->user()->headquarter->client->sunatCredentials->sunat_client_secret : '';
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Client ID</label>
                                                                <input type="text" class="form-control" name="sunat_client_id" id="sunat_client_id" value="{{ $sunatClientId }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label>Clave</label>
                                                                <input type="text" class="form-control" name="sunat_client_secret" id="sunat_client_id" value="{{ $sunatClientSunat }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-12">
                                                            @if (auth()->user()->headquarter->client->sunatCredentials == null)
                                                                <p class="text-black"><strong>Estado: </strong> <span class="badge badge-danger">No Conectado</span></p>
                                                            @else
                                                                @if (auth()->user()->headquarter->client->sunatCredentials->connection_status == 'not_connected')
                                                                    <p class="text-black"><strong>Estado: </strong> <span class="badge badge-danger">No Conectado</span></p>
                                                                @else
                                                                    <p class="text-black"><strong>Estado: </strong> <span class="badge badge-success">Conectado</span></p>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <button class="btn btn-primary" type="submit">GRABAR
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12"><br></div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                                    <div class="card">
                                        <div class="card-header" id="headingFour">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseT">
                                                    CERTIFICADO DIGITAL
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item active"><a href="#certificate" data-toggle="pill" class="nav-link active show" role="tab"><i class="fa fa-key"></i> Certificado Digital</a></li>
                                                    <li class="nav-item"><a href="#credential" data-toggle="pill" role="tab" class="nav-link"><i class="fa fa-user"></i> Usuario</a></li>
                                                    <button class="btn btn-secondary-custom pull-right" style="margin-left: 50px !important;top: -40px">SIGUE LOS PASOS PARA UNA FACTURACION ELECTRONICA ILIMITADA</button>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="certificate" role="tabpanel">
                                                        <form method="post" enctype="multipart/form-data" action="{{route("convertAndCertificate")}}">
                                                                @csrf
                                                            <div class="col-12"><br></div>
                                                            <div class="col-12">
                                                                @if($exists === true)
                                                                    <div class="alert alert-warning">
                                                                        <h5>Aviso!</h5>
                                                                        Ya tienes un certificado subido que vence: <b>{{Auth::user()->headquarter->client->expiration_certificate}}.</b>
                                                                        <br>
                                                                        Si subes otro certificado, reemplazará al anterior.
                                                                    </div>
                                                                @endif
                                                                <div class="form-group">
                                                                    <label> Agregar Certiticado <small>(El certificado debe de ser de extension .PFX)</small></label>
                                                                    <input type="file" class="form-control" name="certificate" id="certificate" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="password_certificate">Contraseña <small>(Agrega la clave que viene con el certificado)</small></label>
                                                                    <input type="password" class="form-control" name="password_certificate" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="expiration">Fecha de Vencimiento <small>(No olvides renovar tu certificado, cuando este pronto a vencer)</small></label>
                                                                    <input type="text" class="form-control date" name="expiration" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <button type="submit" class="btn btn-primary-custom">SUBIR CERTIFICADO</button>
                                                                <button class="btn btn-gray-custom" id=""><i class="fa fa-close"></i> DAR DE BAJA</button>
                                                                <a href="https://e-menu.sunat.gob.pe/cl-ti-itmenu/MenuInternet.htm?pestana=*&agrupacion=" target="_blank" class="btn btn-gray-custom pull-right" id=""> REGISTRAR CERTIFICADO EN SUNAT</a>
                                                            </div>
                                                        </form>
                                                    </div>
                                
                                
                                                    <div class="tab-pane fade" id="credential" role="tabpanel">
                                                        <div class="col-12"><br></div>
                                                        <form method="put" id="frm_sol">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group row">
                                                                        <label for="" class="col-12 col-sm-12 control-label">Usuario</label>
                                                                        <div class="col-12">
                                                                            <input type="text" class="form-control" name="user" value="{{Auth::user()->headquarter->client->usuario_sol}}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label for="" class="col-12 col-sm-12 control-label">Clave</label>
                                                                        <div class="col-12">
                                                                            <input type="text" class="form-control" name="password" value="{{Auth::user()->headquarter->client->clave_sol}}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6" style="font-size: 1.2em !important;">
                                                                    El registro del usuario secundario, se crea en la sesión del contribuyente dentro del sistema: <a href="#" style="text-decoration: underline !important;">Operaciones en Línea de la SUNAT >></a>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <button class="btn btn-primary-custom" id="save"><i class="fa fa-save"></i> GRABAR</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
        let coins = '';
        let bankAccountTypes = '';
        $.get('{{route("allCoins")}}', function(response) {
            $.each(response, function(index, column) {
                coins += '<option value="' + column.id + '">' + column.description + '</option>';
            });
        }, 'json');

        $.get('{{route("allBankAccountType")}}', function(response) {
            $.each(response, function(index, column) {
                bankAccountTypes += '<option value="' + column.id + '">' + column.description + '</option>';
            });
        }, 'json');

        $('#add_account').click(function() {
            let account = '<div><div class="row"><div class="col-12"><hr style="border: 1px solid gray;"></div></div><div class="row">';
                    account += '<div class="col-12 col-md-2">';
                        account += '<div class="form-group">';
                            account += '<label>Moneda</label>';
                            account += '<select name="coin[]" class="form-control">';
                                account += '<option value="">Seleccionar</option>';
                                account += coins;
                            account += '</select>';
                        account += '</div>';
                    account += '</div>';
                    account += '<div class="col-12 col-md-3">';
                        account += '<div class="form-group">';
                            account += '<label>Tipo de cuenta</label>';
                            account += '<select name="account_type[]" class="form-control">';
                                account += '<option value="">Seleccionar</option>';
                                account += bankAccountTypes;
                            account += '</select>';
                        account += '</div>';
                    account += '</div>';
                    account += '<div class="col-12 col-md-3">';
                        account += '<div class="form-group">';
                            account += '<label>Nombre de Banco</label>';
                            account += '<input type="text" class="form-control" name="name_bank[]">';
                        account += '</div>';
                    account += '</div>';
                    account += '<div class="col-12 col-md-3">';
                        account += '<div class="form-group">';
                            account += '<label>Titular</label>';
                            account += '<input type="text" class="form-control" name="headline[]">';
                        account += '</div>';
                    account += '</div>';
            account += '</div>';
            account += '<div class="row">';
                account += '<div class="col-12 col-md-2">';
                    account += '<div class="form-group">';
                        account += '<label>Número de Cuenta</label>';
                        account += '<input type="text" class="form-control" name="account_number[]">';
                    account += '</div>';
                account += '</div>';
                account += '<div class="col-12 col-md-3">';
                    account += '<div class="form-group">';
                        account += '<label>CCI</label>';
                        account += '<input type="text" class="form-control" name="cci[]">';
                    account += '</div>';
                account += '</div>';
                account += '<div class="col-12 col-md-4">';
                    account += '<div class="form-group">';
                        account += '<label>Descripción adicinal(Opcional)</label>';
                        account += '<input type="text" class="form-control" name="additional_description[]">';
                    account += '</div>';
                account += '</div>';
                account += '<div class="col-12 col-md-3">';
                    account += '<div class="form-group">';
                        account += '<label>Cuenta Contable</label>';
                        account += '<input type="text" class="form-control" name="accounting_account[]">';
                    account += '</div>';
                account += '</div>';
            account += '</div>';
            account += '<div class="row">';
                account += '<div class="col-12 col-md-12">';
                    account += '<div class="form-group">';
                        account += '<button class="btn btn-danger-custom btn-block delete_temporal">ELIMINAR CUENTA</button>';
                    account += '</div>';
                account += '</div>';
            account += '</div></div>';
            $('.account_bank').append(account);
        });

        $('#add_method').click(function() {
            let method = `<div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>Nombre de Medio de Pago</label>
                                        <input type="text" class="form-control" name="name_payment_method[]">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>Cuenta Contable</label>
                                        <input type="text" class="form-control" name="account_number_payment_method[]">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-danger-custom btn-block delete_payment">ELIMINAR METODO DE PAGO</button>
                                    </div>
                                </div>
                            </div>
                        <div>
            `;
            $('.payment_methods').append(method);
        });

    	$('#frm_transmitter').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frm_transmitter').serialize();
                $.confirm({
                    theme: 'modern',
                    animation: 'scale',
                    icon: 'fa fa-exclamation-triangle',
                    type: 'green',
                    draggable: false,
                    title: '¿Está seguro de guardar los cambios?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function () {
                                $.ajax({
                                    url: '{{route("saveConfigurationOne")}}',
                                    type: 'post',
                                    data: data + '&_token=' + '{{ csrf_token() }}',
                                    dataType: 'json',
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                    success: function(response) {
                                        if(response === true) {
                                            // toastr.success('Se actualizaron satisfactoriamente los datos');
                                            $('#collapseOne').removeClass('show');
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

        $('#frm_personalize').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios.");
            }else {
                e.preventDefault();
                let data = $('#frm_personalize').serialize();
                $.confirm({
                    theme: 'modern',
                    animation: 'scale',
                    icon: 'fa fa-exclamation-triangle',
                    type: 'green',
                    draggable: false,
                    title: '¿Está seguro de guardar los cambios?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function () {
                                $.ajax({
                                    url: '{{route("saveConfigurationTwo")}}',
                                    type: 'post',
                                    data: data + '&_token=' + '{{ csrf_token() }}',
                                    dataType: 'json',
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                    success: function(response) {
                                        if(response === true) {
                                            // toastr.success('Se actualizaron satisfactoriamente los datos');
                                            $('#collapseTwo').removeClass('show');
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

        $('.update_account_bank').click(function() {
            let id = $(this).data('account-bank');

            let data = formSerializer($('#account-form-' + id ));

            $.ajax({
                url: '{{route("updateAccountBank")}}',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                beforeSend: function() {

                },
                complete: function() {

                },
                success: function(response) {
                    if(response === true) {
                        toastr.success('Se actualizaron satisfactoriamente los datos');
                        $('#collapseThree').removeClass('show');
                    } else {
                        // console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });
        function formSerializer(selector){
             data="";
            selector.find('input, select').each(function(){
                data += $(this).attr("name") + "=" + $(this).val()+ "&" ;
                console.log($(this))
            });
            return data.replace(/&$/g,"");
        }

        $('#frm_additional').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frm_additional').serialize();
                $.confirm({
                    theme: 'modern',
                    animation: 'scale',
                    icon: 'fa fa-exclamation-triangle',
                    type: 'green',
                    draggable: false,
                    title: '¿Está seguro de guardar los cambios?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function () {
                                $.ajax({
                                    url: '{{route("saveConfigurationThree")}}',
                                    type: 'post',
                                    data: data + '&_token=' + '{{ csrf_token() }}',
                                    dataType: 'json',
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                    success: function(response) {
                                        if(response === true) {
                                            // toastr.success('Se actualizaron satisfactoriamente los datos');
                                            $('#collapseThree').removeClass('show');
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

        $('body').on('click', '.delete_temporal', function() {
            $(this).parent().parent().parent().parent().remove();
        });

        $('body').on('click', '.delete_permanent_account', function() {
            let t = $(this);
            let id = t.parent().find('.account_id').val();
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                title: '¿Está seguro de eliminar esta cuenta?',
                content: '',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            url: '{{route("deleteBankAccount")}}',
                            type: 'delete',
                            data: {
                                _token: '{{csrf_token()}}',
                                id: id
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                if(response === true) {
                                    t.parent().parent().parent().parent().remove();
                                    toastr.success('Se eliminó satisfactoriamente.');
                                } else {
                                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                                }
                            },
                            error: function(response) {
                                console.log(response.responseText);
                                toastr.error('No se puede eliminar ya que tiene datos asociados');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });
        });

        $('body').on('click', '.delete_payment', function() {
            $(this).parent().parent().parent().parent().remove();
        });

        $('body').on('click', '.delete_permanent_payment', function() {
            let t = $(this);
            let id = t.data('payment');
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                title: '¿Está seguro de eliminar éste método de pago?',
                content: '',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            url: '{{route("deletePaymentMethod")}}',
                            type: 'delete',
                            data: {
                                _token: '{{csrf_token()}}',
                                id: id
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                if(response === true) {
                                    t.parent().parent().parent().parent().remove();
                                    toastr.success('Se eliminó satisfactoriamente.');
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
                    },
                    Cancelar: function () {

                    }
                }
            });
        });
        $('.date').datepicker();
        $('#frm_sol').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios.");
            }else {
                e.preventDefault();
                let data = $('#frm_sol').serialize();
                $.ajax({
                    url: '{{route("saveDataSol")}}',
                    type: 'put',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response === true) {
                            toastr.success('Se actualizaron satisfactoriamente los datos.');
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
            }
        });


        /**
         * PRODUCTION
         */
        $('#production').on('change', function() {
            if($(this).is(':checked')) {
                $.confirm({
                    icon: 'fa fa-warning',
                    theme: 'modern',
                    // closeIcon: true,
                    animation: 'scale',
                    type: 'green',
                    title: 'ACTIVAR MODO PROCUCCIÓN',
                    content: 'Una vez activo los cambios no se podrán revertir, todos los registros como correlativos seran reiniciados',
                    buttons: {
                        activar: {
                            text: 'Confirmar',
                            btnClass: 'btn-orange',
                            action: function(){
                                $.ajax({
                                    url: "/production",
                                    type: 'post',
                                    data: '_token=' + '{{csrf_token()}}',
                                    dataType: 'json',
                                    success:function(request)
                                    {
                                        console.log()
                                        let response = request['response'];
                                        if(response == true) {
                                            $("#btn-demo").removeClass( "btn-danger-custom")
                                            $("#btn-demo").addClass( "btn-secondary-custom" );
                                            $("#btn-demo").html( "MODO PRODUCCIÓN" );
                                            
                                            toastr.success(request['message']);
                                            location.reload();
                                        }else{
                                            toastr.error(request['message']);
                                        }
                                        
                                    },
                                    error: function(response) {
                                        toastr.error('Ocurrio un error al momento de pasar a producción');
                                    }
                                })
                            }
                        },
                        
                        Cancelar:  {
                            text: 'Cancelar',
                            btnClass: 'btn-secondary',
                            action: function(){
                                toastr.warning("Accion Cancelada");
                            }
                        },
                        
                    }
                });
            }
        })

        $('#exchangeRate').click(function() {
           let date = moment().format("Y-MM-DD");
            $.get(`/get-exchangerate/by-date/${date}`, function(response) {
                $('#exchange_rate_pusrchase').val(response.compra);
                $('#exchange_rate_sale').val(response.venta);
            }, 'json');
        });

        $('#frm_theme').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios.");
            }else {
                e.preventDefault();
                let data = $('#frm_theme').serialize();
                $.ajax({
                    url: '/configuration.theme.store',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response === true) {
                            $('#collapseSeventeen').removeClass('show');
                            location.reload();
                        } else {
                            console.log(response.responseText);
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
                    }
                });
            }
        });
        $('#sunatApiForm').submit(function(e) {
            e.preventDefault();

            const data = $(this).serialize();

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de registrar estas credenciales?',
                content: '',
                type: 'orange',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.confirm({
                                theme: 'modern',
                                buttons: {
                                    Cerrar: {
                                        text: 'Cerrar',
                                        btnClass: 'btn-red',
                                        action: function(){
                                        }
                                    },
                                },
                                content: function() {
                                    var self = this;
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                        }
                                    });
                                    return $.ajax({
                                        type: 'post',
                                        url: '/configuration/store/api-sunat',
                                        data: data,
                                        dataType: 'json',
                                        success: function(response) {
                                            if(response['success'] == true) {
                                                self.setTitle('Credenciales grabadas correctamente.');
                                                self.setIcon('fa fa-check')
                                                self.setType('green')
                                                self.setContent(response['message'])
                                            } else {
                                                self.setTitle('Error al Grabas las credenciales');
                                                self.setIcon('fa fa-close')
                                                self.setType('red')
                                                self.setContent(response['message'])
                                            }
                                        },
                                        error: function(response) {
                                            self.setTitle('Error al Grabas las credenciales');
                                            self.setIcon('fa fa-close')
                                            self.setType('red')
                                        }
                                    });
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    },
                }
            });
        })
    </script>
    @if(Session::has('success'))
        <script>
            toastr.success('Se registró correctamente el certificado.');
        </script>
    @endif

    @if(Session::has('error'))
        <script>
            toastr.error('Oucrrió un error cuando intentaba grabar el certificado.');
        </script>
    @endif
@stop
