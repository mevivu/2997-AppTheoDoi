<span>
    @if ($phone)
        {{ $phone }}
        <i class="ti ti-copy copy-btn" style="font-size: 18px; cursor: pointer;" data-value="{{ $phone }}"></i>
        <i class="ti ti-check check-icon" style="display:none;font-size: 18px"></i>
    @else
        N/A
    @endif
</span>
