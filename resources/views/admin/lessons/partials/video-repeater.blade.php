@php
    $video = isset($instance) ? ($instance->video ?? $instance->videos->first()) : null;
    $videoType = old('videos.0.video_type', $video?->video_type?->value ?? ($video?->video_type ?? 'youtube'));
    $videoUrl = old('videos.0.video_url', $video?->video_url ?? '');
    $videoPath = old('videos.0.video_path', $video?->video_path ?? '');
    $videoTitle = old('videos.0.title', $video?->title ?? '');
    $videoDuration = old('videos.0.duration_seconds', $video?->duration_seconds ?? 0);
    $videoId = $video?->id ?? '';
    $videoThumb = old('videos.0.thumbnail', $video?->thumbnail_url ?? ($video?->thumbnail ?? ''));
    $formattedDuration = $video?->formatted_duration ?? ($videoDuration > 0 ? gmdate('i:s', $videoDuration) : '00:00');
@endphp

{{-- Khối Video bài học (1 Video duy nhất) --}}
<div class="card custom-shadow mb-4 border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
        <div>
            <h4 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-video text-danger fs-2"></i>
                <span>{{ __('3. Video bài học') }}</span>
                <span class="badge {{ $videoType === 'r2' ? 'bg-warning text-dark' : 'bg-danger text-white' }} px-2 py-1 fs-12 ms-2" id="single_video_type_badge">
                    {{ $videoType === 'r2' ? 'Cloudflare R2' : 'YouTube' }}
                </span>
            </h4>
            <div class="text-muted fs-12 mt-1">
                {{ __('Mỗi bài học đính kèm 1 video hướng dẫn (hỗ trợ liên kết YouTube hoặc tải file video MP4 lên Cloudflare R2).') }}
            </div>
        </div>
    </div>

    <div class="card-body p-3 p-md-4">
        {{-- Hidden fields cho Video duy nhất --}}
        <input type="hidden" name="videos[0][id]" value="{{ $videoId }}">
        <input type="hidden" name="videos[0][sort_order]" value="1">
        <input type="hidden" name="videos[0][video_path]" id="single_r2_path" value="{{ $videoPath }}">
        <input type="hidden" name="videos[0][video_url]" id="single_final_video_url" value="{{ $videoUrl }}">

        <div class="row g-3">
            {{-- 1. Nguồn phát video (YouTube / Cloudflare R2) - 2 Thẻ chọn hiện đại --}}
            <div class="col-12">
                <label class="form-label fw-bold text-dark fs-13 mb-2">
                    {{ __('Nguồn phát video') }}: <span class="text-danger">*</span>
                </label>
                <div class="row g-3">
                    {{-- Thẻ YouTube --}}
                    <div class="col-12 col-sm-6">
                        <label class="video-source-card video-card-yt {{ $videoType === 'youtube' ? 'is-active' : '' }} p-3 rounded-3 d-flex align-items-center gap-3 cursor-pointer w-100"
                               for="vtype_yt">
                            <input type="radio" class="d-none single-video-type-radio" name="videos[0][video_type]" id="vtype_yt" value="youtube" {{ $videoType === 'youtube' ? 'checked' : '' }} autocomplete="off">
                            <div class="source-icon-box rounded-circle d-flex align-items-center justify-content-center bg-danger-lt" style="width: 44px; height: 44px; flex-shrink: 0;">
                                <i class="ti ti-brand-youtube fs-1 text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark fs-14">YouTube (URL / Shorts)</div>
                                <div class="text-muted fs-11 mt-1">Nhúng nhanh qua link, tự quét thông tin</div>
                            </div>
                            <div class="source-check-icon">
                                <i class="ti ti-circle-check-filled text-danger fs-2"></i>
                            </div>
                        </label>
                    </div>

                    {{-- Thẻ Cloudflare R2 --}}
                    <div class="col-12 col-sm-6">
                        <label class="video-source-card video-card-r2 {{ $videoType === 'r2' ? 'is-active' : '' }} p-3 rounded-3 d-flex align-items-center gap-3 cursor-pointer w-100"
                               for="vtype_r2">
                            <input type="radio" class="d-none single-video-type-radio" name="videos[0][video_type]" id="vtype_r2" value="r2" {{ $videoType === 'r2' ? 'checked' : '' }} autocomplete="off">
                            <div class="source-icon-box rounded-circle d-flex align-items-center justify-content-center bg-warning-lt" style="width: 44px; height: 44px; flex-shrink: 0;">
                                <i class="ti ti-cloud-upload fs-1 text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark fs-14">Cloudflare R2 (Tải file MP4)</div>
                                <div class="text-muted fs-11 mt-1">Tải file lên CDN riêng, bảo mật tốc độ cao</div>
                            </div>
                            <div class="source-check-icon">
                                <i class="ti ti-circle-check-filled text-warning fs-2"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 2A. Khối nhập liên kết YouTube --}}
            <div class="col-12 {{ $videoType === 'r2' ? 'd-none' : '' }}" id="single_yt_box">
                <label class="form-label fw-bold text-dark fs-13 mb-1">
                    {{ __('Liên kết video YouTube') }}: <span class="text-danger">*</span>
                </label>
                <div class="input-group shadow-xs rounded-3 overflow-hidden">
                    <span class="input-group-text bg-white text-danger border-end-0">
                        <i class="ti ti-link fs-2"></i>
                    </span>
                    <input type="text" id="single_yt_url" class="form-control border-start-0 ps-0"
                           value="{{ $videoType === 'youtube' ? $videoUrl : '' }}"
                           placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/..."
                           autocomplete="off">
                    <button type="button" class="btn btn-danger fw-semibold px-3" id="btn_fetch_single_yt" title="Tự động nhận diện tiêu đề, thời lượng và ảnh bìa">
                        <i class="ti ti-sparkles me-1"></i>{{ __('Lấy thông tin') }}
                    </button>
                </div>
                <div class="form-text text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                    <i class="ti ti-info-circle text-primary"></i>
                    {{ __('Dán link YouTube rồi bấm "Lấy thông tin" để hệ thống tự động điền tiêu đề, thời lượng và ảnh bìa.') }}
                </div>
            </div>

            {{-- 2B. Khối tải lên Cloudflare R2 (Modern Dropzone & Upload Box) --}}
            <div class="col-12 {{ $videoType === 'r2' ? '' : 'd-none' }}" id="single_r2_box">
                <label class="form-label fw-bold text-dark fs-13 mb-1">
                    {{ __('Tải video lên Cloudflare R2') }}: <span class="text-danger">*</span>
                </label>
                
                {{-- Dropzone Area --}}
                <div class="r2-dropzone rounded-3 p-4 text-center cursor-pointer position-relative" id="r2_drop_area">
                    <input type="file" id="single_r2_file" class="d-none" accept="video/mp4,video/quicktime,video/webm">
                    
                    {{-- Trạng thái chờ chọn file --}}
                    <div class="r2-dropzone-idle" id="r2_drop_idle">
                        <div class="mb-3">
                            <span class="avatar avatar-xl rounded-circle bg-warning-lt text-warning shadow-xs dropzone-cloud-icon">
                                <i class="ti ti-cloud-upload fs-1"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark mb-1 fs-15">{{ __('Kéo & thả file video vào đây hoặc Click để chọn file') }}</h4>
                        <p class="text-muted fs-12 mb-3">
                            {{ __('Định dạng hỗ trợ:') }} <span class="badge bg-light text-dark fw-semibold">MP4</span> <span class="badge bg-light text-dark fw-semibold">MOV</span> <span class="badge bg-light text-dark fw-semibold">WebM</span> — {{ __('Tối đa 500MB') }}
                        </p>
                        <button type="button" class="btn btn-outline-warning fw-semibold px-4" id="btn_browse_r2">
                            <i class="ti ti-folder-open me-2"></i>{{ __('Duyệt file từ máy tính') }}
                        </button>
                    </div>

                    {{-- Trạng thái đã chọn file, sẵn sàng upload --}}
                    <div class="r2-file-selected d-none" id="r2_file_selected_box">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-3 bg-white rounded-3 border border-warning shadow-xs gap-3 text-start">
                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                <span class="avatar avatar-md rounded bg-warning text-white flex-shrink-0">
                                    <i class="ti ti-file-video fs-2"></i>
                                </span>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate fs-14" id="r2_selected_filename">video.mp4</div>
                                    <div class="text-muted fs-12" id="r2_selected_filesize">0 MB</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-light btn-sm text-danger" id="btn_cancel_r2_file" title="Đổi file khác">
                                    <i class="ti ti-trash me-1"></i>{{ __('Đổi file') }}
                                </button>
                                <button type="button" class="btn btn-warning btn-sm fw-bold px-3 shadow-xs" id="btn_upload_single_r2">
                                    <i class="ti ti-upload me-1"></i>{{ __('Tải lên Cloudflare R2 ngay') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Thanh tiến trình upload --}}
                    <div class="mt-3 d-none text-start" id="single_r2_progress_wrapper">
                        <div class="d-flex justify-content-between text-dark fw-semibold fs-12 mb-1">
                            <span id="single_r2_upload_status" class="d-flex align-items-center gap-1">
                                <span class="spinner-border spinner-border-sm text-warning" role="status"></span>
                                {{ __('Đang tải video lên Cloudflare R2...') }}
                            </span>
                            <span id="single_r2_upload_percent" class="text-warning fw-bold">0%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="single_r2_progress_bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                {{-- Hộp thông báo kết quả tải lên thành công --}}
                <div class="mt-3 {{ !empty($videoPath) && $videoType === 'r2' ? '' : 'd-none' }}" id="single_r2_result_box">
                    <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-0 rounded-3 border-0 shadow-xs">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <span class="avatar avatar-md rounded-circle bg-success text-white flex-shrink-0">
                                <i class="ti ti-check fs-2"></i>
                            </span>
                            <div class="overflow-hidden">
                                <div class="fw-bold fs-13 text-success">{{ __('Video đã được lưu trữ an toàn trên Cloudflare R2!') }}</div>
                                <div class="text-muted fs-11 text-truncate mt-1" id="single_r2_url_text">{{ $videoType === 'r2' ? $videoUrl : '' }}</div>
                                <input type="hidden" id="single_r2_url" value="{{ $videoType === 'r2' ? $videoUrl : '' }}">
                            </div>
                        </div>
                        <a href="{{ $videoType === 'r2' ? $videoUrl : '#' }}" target="_blank" class="btn btn-success btn-sm flex-shrink-0 ms-2" id="single_r2_preview_link">
                            <i class="ti ti-external-link me-1"></i>{{ __('Xem stream') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- 3. Tiêu đề video bài học --}}
            <div class="col-12 col-md-8">
                <label class="form-label fw-bold text-dark fs-13 mb-1">
                    {{ __('Tiêu đề video') }}:
                </label>
                <input type="text" name="videos[0][title]" id="single_video_title" class="form-control"
                       value="{{ $videoTitle }}" placeholder="Ví dụ: Video hướng dẫn động tác vươn cao hái sao...">
            </div>

            {{-- 4. Thời lượng video (giây) --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-dark fs-13 mb-1">
                    {{ __('Thời lượng video') }}:
                </label>
                <div class="input-group">
                    <input type="number" name="videos[0][duration_seconds]" id="single_video_duration" class="form-control"
                           value="{{ $videoDuration }}" min="0" placeholder="vd: 210">
                    <span class="input-group-text bg-white fw-bold text-primary" id="single_duration_preview">
                        {{ $formattedDuration }}
                    </span>
                </div>
                <div class="form-text text-muted fs-11">{{ __('Nhập số giây, hệ thống tự động đổi sang phút:giây.') }}</div>
            </div>

            {{-- 5. Ảnh đại diện Thumbnail (tùy chọn) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Ảnh bìa / Thumbnail video (Tùy chọn)') }}:</label>
                <div class="input-group">
                    <input type="text" name="videos[0][thumbnail]" id="single_video_thumbnail" class="form-control"
                           value="{{ $videoThumb }}" placeholder="URL ảnh hoặc tự động lấy từ YouTube...">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('single_video_thumbnail').value=''">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>

            {{-- 6. Xem trước Video Preview Box --}}
            <div class="col-12 {{ !empty($videoUrl) ? '' : 'd-none' }}" id="single_video_preview_box">
                <div class="card border border-2 rounded-3 overflow-hidden shadow-xs" style="background-color: #fafbfc;">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            <div class="position-relative rounded-3 overflow-hidden shadow-xs" style="width: 140px; height: 85px; flex-shrink: 0; background: #1a1a1a;">
                                <img src="{{ !empty($videoThumb) ? $videoThumb : asset('assets/images/default.png') }}"
                                     id="single_preview_thumb" class="w-100 h-100" style="object-fit: cover;"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/images/default.png') }}';">
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <span class="avatar avatar-sm rounded-circle bg-dark-lt text-white" style="backdrop-filter: blur(4px);">
                                        <i class="ti ti-player-play-filled fs-3 text-white"></i>
                                    </span>
                                </div>
                                <span class="badge bg-dark text-white position-absolute bottom-0 end-0 m-1 fs-10 px-1 py-0" id="single_preview_duration_text">
                                    {{ $formattedDuration }}
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge {{ $videoType === 'r2' ? 'bg-warning text-dark' : 'bg-danger text-white' }} px-2 py-0 fs-11 fw-bold" id="single_preview_source">
                                        {{ $videoType === 'r2' ? 'Cloudflare R2' : 'YouTube' }}
                                    </span>
                                    <div class="fw-bold text-dark text-truncate fs-14" id="single_preview_title">
                                        {{ !empty($videoTitle) ? $videoTitle : __('Video hướng dẫn bài học') }}
                                    </div>
                                </div>
                                <div class="text-muted fs-12 text-truncate mt-1" id="single_preview_link">
                                    {{ $videoUrl }}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ $videoUrl ?: '#' }}" target="_blank" class="btn btn-outline-secondary btn-sm" id="single_preview_btn_open">
                                    <i class="ti ti-external-link me-1"></i>{{ __('Xem thử') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Source Choice Cards */
    .video-source-card {
        border: 2px solid #e2e8f0;
        background-color: #ffffff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }
    .video-source-card:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
        transform: translateY(-1px);
    }
    .video-source-card .source-check-icon {
        display: none;
        flex-shrink: 0;
    }
    
    /* YouTube Card Active */
    .video-card-yt.is-active,
    .video-card-yt:has(input:checked) {
        border-color: #e03131 !important;
        background-color: #fff5f5 !important;
        box-shadow: 0 4px 14px rgba(224, 49, 49, 0.15) !important;
        transform: translateY(-1px);
    }
    .video-card-yt.is-active .source-check-icon,
    .video-card-yt:has(input:checked) .source-check-icon {
        display: block;
    }

    /* Cloudflare R2 Card Active */
    .video-card-r2.is-active,
    .video-card-r2:has(input:checked) {
        border-color: #f59f00 !important;
        background-color: #fffbf0 !important;
        box-shadow: 0 4px 14px rgba(245, 159, 0, 0.18) !important;
        transform: translateY(-1px);
    }
    .video-card-r2.is-active .source-check-icon,
    .video-card-r2:has(input:checked) .source-check-icon {
        display: block;
    }

    /* Modern Dropzone */
    .r2-dropzone {
        border: 2px dashed #f59f00;
        background-color: #fffdf9;
        transition: all 0.25s ease-in-out;
    }
    .r2-dropzone:hover,
    .r2-dropzone.dragover {
        border-color: #d97706;
        background-color: #fef9ed;
        transform: scale(1.005);
    }
    .r2-dropzone .dropzone-cloud-icon {
        transition: transform 0.2s ease;
    }
    .r2-dropzone:hover .dropzone-cloud-icon {
        transform: translateY(-3px);
    }
</style>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeRadios = document.querySelectorAll('.single-video-type-radio');
    const sourceCards = document.querySelectorAll('.video-source-card');
    const ytBox = document.getElementById('single_yt_box');
    const r2Box = document.getElementById('single_r2_box');
    const ytUrlInput = document.getElementById('single_yt_url');
    const r2UrlInput = document.getElementById('single_r2_url');
    const r2PathInput = document.getElementById('single_r2_path');
    const finalVideoUrlInput = document.getElementById('single_final_video_url');
    const btnFetchYt = document.getElementById('btn_fetch_single_yt');
    const titleInput = document.getElementById('single_video_title');
    const durationInput = document.getElementById('single_video_duration');
    const durationPreview = document.getElementById('single_duration_preview');
    const thumbInput = document.getElementById('single_video_thumbnail');
    const previewBox = document.getElementById('single_video_preview_box');
    const previewThumb = document.getElementById('single_preview_thumb');
    const previewTitle = document.getElementById('single_preview_title');
    const previewDuration = document.getElementById('single_preview_duration_text');
    const previewSource = document.getElementById('single_preview_source');
    const previewLink = document.getElementById('single_preview_link');
    const previewBtnOpen = document.getElementById('single_preview_btn_open');
    const badgeType = document.getElementById('single_video_type_badge');

    // Dropzone elements
    const dropArea = document.getElementById('r2_drop_area');
    const r2FileInput = document.getElementById('single_r2_file');
    const btnBrowse = document.getElementById('btn_browse_r2');
    const idleBox = document.getElementById('r2_drop_idle');
    const selectedBox = document.getElementById('r2_file_selected_box');
    const selectedFileName = document.getElementById('r2_selected_filename');
    const selectedFileSize = document.getElementById('r2_selected_filesize');
    const btnCancelFile = document.getElementById('btn_cancel_r2_file');
    const btnUploadR2 = document.getElementById('btn_upload_single_r2');
    const r2ProgressWrapper = document.getElementById('single_r2_progress_wrapper');
    const r2ProgressBar = document.getElementById('single_r2_progress_bar');
    const r2UploadPercent = document.getElementById('single_r2_upload_percent');
    const r2UploadStatus = document.getElementById('single_r2_upload_status');
    const r2ResultBox = document.getElementById('single_r2_result_box');
    const r2UrlText = document.getElementById('single_r2_url_text');
    const r2PreviewLink = document.getElementById('single_r2_preview_link');

    function formatDuration(sec) {
        sec = parseInt(sec) || 0;
        if (sec <= 0) return '00:00';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        if (h > 0) {
            return (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        }
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }

    function formatBytes(bytes, decimals = 2) {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    }

    function extractYouTubeId(url) {
        if (!url) return null;
        url = url.trim();
        const m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([a-zA-Z0-9_-]{11})/);
        return m ? m[1] : null;
    }

    // Toggle Loại video (YouTube / R2)
    function syncSourceCards() {
        sourceCards.forEach(card => {
            const radio = card.querySelector('.single-video-type-radio');
            if (radio && radio.checked) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            syncSourceCards();
            if (this.value === 'r2') {
                ytBox.classList.add('d-none');
                r2Box.classList.remove('d-none');
                badgeType.textContent = 'Cloudflare R2';
                badgeType.className = 'badge bg-warning text-dark px-2 py-1 fs-12 ms-2';
                previewSource.textContent = 'Cloudflare R2';
                previewSource.className = 'badge bg-warning text-dark px-2 py-0 fs-11 fw-bold';
                
                const currentR2Url = r2UrlInput ? r2UrlInput.value.trim() : '';
                if (finalVideoUrlInput) finalVideoUrlInput.value = currentR2Url;

                if (currentR2Url) {
                    previewLink.textContent = currentR2Url;
                    previewBtnOpen.href = currentR2Url;
                    previewBox.classList.remove('d-none');
                } else {
                    previewBox.classList.add('d-none');
                }
            } else {
                ytBox.classList.remove('d-none');
                r2Box.classList.add('d-none');
                badgeType.textContent = 'YouTube';
                badgeType.className = 'badge bg-danger text-white px-2 py-1 fs-12 ms-2';
                previewSource.textContent = 'YouTube';
                previewSource.className = 'badge bg-danger text-white px-2 py-0 fs-11 fw-bold';
                
                const currentYtUrl = ytUrlInput ? ytUrlInput.value.trim() : '';
                if (finalVideoUrlInput) finalVideoUrlInput.value = currentYtUrl;

                if (currentYtUrl) {
                    handleYouTubeUrlChange(true);
                } else {
                    previewBox.classList.add('d-none');
                }
            }
        });
    });

    syncSourceCards();

    // Cập nhật preview thời lượng khi gõ tay
    durationInput.addEventListener('input', function () {
        const fmt = formatDuration(this.value);
        durationPreview.textContent = fmt;
        previewDuration.textContent = fmt;
    });

    // Cập nhật preview tiêu đề khi gõ tay
    titleInput.addEventListener('input', function () {
        previewTitle.textContent = this.value || 'Video hướng dẫn bài học';
    });

    // Xử lý thông minh: Load ngay YouTube URL khi gõ / dán link
    let ytFetchTimeout = null;
    let isFetchingYt = false;

    function handleYouTubeUrlChange(immediate = false, forceRefresh = false) {
        if (!ytUrlInput) return;
        const url = ytUrlInput.value.trim();
        if (finalVideoUrlInput) finalVideoUrlInput.value = url;

        if (!url) {
            previewBox.classList.add('d-none');
            return;
        }

        const ytId = extractYouTubeId(url);
        if (ytId) {
            // 1. TẢI NGAY LẬP TỨC (0ms latency): Hiển thị thumbnail và khung preview ngay
            const instantThumb = 'https://img.youtube.com/vi/' + ytId + '/hqdefault.jpg';
            previewThumb.src = instantThumb;
            if (!thumbInput.value || thumbInput.value.includes('img.youtube.com') || thumbInput.value.includes('default.png') || forceRefresh) {
                thumbInput.value = instantThumb;
            }
            previewLink.textContent = url;
            previewBtnOpen.href = url;
            previewSource.textContent = 'YouTube';
            previewSource.className = 'badge bg-danger text-white px-2 py-0 fs-11 fw-bold';
            previewBox.classList.remove('d-none');

            // 2. Tự động gọi API lấy Tiêu đề & Thời lượng chính xác
            clearTimeout(ytFetchTimeout);
            if (immediate) {
                fetchYouTubeInfoData(url);
            } else {
                ytFetchTimeout = setTimeout(() => {
                    fetchYouTubeInfoData(url);
                }, 400);
            }
        } else {
            previewLink.textContent = url;
            previewBtnOpen.href = url;
            previewBox.classList.remove('d-none');
        }
    }

    function fetchYouTubeInfoData(url, isManual = false) {
        if (!url || isFetchingYt) return;
        isFetchingYt = true;

        if (btnFetchYt) {
            btnFetchYt.disabled = true;
            btnFetchYt.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Đang quét...") }}';
        }

        fetch('{{ route("admin.lesson.fetch_youtube_info") }}?url=' + encodeURIComponent(url))
            .then(res => res.json())
            .then(res => {
                isFetchingYt = false;
                if (btnFetchYt) {
                    btnFetchYt.disabled = false;
                    btnFetchYt.innerHTML = '<i class="ti ti-sparkles me-1"></i>{{ __("Lấy thông tin") }}';
                }

                if (res.status) {
                    if (res.title) {
                        if (!titleInput.value || titleInput.value.trim() === '' || titleInput.value.startsWith('Video') || isManual) {
                            titleInput.value = res.title;
                        }
                        previewTitle.textContent = res.title;
                    }
                    if (res.duration_seconds && res.duration_seconds > 0) {
                        durationInput.value = res.duration_seconds;
                        durationPreview.textContent = formatDuration(res.duration_seconds);
                        previewDuration.textContent = res.formatted_duration || formatDuration(res.duration_seconds);
                    }
                    if (res.thumbnail_url) {
                        thumbInput.value = res.thumbnail_url;
                        previewThumb.src = res.thumbnail_url;
                    }

                    previewBox.classList.remove('d-none');
                    previewLink.textContent = url;
                    previewBtnOpen.href = url;
                } else if (isManual) {
                    alert(res.message || 'Không thể lấy thông tin video YouTube. Vui lòng kiểm tra lại đường dẫn.');
                }
            })
            .catch(() => {
                isFetchingYt = false;
                if (btnFetchYt) {
                    btnFetchYt.disabled = false;
                    btnFetchYt.innerHTML = '<i class="ti ti-sparkles me-1"></i>{{ __("Lấy thông tin") }}';
                }
                if (isManual) {
                    alert('Lỗi kết nối khi quét thông tin YouTube.');
                }
            });
    }

    // Sự kiện nhập link YouTube: Gõ phím, dán (paste), blur, change
    ytUrlInput.addEventListener('input', function () {
        handleYouTubeUrlChange(false);
    });

    ytUrlInput.addEventListener('paste', function () {
        setTimeout(function () {
            handleYouTubeUrlChange(true);
        }, 30);
    });

    ytUrlInput.addEventListener('change', function () {
        handleYouTubeUrlChange(true);
    });

    ytUrlInput.addEventListener('blur', function () {
        handleYouTubeUrlChange(true);
    });

    // Nút bấm thủ công
    if (btnFetchYt) {
        btnFetchYt.addEventListener('click', function () {
            const url = ytUrlInput.value.trim();
            if (!url) {
                alert('Vui lòng dán link YouTube trước khi bấm lấy thông tin.');
                return;
            }
            fetchYouTubeInfoData(url, true);
        });
    }

    // Đảm bảo dữ liệu trước khi submit form
    const parentForm = ytUrlInput.closest('form');
    if (parentForm) {
        parentForm.addEventListener('submit', function () {
            const checkedRadio = document.querySelector('.single-video-type-radio:checked');
            const vType = checkedRadio ? checkedRadio.value : 'youtube';
            if (vType === 'youtube') {
                if (finalVideoUrlInput) finalVideoUrlInput.value = ytUrlInput.value.trim();
            } else {
                if (finalVideoUrlInput) finalVideoUrlInput.value = r2UrlInput.value.trim();
            }
        });
    }

    // Tự động nhận diện ban đầu khi load trang (đặc biệt khi mở trang edit)
    const initialVideoType = document.querySelector('.single-video-type-radio:checked')?.value || 'youtube';
    if (initialVideoType === 'youtube' && ytUrlInput.value.trim()) {
        handleYouTubeUrlChange(false, false);
    }

    // Dropzone Interactivity
    btnBrowse.addEventListener('click', (e) => {
        e.stopPropagation();
        r2FileInput.click();
    });

    dropArea.addEventListener('click', (e) => {
        if (!e.target.closest('#r2_file_selected_box') && !e.target.closest('#single_r2_progress_wrapper')) {
            r2FileInput.click();
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.remove('dragover');
        });
    });

    dropArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            handleSelectedR2File(files[0]);
        }
    });

    r2FileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            handleSelectedR2File(this.files[0]);
        }
    });

    function handleSelectedR2File(file) {
        selectedFileName.textContent = file.name;
        selectedFileSize.textContent = formatBytes(file.size);
        idleBox.classList.add('d-none');
        selectedBox.classList.remove('d-none');
    }

    btnCancelFile.addEventListener('click', (e) => {
        e.stopPropagation();
        r2FileInput.value = '';
        selectedBox.classList.add('d-none');
        idleBox.classList.remove('d-none');
    });

    // Tải lên Cloudflare R2 qua AJAX
    btnUploadR2.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!r2FileInput.files || r2FileInput.files.length === 0) {
            alert('Vui lòng chọn một file video (MP4/MOV) để tải lên.');
            return;
        }

        const file = r2FileInput.files[0];
        const formData = new FormData();
        formData.append('video_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        btnUploadR2.disabled = true;
        btnCancelFile.disabled = true;
        r2ProgressWrapper.classList.remove('d-none');
        r2ProgressBar.style.width = '0%';
        r2ProgressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
        r2UploadPercent.textContent = '0%';
        r2UploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning" role="status"></span> Đang tải video lên Cloudflare R2...';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.lesson.upload_r2_video") }}', true);

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                r2ProgressBar.style.width = percent + '%';
                r2UploadPercent.textContent = percent + '%';
                r2UploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning" role="status"></span> Đang tải: ' + formatBytes(e.loaded) + ' / ' + formatBytes(e.total);
            }
        };

        xhr.onload = function () {
            btnUploadR2.disabled = false;
            btnCancelFile.disabled = false;
            if (xhr.status === 200) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.status) {
                        r2ProgressBar.className = 'progress-bar bg-success';
                        r2ProgressBar.style.width = '100%';
                        r2UploadPercent.textContent = '100%';
                        r2UploadStatus.innerHTML = '<i class="ti ti-check text-success me-1"></i> Tải lên Cloudflare R2 thành công!';
                        
                        // Update fields
                        r2ResultBox.classList.remove('d-none');
                        r2UrlInput.value = res.url;
                        if (finalVideoUrlInput) finalVideoUrlInput.value = res.url;
                        r2PathInput.value = res.path;
                        r2UrlText.textContent = res.url;
                        r2PreviewLink.href = res.url;

                        if (res.duration_seconds && res.duration_seconds > 0) {
                            durationInput.value = res.duration_seconds;
                            durationPreview.textContent = formatDuration(res.duration_seconds);
                            previewDuration.textContent = res.formatted_duration || formatDuration(res.duration_seconds);
                        }

                        if (!titleInput.value) {
                            titleInput.value = file.name.replace(/\.[^/.]+$/, "");
                            previewTitle.textContent = titleInput.value;
                        }

                        previewBox.classList.remove('d-none');
                        previewLink.textContent = res.url;
                        previewBtnOpen.href = res.url;
                        previewSource.textContent = 'Cloudflare R2';
                        previewSource.className = 'badge bg-warning text-dark px-2 py-0 fs-11 fw-bold';

                        setTimeout(() => {
                            selectedBox.classList.add('d-none');
                            idleBox.classList.remove('d-none');
                            r2ProgressWrapper.classList.add('d-none');
                        }, 2500);
                    } else {
                        r2ProgressBar.className = 'progress-bar bg-danger';
                        r2UploadStatus.textContent = res.message || 'Lỗi tải lên R2.';
                    }
                } catch (e) {
                    r2ProgressBar.className = 'progress-bar bg-danger';
                    r2UploadStatus.textContent = 'Lỗi xử lý phản hồi từ server.';
                }
            } else {
                r2ProgressBar.className = 'progress-bar bg-danger';
                r2UploadStatus.textContent = 'Lỗi máy chủ (' + xhr.status + ').';
            }
        };

        xhr.onerror = function () {
            btnUploadR2.disabled = false;
            btnCancelFile.disabled = false;
            r2ProgressBar.className = 'progress-bar bg-danger';
            r2UploadStatus.textContent = 'Lỗi kết nối mạng khi tải lên R2.';
        };

        xhr.send(formData);
    });
});
</script>
@endpush
