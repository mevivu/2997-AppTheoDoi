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
                    <input type="file" id="single_r2_file" class="d-none" accept="video/mp4,video/quicktime,video/webm,video/x-matroska,video/avi">
                    
                    {{-- Trạng thái chờ chọn file --}}
                    <div class="r2-dropzone-idle" id="r2_drop_idle">
                        <div class="mb-3">
                            <span class="avatar avatar-xl rounded-circle bg-warning-lt text-warning shadow-xs dropzone-cloud-icon">
                                <i class="ti ti-cloud-upload fs-1"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark mb-1 fs-15">{{ __('Kéo & thả file video vào đây hoặc Click để chọn file') }}</h4>
                        <p class="text-muted fs-12 mb-3">
                            {{ __('Định dạng hỗ trợ:') }} <span class="badge bg-light text-dark fw-semibold">MP4</span> <span class="badge bg-light text-dark fw-semibold">MOV</span> <span class="badge bg-light text-dark fw-semibold">WebM</span> — {{ __('Tối đa 200MB') }}
                        </p>
                        <button type="button" class="btn btn-outline-warning fw-semibold px-4" id="btn_browse_r2">
                            <i class="ti ti-folder-open me-2"></i>{{ __('Duyệt file từ máy tính') }}
                        </button>
                    </div>

                    {{-- Trạng thái đã chọn file, sẵn sàng upload & thanh tiến trình --}}
                    <div class="r2-file-selected d-none" id="r2_file_selected_box">
                        <div class="p-3 bg-white rounded-3 border border-warning shadow-xs text-start">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <span class="avatar avatar-md rounded-3 bg-warning text-white flex-shrink-0">
                                        <i class="ti ti-file-video fs-2"></i>
                                    </span>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate fs-14" id="r2_selected_filename">video.mp4</div>
                                        <div class="text-muted fs-12 d-flex align-items-center gap-2 mt-1">
                                            <span id="r2_selected_filesize" class="badge bg-light text-dark fw-semibold">0 MB</span>
                                            <span id="r2_video_meta_duration" class="badge bg-blue-lt fw-semibold d-none"><i class="ti ti-clock me-1"></i>--:--</span>
                                            <span id="r2_upload_status_tag" class="badge bg-warning-lt text-warning fw-semibold"><i class="ti ti-alert-circle me-1"></i>Chưa tải lên</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm text-danger" id="btn_cancel_r2_file" title="Đổi file khác">
                                        <i class="ti ti-rotate me-1"></i>{{ __('Đổi file') }}
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 shadow-xs" id="btn_upload_single_r2">
                                        <i class="ti ti-cloud-upload me-1"></i>{{ __('Tải lên Cloudflare R2 ngay') }}
                                    </button>
                                </div>
                            </div>

                            {{-- Thanh tiến trình upload trực quan thời gian thực --}}
                            <div class="mt-3 pt-3 border-top d-none" id="single_r2_progress_wrapper">
                                <div class="d-flex justify-content-between align-items-center text-dark fw-semibold fs-12 mb-1">
                                    <span id="single_r2_upload_status" class="d-flex align-items-center gap-2">
                                        <span class="spinner-border spinner-border-sm text-warning" role="status"></span>
                                        <span>{{ __('Đang chuẩn bị tải file lên Cloudflare R2...') }}</span>
                                    </span>
                                    <span id="single_r2_upload_percent" class="badge bg-warning text-dark fw-bold fs-12 px-2 py-1">0%</span>
                                </div>
                                <div class="progress" style="height: 14px; border-radius: 8px; background-color: #f1f5f9; overflow: hidden;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="single_r2_progress_bar" role="progressbar" style="width: 0%; font-size: 10px; font-weight: bold; line-height: 14px;">0%</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-11 text-muted mt-2">
                                    <span id="single_r2_bytes_status">0 MB / 0 MB</span>
                                    <span id="single_r2_speed_status"><i class="ti ti-cloud-upload text-warning me-1"></i>Đang truyền tải video an toàn lên Cloudflare R2...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hộp thông báo kết quả lưu trữ R2 thành công --}}
                <div class="mt-3 {{ !empty($videoPath) && $videoType === 'r2' ? '' : 'd-none' }}" id="single_r2_result_box">
                    <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-0 rounded-3 border-0 shadow-xs">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <span class="avatar avatar-md rounded-circle bg-success text-white flex-shrink-0">
                                <i class="ti ti-check fs-2"></i>
                            </span>
                            <div class="overflow-hidden">
                                <div class="fw-bold fs-13 text-success d-flex align-items-center gap-1">
                                    <span>{{ __('Video đã được lưu trữ an toàn trên Cloudflare R2!') }}</span>
                                    <span class="badge bg-success-lt text-success fs-11">CDN Active</span>
                                </div>
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
                <div class="form-text text-muted fs-11">{{ __('Nhập số giây hoặc hệ thống tự động quét từ video.') }}</div>
            </div>

            {{-- 5. Ảnh đại diện Thumbnail (tùy chọn) --}}
            <div class="col-12" id="single_thumb_wrapper">
                <label class="form-label fw-bold text-dark fs-13 mb-1">
                    {{ __('Ảnh bìa / Thumbnail video (Tùy chọn)') }}:
                </label>
                <div class="input-group">
                    <input type="text" name="videos[0][thumbnail]" id="single_video_thumbnail" class="form-control"
                           value="{{ $videoThumb }}" placeholder="URL ảnh bìa...">
                    <button type="button" class="btn btn-outline-secondary" id="btn_clear_thumb" title="Xóa ảnh bìa">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="form-text text-muted fs-11" id="thumb_hint_text">
                    {{ $videoType === 'youtube' ? __('Hệ thống tự động lấy ảnh bìa từ YouTube khi dán link.') : __('Với Cloudflare R2, video tự dùng frame đầu hoặc bạn có thể nhập link ảnh bìa riêng tùy chọn.') }}
                </div>
            </div>

            {{-- 6A. Khung xem trước Video YouTube (YouTube Preview) --}}
            <div class="col-12 {{ $videoType === 'youtube' && !empty($videoUrl) ? '' : 'd-none' }}" id="single_yt_preview_box">
                <div class="card border border-2 border-danger-subtle rounded-3 overflow-hidden shadow-xs" style="background-color: #fafbfc;">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                            <div class="position-relative rounded-3 overflow-hidden shadow-xs" style="width: 150px; height: 90px; flex-shrink: 0; background: #1a1a1a;">
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
                                    <span class="badge bg-danger text-white px-2 py-0 fs-11 fw-bold">YouTube</span>
                                    <div class="fw-bold text-dark text-truncate fs-14" id="single_preview_title">
                                        {{ !empty($videoTitle) ? $videoTitle : __('Video hướng dẫn bài học') }}
                                    </div>
                                </div>
                                <div class="text-muted fs-12 text-truncate mt-1" id="single_preview_link">
                                    {{ $videoUrl }}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ $videoUrl ?: '#' }}" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold" id="single_preview_btn_open">
                                    <i class="ti ti-brand-youtube me-1"></i>{{ __('Xem trên YouTube') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6B. Khung xem trước Trình phát Video Cloudflare R2 (HTML5 Video Player Preview) --}}
            <div class="col-12 {{ $videoType === 'r2' && (!empty($videoUrl) || !empty($videoPath)) ? '' : 'd-none' }}" id="single_r2_preview_box">
                <div class="card border border-2 border-warning rounded-3 overflow-hidden shadow-xs" style="background-color: #fdfcf9;">
                    <div class="card-header bg-warning-lt py-2 px-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 fs-11">
                                <i class="ti ti-player-play me-1"></i>Cloudflare R2 Player
                            </span>
                            <span class="text-dark fw-bold fs-13" id="r2_preview_badge_status">
                                {{ !empty($videoUrl) ? 'Đang phát từ Cloudflare R2' : 'Xem trước video cục bộ' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white fs-11 px-2 py-1" id="r2_player_duration_badge">
                                {{ $formattedDuration }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            {{-- Trình phát Video HTML5 --}}
                            <div class="col-12 col-md-7 col-lg-8">
                                <div class="position-relative rounded-3 overflow-hidden bg-black shadow-sm" style="max-height: 280px; min-height: 180px; display: flex; align-items: center; justify-content: center;">
                                    <video id="single_r2_video_player" controls playsinline preload="metadata" class="w-100 h-100" style="max-height: 280px; object-fit: contain; background: #000;"
                                           src="{{ $videoType === 'r2' ? $videoUrl : '' }}">
                                        Trình duyệt của bạn không hỗ trợ thẻ video HTML5.
                                    </video>
                                </div>
                            </div>
                            {{-- Thông tin video R2 --}}
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="p-3 bg-white rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted fs-11 text-uppercase fw-bold mb-1">{{ __('Tiêu đề video') }}</div>
                                        <h5 class="fw-bold text-dark fs-14 mb-2 text-truncate-2" id="r2_preview_info_title">
                                            {{ !empty($videoTitle) ? $videoTitle : __('Video hướng dẫn bài học') }}
                                        </h5>
                                        
                                        <div class="text-muted fs-11 text-uppercase fw-bold mb-1 mt-3">{{ __('Đường dẫn Stream R2') }}</div>
                                        <div class="text-break fs-11 text-muted p-2 rounded bg-light font-monospace" id="r2_preview_info_url" style="word-break: break-all; max-height: 70px; overflow-y: auto;">
                                            {{ $videoType === 'r2' && !empty($videoUrl) ? $videoUrl : __('Chưa tải lên R2 (Đang xem file tạm từ máy)') }}
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ $videoType === 'r2' && !empty($videoUrl) ? $videoUrl : '#' }}" target="_blank"
                                           class="btn btn-outline-warning btn-sm w-100 fw-semibold {{ $videoType === 'r2' && !empty($videoUrl) ? '' : 'disabled' }}"
                                           id="btn_r2_open_external">
                                            <i class="ti ti-external-link me-1"></i>{{ __('Mở xem stream CDN') }}
                                        </a>
                                    </div>
                                </div>
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
        transform: scale(1.002);
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
    const btnClearThumb = document.getElementById('btn_clear_thumb');
    const thumbHintText = document.getElementById('thumb_hint_text');
    const badgeType = document.getElementById('single_video_type_badge');

    // YouTube Preview elements
    const ytPreviewBox = document.getElementById('single_yt_preview_box');
    const ytPreviewThumb = document.getElementById('single_preview_thumb');
    const ytPreviewTitle = document.getElementById('single_preview_title');
    const ytPreviewDuration = document.getElementById('single_preview_duration_text');
    const ytPreviewLink = document.getElementById('single_preview_link');
    const ytPreviewBtnOpen = document.getElementById('single_preview_btn_open');

    // R2 Player Preview elements
    const r2PreviewBox = document.getElementById('single_r2_preview_box');
    const r2VideoPlayer = document.getElementById('single_r2_video_player');
    const r2PreviewBadgeStatus = document.getElementById('r2_preview_badge_status');
    const r2PlayerDurationBadge = document.getElementById('r2_player_duration_badge');
    const r2PreviewInfoTitle = document.getElementById('r2_preview_info_title');
    const r2PreviewInfoUrl = document.getElementById('r2_preview_info_url');
    const btnR2OpenExternal = document.getElementById('btn_r2_open_external');

    // Dropzone elements
    const dropArea = document.getElementById('r2_drop_area');
    const r2FileInput = document.getElementById('single_r2_file');
    const btnBrowse = document.getElementById('btn_browse_r2');
    const idleBox = document.getElementById('r2_drop_idle');
    const selectedBox = document.getElementById('r2_file_selected_box');
    const selectedFileName = document.getElementById('r2_selected_filename');
    const selectedFileSize = document.getElementById('r2_selected_filesize');
    const r2VideoMetaDuration = document.getElementById('r2_video_meta_duration');
    const r2UploadStatusTag = document.getElementById('r2_upload_status_tag');
    const btnCancelFile = document.getElementById('btn_cancel_r2_file');
    const btnUploadR2 = document.getElementById('btn_upload_single_r2');
    const r2ProgressWrapper = document.getElementById('single_r2_progress_wrapper');
    const r2ProgressBar = document.getElementById('single_r2_progress_bar');
    const r2UploadPercent = document.getElementById('single_r2_upload_percent');
    const r2UploadStatus = document.getElementById('single_r2_upload_status');
    const r2BytesStatus = document.getElementById('single_r2_bytes_status');
    const r2SpeedStatus = document.getElementById('single_r2_speed_status');
    const r2ResultBox = document.getElementById('single_r2_result_box');
    const r2UrlText = document.getElementById('single_r2_url_text');
    const r2PreviewLink = document.getElementById('single_r2_preview_link');

    // State management: Quản lý riêng biệt dữ liệu YouTube vs R2
    let isR2Uploaded = Boolean(r2PathInput && r2PathInput.value.trim());
    let currentLocalBlobUrl = null;
    let currentR2File = null;

    const sourceDataCache = {
        youtube: {
            url: ytUrlInput ? ytUrlInput.value.trim() : '',
            title: titleInput ? titleInput.value.trim() : '',
            duration: durationInput ? durationInput.value : 0,
            thumbnail: thumbInput ? thumbInput.value.trim() : ''
        },
        r2: {
            url: r2UrlInput ? r2UrlInput.value.trim() : '',
            path: r2PathInput ? r2PathInput.value.trim() : '',
            title: '',
            duration: 0,
            thumbnail: ''
        }
    };

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

    if (btnClearThumb) {
        btnClearThumb.addEventListener('click', function () {
            if (thumbInput) thumbInput.value = '';
        });
    }

    // Xử lý chuyển đổi Tab Nguồn phát Video
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            syncSourceCards();
            const selectedType = this.value;

            if (selectedType === 'r2') {
                // Lưu lại state hiện tại của YouTube
                if (ytUrlInput) sourceDataCache.youtube.url = ytUrlInput.value.trim();
                if (titleInput && (!currentR2File || sourceDataCache.youtube.title)) {
                    sourceDataCache.youtube.title = titleInput.value.trim();
                }
                if (durationInput) sourceDataCache.youtube.duration = durationInput.value;
                if (thumbInput) sourceDataCache.youtube.thumbnail = thumbInput.value.trim();

                // Chuyển sang Cloudflare R2 UI
                ytBox.classList.add('d-none');
                r2Box.classList.remove('d-none');
                ytPreviewBox.classList.add('d-none'); // Ẩn hoàn toàn preview YouTube

                badgeType.textContent = 'Cloudflare R2';
                badgeType.className = 'badge bg-warning text-dark px-2 py-1 fs-12 ms-2';
                thumbHintText.textContent = '{{ __("Với Cloudflare R2, video tự dùng frame đầu hoặc bạn có thể nhập link ảnh bìa riêng tùy chọn.") }}';

                // Tách biệt Thumbnail: Nếu đang là URL YouTube thì reset
                if (thumbInput && thumbInput.value.includes('img.youtube.com')) {
                    thumbInput.value = '';
                }

                // Kiểm tra xem đã có video R2 trước đó hay vừa chọn file chưa
                const currentR2Url = r2UrlInput ? r2UrlInput.value.trim() : '';
                if (finalVideoUrlInput) finalVideoUrlInput.value = currentR2Url;

                if (currentLocalBlobUrl) {
                    // Nếu đang có local preview của file MP4 vừa chọn
                    showR2VideoPreview(currentLocalBlobUrl, false, currentR2File ? currentR2File.name : '');
                } else if (currentR2Url) {
                    // Nếu đã có link R2 từ server
                    showR2VideoPreview(currentR2Url, true);
                } else {
                    // Chưa có video R2 nào -> ẩn preview box R2
                    r2PreviewBox.classList.add('d-none');
                    if (r2VideoPlayer) {
                        r2VideoPlayer.pause();
                        r2VideoPlayer.src = '';
                    }
                }
            } else {
                // Chuyển sang YouTube UI
                ytBox.classList.remove('d-none');
                r2Box.classList.add('d-none');
                r2PreviewBox.classList.add('d-none'); // Ẩn preview R2
                if (r2VideoPlayer) {
                    r2VideoPlayer.pause();
                }

                badgeType.textContent = 'YouTube';
                badgeType.className = 'badge bg-danger text-white px-2 py-1 fs-12 ms-2';
                thumbHintText.textContent = '{{ __("Hệ thống tự động lấy ảnh bìa từ YouTube khi dán link.") }}';

                // Khôi phục lại dữ liệu YouTube
                if (sourceDataCache.youtube.url && ytUrlInput) {
                    ytUrlInput.value = sourceDataCache.youtube.url;
                }
                if (sourceDataCache.youtube.thumbnail && thumbInput && !thumbInput.value) {
                    thumbInput.value = sourceDataCache.youtube.thumbnail;
                }
                if (sourceDataCache.youtube.title && titleInput && (!titleInput.value || (currentR2File && titleInput.value.includes(currentR2File.name.replace(/\.[^/.]+$/, ""))))) {
                    titleInput.value = sourceDataCache.youtube.title;
                }
                if (sourceDataCache.youtube.duration && durationInput) {
                    durationInput.value = sourceDataCache.youtube.duration;
                    durationPreview.textContent = formatDuration(sourceDataCache.youtube.duration);
                }

                const currentYtUrl = ytUrlInput ? ytUrlInput.value.trim() : '';
                if (finalVideoUrlInput) finalVideoUrlInput.value = currentYtUrl;

                if (currentYtUrl) {
                    handleYouTubeUrlChange(true);
                } else {
                    ytPreviewBox.classList.add('d-none');
                }
            }
        });
    });

    syncSourceCards();

    // Cập nhật preview thời lượng khi gõ tay
    durationInput.addEventListener('input', function () {
        const fmt = formatDuration(this.value);
        durationPreview.textContent = fmt;
        if (ytPreviewDuration) ytPreviewDuration.textContent = fmt;
        if (r2PlayerDurationBadge) r2PlayerDurationBadge.textContent = fmt;
    });

    // Cập nhật preview tiêu đề khi gõ tay
    titleInput.addEventListener('input', function () {
        const val = this.value || '{{ __("Video hướng dẫn bài học") }}';
        if (ytPreviewTitle) ytPreviewTitle.textContent = val;
        if (r2PreviewInfoTitle) r2PreviewInfoTitle.textContent = val;
    });

    // Hiển thị Trình phát Video Cloudflare R2
    function showR2VideoPreview(srcUrl, isUploaded = false, originalFileName = '') {
        if (!r2PreviewBox || !r2VideoPlayer) return;

        r2VideoPlayer.src = srcUrl;
        r2VideoPlayer.load();
        r2PreviewBox.classList.remove('d-none');

        const titleVal = titleInput.value || originalFileName.replace(/\.[^/.]+$/, "").replace(/[_-]/g, " ") || '{{ __("Video bài học R2") }}';
        if (r2PreviewInfoTitle) r2PreviewInfoTitle.textContent = titleVal;

        if (isUploaded) {
            r2PreviewBadgeStatus.innerHTML = '<i class="ti ti-circle-check-filled text-success me-1"></i>{{ __("Đang phát từ Cloudflare R2") }}';
            r2PreviewInfoUrl.textContent = srcUrl;
            if (btnR2OpenExternal) {
                btnR2OpenExternal.classList.remove('disabled');
                btnR2OpenExternal.href = srcUrl;
            }
            if (r2UploadStatusTag) {
                r2UploadStatusTag.className = 'badge bg-success-lt text-success fw-semibold';
                r2UploadStatusTag.innerHTML = '<i class="ti ti-check me-1"></i>Đã tải lên';
            }
        } else {
            r2PreviewBadgeStatus.innerHTML = '<i class="ti ti-device-floppy text-warning me-1"></i>{{ __("Xem trước video từ máy tính (Chưa tải lên R2)") }}';
            r2PreviewInfoUrl.textContent = 'File: ' + originalFileName + ' (Chờ bấm tải lên Cloudflare R2)';
            if (btnR2OpenExternal) {
                btnR2OpenExternal.classList.add('disabled');
                btnR2OpenExternal.href = '#';
            }
            if (r2UploadStatusTag) {
                r2UploadStatusTag.className = 'badge bg-warning-lt text-warning fw-semibold';
                r2UploadStatusTag.innerHTML = '<i class="ti ti-alert-circle me-1"></i>Chưa tải lên';
            }
        }
    }

    // Xử lý khi chọn file R2 (Local Preview + Duration Metadata extraction)
    function handleSelectedR2File(file) {
        if (!file) return;

        currentR2File = file;
        isR2Uploaded = false;

        selectedFileName.textContent = file.name;
        selectedFileSize.textContent = formatBytes(file.size);
        idleBox.classList.add('d-none');
        selectedBox.classList.remove('d-none');

        // Reset progress bar
        r2ProgressWrapper.classList.add('d-none');
        r2ProgressBar.style.width = '0%';
        r2ProgressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
        r2ProgressBar.textContent = '0%';
        r2UploadPercent.className = 'badge bg-warning text-dark fw-bold fs-12 px-2 py-1';
        r2UploadPercent.textContent = '0%';

        // 1. Tạo Local Preview phát trực tiếp bằng thẻ <video>
        if (currentLocalBlobUrl) {
            URL.revokeObjectURL(currentLocalBlobUrl);
        }
        currentLocalBlobUrl = URL.createObjectURL(file);
        showR2VideoPreview(currentLocalBlobUrl, false, file.name);

        // 2. Trích xuất Metadata Duration của video MP4 ngay trên trình duyệt (0ms)
        const tempVideo = document.createElement('video');
        tempVideo.preload = 'metadata';
        tempVideo.src = currentLocalBlobUrl;
        tempVideo.onloadedmetadata = function () {
            const sec = Math.round(tempVideo.duration) || 0;
            if (sec > 0) {
                durationInput.value = sec;
                const formatted = formatDuration(sec);
                durationPreview.textContent = formatted;
                if (r2PlayerDurationBadge) r2PlayerDurationBadge.textContent = formatted;
                if (r2VideoMetaDuration) {
                    r2VideoMetaDuration.innerHTML = '<i class="ti ti-clock me-1"></i>' + formatted;
                    r2VideoMetaDuration.classList.remove('d-none');
                }
            }
        };

        // 3. Tự động gợi ý Tiêu đề từ tên file (nếu tiêu đề rỗng hoặc đang là tiêu đề cũ của YouTube)
        const isFromYouTube = ytUrlInput && extractYouTubeId(ytUrlInput.value.trim());
        const cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/[_-]/g, " ").trim();
        if (!titleInput.value || titleInput.value.trim() === '' || isFromYouTube) {
            titleInput.value = cleanName;
            if (r2PreviewInfoTitle) r2PreviewInfoTitle.textContent = cleanName;
        }

        // Xóa link thumbnail YouTube nếu còn sót
        if (thumbInput && thumbInput.value.includes('img.youtube.com')) {
            thumbInput.value = '';
        }
    }

    // Xử lý thông minh: Load ngay YouTube URL khi gõ / dán link
    let ytFetchTimeout = null;
    let isFetchingYt = false;

    function handleYouTubeUrlChange(immediate = false, forceRefresh = false) {
        if (!ytUrlInput) return;
        const url = ytUrlInput.value.trim();
        if (finalVideoUrlInput) finalVideoUrlInput.value = url;

        if (!url) {
            ytPreviewBox.classList.add('d-none');
            return;
        }

        const ytId = extractYouTubeId(url);
        if (ytId) {
            // Hiển thị thumbnail và khung preview YouTube ngay lập tức
            const instantThumb = 'https://img.youtube.com/vi/' + ytId + '/hqdefault.jpg';
            ytPreviewThumb.src = instantThumb;
            if (!thumbInput.value || thumbInput.value.includes('img.youtube.com') || forceRefresh) {
                thumbInput.value = instantThumb;
            }
            ytPreviewLink.textContent = url;
            ytPreviewBtnOpen.href = url;
            ytPreviewBox.classList.remove('d-none');

            // Gọi API lấy Tiêu đề & Thời lượng chính xác
            clearTimeout(ytFetchTimeout);
            if (immediate) {
                fetchYouTubeInfoData(url);
            } else {
                ytFetchTimeout = setTimeout(() => {
                    fetchYouTubeInfoData(url);
                }, 400);
            }
        } else {
            ytPreviewLink.textContent = url;
            ytPreviewBtnOpen.href = url;
            ytPreviewBox.classList.remove('d-none');
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
                        if (ytPreviewTitle) ytPreviewTitle.textContent = res.title;
                    }
                    if (res.duration_seconds && res.duration_seconds > 0) {
                        durationInput.value = res.duration_seconds;
                        durationPreview.textContent = formatDuration(res.duration_seconds);
                        if (ytPreviewDuration) ytPreviewDuration.textContent = res.formatted_duration || formatDuration(res.duration_seconds);
                    }
                    if (res.thumbnail_url) {
                        thumbInput.value = res.thumbnail_url;
                        ytPreviewThumb.src = res.thumbnail_url;
                    }

                    ytPreviewBox.classList.remove('d-none');
                    ytPreviewLink.textContent = url;
                    ytPreviewBtnOpen.href = url;
                } else if (isManual) {
                    alert(res.message || '{{ __("Không thể lấy thông tin video YouTube. Vui lòng kiểm tra lại đường dẫn.") }}');
                }
            })
            .catch(() => {
                isFetchingYt = false;
                if (btnFetchYt) {
                    btnFetchYt.disabled = false;
                    btnFetchYt.innerHTML = '<i class="ti ti-sparkles me-1"></i>{{ __("Lấy thông tin") }}';
                }
                if (isManual) {
                    alert('{{ __("Lỗi kết nối khi quét thông tin YouTube.") }}');
                }
            });
    }

    // Sự kiện nhập link YouTube
    ytUrlInput.addEventListener('input', () => handleYouTubeUrlChange(false));
    ytUrlInput.addEventListener('paste', () => setTimeout(() => handleYouTubeUrlChange(true), 30));
    ytUrlInput.addEventListener('change', () => handleYouTubeUrlChange(true));
    ytUrlInput.addEventListener('blur', () => handleYouTubeUrlChange(true));

    if (btnFetchYt) {
        btnFetchYt.addEventListener('click', function () {
            const url = ytUrlInput.value.trim();
            if (!url) {
                alert('{{ __("Vui lòng dán link YouTube trước khi bấm lấy thông tin.") }}');
                return;
            }
            fetchYouTubeInfoData(url, true);
        });
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

    btnCancelFile.addEventListener('click', (e) => {
        e.stopPropagation();
        r2FileInput.value = '';
        currentR2File = null;
        isR2Uploaded = false;
        selectedBox.classList.add('d-none');
        idleBox.classList.remove('d-none');
        if (currentLocalBlobUrl) {
            URL.revokeObjectURL(currentLocalBlobUrl);
            currentLocalBlobUrl = null;
        }

        const existingUrl = r2UrlInput ? r2UrlInput.value.trim() : '';
        if (existingUrl) {
            showR2VideoPreview(existingUrl, true);
        } else {
            r2PreviewBox.classList.add('d-none');
            if (r2VideoPlayer) {
                r2VideoPlayer.pause();
                r2VideoPlayer.src = '';
            }
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Hiển thị Dialog Popup chứa thanh tiến trình Upload trực tiếp (SweetAlert2)
    function openR2UploadProgressDialog(file, isAutoSubmit = false) {
        if (typeof Swal === 'undefined') return;

        const safeName = escapeHtml(file.name);
        const sizeStr = formatBytes(file.size);
        const subNotice = isAutoSubmit
            ? '{{ __("Hệ thống đang tự động tải video của bạn lên Cloudflare R2 và sẽ lưu bài học ngay khi hoàn tất.") }}'
            : '{{ __("Vui lòng giữ kết nối mạng trong quá trình tải video lên CDN Cloudflare R2.") }}';

        Swal.fire({
            title: '<div class="d-flex align-items-center justify-content-center gap-2 text-dark fs-16 fw-bold"><i class="ti ti-cloud-upload text-warning fs-1"></i><span>{{ __("Đang tải video lên Cloudflare R2...") }}</span></div>',
            html: `
                <div class="text-start px-1 py-1">
                    <!-- Khối thông tin file video đang tải -->
                    <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3 border mb-3">
                        <span class="avatar avatar-md rounded-3 bg-warning text-white flex-shrink-0">
                            <i class="ti ti-file-video fs-2"></i>
                        </span>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-dark text-truncate fs-13" title="${safeName}">${safeName}</div>
                            <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-2">
                                <span>${sizeStr}</span>
                                <span class="badge bg-warning-lt text-warning fw-semibold">Cloudflare R2 CDN</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tiêu đề tiến trình & % hoàn thành -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-12 fw-bold text-dark d-flex align-items-center gap-1" id="swal_status_text">
                            <span class="spinner-border spinner-border-sm text-warning" role="status"></span>
                            <span>{{ __("Đang kết nối Cloudflare...") }}</span>
                        </span>
                        <span class="badge bg-warning text-dark fw-bold fs-12 px-2 py-1 shadow-xs" id="swal_percent_badge">0%</span>
                    </div>

                    <!-- Thanh Progress Bar lớn và rực rỡ TRỰC TIẾP TRONG DIALOG -->
                    <div class="progress shadow-xs" style="height: 18px; border-radius: 9px; background-color: #e2e8f0; overflow: hidden;">
                        <div id="swal_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning fw-bold text-dark"
                             role="progressbar" style="width: 0%; font-size: 11px; line-height: 18px; transition: width 0.15s ease;">0%</div>
                    </div>

                    <!-- Chi tiết số MB đã tải và trạng thái mạng -->
                    <div class="d-flex justify-content-between align-items-center mt-2 fs-11 text-muted">
                        <span id="swal_bytes_text" class="fw-semibold">0 MB / ${sizeStr}</span>
                        <span id="swal_speed_text"><i class="ti ti-arrow-up text-warning me-1"></i>{{ __("Đang truyền dữ liệu...") }}</span>
                    </div>

                    <!-- Ghi chú dưới dialog -->
                    <div class="mt-3 p-2 rounded bg-warning-lt text-warning fs-11 text-center border border-warning-subtle">
                        <i class="ti ti-info-circle me-1"></i>${subNotice}
                    </div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            width: '490px',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        });
    }

    // Hàm thực hiện upload Cloudflare R2 qua AJAX với thanh tiến trình trực quan (đồng bộ cả Page & Dialog)
    function executeR2Upload(callback = null, showDialog = true) {
        if (!r2FileInput.files || r2FileInput.files.length === 0) {
            if (callback) callback(false, '{{ __("Vui lòng chọn file video.") }}');
            return;
        }

        const file = r2FileInput.files[0];
        const isAutoSubmit = Boolean(callback);

        // Mở Dialog Popup hiển thị thanh Progress ngay lập tức
        if (showDialog) {
            openR2UploadProgressDialog(file, isAutoSubmit);
        }

        const formData = new FormData();
        formData.append('video_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        btnUploadR2.disabled = true;
        btnCancelFile.disabled = true;
        btnUploadR2.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Đang tải lên...") }}';

        // Cập nhật giao diện trên Dropzone của trang
        r2ProgressWrapper.classList.remove('d-none');
        r2ProgressBar.style.width = '0%';
        r2ProgressBar.textContent = '0%';
        r2ProgressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
        r2UploadPercent.className = 'badge bg-warning text-dark fw-bold fs-12 px-2 py-1';
        r2UploadPercent.textContent = '0%';
        r2UploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning" role="status"></span> {{ __("Đang chuẩn bị truyền tải video...") }}';
        r2BytesStatus.textContent = '0 MB / ' + formatBytes(file.size);
        r2SpeedStatus.innerHTML = '<i class="ti ti-cloud-upload text-warning me-1"></i>{{ __("Đang kết nối Cloudflare...") }}';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.lesson.upload_r2_video") }}', true);

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percent = Math.min(Math.round((e.loaded / e.total) * 100), 99);
                const loadedStr = formatBytes(e.loaded);
                const totalStr = formatBytes(e.total);

                // 1. Cập nhật Dropzone trên trang chính
                r2ProgressBar.style.width = percent + '%';
                r2ProgressBar.textContent = percent + '%';
                r2UploadPercent.textContent = percent + '%';
                r2BytesStatus.textContent = loadedStr + ' / ' + totalStr;
                r2UploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning" role="status"></span> {{ __("Đang tải lên Cloudflare R2: ") }}' + percent + '%';
                r2SpeedStatus.innerHTML = '<i class="ti ti-arrow-up text-warning me-1"></i>{{ __("Đang đồng bộ dữ liệu lên CDN...") }}';

                // 2. CẬP NHẬT TRỰC TIẾP TRONG SWEETALERT DIALOG
                const swalBar = document.getElementById('swal_progress_bar');
                const swalPercent = document.getElementById('swal_percent_badge');
                const swalBytes = document.getElementById('swal_bytes_text');
                const swalStatus = document.getElementById('swal_status_text');
                const swalSpeed = document.getElementById('swal_speed_text');

                if (swalBar) {
                    swalBar.style.width = percent + '%';
                    swalBar.textContent = percent + '%';
                }
                if (swalPercent) {
                    swalPercent.textContent = percent + '%';
                }
                if (swalBytes) {
                    swalBytes.textContent = loadedStr + ' / ' + totalStr;
                }
                if (swalStatus) {
                    swalStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning me-1" role="status"></span> {{ __("Đang tải lên R2: ") }}' + percent + '%';
                }
                if (swalSpeed) {
                    swalSpeed.innerHTML = '<i class="ti ti-arrow-up text-warning me-1"></i>{{ __("Đang truyền dữ liệu...") }}';
                }
            }
        };

        xhr.onload = function () {
            btnUploadR2.disabled = false;
            btnCancelFile.disabled = false;
            btnUploadR2.innerHTML = '<i class="ti ti-cloud-upload me-1"></i>{{ __("Tải lên Cloudflare R2 ngay") }}';

            if (xhr.status === 200) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.status) {
                        isR2Uploaded = true;

                        // 1. Cập nhật Dropzone trên trang chính
                        r2ProgressBar.className = 'progress-bar bg-success';
                        r2ProgressBar.style.width = '100%';
                        r2ProgressBar.textContent = '100%';
                        r2UploadPercent.className = 'badge bg-success text-white fw-bold fs-12 px-2 py-1';
                        r2UploadPercent.textContent = 'Hoàn tất 100%';
                        r2UploadStatus.innerHTML = '<i class="ti ti-circle-check-filled text-success fs-3 me-1"></i> <strong>{{ __("Tải lên Cloudflare R2 thành công!") }}</strong>';
                        r2BytesStatus.textContent = formatBytes(file.size) + ' / ' + formatBytes(file.size);
                        r2SpeedStatus.innerHTML = '<i class="ti ti-check text-success me-1"></i>{{ __("Video đã sẵn sàng phát từ CDN") }}';

                        // 2. CẬP NHẬT TRỰC TIẾP TRONG DIALOG POPUP SANG MÀU XANH 100%
                        const swalBar = document.getElementById('swal_progress_bar');
                        const swalPercent = document.getElementById('swal_percent_badge');
                        const swalStatus = document.getElementById('swal_status_text');
                        const swalSpeed = document.getElementById('swal_speed_text');
                        const swalBytes = document.getElementById('swal_bytes_text');

                        if (swalBar) {
                            swalBar.className = 'progress-bar bg-success fw-bold text-white';
                            swalBar.style.width = '100%';
                            swalBar.textContent = '100%';
                        }
                        if (swalPercent) {
                            swalPercent.className = 'badge bg-success text-white fw-bold fs-12 px-2 py-1';
                            swalPercent.textContent = 'Hoàn tất 100%';
                        }
                        if (swalStatus) {
                            swalStatus.innerHTML = '<i class="ti ti-circle-check-filled text-success fs-3 me-1"></i> <strong>' +
                                (isAutoSubmit ? '{{ __("Tải lên R2 thành công! Đang lưu bài học...") }}' : '{{ __("Tải lên Cloudflare R2 thành công!") }}') + '</strong>';
                        }
                        if (swalBytes) {
                            swalBytes.textContent = formatBytes(file.size) + ' / ' + formatBytes(file.size);
                        }
                        if (swalSpeed) {
                            swalSpeed.innerHTML = '<i class="ti ti-check text-success me-1"></i>{{ __("File đã lưu trữ an toàn trên CDN") }}';
                        }

                        // Cập nhật Hidden inputs
                        r2ResultBox.classList.remove('d-none');
                        r2UrlInput.value = res.url;
                        if (finalVideoUrlInput) finalVideoUrlInput.value = res.url;
                        r2PathInput.value = res.path;
                        r2UrlText.textContent = res.url;
                        r2PreviewLink.href = res.url;

                        // Chuyển sang Preview Stream CDN trực tiếp
                        showR2VideoPreview(res.url, true, file.name);

                        if (isAutoSubmit) {
                            // Chờ 800ms để người dùng kịp nhìn thấy kết quả 100% hoàn tất rồi submit form
                            setTimeout(() => {
                                if (callback) callback(true, null);
                            }, 800);
                        } else {
                            // Đóng dialog sau 1.2s nếu là thao tác upload thủ công
                            setTimeout(() => {
                                if (typeof Swal !== 'undefined') Swal.close();
                            }, 1200);
                            if (callback) callback(true, null);
                        }
                    } else {
                        handleUploadError(res.message || '{{ __("Lỗi tải lên Cloudflare R2.") }}');
                    }
                } catch (e) {
                    handleUploadError('{{ __("Lỗi xử lý phản hồi từ máy chủ.") }}');
                }
            } else {
                handleUploadError('{{ __("Lỗi máy chủ: ") }}' + xhr.status);
            }
        };

        function handleUploadError(errMsg) {
            r2ProgressBar.className = 'progress-bar bg-danger';
            r2UploadStatus.textContent = errMsg;

            const swalBar = document.getElementById('swal_progress_bar');
            if (swalBar) {
                swalBar.className = 'progress-bar bg-danger fw-bold text-white';
            }
            const swalStatus = document.getElementById('swal_status_text');
            if (swalStatus) {
                swalStatus.innerHTML = '<i class="ti ti-alert-circle text-danger me-1"></i> <span class="text-danger">' + errMsg + '</span>';
            }

            if (typeof Swal !== 'undefined') {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Tải video lên R2 thất bại") }}',
                        text: errMsg,
                        confirmButtonText: '{{ __("Đóng") }}'
                    });
                }, 800);
            }

            if (callback) callback(false, errMsg);
        }

        xhr.onerror = function () {
            btnUploadR2.disabled = false;
            btnCancelFile.disabled = false;
            btnUploadR2.innerHTML = '<i class="ti ti-cloud-upload me-1"></i>{{ __("Tải lên Cloudflare R2 ngay") }}';
            handleUploadError('{{ __("Lỗi kết nối mạng khi tải lên R2.") }}');
        };

        xhr.send(formData);
    }

    btnUploadR2.addEventListener('click', function (e) {
        e.stopPropagation();
        executeR2Upload(null, true);
    });

    // BẢO VỆ TIẾN TRÌNH LƯU BÀI HỌC (Pre-submit Auto-Upload Guard)
    const parentForm = document.getElementById('lesson_form') || ytUrlInput.closest('form');
    if (parentForm) {
        let isSubmittingForm = false;

        parentForm.addEventListener('submit', function (e) {
            if (isSubmittingForm) return; // Đang submit sau khi auto-upload xong

            const checkedRadio = document.querySelector('.single-video-type-radio:checked');
            const vType = checkedRadio ? checkedRadio.value : 'youtube';

            if (vType === 'r2') {
                // Tình huống 1: Đã chọn file MP4 nhưng QUÊN bấm nút "Tải lên Cloudflare R2 ngay"
                if (r2FileInput.files && r2FileInput.files.length > 0 && !isR2Uploaded) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Tự động mở Dialog có Progress Bar và upload R2, hoàn tất thì submit form
                    executeR2Upload(function (success, errorMsg) {
                        if (success) {
                            isSubmittingForm = true;
                            if (typeof Swal !== 'undefined') Swal.close();
                            parentForm.submit();
                        }
                    }, true);
                    return false;
                }

                // Tình huống 2: Chọn R2 nhưng chưa có file video nào
                const currentR2Url = r2UrlInput ? r2UrlInput.value.trim() : '';
                const currentR2Path = r2PathInput ? r2PathInput.value.trim() : '';
                if (!currentR2Url && !currentR2Path) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('{{ __("Vui lòng chọn và tải file video lên Cloudflare R2 hoặc chuyển sang nguồn YouTube.") }}');
                    return false;
                }

                if (finalVideoUrlInput) finalVideoUrlInput.value = currentR2Url;
            } else {
                // Nguồn YouTube
                const currentYtUrl = ytUrlInput ? ytUrlInput.value.trim() : '';
                if (!currentYtUrl) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('{{ __("Vui lòng dán liên kết video YouTube hợp lệ.") }}');
                    return false;
                }
                if (finalVideoUrlInput) finalVideoUrlInput.value = currentYtUrl;
            }
        });
    }

    // Tự động nhận diện ban đầu khi load trang (đặc biệt khi mở trang edit)
    const initialVideoType = document.querySelector('.single-video-type-radio:checked')?.value || 'youtube';
    if (initialVideoType === 'youtube' && ytUrlInput.value.trim()) {
        handleYouTubeUrlChange(false, false);
    }
});
</script>
@endpush
