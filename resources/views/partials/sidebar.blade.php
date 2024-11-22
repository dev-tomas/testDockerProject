<div class="az-sidebar">
    <div class="az-sidebar-header text-center">
        <a href="{{ route('home') }}" class="az-logo text-center" style="padding-top: 10px; padding-bottom: 10px;">
            <img src="{{ asset('images/logo.png') }}" style="display: block; margin: 0 auto;" class="text-center" height="45px">
        </a>
    </div>
    <div class="az-sidebar-loggedin">
        <div class="az-img-user online"><img src="{{ asset('images/profile/' . auth()->user()->logo) }}"></div>
        <div class="media-body">
            <h6>{{ Auth::user()->name }}</h6>
            @foreach (auth()->user()->roles as $item)
                @if ($loop->first)
                    <span>{{ $item->name }}</span>
                @endif
            @endforeach
        </div>
    </div>
    <div class="az-sidebar-body">
        <ul class="nav">
            <li class="nav-label">Menu Principal</li>
            <li class="nav-item {{ request()->is('home') ? 'active' : '' }}" >
                <a href="{{ route('home') }}" class="nav-link"><i class="ti ti-home"></i>Inicio</a>
            </li>
            @foreach (config('adminlte.menu') as $item)
                @if (isset($item['can']))
                    @can($item['can'])
                        <li class="nav-item">
                            <a href="{{ isset($item['url']) ? $item['url'] : '#' }}" class="nav-link with-sub"><i class="ti ti-{{ $item['icon'] }}"></i>{{ $item['text'] }}</a>
                            @if (isset($item['submenu']))
                                <ul class="nav-sub">
                                    @foreach ($item['submenu'] as $sm)
                                        @if (isset($sm['can']))
                                            @can($sm['can'])
                                                @if (isset($sm['url']))
                                                    <li class="nav-sub-item {{ request()->is($sm['url']) ? 'active' : '' }}">
                                                        <a href="{{ $sm['url'] }}" class="nav-sub-link">{{ $sm['text'] }}</a>
                                                    </li>
                                                @else
                                                    <li class="nav-sub-item">
                                                        <a href="#" class="nav-sub-link">{{ $sm['text'] }}</a>
                                                    </li>
                                                @endif
                                            @endcan
                                        @else
                                            @if (isset($sm['url']))
                                                <li class="nav-sub-item {{ request()->is($sm['url']) ? 'active' : '' }}">
                                                    <a href="{{ $sm['url'] }}" class="nav-sub-link">{{ $sm['text'] }}</a>
                                                </li>
                                            @else
                                                <li class="nav-sub-item">
                                                    <a href="#" class="nav-sub-link">{{ $sm['text'] }}</a>
                                                </li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endcan
                @else
                    <li class="nav-item">
                        <a href="{{ isset($item['url']) ? $item['url'] : '#' }}" class="nav-link with-sub"><i class="ti ti-{{ $item['icon'] }}"></i>{{ $item['text'] }}</a>
                        @if (isset($item['submenu']))
                            <ul class="nav-sub">
                                @foreach ($item['submenu'] as $sm)
                                    @if (isset($sm['can']))
                                        @can($sm['can'])
                                            <li class="nav-sub-item">
                                                <a href="{{ isset($sm['url']) ? $sm['url'] : '#' }}" class="nav-sub-link">{{ $sm['text'] }}</a>
                                            </li>
                                        @endcan
                                    @else
                                        <li class="nav-sub-item">
                                            <a href="{{ isset($sm['url']) ? $sm['url'] : '#' }}" class="nav-sub-link">{{ $sm['text'] }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
            <li class="nav-item" >
                <a href="{{ route('logout') }}" class="nav-link"><i class="ti ti-power-off"></i>Cerrar Sesión</a>
            </li>
        </ul>
    </div>
</div>