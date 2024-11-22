<div class="az-header">
    <div class="container-fluid">
        <div class="az-header-left">
            <a href="#" id="azSidebarToggle" class="az-header-menu-icon"><span></span></a>
        </div>
        <div class="az-header-center"></div>
        <div class="az-header-right">
                <div style="padding: 0 5px;">
                    @if (Auth::user()->headquarter->client->production == 0)
                        <span class="modelabelProduction danger">MODO DEMO</span>
                    @elseif(Auth::user()->headquarter->client->production == 1)
                        <span class="modelabelProduction">MODO PRODUCCIÓN</span>
                    @endif
                </div>
                <div style="padding: 0 5px;">
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                        <button class="btn btn-dark-custom" style="height: 35px !important;" id="changeLocal">{{ $headquarter->description }}</button>
                    @else
                        {{ Auth::user()->headquarter->description }}
                    @endif
                </div>
                <div style="padding: 0 5px;">
                    @if (auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('mange.index') }}" style="height: 35px !important;" class="btn btn-gray-custom">GESTIONAR</a>
                    @endif
                    @if (session()->has('saou'))
                        <a href="{{ route('mange.revertir') }}" style="height: 35px !important;" class="btn btn-gray-custom">CERRAR SESSION ACTUAL</a>
                    @endif
                </div>
            <div class="dropdown az-header-notification"></div>
        </div>
    </div>
</div>