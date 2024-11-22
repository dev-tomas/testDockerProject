@if (is_string($item))
    <li class="nav-item">{{ $item }}</li>
@else
    <li class="nav-item">
        <a href="{{ $item['href'] }}">
            <i class="fa fa-{{ isset($item['icon']) ? $item['icon'] : '' }}
            {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i> {{ $item['text'] }} @if (isset($item['submenu']))<span class="fa fa-chevron-down"></span>@endif
        </a>
        @if (isset($item['submenu']))
            <ul class="nav child_menu">
                @each('adminlte::partials.menu-item', $item['submenu'], 'item')
            </ul>
        @endif
    </li>
@endif
