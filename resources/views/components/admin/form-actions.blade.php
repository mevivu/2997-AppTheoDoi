{{-- Admin Form Actions Component (Floating Bottom Bar) --}}
@props([
    'submitTitle' => __('Lưu thông tin'),
    'submitIcon' => 'ti ti-device-floppy',
    'submitName' => 'submitter',
    'submitValue' => 'save',
    'backRoute' => null,
    'backTitle' => __('Quay lại'),
])

<div {{ $attributes->class(['floating-bottom-actions']) }}>
    <div class="d-none d-sm-flex align-items-center gap-2 text-dark font-medium fs-13 me-1">
        <i class="ti ti-adjustments text-primary fs-4"></i>
        <span class="fw-semibold">{{ __('Thao tác:') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        {{-- Nút Submit chính --}}
        <button type="submit" name="{{ $submitName }}" value="{{ $submitValue }}" class="btn btn-save-settings d-inline-flex align-items-center gap-2">
            <i class="{{ $submitIcon }} fs-5"></i>
            <span>{{ $submitTitle }}</span>
        </button>

        {{-- Nút Action bổ sung (ví dụ: Nút Xóa) --}}
        {{ $slot }}

        {{-- Nút Quay lại (Nếu có) --}}
        @if ($backRoute)
            <a href="{{ $backRoute }}" class="btn btn-save-exit-settings d-inline-flex align-items-center gap-2">
                <i class="ti ti-arrow-left fs-5"></i>
                <span>{{ $backTitle }}</span>
            </a>
        @endif
    </div>
</div>
