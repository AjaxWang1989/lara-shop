@if (is_array($item))
    <li class="{{ $item['top_nav_class'] }}">
        <a href="{{ $item['href'] }}"
           @if (isset($item['children'])) class="dropdown-toggle" data-toggle="dropdown" @endif
           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
        >
            <i class="fa fa-fw {{ $item['icon'] or 'fa-circle-o' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
            {{ $item['text'] }}
            @if (isset($item['label']))
                <span class="label label-{{ $item['label_color'] or 'primary' }}">{{ $item['label'] }}</span>
            @elseif (isset($item['children']))
                <span class="caret"></span>
            @endif
        </a>
        @if (isset($item['children']))
            <ul class="dropdown-menu" role="menu">
                @foreach($item['children'] as $subitem)
                    @if (is_string($subitem))
                        @if($subitem == '-')
                            <li role="separator" class="dropdown-item divider"></li>
                        @else
                            <li class="dropdown-item dropdown-header">{{ $subitem }}</li>
                        @endif
                    @else
                    <li class="dropdown-item {{ $subitem['top_nav_class'] }}">
                        <a href="{{ $subitem['href'] }}">
                            <i class="fa {{ $subitem['icon'] or 'fa-circle-o' }} {{ isset($subitem['icon_color']) ? 'text-' . $subitem['icon_color'] : '' }}"></i>
                            {{ $subitem['text'] }}
                            @if (isset($subitem['label']))
                                <span class="label label-{{ $subitem['label_color'] or 'primary' }}">{{ $subitem['label'] }}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </li>
@endif
