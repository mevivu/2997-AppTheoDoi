@php use App\Traits\RouteAdminSystem; @endphp

{{-- Main Column (8 cols): Theme Selector + Dropzone + Gallery --}}
<div class="col-12 col-lg-8">
    {{-- STEP 1: Visual Theme Selector --}}
    <div class="card custom-shadow mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0 text-dark fw-bold d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">1</span>
                {{ __('Bước 1: Chọn Chủ đề nạp thẻ bài') }}
            </h4>
            <span class="text-danger fs-12 fw-bold">* {{ __('Bắt buộc') }}</span>
        </div>
        <div class="card-body p-4">
            @php
                $currentThemeId = old('memo_theme_id', request('theme_id', $themes->first()->id ?? null));
            @endphp
            <input type="hidden" name="memo_theme_id" id="selectedThemeInput" value="{{ $currentThemeId }}" required>

            <div class="memo-theme-picker-grid">
                @foreach ($themes as $t)
                    @php
                        $code = strtolower($t->code);
                        $themeClass = 'theme-default';
                        $iconClass = 'ti ti-cards';

                        if (str_contains($code, 'vehic') || str_contains($code, 'xe')) {
                            $themeClass = 'theme-vehicles';
                            $iconClass = 'ti ti-car';
                        } elseif (str_contains($code, 'flow') || str_contains($code, 'hoa')) {
                            $themeClass = 'theme-flowers';
                            $iconClass = 'ti ti-flower';
                        } elseif (str_contains($code, 'numb') || str_contains($code, 'so')) {
                            $themeClass = 'theme-numbers';
                            $iconClass = 'ti ti-numbers';
                        } elseif (str_contains($code, 'flag') || str_contains($code, 'co')) {
                            $themeClass = 'theme-flags';
                            $iconClass = 'ti ti-flag';
                        }

                        $isSelected = ($currentThemeId == $t->id);
                    @endphp
                    <div class="memo-theme-card-option {{ $themeClass }} {{ $isSelected ? 'active' : '' }}"
                         data-id="{{ $t->id }}"
                         data-name="{{ $t->name }}">
                        <div class="memo-theme-check-mark">
                            <i class="ti ti-check"></i>
                        </div>
                        <div class="memo-theme-card-icon">
                            <i class="{{ $iconClass }}"></i>
                        </div>
                        <div class="memo-theme-card-info">
                            <div class="memo-theme-card-title">{{ $t->name }}</div>
                            <div class="memo-theme-card-count">
                                <i class="ti ti-cards fs-12"></i>
                                <span>{{ $t->cards_count ?? 0 }} {{ __('thẻ đã có') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- STEP 2: Drag & Drop Upload Zone --}}
    <div class="card custom-shadow mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0 text-dark fw-bold d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">2</span>
                {{ __('Bước 2: Tải lên danh sách ảnh thẻ') }}
            </h4>
            <span class="text-danger fs-12 fw-bold">* {{ __('Bắt buộc') }}</span>
        </div>
        <div class="card-body p-4">
            {{-- Hidden File Input --}}
            <input type="file" name="images[]" id="bulkImagesInput" multiple accept="image/jpeg,image/png,image/webp,image/jpg" style="display: none;" required>

            {{-- Interactive Dropzone --}}
            <div class="memo-dropzone" id="memoDropzone" role="button" tabindex="0">
                <div class="memo-dropzone-icon">
                    <i class="ti ti-cloud-upload"></i>
                </div>
                <div class="memo-dropzone-title">
                    {{ __('Kéo & thả ảnh thẻ bài vào đây hoặc Bấm để chọn tệp') }}
                </div>
                <div class="memo-dropzone-sub">
                    {{ __('Hỗ trợ chọn cùng lúc từ 2 đến 50 tệp ảnh từ máy tính hoặc thiết bị') }}
                </div>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <span class="memo-spec-tag">
                        <i class="ti ti-photo text-primary"></i> JPG, PNG, WEBP
                    </span>
                    <span class="memo-spec-tag">
                        <i class="ti ti-database text-warning"></i> {{ __('Tối đa 5MB / ảnh') }}
                    </span>
                    <span class="memo-spec-tag">
                        <i class="ti ti-layers-intersect text-success"></i> 2 - 50 {{ __('ảnh / lượt nạp') }}
                    </span>
                </div>
            </div>

            {{-- STEP 3: Live Preview Gallery (Initially Hidden) --}}
            <div id="selectedFilesSummary" class="memo-gallery-box d-none">
                <div class="memo-gallery-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-14">
                            <i class="ti ti-check-circle text-success me-1"></i> {{ __('Đã chọn:') }}
                        </span>
                        <span id="filesCountBadge" class="badge bg-primary px-2.5 py-1.5 rounded-pill fs-12">0</span>
                        <span class="text-muted fs-13">thẻ bài</span>
                        <span class="text-muted fs-12 ms-1">(~<span id="filesTotalSize" class="fw-bold text-dark">0 KB</span>)</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill px-3" id="btnClearSelection">
                        <i class="ti ti-trash"></i>
                        <span>{{ __('Xóa tất cả') }}</span>
                    </button>
                </div>

                {{-- Image Cards Grid --}}
                <div id="fileNamesList" class="memo-gallery-grid"></div>
            </div>
        </div>
    </div>
</div>

{{-- Sidebar Column (4 cols): Sticky Action + Guidelines --}}
<div class="col-12 col-lg-4">
    <div class="memo-side-sticky">
        {{-- Sticky Action / Summary Card --}}
        <div class="memo-summary-card">
            <h4 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="ti ti-clipboard-check text-primary"></i>
                {{ __('Xác nhận & Tải lên') }}
            </h4>

            <div class="bg-light-subtle rounded-3 p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-13">{{ __('Chủ đề đã chọn') }}:</span>
                    <span id="summaryThemeName" class="badge bg-white text-dark border px-2 py-1 fs-12 fw-bold text-truncate" style="max-width: 140px;">
                        {{ $themes->firstWhere('id', $currentThemeId)->name ?? __('Chưa chọn') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-13">{{ __('Số lượng ảnh') }}:</span>
                    <span class="fw-bold fs-13 text-dark">
                        <span id="summaryFileCount" class="text-primary">0</span> / 50 thẻ
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-13">{{ __('Tổng dung lượng') }}:</span>
                    <span id="summaryTotalSize" class="fw-bold fs-13 text-dark">0 KB</span>
                </div>
            </div>

            <button type="submit" id="btnSubmitBulk" class="btn btn-memo-bulk w-100 justify-content-center py-2.5 fs-15" disabled>
                <i class="ti ti-cloud-upload fs-18"></i>
                <span id="btnSubmitText">{{ __('Chọn ảnh để bắt đầu nạp') }}</span>
            </button>

            <a href="{{ route(RouteAdminSystem::MEMO_CARD_INDEX) }}" class="btn btn-save-exit-settings w-100 justify-content-center mt-2 py-2">
                <i class="ti ti-arrow-left"></i>
                <span>{{ __('Quay lại danh sách thẻ') }}</span>
            </a>
        </div>

        {{-- Guide & Best Practices Card --}}
        <div class="memo-guide-card">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="ti ti-bulb text-warning"></i>
                {{ __('Quy tắc & Mẹo hay') }}
            </h5>

            <div class="memo-guide-item">
                <div class="memo-guide-icon text-primary bg-primary-subtle">
                    <i class="ti ti-typography"></i>
                </div>
                <div>
                    <strong class="text-dark">{{ __('Tự động chuẩn hóa tên thẻ:') }}</strong>
                    <div>Tên tệp ảnh (VD: <code>xe_cuu_hoa.png</code>) sẽ tự động được chuyển thành tên thẻ bài tiếng Việt đẹp mắt (<code>Xe Cuu Hoa</code>).</div>
                </div>
            </div>

            <div class="memo-guide-item">
                <div class="memo-guide-icon text-success bg-success-subtle">
                    <i class="ti ti-aspect-ratio"></i>
                </div>
                <div>
                    <strong class="text-dark">{{ __('Khuyến nghị tỷ lệ vuông 1:1:') }}</strong>
                    <div>Nên dùng ảnh tỷ lệ 1:1 (từ 400x400px đến 800x800px), chủ thể nằm ở chính giữa để thẻ hiển thị cân đối nhất.</div>
                </div>
            </div>

            <div class="memo-guide-item">
                <div class="memo-guide-icon text-info bg-info-subtle">
                    <i class="ti ti-photo-circle"></i>
                </div>
                <div>
                    <strong class="text-dark">{{ __('Định dạng trong suốt PNG/WEBP:') }}</strong>
                    <div>Ảnh PNG nền trong suốt giúp thẻ bài hiển thị sạch sẽ, chuyên nghiệp trên nền thẻ game.</div>
                </div>
            </div>

            <div class="memo-guide-item">
                <div class="memo-guide-icon text-warning bg-warning-subtle">
                    <i class="ti ti-volume"></i>
                </div>
                <div>
                    <strong class="text-dark">{{ __('Bổ sung phát âm sau:') }}</strong>
                    <div>Sau khi nạp ảnh xong, bạn có thể chỉnh sửa từng thẻ bài để nạp file âm thanh phát âm tương ứng.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const dropzone = document.getElementById('memoDropzone');
    const input = document.getElementById('bulkImagesInput');
    const summaryBox = document.getElementById('selectedFilesSummary');
    const countBadge = document.getElementById('filesCountBadge');
    const totalSizeBadge = document.getElementById('filesTotalSize');
    const galleryGrid = document.getElementById('fileNamesList');
    const btnClear = document.getElementById('btnClearSelection');
    const btnSubmit = document.getElementById('btnSubmitBulk');
    const btnSubmitText = document.getElementById('btnSubmitText');
    const summaryThemeName = document.getElementById('summaryThemeName');
    const summaryFileCount = document.getElementById('summaryFileCount');
    const summaryTotalSize = document.getElementById('summaryTotalSize');
    const themeInput = document.getElementById('selectedThemeInput');
    const themeOptions = document.querySelectorAll('.memo-theme-card-option');

    // In-memory DataTransfer to manage files dynamically
    let fileTransfer = new DataTransfer();

    // 1. Theme Option Click Handlers
    themeOptions.forEach(card => {
        card.addEventListener('click', function () {
            themeOptions.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const themeId = this.getAttribute('data-id');
            const themeName = this.getAttribute('data-name');
            if (themeInput) themeInput.value = themeId;
            if (summaryThemeName) summaryThemeName.textContent = themeName;
        });
    });

    // 2. Format Beautified Title from Filename
    function beautifyName(filename) {
        if (!filename) return '';
        const base = filename.replace(/\.[^/.]+$/, '');
        return base
            .replace(/[_-]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .split(' ')
            .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
            .join(' ');
    }

    // 3. Format File Size
    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 KB';
        const k = 1024;
        if (bytes < k) return bytes + ' B';
        if (bytes < k * k) return (bytes / k).toFixed(1) + ' KB';
        return (bytes / (k * k)).toFixed(1) + ' MB';
    }

    // 4. Update UI State & Summary
    function updateGalleryState() {
        const files = Array.from(fileTransfer.files);
        const count = files.length;
        let totalBytes = 0;
        files.forEach(f => totalBytes += f.size);

        // Sync input.files
        input.files = fileTransfer.files;

        // Counters
        const formattedSize = formatBytes(totalBytes);
        if (countBadge) countBadge.textContent = count;
        if (totalSizeBadge) totalSizeBadge.textContent = formattedSize;
        if (summaryFileCount) summaryFileCount.textContent = count;
        if (summaryTotalSize) summaryTotalSize.textContent = formattedSize;

        // Submit Button State
        if (btnSubmit) {
            if (count >= 1) {
                btnSubmit.removeAttribute('disabled');
                if (btnSubmitText) btnSubmitText.textContent = `Nạp ${count} ảnh thẻ bài ngay`;
            } else {
                btnSubmit.setAttribute('disabled', 'disabled');
                if (btnSubmitText) btnSubmitText.textContent = 'Chọn ảnh để bắt đầu nạp';
            }
        }

        // Gallery Box Visibility
        if (count > 0) {
            summaryBox.classList.remove('d-none');
        } else {
            summaryBox.classList.add('d-none');
            galleryGrid.innerHTML = '';
        }
    }

    // 5. Render Gallery Items
    function renderGallery() {
        galleryGrid.innerHTML = '';
        const files = Array.from(fileTransfer.files);

        files.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'memo-gallery-item';

            const thumbWrap = document.createElement('div');
            thumbWrap.className = 'memo-gallery-thumb-wrap';

            const img = document.createElement('img');
            img.className = 'memo-gallery-thumb';
            img.src = URL.createObjectURL(file);
            img.alt = file.name;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'memo-item-remove-btn';
            removeBtn.title = 'Xóa ảnh này';
            removeBtn.innerHTML = '<i class="ti ti-x"></i>';
            removeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                removeFileAtIndex(index);
            });

            thumbWrap.appendChild(img);
            thumbWrap.appendChild(removeBtn);

            const info = document.createElement('div');
            info.className = 'memo-gallery-info';

            const nameEl = document.createElement('div');
            nameEl.className = 'memo-gallery-name';
            const prettyName = beautifyName(file.name);
            nameEl.textContent = prettyName;
            nameEl.title = `${prettyName} (${file.name})`;

            const sizeEl = document.createElement('div');
            sizeEl.className = 'memo-gallery-size';
            sizeEl.textContent = formatBytes(file.size);

            info.appendChild(nameEl);
            info.appendChild(sizeEl);

            card.appendChild(thumbWrap);
            card.appendChild(info);

            galleryGrid.appendChild(card);
        });

        updateGalleryState();
    }

    // 6. Add Files to Transfer
    function addFiles(newFiles) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        const existingNames = new Set(Array.from(fileTransfer.files).map(f => f.name + f.size));

        Array.from(newFiles).forEach(file => {
            if (!allowedTypes.includes(file.type)) return;
            if (file.size > 5 * 1024 * 1024) return; // 5MB limit
            const key = file.name + file.size;
            if (!existingNames.has(key)) {
                fileTransfer.items.add(file);
                existingNames.add(key);
            }
        });

        renderGallery();
    }

    // 7. Remove File at Index
    function removeFileAtIndex(targetIndex) {
        const newTransfer = new DataTransfer();
        const files = Array.from(fileTransfer.files);
        files.forEach((file, index) => {
            if (index !== targetIndex) {
                newTransfer.items.add(file);
            }
        });
        fileTransfer = newTransfer;
        renderGallery();
    }

    // 8. Clear All Selection
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            fileTransfer = new DataTransfer();
            input.value = '';
            renderGallery();
        });
    }

    // 9. Dropzone Click to Browse
    if (dropzone) {
        dropzone.addEventListener('click', function () {
            input.click();
        });

        // Drag & Drop Events
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            }, false);
        });

        ['dragleave', 'dragend', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            }, false);
        });

        dropzone.addEventListener('drop', function (e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                addFiles(dt.files);
            }
        }, false);
    }

    // 10. File Input Change Event
    if (input) {
        input.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                addFiles(this.files);
            }
        });
    }
});
</script>
@endpush
