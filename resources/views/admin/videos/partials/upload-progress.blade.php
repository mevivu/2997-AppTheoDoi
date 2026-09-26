{{-- Modal & Logic hiển thị Tiến trình Upload Video lên Cloudflare R2 --}}
<div id="video_upload_modal" class="upload-progress-modal-backdrop" style="display: none;">
    <div class="upload-progress-modal-dialog">
        <div class="upload-progress-card">
            {{-- Header: Animated Cloud Icon & Main Title --}}
            <div class="upload-progress-header text-center">
                <div class="upload-progress-icon-wrapper mb-3" id="upload_icon_container">
                    <div class="upload-pulse-ring"></div>
                    <div class="upload-icon-circle">
                        <i class="ti ti-cloud-upload fs-1 text-white" id="upload_main_icon"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1 fs-20" id="upload_modal_title">
                    {{ __('Đang tải video lên máy chủ...') }}
                </h3>
                <p class="text-secondary fs-14 mb-0" id="upload_modal_subtitle">
                    {{ __('Vui lòng giữ cửa sổ trình duyệt mở trong suốt quá trình tải lên.') }}
                </p>
            </div>

            {{-- File Info Badge --}}
            <div class="upload-file-meta-box mt-3 mb-3 p-3 rounded-3 d-flex align-items-center gap-3">
                <div class="upload-file-icon">
                    <i class="ti ti-video fs-2 text-orange"></i>
                </div>
                <div class="flex-fill overflow-hidden">
                    <div class="fw-bold text-dark text-truncate fs-15" id="upload_modal_filename">video.mp4</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-orange text-white fs-12 px-2 py-0 fw-semibold" id="upload_modal_filesize">0 MB</span>
                        <span class="text-muted fs-13" id="upload_modal_source_tag">Nguồn: Cloudflare R2</span>
                    </div>
                </div>
            </div>

            {{-- 2-Stage Stepper Indicator --}}
            <div class="upload-stepper-box mb-3 p-2 rounded-3 bg-light border">
                <div class="d-flex align-items-center justify-content-between">
                    {{-- Step 1 --}}
                    <div class="stepper-item active d-flex align-items-center gap-2 flex-fill" id="upload_step_1">
                        <span class="stepper-badge" id="upload_step_1_badge">1</span>
                        <div class="stepper-text">
                            <strong class="d-block fs-13 text-dark">{{ __('Bước 1: Tải lên Server') }}</strong>
                            <span class="fs-12 text-muted" id="upload_step_1_status">{{ __('Đang truyền dữ liệu...') }}</span>
                        </div>
                    </div>
                    <i class="ti ti-chevron-right text-muted fs-4 mx-2"></i>
                    {{-- Step 2 --}}
                    <div class="stepper-item pending d-flex align-items-center gap-2 flex-fill" id="upload_step_2">
                        <span class="stepper-badge" id="upload_step_2_badge">2</span>
                        <div class="stepper-text">
                            <strong class="d-block fs-13 text-dark">{{ __('Bước 2: Chuyển sang R2') }}</strong>
                            <span class="fs-12 text-muted" id="upload_step_2_status">{{ __('Chờ hoàn thành bước 1') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Progress Bar Container --}}
            <div class="upload-progress-bar-container mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-14 fw-semibold text-secondary" id="upload_progress_label">{{ __('Tiến trình tải lên') }}</span>
                    <span class="fs-16 fw-bold text-orange" id="upload_percent_text">0%</span>
                </div>
                <div class="upload-progress-track">
                    <div class="upload-progress-fill" id="upload_progress_fill" style="width: 0%;"></div>
                </div>
            </div>

            {{-- Real-time Metrics (Bytes, Speed, Remaining Time) --}}
            <div class="upload-metrics-grid d-flex align-items-center justify-content-between text-secondary fs-13 mb-3 px-1" id="upload_metrics_row">
                <div>
                    <i class="ti ti-database me-1 text-primary"></i>
                    <span id="upload_bytes_text">0 MB / 0 MB</span>
                </div>
                <div>
                    <i class="ti ti-dashboard me-1 text-success"></i>
                    <span id="upload_speed_text">0 MB/s</span>
                </div>
                <div>
                    <i class="ti ti-clock me-1 text-warning"></i>
                    <span id="upload_eta_text">Đang tính...</span>
                </div>
            </div>

            {{-- Notice Box when processing Cloudflare R2 sync --}}
            <div id="upload_r2_notice" class="alert alert-warning border-warning d-flex align-items-center gap-2 py-2 px-3 rounded-3 mb-3" style="display: none !important;">
                <div class="spinner-border spinner-border-sm text-warning flex-shrink-0" role="status"></div>
                <div class="fs-13 text-dark lh-sm">
                    <strong>{{ __('Máy chủ đang đồng bộ video lên Cloudflare R2...') }}</strong><br>
                    {{ __('Quá trình này mất khoảng 5 - 15 giây. Vui lòng không đóng trình duyệt.') }}
                </div>
            </div>

            {{-- Success Notification Box --}}
            <div id="upload_success_notice" class="alert alert-success border-success d-flex align-items-center gap-2 py-2 px-3 rounded-3 mb-3" style="display: none !important;">
                <i class="ti ti-circle-check fs-2 text-success flex-shrink-0"></i>
                <div class="fs-14 text-success fw-bold lh-sm">
                    {{ __('Tải lên & Lưu video thành công! Đang chuyển hướng...') }}
                </div>
            </div>

            {{-- Footer Action: Cancel Upload --}}
            <div class="upload-progress-footer text-center pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary fs-14 px-4 py-2 rounded-3" id="btn_cancel_upload">
                    <i class="ti ti-x me-1"></i> {{ __('Hủy tải lên') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal Backdrop with Glassmorphism */
    .upload-progress-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.72);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: uploadFadeIn 0.25s ease-out;
    }
    @keyframes uploadFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Modal Dialog & Card */
    .upload-progress-modal-dialog {
        width: 100%;
        max-width: 540px;
        animation: uploadSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes uploadSlideUp {
        from { transform: translateY(24px) scale(0.96); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    .upload-progress-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 28px 26px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    /* Animated Icon */
    .upload-progress-icon-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .upload-icon-circle {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(234, 88, 12, 0.35);
        position: relative;
        z-index: 2;
    }
    .upload-pulse-ring {
        position: absolute;
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: rgba(249, 115, 22, 0.22);
        animation: uploadPulse 1.8s ease-out infinite;
        z-index: 1;
    }
    @keyframes uploadPulse {
        0% { transform: scale(0.85); opacity: 0.9; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    /* File Meta Box */
    .upload-file-meta-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .upload-file-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #ffedd5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Stepper Styling */
    .stepper-item {
        transition: all 0.2s;
    }
    .stepper-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #cbd5e1;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .stepper-item.active .stepper-badge {
        background: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.2);
    }
    .stepper-item.completed .stepper-badge {
        background: #16a34a;
        color: #ffffff;
    }

    /* Progress Bar */
    .upload-progress-track {
        height: 12px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        position: relative;
    }
    .upload-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #f97316 0%, #ea580c 50%, #0284c7 100%);
        background-size: 30px 30px;
        background-image: linear-gradient(
            45deg,
            rgba(255, 255, 255, 0.25) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.25) 50%,
            rgba(255, 255, 255, 0.25) 75%,
            transparent 75%,
            transparent
        );
        animation: uploadStripes 1s linear infinite;
        transition: width 0.15s ease-out;
    }
    .upload-progress-fill.completed {
        background: #16a34a !important;
        animation: none;
    }
    @keyframes uploadStripes {
        0% { background-position: 0 0; }
        100% { background-position: 30px 0; }
    }
</style>

<script>
(function () {
    let currentXhr = null;
    let uploadStartTime = 0;
    let lastLoadedBytes = 0;
    let lastTime = 0;

    document.addEventListener('DOMContentLoaded', function () {
        const createForm = document.getElementById('video_create_form');
        const editForm = document.getElementById('video_edit_form');
        const form = createForm || editForm;
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const videoTypeInput = form.querySelector('input[name="video_type"]:checked');
            const videoType = videoTypeInput ? videoTypeInput.value : 'youtube';
            const r2FileInput = document.getElementById('r2_video_file');
            const file = r2FileInput && r2FileInput.files && r2FileInput.files[0] ? r2FileInput.files[0] : null;

            // Nếu là YouTube hoặc không có tệp video mới được chọn (trong màn hình chỉnh sửa) -> submit bình thường
            if (videoType !== 'r2' || !file) {
                return; // Cho phép submit form đồng bộ mặc định
            }

            // Nếu người dùng chọn tải file R2 -> chặn submit mặc định và chạy upload progress XHR
            e.preventDefault();

            // 1. Đồng bộ CKEditor vào textarea nếu có
            if (window.CKEDITOR && window.CKEDITOR.instances) {
                for (let k in window.CKEDITOR.instances) {
                    try { window.CKEDITOR.instances[k].updateElement(); } catch (err) {}
                }
            }

            // 2. Kiểm tra nhanh các trường bắt buộc trên client để tránh upload uổng phí
            const titleInput = form.querySelector('input[name="title"]');
            if (titleInput && !titleInput.value.trim()) {
                if (typeof $.toast === 'function') {
                    $.toast({
                        heading: 'Thông báo',
                        text: 'Vui lòng nhập tiêu đề video trước khi tải lên.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                } else {
                    alert('Vui lòng nhập tiêu đề video trước khi tải lên.');
                }
                titleInput.focus();
                return;
            }

            const categorySelect = form.querySelector('select[name="video_category_id"]');
            if (categorySelect && !categorySelect.value) {
                if (typeof $.toast === 'function') {
                    $.toast({
                        heading: 'Thông báo',
                        text: 'Vui lòng chọn danh mục video trước khi tải lên.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                } else {
                    alert('Vui lòng chọn danh mục video trước khi tải lên.');
                }
                categorySelect.focus();
                return;
            }

            // 3. Hiển thị Upload Modal & bắt đầu tải lên
            startUploadProcess(form, file);
        });

        // Nút hủy tải lên
        const btnCancel = document.getElementById('btn_cancel_upload');
        if (btnCancel) {
            btnCancel.addEventListener('click', function () {
                if (currentXhr) {
                    if (confirm('Bạn có chắc muốn hủy quá trình tải lên video này?')) {
                        currentXhr.abort();
                        hideUploadModal();
                        if (typeof $.toast === 'function') {
                            $.toast({
                                heading: 'Thông báo',
                                text: 'Đã hủy quá trình tải lên video.',
                                icon: 'info',
                                position: 'top-right'
                            });
                        }
                    }
                } else {
                    hideUploadModal();
                }
            });
        }
    });

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function startUploadProcess(form, file) {
        const modal = document.getElementById('video_upload_modal');
        const filenameEl = document.getElementById('upload_modal_filename');
        const filesizeEl = document.getElementById('upload_modal_filesize');
        const progressFill = document.getElementById('upload_progress_fill');
        const percentText = document.getElementById('upload_percent_text');
        const bytesText = document.getElementById('upload_bytes_text');
        const speedText = document.getElementById('upload_speed_text');
        const etaText = document.getElementById('upload_eta_text');
        const r2Notice = document.getElementById('upload_r2_notice');
        const successNotice = document.getElementById('upload_success_notice');
        const step1 = document.getElementById('upload_step_1');
        const step1Badge = document.getElementById('upload_step_1_badge');
        const step1Status = document.getElementById('upload_step_1_status');
        const step2 = document.getElementById('upload_step_2');
        const step2Badge = document.getElementById('upload_step_2_badge');
        const step2Status = document.getElementById('upload_step_2_status');
        const modalTitle = document.getElementById('upload_modal_title');
        const mainIcon = document.getElementById('upload_main_icon');

        // Reset trạng thái
        filenameEl.textContent = file.name;
        filesizeEl.textContent = formatBytes(file.size);
        progressFill.style.width = '0%';
        progressFill.classList.remove('completed');
        percentText.textContent = '0%';
        bytesText.textContent = '0 MB / ' + formatBytes(file.size);
        speedText.textContent = '0 MB/s';
        etaText.textContent = 'Đang tính...';
        r2Notice.style.setProperty('display', 'none', 'important');
        successNotice.style.setProperty('display', 'none', 'important');

        step1.className = 'stepper-item active d-flex align-items-center gap-2 flex-fill';
        step1Badge.innerHTML = '1';
        step1Status.textContent = 'Đang truyền dữ liệu...';

        step2.className = 'stepper-item pending d-flex align-items-center gap-2 flex-fill';
        step2Badge.innerHTML = '2';
        step2Status.textContent = 'Chờ hoàn thành bước 1';

        modalTitle.textContent = 'Đang tải video lên máy chủ...';
        mainIcon.className = 'ti ti-cloud-upload fs-1 text-white';

        modal.style.display = 'flex';

        // Chuẩn bị FormData
        const formData = new FormData(form);
        formData.append('submitter', 'save');

        // Khởi tạo XHR
        const xhr = new XMLHttpRequest();
        currentXhr = xhr;
        uploadStartTime = Date.now();
        lastTime = uploadStartTime;
        lastLoadedBytes = 0;

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const now = Date.now();
                const timeDiff = (now - lastTime) / 1000; // giây
                const bytesDiff = e.loaded - lastLoadedBytes;

                let currentSpeed = 0;
                if (timeDiff >= 0.3) {
                    currentSpeed = bytesDiff / timeDiff; // bytes/sec
                    lastTime = now;
                    lastLoadedBytes = e.loaded;
                } else if (lastLoadedBytes === 0 && (now - uploadStartTime) > 0) {
                    currentSpeed = e.loaded / ((now - uploadStartTime) / 1000);
                }

                const percent = Math.min(100, Math.round((e.loaded / e.total) * 100));
                progressFill.style.width = percent + '%';
                percentText.textContent = percent + '%';
                bytesText.textContent = formatBytes(e.loaded) + ' / ' + formatBytes(e.total);

                if (currentSpeed > 0) {
                    speedText.textContent = formatBytes(currentSpeed) + '/s';
                    const remainingBytes = e.total - e.loaded;
                    const remainingSec = Math.round(remainingBytes / currentSpeed);
                    if (remainingSec > 60) {
                        const m = Math.floor(remainingSec / 60);
                        const s = remainingSec % 60;
                        etaText.textContent = `Còn ~${m}p ${s}s`;
                    } else {
                        etaText.textContent = `Còn ~${remainingSec}s`;
                    }
                }

                // Khi tải lên server xong 100% -> Chuyển sang Bước 2: Đồng bộ R2
                if (percent >= 100 || e.loaded >= e.total) {
                    step1.className = 'stepper-item completed d-flex align-items-center gap-2 flex-fill';
                    step1Badge.innerHTML = '<i class="ti ti-check"></i>';
                    step1Status.textContent = 'Hoàn thành';

                    step2.className = 'stepper-item active d-flex align-items-center gap-2 flex-fill';
                    step2Badge.innerHTML = '<span class="spinner-border spinner-border-sm" style="width: 14px; height: 14px;"></span>';
                    step2Status.textContent = 'Đang chuyển sang R2...';

                    modalTitle.textContent = 'Đang đồng bộ hóa sang Cloudflare R2...';
                    r2Notice.style.setProperty('display', 'flex', 'important');
                    speedText.textContent = 'Đang đồng bộ';
                    etaText.textContent = '5 - 15 giây';
                }
            }
        };

        xhr.onload = function () {
            currentXhr = null;
            if (xhr.status >= 200 && xhr.status < 400) {
                // Thành công
                step2.className = 'stepper-item completed d-flex align-items-center gap-2 flex-fill';
                step2Badge.innerHTML = '<i class="ti ti-check"></i>';
                step2Status.textContent = 'Hoàn tất';

                progressFill.style.width = '100%';
                progressFill.classList.add('completed');
                percentText.textContent = '100%';
                r2Notice.style.setProperty('display', 'none', 'important');
                successNotice.style.setProperty('display', 'flex', 'important');
                modalTitle.textContent = 'Tải lên & Lưu video thành công!';
                mainIcon.className = 'ti ti-check fs-1 text-white';

                // Tự động chuyển hướng sau 700ms
                setTimeout(function () {
                    const redirectUrl = xhr.responseURL || form.getAttribute('data-redirect') || window.location.href;
                    window.location.href = redirectUrl;
                }, 700);
            } else {
                hideUploadModal();
                handleUploadError(xhr);
            }
        };

        xhr.onerror = function () {
            currentXhr = null;
            hideUploadModal();
            showErrorToast('Lỗi kết nối mạng trong quá trình upload. Vui lòng kiểm tra lại đường truyền internet.');
        };

        xhr.ontimeout = function () {
            currentXhr = null;
            hideUploadModal();
            showErrorToast('Quá thời gian kết nối (Timeout). Vui lòng thử lại.');
        };

        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    }

    function hideUploadModal() {
        const modal = document.getElementById('video_upload_modal');
        if (modal) modal.style.display = 'none';
        currentXhr = null;
    }

    function handleUploadError(xhr) {
        if (xhr.status === 413) {
            showErrorToast('Dung lượng tệp video vượt quá giới hạn máy chủ cho phép (413 Content Too Large).');
            return;
        }

        try {
            const data = JSON.parse(xhr.responseText);
            if (data && data.errors) {
                let errorMessages = [];
                for (let key in data.errors) {
                    errorMessages.push(data.errors[key].join('<br>'));
                }
                showErrorToast(errorMessages.join('<br>'));
                return;
            } else if (data && data.message) {
                showErrorToast(data.message);
                return;
            }
        } catch (e) {}

        showErrorToast(`Có lỗi xảy ra trong quá trình lưu video (Mã lỗi HTTP: ${xhr.status}). Vui lòng thử lại.`);
    }

    function showErrorToast(msg) {
        if (typeof $.toast === 'function') {
            $.toast({
                heading: 'Lỗi tải lên',
                text: msg,
                icon: 'error',
                position: 'top-right',
                hideAfter: 8000
            });
        } else {
            alert(msg.replace(/<br>/g, '\n'));
        }
    }
})();
</script>
