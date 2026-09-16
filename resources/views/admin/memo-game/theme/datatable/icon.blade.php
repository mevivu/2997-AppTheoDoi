<div class="d-flex justify-content-center align-items-center">
    @if ($icon && file_exists(public_path($icon)))
        <img src="{{ asset($icon) }}" alt="{{ $name }}" class="rounded border shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
    @else
        <div class="rounded bg-light border d-flex align-items-center justify-center text-primary" style="width: 42px; height: 42px;">
            @if ($code === 'vehicles')
                <i class="ti ti-car fs-3"></i>
            @elseif ($code === 'flowers')
                <i class="ti ti-flower fs-3"></i>
            @elseif ($code === 'numbers')
                <i class="ti ti-numbers fs-3"></i>
            @elseif ($code === 'flags')
                <i class="ti ti-flag fs-3"></i>
            @else
                <i class="ti ti-palette fs-3"></i>
            @endif
        </div>
    @endif
</div>
