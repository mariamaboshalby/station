<div class="mb-3">
    <label for="{{ $id }}" class="form-label fw-bold">{{ $label }}</label>

    @if($type === 'select')
        <select name="{{ $name }}" id="{{ $id }}" class="form-select" {{ $attributes }}>
            {{ $slot }}
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" 
               class="form-control form-control-lg text-center" 
               {{ $attributes }}>
    @endif
</div>
