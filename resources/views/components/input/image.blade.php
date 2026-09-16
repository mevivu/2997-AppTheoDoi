@php
    $uniqueId = 'img_picker_' . \Illuminate\Support\Str::random(8);
@endphp

<div class="custom-image-uploader w-100" id="{{ $uniqueId }}">
    @if($label)
        <label class="form-label fw-bold mb-2">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{-- Main Container Card --}}
    <div class="image-uploader-card p-3 rounded-3 border bg-white position-relative" id="{{ $uniqueId }}_card">
        <div class="d-flex align-items-center gap-3 flex-wrap flex-sm-nowrap">

            {{-- Left: Image Thumbnail Box --}}
            <div class="image-thumbnail-box flex-shrink-0 position-relative"
                 id="{{ $uniqueId }}_box"
                 style="width: {{ $width ?: '110px' }}; height: {{ $height ?: '110px' }};">
                <img src="{{ $displayUrl }}"
                     alt="Preview"
                     id="{{ $uniqueId }}_preview"
                     class="image-thumbnail-img"
                     data-original-src="{{ $displayUrl }}"
                     data-default-src="{{ asset(ltrim($default, '/')) }}"
                     onerror="this.onerror=null;this.src='{{ asset(ltrim($default, '/')) }}';">

                {{-- Hover Quick Action Icon --}}
                <div class="image-thumbnail-overlay" id="{{ $uniqueId }}_overlay">
                    <button type="button" class="btn-thumb-overlay" id="{{ $uniqueId }}_btn_browse" title="{{ __('Đổi ảnh') }}">
                        <i class="ti ti-camera fs-16"></i>
                    </button>
                </div>
            </div>

            {{-- Right: Details & Action Controls --}}
            <div class="image-details-col flex-grow-1 min-w-0 d-flex flex-column justify-content-center gap-2">

                {{-- Short Title & Formats --}}
                <div>
                    <div class="fw-bold text-dark fs-13 text-truncate" id="{{ $uniqueId }}_title">
                        {{ $isDefault ? __('Chưa có tệp nào được chọn') : __('Tệp hiện tại') }}
                    </div>
                    <div class="text-muted fs-12 mt-0.5" id="{{ $uniqueId }}_sub_text">
                        {{ __('Hỗ trợ: png, jpg, webp (Tối đa 5MB)') }}
                    </div>
                </div>

                {{-- File Info Pill (Shown only when new file chosen) --}}
                <div class="file-info-pill d-none" id="{{ $uniqueId }}_info">
                    <i class="ti ti-check text-success fs-14"></i>
                    <span class="file-name text-truncate fw-semibold" id="{{ $uniqueId }}_filename"></span>
                    <span class="file-size text-muted" id="{{ $uniqueId }}_filesize"></span>
                </div>

                {{-- Action Buttons Row (Clean, No Purple) --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Select / Change Button (Standard Blue Outline, NOT purple) --}}
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1.5" id="{{ $uniqueId }}_btn_select">
                        <i class="ti ti-upload fs-14"></i>
                        <span id="{{ $uniqueId }}_btn_select_text">{{ $isDefault ? __('Chọn tệp') : __('Đổi tệp') }}</span>
                    </button>

                    {{-- Delete / Reset Button --}}
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 d-inline-flex align-items-center gap-1 {{ $isDefault ? 'd-none' : '' }}" id="{{ $uniqueId }}_btn_reset" title="{{ __('Xóa ảnh') }}">
                        <i class="ti ti-trash fs-14"></i>
                        <span>{{ __('Xóa') }}</span>
                    </button>

                    {{-- View Full-size Button --}}
                    <a href="{{ $displayUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 d-inline-flex align-items-center gap-1" id="{{ $uniqueId }}_btn_view" title="{{ __('Xem ảnh lớn') }}">
                        <i class="ti ti-eye fs-14"></i>
                    </a>

                    {{-- Cancel New File Selection Button --}}
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 d-none align-items-center gap-1" id="{{ $uniqueId }}_btn_clear" title="{{ __('Hủy chọn tệp') }}">
                        <i class="ti ti-x text-danger fs-14"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden Real Inputs --}}
        <input type="hidden"
               name="reset_{{ $name }}"
               id="{{ $uniqueId }}_reset_flag"
               value="0">
        <input type="file"
               name="{{ $name }}"
               id="{{ $uniqueId }}_input"
               class="d-none"
               accept="image/jpeg,image/png,image/webp,image/jpg,image/svg+xml">
    </div>
</div>

{{-- Scoped Styles for Image Uploader Component --}}
@once
@push('libs-css')
<style>
    .image-uploader-card {
        border: 1.5px dashed #cbd5e1 !important;
        background: #ffffff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .image-uploader-card:hover {
        border-color: #206bc4 !important;
        box-shadow: 0 4px 16px rgba(32, 107, 196, 0.08);
    }

    .image-uploader-card.is-dragover {
        border-color: #10b981 !important;
        background: #ecfdf5 !important;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15) !important;
    }

    .image-thumbnail-box {
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .image-thumbnail-box:hover {
        border-color: #206bc4;
    }

    .image-thumbnail-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 4px;
        transition: transform 0.2s ease;
    }

    .image-thumbnail-box:hover .image-thumbnail-img {
        transform: scale(1.05);
    }

    .image-thumbnail-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .image-thumbnail-box:hover .image-thumbnail-overlay {
        opacity: 1;
        visibility: visible;
    }

    .btn-thumb-overlay {
        background: rgba(255, 255, 255, 0.95);
        color: #206bc4;
        border: none;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: transform 0.15s ease, background 0.15s ease;
    }

    .btn-thumb-overlay:hover {
        transform: scale(1.1);
        background: #ffffff;
        color: #1a569d;
    }

    /* Enforce pure blue for outline primary (NO PURPLE) */
    .custom-image-uploader .btn-outline-primary {
        color: #206bc4 !important;
        border-color: #206bc4 !important;
        background-color: transparent !important;
    }

    .custom-image-uploader .btn-outline-primary:hover {
        color: #ffffff !important;
        background-color: #206bc4 !important;
        border-color: #206bc4 !important;
    }

    .custom-image-uploader .btn-outline-danger {
        color: #d63939 !important;
        border-color: #d63939 !important;
        background-color: transparent !important;
    }

    .custom-image-uploader .btn-outline-danger:hover {
        color: #ffffff !important;
        background-color: #d63939 !important;
        border-color: #d63939 !important;
    }

    .custom-image-uploader .btn-outline-secondary {
        color: #626976 !important;
        border-color: #cbd5e1 !important;
        background-color: transparent !important;
    }

    .custom-image-uploader .btn-outline-secondary:hover {
        color: #1e293b !important;
        background-color: #f1f5f9 !important;
        border-color: #94a3b8 !important;
    }

    .file-info-pill {
        font-size: 0.75rem;
        color: #334155;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 100%;
        border: 1px solid #e2e8f0;
    }
</style>
@endpush
@endonce

{{-- Component Script --}}
@push('custom-js')
<script>
(function () {
    const root = document.getElementById('{{ $uniqueId }}');
    if (!root) return;

    const card = document.getElementById('{{ $uniqueId }}_card');
    const box = document.getElementById('{{ $uniqueId }}_box');
    const input = document.getElementById('{{ $uniqueId }}_input');
    const preview = document.getElementById('{{ $uniqueId }}_preview');
    const btnSelect = document.getElementById('{{ $uniqueId }}_btn_select');
    const btnSelectText = document.getElementById('{{ $uniqueId }}_btn_select_text');
    const btnBrowse = document.getElementById('{{ $uniqueId }}_btn_browse');
    const btnView = document.getElementById('{{ $uniqueId }}_btn_view');
    const btnReset = document.getElementById('{{ $uniqueId }}_btn_reset');
    const btnClear = document.getElementById('{{ $uniqueId }}_btn_clear');
    const titleEl = document.getElementById('{{ $uniqueId }}_title');
    const infoEl = document.getElementById('{{ $uniqueId }}_info');
    const filenameEl = document.getElementById('{{ $uniqueId }}_filename');
    const filesizeEl = document.getElementById('{{ $uniqueId }}_filesize');
    const resetFlagEl = document.getElementById('{{ $uniqueId }}_reset_flag');

    const originalSrc = preview.getAttribute('data-original-src');
    const defaultSrc = preview.getAttribute('data-default-src');

    function openBrowse() {
        input.click();
    }

    if (btnSelect) btnSelect.addEventListener('click', openBrowse);
    if (btnBrowse) btnBrowse.addEventListener('click', function (e) {
        e.stopPropagation();
        openBrowse();
    });
    if (box) box.addEventListener('click', openBrowse);

    // Handle file chosen
    if (input) {
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file) {
                if (resetFlagEl) resetFlagEl.value = '0';
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    if (btnView) btnView.href = e.target.result;

                    // Update Title
                    if (titleEl) titleEl.textContent = 'Tệp mới chọn';

                    // Update button label
                    if (btnSelectText) btnSelectText.textContent = 'Đổi tệp';

                    // Update info pill
                    if (filenameEl) filenameEl.textContent = file.name;
                    if (filesizeEl) {
                        const kb = (file.size / 1024).toFixed(1);
                        filesizeEl.textContent = `(${kb} KB)`;
                    }
                    if (infoEl) infoEl.classList.remove('d-none');
                    if (btnClear) btnClear.classList.remove('d-none');
                    if (btnReset) btnReset.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Reset to default action
    if (btnReset) {
        btnReset.addEventListener('click', function (e) {
            e.stopPropagation();
            input.value = '';
            if (resetFlagEl) resetFlagEl.value = '1';
            preview.src = defaultSrc;
            if (btnView) btnView.href = defaultSrc;

            if (titleEl) titleEl.textContent = 'Chưa có tệp nào được chọn';
            if (btnSelectText) btnSelectText.textContent = 'Chọn tệp';
            if (infoEl) infoEl.classList.add('d-none');
            if (btnClear) btnClear.classList.add('d-none');
            btnReset.classList.add('d-none');
        });
    }

    // Clear newly selected file and revert to original
    if (btnClear) {
        btnClear.addEventListener('click', function (e) {
            e.stopPropagation();
            input.value = '';
            if (resetFlagEl) resetFlagEl.value = '0';
            preview.src = originalSrc;
            if (btnView) btnView.href = originalSrc;

            const isOrigDef = originalSrc === defaultSrc;
            if (titleEl) {
                titleEl.textContent = isOrigDef ? 'Chưa có tệp nào được chọn' : 'Tệp hiện tại';
            }
            if (btnSelectText) {
                btnSelectText.textContent = isOrigDef ? 'Chọn tệp' : 'Đổi tệp';
            }
            if (btnReset) {
                if (isOrigDef) btnReset.classList.add('d-none');
                else btnReset.classList.remove('d-none');
            }

            if (infoEl) infoEl.classList.add('d-none');
            btnClear.classList.add('d-none');
        });
    }

    // Drag & Drop
    ['dragenter', 'dragover'].forEach(eventName => {
        card.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            card.classList.add('is-dragover');
        }, false);
    });

    ['dragleave', 'dragend', 'drop'].forEach(eventName => {
        card.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            card.classList.remove('is-dragover');
        }, false);
    });

    card.addEventListener('drop', function (e) {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            const file = dt.files[0];
            if (file.type.startsWith('image/')) {
                input.files = dt.files;
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        }
    }, false);
})();
</script>
@endpush
