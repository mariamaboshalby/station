@props([
    'type' => 'button',   // submit, link, button
    'color' => 'primary', // bootstrap colors
    'size' => '',         // sm, lg
    'icon' => null,
    'label' => '',
    'href' => null,
])

@if ($type === 'link' && $href)
    <a href="{{ $href }}" class="btn btn-{{ $color }} btn-{{ $size }}">
        @if ($icon)
            <i class="fas fa-{{ $icon }} me-1"></i>
        @endif
        {{ $label }}
    </a>
@elseif ($type === 'submit')
    <button type="submit" class="btn btn-{{ $color }} btn-{{ $size }}">
        @if ($icon)
            <i class="fas fa-{{ $icon }} me-1"></i>
        @endif
        {{ $label }}
    </button>
@else
    <button type="button" class="btn btn-{{ $color }} btn-{{ $size }}">
        @if ($icon)
            <i class="fas fa-{{ $icon }} me-1"></i>
        @endif
        {{ $label }}
    </button>
@endif
