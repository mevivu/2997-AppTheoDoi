@php
    $uniqueId = 'file_picker_' . \Illuminate\Support\Str::random(8);
@endphp

<div class="custom-file-uploader" id="{{ $uniqueId }}">
    @if($label)
        <label class="form-label fw-bold mb-2">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{-- File Card Box --}}
    <div class="file-uploader-card p-3 rounded-3 border bg-white position-relative" id="{{ $uniqueId }}_card">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="file-type-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 {{ $isAudio ? 'bg-purple-subtle text-purple' : 'bg-primary-subtle text-primary' }}"
                     style="width: 44px; height: 44px; font-size: 1.3rem;">
                    @if($isAudio)
                        <i class="ti ti-music"></i>
                    @else
                        <i class="ti ti-file"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="fw-bold text-dark fs-13 text-truncate" id="{{ $uniqueId }}_filename">
                        {{ $fileName ?: __('Chưa có tệp nào được chọn') }}
                    </div>
                    <div class="text-muted fs-12 d-flex align-items-center gap-2 mt-0.5">
                        <span id="{{ $uniqueId }}_filesize">
                            @if($fileUrl)
                                <span class="badge bg-light text-muted border">{{ __('Tệp hiện tại') }}</span>
                            @else
                                {{ __('Hỗ trợ: ') }} {{ str_replace('audio/', '', $accept) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1" id="{{ $uniqueId }}_btn_browse">
                    <i class="ti ti-upload"></i>
                    <span id="{{ $uniqueId }}_btn_text">{{ $fileUrl ? __('Đổi tệp') : __('Chọn tệp') }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 {{ $fileUrl ? '' : 'd-none' }}" id="{{ $uniqueId }}_btn_clear" title="{{ __('Hủy chọn tệp') }}">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        {{-- Audio Player Preview (Visible if audio file exists or newly selected) --}}
        @if($isAudio)
            <div class="audio-player-wrapper mt-3 pt-2 border-top {{ $fileUrl ? '' : 'd-none' }}" id="{{ $uniqueId }}_player_box">
                <div class="d-flex align-items-center gap-2 mb-1 text-muted fs-12">
                    <i class="ti ti-volume text-primary"></i>
                    <span class="fw-bold">{{ __('Nghe thử âm thanh:') }}</span>
                </div>
                <audio controls
                       class="w-100 rounded"
                       style="height: 36px;"
                       id="{{ $uniqueId }}_audio"
                       src="{{ $fileUrl ?: '' }}"
                       preload="none">
                </audio>
            </div>
        @endif

        {{-- Hidden Real File Input --}}
        <input type="file"
               name="{{ $name }}"
               id="{{ $uniqueId }}_input"
               class="d-none"
               accept="{{ $accept }}">
    </div>

    @if($sub)
        <div class="text-muted fs-12 mt-1.5">{{ $sub }}</div>
    @endif
</div>

{{-- Scoped Styles for File Uploader Component --}}
@once
@push('libs-css')
<style>
    .file-uploader-card {
        border: 1.5px dashed #cbd5e1 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
    }

    .file-uploader-card:hover {
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.08);
    }

    .file-uploader-card.is-dragover {
        border-color: #10b981 !important;
        background: #ecfdf5 !important;
    }

    .bg-purple-subtle {
        background-color: #f3e8ff !important;
    }
    .text-purple {
        color: #7e22ce !important;
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
    const input = document.getElementById('{{ $uniqueId }}_input');
    const btnBrowse = document.getElementById('{{ $uniqueId }}_btn_browse');
    const btnText = document.getElementById('{{ $uniqueId }}_btn_text');
    const btnClear = document.getElementById('{{ $uniqueId }}_btn_clear');
    const filenameEl = document.getElementById('{{ $uniqueId }}_filename');
    const filesizeEl = document.getElementById('{{ $uniqueId }}_filesize');
    const playerBox = document.getElementById('{{ $uniqueId }}_player_box');
    const audioEl = document.getElementById('{{ $uniqueId }}_audio');

    const originalUrl = "{{ $fileUrl ?: '' }}";
    const originalName = "{{ $fileName ?: '' }}";

    function openBrowse() {
        input.click();
    }

    if (btnBrowse) btnBrowse.addEventListener('click', openBrowse);

    if (input) {
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file) {
                filenameEl.textContent = file.name;
                const kb = (file.size / 1024).toFixed(1);
                filesizeEl.innerHTML = `<span class="badge bg-success-subtle text-success border"><i class="ti ti-check"></i> Đã chọn (${kb} KB)</span>`;
                btnText.textContent = 'Đổi tệp';
                btnClear.classList.remove('d-none');

                if (audioEl && file.type.startsWith('audio/')) {
                    const objectUrl = URL.createObjectURL(file);
                    audioEl.src = objectUrl;
                    if (playerBox) playerBox.classList.remove('d-none');
                }
            }
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', function () {
            input.value = '';
            if (originalUrl) {
                filenameEl.textContent = originalName;
                filesizeEl.innerHTML = '<span class="badge bg-light text-muted border">Tệp hiện tại</span>';
                btnText.textContent = 'Đổi tệp';
                if (audioEl) audioEl.src = originalUrl;
            } else {
                filenameEl.textContent = 'Chưa có tệp nào được chọn';
                filesizeEl.textContent = 'Hỗ trợ: {{ str_replace("audio/", "", $accept) }}';
                btnText.textContent = 'Chọn tệp';
                btnClear.classList.add('d-none');
                if (playerBox) playerBox.classList.add('d-none');
                if (audioEl) audioEl.src = '';
            }
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
            input.files = dt.files;
            const event = new Event('change');
            input.dispatchEvent(event);
        }
    }, false);
})();
</script>
@endpush
