<style>
    /* Card nguồn video active */
    .video-source-radio[value="youtube"]:checked + .form-selectgroup-label {
        border-color: #ef4444 !important;
        background-color: #fff5f5 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
    }
    .video-source-radio[value="r2"]:checked + .form-selectgroup-label {
        border-color: #ea580c !important;
        background-color: #fff7ed !important;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.22) !important;
    }

    /* Modern YouTube Link Input Box */
    .yt-link-input-wrapper {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 6px 8px 6px 14px;
        transition: all 0.25s ease;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        min-height: 58px;
    }
    .yt-link-input-wrapper:focus-within {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12), 0 4px 16px rgba(239, 68, 68, 0.08) !important;
    }
    .yt-link-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .yt-link-icon-badge i {
        font-size: 26px;
        color: #dc2626;
    }
    .yt-link-input-field {
        flex: 1;
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        padding: 10px 4px !important;
        min-width: 0;
        box-shadow: none !important;
    }
    .yt-link-input-field::placeholder {
        color: #94a3b8 !important;
        font-size: 16px !important;
        font-weight: 400 !important;
    }
    .yt-btn-clear {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .yt-btn-clear:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .btn-yt-fetch {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 12px !important;
        height: 48px !important;
        padding: 0 24px !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3) !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        cursor: pointer !important;
    }
    .btn-yt-fetch:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4) !important;
    }
    .btn-yt-fetch i {
        font-size: 20px !important;
    }

    /* R2 Dropzone & Nút bấm - TUYỆT ĐỐI KHÔNG MÀU TÍM */
    .r2-dropzone-box {
        border: 2.5px dashed #f97316 !important;
        background: linear-gradient(180deg, #fffbf5 0%, #fff7ed 100%) !important;
        border-radius: 20px !important;
        padding: 44px 24px !important;
        transition: all 0.25s ease !important;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.06) !important;
    }
    .r2-dropzone-box:hover, .r2-dropzone-box.dragover {
        border-color: #ea580c !important;
        background: #fff4e5 !important;
        box-shadow: 0 8px 24px rgba(234, 88, 12, 0.15) !important;
    }
    .r2-cloud-icon-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(249, 115, 22, 0.22);
        margin-bottom: 16px;
    }
    .r2-cloud-icon-circle i {
        font-size: 42px;
        color: #ea580c;
    }
    .r2-dropzone-title {
        font-size: 21px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: -0.3px;
    }
    .r2-limit-badge {
        background: #ffedd5 !important;
        color: #c2410c !important;
        border: 1px solid #fdba74 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        padding: 5px 12px !important;
    }
    .btn-r2-upload {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 14px !important;
        padding: 14px 38px !important;
        font-size: 17px !important;
        font-weight: 700 !important;
        box-shadow: 0 6px 18px rgba(234, 88, 12, 0.35) !important;
        transition: all 0.25s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px !important;
        cursor: pointer !important;
    }
    .btn-r2-upload:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
        color: #ffffff !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 24px rgba(234, 88, 12, 0.45) !important;
    }
    .r2-file-icon-badge {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .r2-file-icon-badge i {
        font-size: 30px;
        color: #16a34a;
    }

    .btn-cyan-action {
        border: 1.5px solid #0284c7 !important;
        color: #0284c7 !important;
        background: #f0f9ff !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    .btn-cyan-action:hover {
        background: #0284c7 !important;
        color: #ffffff !important;
    }

    /* Labels lớn, đậm, nổi bật theo yêu cầu */
    .form-label-lg-custom {
        font-size: 18.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        margin-bottom: 8px !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.25px;
    }
    .form-label-lg-custom .required-star {
        color: #ef4444;
        font-size: 19px;
        font-weight: 800;
        margin-left: 2px;
    }

    /* Input & Select cao cấp, bóng bẩy */
    .input-custom-lg {
        height: 52px !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 14px !important;
        padding: 10px 18px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
        transition: all 0.2s ease !important;
    }
    .input-custom-lg:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
        background-color: #ffffff !important;
    }
    .select-custom-lg {
        height: 52px !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 14px !important;
        padding: 10px 18px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .select-custom-lg:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    }

    /* Duration Modern Dashboard Widget */
    .duration-widget-card {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 22px;
        transition: all 0.25s ease;
    }
    .duration-widget-card:hover {
        border-color: #cbd5e1;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }
    .duration-digital-box {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 10px 18px;
        gap: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }
    .duration-clock-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #e0f2fe;
        color: #0284c7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .duration-digits {
        font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.5px;
        line-height: 1.1;
    }
    .duration-input-box {
        width: 76px;
        height: 46px;
        text-align: center;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.2s;
    }
    .duration-input-box:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        outline: none;
    }
</style>

<div class="col-12 col-lg-8">
    {{-- Card 1: Nguồn Video (YouTube hoặc Cloudflare R2) & Xem trước --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar avatar-md rounded-circle" id="header_source_icon" style="{{ $instance->isR2() ? 'background: #ffedd5; color: #ea580c;' : 'background: #fee2e2; color: #dc2626;' }} width: 48px; height: 48px;">
                    <i class="ti {{ $instance->isR2() ? 'ti-cloud-upload' : 'ti-brand-youtube' }} fs-2"></i>
                </span>
                <div>
                    <h4 class="mb-1 fw-bold text-dark fs-19">{{ __('Nguồn Video & Xem trước') }}</h4>
                    <span class="text-secondary fs-15" id="header_source_desc">
                        {{ $instance->isR2() ? __('Video lưu trữ bảo mật trên Cloudflare R2 (tối đa 200MB)') : __('Hệ thống tự động phát hiện YouTube ID và tính toán thời lượng video') }}
                    </span>
                </div>
            </div>
            <span id="source_status_pill" class="badge {{ $instance->isR2() ? 'bg-orange text-white' : 'bg-success' }} fs-14 px-3 py-2 fw-semibold">
                @if($instance->isR2())
                    <i class="ti ti-cloud"></i> {{ __('Cloudflare R2') }}
                @else
                    <i class="ti ti-check"></i> {{ __('Đã nhận diện YouTube') }}
                @endif
            </span>
        </div>

        <div class="card-body p-4">
            {{-- Bộ chọn nguồn Video: YouTube vs Cloudflare R2 --}}
            <label class="form-label-lg-custom mb-3">
                <i class="ti ti-device-tv text-danger fs-20"></i>
                <span>{{ __('Chọn hình thức cung cấp Video') }}</span>
                <span class="required-star">*</span>
            </label>
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-12">
                    <label class="form-selectgroup-item w-100 cursor-pointer" onclick="window.switchVideoSource('youtube')">
                        <input type="radio" name="video_type" value="youtube" class="form-selectgroup-input video-source-radio" {{ old('video_type', $instance->video_type?->value ?? 'youtube') === 'youtube' ? 'checked' : '' }} onchange="window.switchVideoSource('youtube')">
                        <div id="card_source_youtube" class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all cursor-pointer" style="min-height: 84px;">
                            <span class="avatar avatar-md bg-danger-lt rounded-circle me-3 flex-shrink-0" style="width: 52px; height: 52px;">
                                <i class="ti ti-brand-youtube fs-1 text-danger"></i>
                            </span>
                            <div class="flex-fill">
                                <div class="fw-bold text-dark d-flex align-items-center justify-content-between fs-18 mb-1">
                                    <span>{{ __('Nguồn YouTube') }}</span>
                                    <span class="badge bg-danger text-white fs-12 px-2 py-1 fw-bold">Link</span>
                                </div>
                                <div class="text-secondary fs-14 lh-sm">{{ __('Nhập link chia sẻ, tự động lấy thumbnail & thời lượng.') }}</div>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="col-sm-6 col-12">
                    <label class="form-selectgroup-item w-100 cursor-pointer" onclick="window.switchVideoSource('r2')">
                        <input type="radio" name="video_type" value="r2" class="form-selectgroup-input video-source-radio" {{ old('video_type', $instance->video_type?->value ?? 'youtube') === 'r2' ? 'checked' : '' }} onchange="window.switchVideoSource('r2')">
                        <div id="card_source_r2" class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all cursor-pointer" style="min-height: 84px;">
                            <span class="avatar avatar-md bg-orange-lt rounded-circle me-3 flex-shrink-0" style="width: 52px; height: 52px;">
                                <i class="ti ti-cloud-upload fs-1 text-orange"></i>
                            </span>
                            <div class="flex-fill">
                                <div class="fw-bold text-dark d-flex align-items-center justify-content-between fs-18 mb-1">
                                    <span>{{ __('Cloudflare R2') }}</span>
                                    <span class="badge bg-orange text-white fs-12 px-2 py-1 fw-bold">Tối đa 200MB</span>
                                </div>
                                <div class="text-secondary fs-14 lh-sm">{{ __('Upload file trực tiếp lên Cloud, phát riêng tư bảo mật.') }}</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- 1. SECTION YOUTUBE --}}
            <div id="section_youtube_wrapper" style="{{ old('video_type', $instance->video_type?->value ?? 'youtube') === 'r2' ? 'display: none;' : '' }}">
                <div class="mb-3">
                    <label class="form-label-lg-custom mb-2" for="youtube_video_url">
                        <i class="ti ti-brand-youtube text-danger fs-20"></i>
                        <span>{{ __('Đường dẫn Video YouTube') }}</span>
                        <span class="required-star">*</span>
                    </label>
                    <div class="yt-link-input-wrapper">
                        <div class="yt-link-icon-badge">
                            <i class="ti ti-brand-youtube"></i>
                        </div>
                        <input type="text"
                               name="video_url"
                               id="youtube_video_url"
                               class="yt-link-input-field @error('video_url') is-invalid @enderror"
                               placeholder="{{ __('Dán link YouTube tại đây (VD: https://www.youtube.com/watch?v=... hoặc https://youtu.be/...)') }}"
                               value="{{ old('video_url', $instance->isYouTube() ? $instance->video_url : '') }}"
                               autocomplete="off"
                               inputmode="url"
                               aria-describedby="youtube_url_help youtube_url_error" />
                        <button class="yt-btn-clear" type="button" id="btn_clear_yt" title="{{ __('Xóa link') }}">
                            <i class="ti ti-x fs-18"></i>
                        </button>
                        <button class="btn-yt-fetch" type="button" id="btn_fetch_yt" title="{{ __('Nhận diện lại') }}">
                            <i class="ti ti-refresh"></i>
                            <span>{{ __('Cập nhật thông tin') }}</span>
                        </button>
                    </div>
                    <div id="youtube_url_error" class="invalid-feedback fs-13">@error('video_url'){{ $message }}@else{{ __('Vui lòng nhập một liên kết YouTube hợp lệ.') }}@enderror</div>
                    
                    {{-- Gợi ý định dạng trực quan --}}
                    <div id="youtube_url_help" class="d-flex flex-wrap align-items-center gap-2 mt-2 pt-1 text-secondary fs-14">
                        <span class="fw-semibold text-dark d-flex align-items-center gap-1">
                            <i class="ti ti-info-circle text-danger fs-18"></i>
                            {{ __('Hỗ trợ các định dạng liên kết:') }}
                        </span>
                        <span class="badge bg-light text-dark border fs-13 px-2 py-1">watch?v=...</span>
                        <span class="badge bg-light text-dark border fs-13 px-2 py-1">youtu.be/...</span>
                        <span class="badge bg-light text-dark border fs-13 px-2 py-1">shorts/...</span>
                        <span class="badge bg-light text-dark border fs-13 px-2 py-1">embed/...</span>
                    </div>
                </div>

                {{-- Live YouTube Preview Box --}}
                <div id="yt_preview_container" class="rounded-3 border overflow-hidden mt-3 shadow-sm" style="background: #0f172a; {{ $instance->isYouTube() && $instance->youtube_id ? '' : 'display: none;' }}">
                    <div class="position-relative" style="padding-top: 56.25%; background: #000;">
                        <div id="yt_player_placeholder" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-3 text-center" style="display: none;">
                            <div class="spinner-border text-danger mb-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="fs-15">{{ __('Đang tải trình phát video...') }}</span>
                        </div>
                        <iframe id="yt_iframe"
                                class="position-absolute top-0 start-0 w-100 h-100 border-0"
                                src="{{ $instance->youtube_id ? 'https://www.youtube.com/embed/' . $instance->youtube_id . '?enablejsapi=1' : '' }}"
                                title="YouTube video player"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>

                    <div class="p-3 bg-white border-top">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-danger text-white fs-13 px-2 py-1">
                                    <i class="ti ti-brand-youtube"></i> YouTube
                                </span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1">
                                    ID: <code class="text-danger fw-bold fs-13" id="yt_meta_id">{{ $instance->youtube_id ?? '—' }}</code>
                                </span>
                                <span class="badge bg-success-lt text-success border border-success fs-14 px-3 py-1 fw-bold d-flex align-items-center gap-1" id="yt_meta_duration_pill">
                                    <i class="ti ti-clock"></i>
                                    <span id="yt_meta_duration_text">⏱️ {{ $instance->formatted_duration }}</span>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-cyan-action d-flex align-items-center gap-1 fs-13 px-3 py-1" id="btn_apply_title" style="display: none;">
                                    <i class="ti ti-writing"></i> {{ __('Áp dụng tiêu đề từ YouTube') }}
                                </button>
                                <a href="{{ $instance->isYouTube() ? $instance->video_url : '#' }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 fs-13 px-3 py-1" id="btn_watch_external">
                                    <i class="ti ti-external-link"></i> {{ __('Mở YouTube') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. SECTION CLOUDFLARE R2 --}}
            <div id="section_r2_wrapper" style="{{ old('video_type', $instance->video_type?->value ?? 'youtube') === 'r2' ? '' : 'display: none;' }}">
                @if($instance->isR2() && $instance->video_url)
                    {{-- Banner thông tin video R2 hiện tại --}}
                    <div class="alert alert-info d-flex align-items-center justify-content-between p-3 mb-3 border-0 bg-blue-lt rounded-3 shadow-sm">
                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                            <i class="ti ti-cloud-check fs-2 text-primary"></i>
                            <div class="text-truncate">
                                <strong class="text-dark d-block text-truncate fs-16">{{ __('Video Cloudflare R2 hiện tại') }}</strong>
                                <small class="text-muted text-truncate d-block fs-14">{{ $instance->video_path ?? basename($instance->video_url) }}</small>
                            </div>
                        </div>
                        <a href="{{ $instance->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-cyan-action text-nowrap d-flex align-items-center gap-1 fs-13 px-3 py-1">
                            <i class="ti ti-external-link"></i> {{ __('Mở link gốc') }}
                        </a>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold fs-17 mb-2 text-dark" for="r2_video_file">
                        {{ __('Tải lên video mới (thay thế video cũ)') }}:
                        @if(!$instance->isR2())
                            <span class="text-danger">*</span>
                        @endif
                    </label>

                    {{-- Dropzone / File Picker UI --}}
                    <div id="r2_dropzone" class="r2-dropzone-box text-center cursor-pointer shadow-sm">
                        <input type="file"
                               name="video_file"
                               id="r2_video_file"
                               class="d-none"
                               accept="video/mp4,video/quicktime,video/webm,video/x-matroska,video/x-msvideo" />
                        
                        <div id="r2_dropzone_default">
                            <div class="r2-cloud-icon-circle">
                                <i class="ti ti-cloud-upload"></i>
                            </div>
                            <h3 class="fw-bold mb-2 text-dark r2-dropzone-title">
                                {{ __('Kéo & thả video mới vào đây hoặc nhấn để chọn tệp') }}
                            </h3>
                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-4">
                                <span class="text-secondary fs-15 fw-medium">{{ __('Hỗ trợ các định dạng:') }}</span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1 fw-bold">MP4</span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1 fw-bold">MOV</span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1 fw-bold">WEBM</span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1 fw-bold">MKV</span>
                                <span class="badge bg-light text-dark border fs-13 px-2 py-1 fw-bold">AVI</span>
                                <span class="badge r2-limit-badge">
                                    <i class="ti ti-database me-1"></i>{{ __('Dung lượng tối đa: 200MB') }}
                                </span>
                                @if($instance->isR2())
                                    <div class="w-100 text-muted fs-14 mt-1">
                                        <i class="ti ti-info-circle me-1"></i>{{ __('(Để trống nếu bạn muốn giữ nguyên video R2 hiện tại)') }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-r2-upload" onclick="document.getElementById('r2_video_file').click()">
                                    <i class="ti ti-folder-up fs-20"></i>
                                    <span>{{ __('Chọn video từ máy tính') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- File Selected Status Card --}}
                        <div id="r2_file_info_card" class="bg-white border rounded-3 p-3 text-start shadow-sm mt-2" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="r2-file-icon-badge">
                                        <i class="ti ti-video"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark text-truncate fs-17" id="r2_filename" style="max-width: 420px;">video.mp4</strong>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-blue text-white fs-13 px-2 py-1 fw-bold" id="r2_filesize">0 MB</span>
                                            <span class="badge bg-success-lt text-success border border-success fs-13 px-2 py-1 fw-bold" id="r2_detected_duration">00:00</span>
                                            <span class="badge bg-warning-lt text-warning fs-13 px-2 py-1 fw-bold">{{ __('Sẽ thay thế file cũ khi lưu') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary fs-14 px-3 py-2 fw-semibold" onclick="document.getElementById('r2_video_file').click()">
                                        <i class="ti ti-refresh me-1"></i>{{ __('Đổi tệp khác') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-danger fs-14 px-3 py-2 fw-semibold" id="btn_remove_r2_file" title="{{ __('Bỏ chọn tệp này') }}">
                                        <i class="ti ti-trash me-1"></i>{{ __('Xóa') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('video_file')
                        <div class="text-danger fs-14 mt-2 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Live HTML5 Video Preview Box (Existing R2 Video or Newly Dropped Video) --}}
                <div id="r2_preview_container" class="rounded-3 border overflow-hidden mt-3 shadow-sm" style="{{ ($instance->isR2() && $instance->video_url) ? '' : 'display: none;' }} background: #0f172a;">
                    <div class="position-relative" style="padding-top: 56.25%; background: #000;">
                        <video id="r2_video_player"
                               controls
                               preload="metadata"
                               class="position-absolute top-0 start-0 w-100 h-100"
                               src="{{ $instance->isR2() ? $instance->video_url : '' }}"
                               style="object-fit: contain; background: #000;">
                        </video>
                    </div>

                    <div class="p-3 bg-white border-top">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-orange text-white fs-13 px-3 py-1 fw-bold">
                                    <i class="ti ti-cloud"></i> Cloudflare R2
                                </span>
                                <span class="badge bg-success-lt text-success border border-success fs-13 px-3 py-1 fw-bold d-flex align-items-center gap-1" id="r2_meta_duration_pill">
                                    <i class="ti ti-clock"></i>
                                    <span id="r2_meta_duration_text">⏱️ {{ $instance->formatted_duration }}</span>
                                </span>
                                <span class="badge bg-light text-dark border fs-12 px-2 py-1" id="r2_meta_resolution">
                                    <i class="ti ti-aspect-ratio"></i> —
                                </span>
                            </div>
                            <div>
                                <span class="text-success fs-13 fw-semibold"><i class="ti ti-sparkles text-success me-1"></i>{{ __('Tự động đo thời lượng từ file video') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Thông tin chi tiết video --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
            <span class="avatar avatar-md rounded-circle" style="background: #e0f2fe; color: #0284c7; width: 48px; height: 48px;">
                <i class="ti ti-file-description fs-2"></i>
            </span>
            <div>
                <h4 class="mb-1 fw-bold text-dark fs-19">{{ __('Thông tin chi tiết video') }}</h4>
                <span class="text-secondary fs-15">{{ __('Tiêu đề, phân loại danh mục theo độ tuổi và thời lượng video') }}</span>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- Tiêu đề --}}
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label-lg-custom mb-0" for="video_title">
                            <i class="ti ti-heading text-primary fs-20"></i>
                            <span>{{ __('Tiêu đề video') }}</span>
                            <span class="required-star">*</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-cyan-action py-1 px-3 fs-13 align-items-center gap-1 shadow-sm rounded-pill" id="btn_apply_title_badge" style="display: none;" title="{{ __('Nhấn để điền tiêu đề từ YouTube') }}">
                            <i class="ti ti-sparkles text-warning"></i> <span id="btn_apply_title_badge_text">{{ __('Áp dụng tiêu đề YouTube') }}</span>
                        </button>
                    </div>
                    <input type="text"
                           name="title"
                           id="video_title"
                           class="form-control input-custom-lg @error('title') is-invalid @enderror"
                           placeholder="Ví dụ: Bắt bóng rèn luyện phản xạ cấp độ 1..."
                           value="{{ old('title', $instance->title) }}"
                           maxlength="300"
                           aria-describedby="video_title_counter"
                           required />
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        @error('title')
                            <div class="text-danger fs-14 fw-semibold d-flex align-items-center gap-1">
                                <i class="ti ti-alert-circle"></i> {{ $message }}
                            </div>
                        @else
                            <div class="text-secondary fs-13 d-flex align-items-center gap-1">
                                <i class="ti ti-info-circle text-primary"></i>
                                <span>{{ __('Tiêu đề súc tích, hấp dẫn giúp phụ huynh dễ dàng nhận biết bài học.') }}</span>
                            </div>
                        @enderror
                        <span id="video_title_counter" class="badge bg-light text-secondary border fs-13 px-2 py-1 fw-bold">0/300 ký tự</span>
                    </div>
                </div>

                {{-- Danh mục theo nhóm tuổi --}}
                <div class="col-12">
                    <label class="form-label-lg-custom" for="video_category_id">
                        <i class="ti ti-category text-primary fs-20"></i>
                        <span>{{ __('Danh mục video (theo nhóm tuổi)') }}</span>
                        <span class="required-star">*</span>
                    </label>
                    <select name="video_category_id" id="video_category_id" class="form-select select-custom-lg @error('video_category_id') is-invalid @enderror" required>
                        <option value="">-- {{ __('Chọn danh mục phù hợp') }} --</option>
                        @isset($categoriesByAge)
                            @foreach ($categoriesByAge as $ageGroupName => $cats)
                                <optgroup label="👶 {{ __('Nhóm tuổi') }}: {{ $ageGroupName }}">
                                    @foreach ($cats as $cat)
                                        <option value="{{ $cat->id }}" {{ old('video_category_id', $instance->video_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @else
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('video_category_id', $instance->video_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->ageGroup?->name ?? 'Tất cả' }})
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('video_category_id')
                        <div class="text-danger fs-14 fw-semibold mt-1 d-flex align-items-center gap-1">
                            <i class="ti ti-alert-circle"></i> {{ $message }}
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-1 text-secondary mt-2 fs-14">
                            <i class="ti ti-info-circle text-primary"></i>
                            <span>{{ __('Danh mục được phân loại sẵn theo độ tuổi để phụ huynh nhanh chóng tìm kiếm.') }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Thời lượng video (Thiết kế hiện đại, đầy đủ, tinh gọn) --}}
                <div class="col-12">
                    <label class="form-label-lg-custom mb-2">
                        <i class="ti ti-clock text-primary fs-20"></i>
                        <span>{{ __('Thời lượng video') }}</span>
                    </label>

                    <div class="duration-widget-card">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            {{-- Khối hiển thị kết quả chính --}}
                            <div class="d-flex align-items-center gap-3">
                                <div class="duration-digital-box">
                                    <div class="duration-clock-icon">
                                        <i class="ti ti-clock-play"></i>
                                    </div>
                                    <div>
                                        <span class="text-secondary fs-13 d-block fw-semibold">{{ __('Thời lượng phát:') }}</span>
                                        <div class="d-flex align-items-baseline gap-2">
                                            <span id="duration_formatted_badge" class="duration-digits">
                                                {{ $instance->formatted_duration ?? '00:00' }}
                                            </span>
                                            <span id="duration_total_seconds_badge" class="badge bg-light text-secondary border fs-12 px-2 py-1 fw-bold">
                                                {{ $instance->duration_seconds ?? 0 }}s
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none d-md-block">
                                    <span class="badge bg-success-lt text-success border border-success fs-13 px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="ti ti-sparkles"></i> {{ __('Tự động đo thời lượng') }}
                                    </span>
                                    <div class="text-secondary fs-13 mt-1">
                                        {{ __('Tự động đo từ YouTube hoặc Cloudflare R2 khi chọn video.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Khối điều chỉnh thủ công (Phút : Giây) --}}
                            <div class="d-flex align-items-center gap-2 p-2 bg-white rounded-3 border shadow-sm">
                                <div class="text-end me-2 d-none d-sm-block">
                                    <span class="d-block fs-13 fw-bold text-dark">{{ __('Chỉnh thủ công') }}</span>
                                    <span class="d-block fs-12 text-muted">{{ __('Nếu cần sửa') }}</span>
                                </div>
                                <div class="text-center">
                                    <input type="number" id="calc_minutes" class="duration-input-box" min="0" placeholder="0" />
                                    <span class="d-block text-secondary fs-12 mt-1 fw-semibold">{{ __('phút') }}</span>
                                </div>
                                <span class="fs-20 fw-bold text-muted pb-3">:</span>
                                <div class="text-center">
                                    <input type="number" id="calc_seconds" class="duration-input-box" min="0" max="59" placeholder="0" />
                                    <span class="d-block text-secondary fs-12 mt-1 fw-semibold">{{ __('giây') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden input duration_seconds gửi về backend --}}
                        <input type="number"
                               name="duration_seconds"
                               id="duration_seconds"
                               class="d-none"
                               min="0"
                               value="{{ old('duration_seconds', $instance->duration_seconds ?? 0) }}" />
                    </div>
                </div>

                {{-- Hidden input sort_order (loại bỏ UI theo yêu cầu của user, giữ nguyên dữ liệu ngầm định) --}}
                <input type="hidden"
                       name="sort_order"
                       id="sort_order"
                       value="{{ old('sort_order', $instance->sort_order ?? 0) }}" />

                {{-- Mô tả nội dung --}}
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label-lg-custom mb-0">
                            <i class="ti ti-notes text-primary fs-20"></i>
                            <span>{{ __('Mô tả nội dung video') }}</span>
                        </label>
                        <span class="text-secondary fs-13">{{ __('Trình soạn thảo văn bản phong phú') }}</span>
                    </div>
                    <div class="editor-wrapper border rounded-3 overflow-hidden bg-white shadow-sm" style="border: 2px solid #e2e8f0 !important;">
                        <textarea name="description" class="ckeditor visually-hidden">{{ old('description', $instance->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden container for YouTube IFrame API probe --}}
<div id="yt_hidden_probe" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;"></div>

@push('custom-js')
<script>
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    const initialOriginalR2Url = @json($instance->isR2() ? $instance->video_url : '');
    let ytProbePlayer = null;
    let detectedYtTitle = '';
    let activeYtId = '';
    let urlInputTimer = null;
    let probeRetryTimer = null;
    let probeTimeoutTimer = null;
    let metadataController = null;

    function stopYouTubeProbe() {
        clearTimeout(probeRetryTimer);
        clearTimeout(probeTimeoutTimer);
        probeRetryTimer = null;
        probeTimeoutTimer = null;
        if (ytProbePlayer) {
            try { ytProbePlayer.destroy(); } catch (e) {}
            ytProbePlayer = null;
        }
        const probe = document.getElementById('yt_hidden_probe');
        if (probe) probe.replaceChildren();
    }

    function parseYouTubeId(url) {
        if (!url) return null;
        let m;
        if ((m = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/))) return m[1];
        if ((m = url.match(/[?&]v=([a-zA-Z0-9_-]+)/))) return m[1];
        if ((m = url.match(/embed\/([a-zA-Z0-9_-]+)/))) return m[1];
        if ((m = url.match(/shorts\/([a-zA-Z0-9_-]+)/))) return m[1];
        return null;
    }

    function formatSeconds(totalSec) {
        totalSec = parseInt(totalSec) || 0;
        if (totalSec <= 0) return '00:00';
        const h = Math.floor(totalSec / 3600);
        const m = Math.floor((totalSec % 3600) / 60);
        const s = totalSec % 60;
        const pad = (n) => (n < 10 ? '0' + n : n);
        if (h > 0) {
            return `${pad(h)}:${pad(m)}:${pad(s)} (${h}h ${m}m ${s}s)`;
        }
        return `${pad(m)}:${pad(s)} (${m} phút ${s} giây)`;
    }

    function updateDurationInputs(totalSec) {
        totalSec = parseInt(totalSec) || 0;
        const durInput = document.getElementById('duration_seconds');
        const calcM = document.getElementById('calc_minutes');
        const calcS = document.getElementById('calc_seconds');
        if (durInput) durInput.value = totalSec;
        if (calcM) calcM.value = Math.floor(totalSec / 60);
        if (calcS) calcS.value = totalSec % 60;

        const formatted = formatSeconds(totalSec);
        const durBadge = document.getElementById('duration_formatted_badge');
        if (durBadge) durBadge.textContent = formatted.split(' ')[0];
        const durSecBadge = document.getElementById('duration_total_seconds_badge');
        if (durSecBadge) durSecBadge.textContent = totalSec + 's';
        const ytMetaDur = document.getElementById('yt_meta_duration_text');
        if (ytMetaDur) ytMetaDur.textContent = '⏱️ ' + formatted;
        const r2MetaDur = document.getElementById('r2_meta_duration_text');
        if (r2MetaDur) r2MetaDur.textContent = '⏱️ ' + formatted;
    }

    function syncFromCalcInputs() {
        const calcM = document.getElementById('calc_minutes');
        const calcS = document.getElementById('calc_seconds');
        const m = parseInt(calcM ? calcM.value : 0) || 0;
        const s = parseInt(calcS ? calcS.value : 0) || 0;
        const total = m * 60 + s;
        const durInput = document.getElementById('duration_seconds');
        if (durInput) durInput.value = total;

        const formatted = formatSeconds(total);
        const durBadge = document.getElementById('duration_formatted_badge');
        if (durBadge) durBadge.textContent = formatted.split(' ')[0];
        const durSecBadge = document.getElementById('duration_total_seconds_badge');
        if (durSecBadge) durSecBadge.textContent = total + 's';
        const ytMetaDur = document.getElementById('yt_meta_duration_text');
        if (ytMetaDur) ytMetaDur.textContent = '⏱️ ' + formatted;
        const r2MetaDur = document.getElementById('r2_meta_duration_text');
        if (r2MetaDur) r2MetaDur.textContent = '⏱️ ' + formatted;
    }

    function stepUpSortOrder() {
        const inp = document.getElementById('sort_order');
        if (inp) inp.value = (parseInt(inp.value) || 0) + 1;
    }

    function stepDownSortOrder() {
        const inp = document.getElementById('sort_order');
        if (inp) inp.value = Math.max(0, (parseInt(inp.value) || 0) - 1);
    }

    // Global switcher
    window.switchVideoSource = function(type) {
        const sectionYt = document.getElementById('section_youtube_wrapper');
        const sectionR2 = document.getElementById('section_r2_wrapper');
        const ytThumbWrapper = document.getElementById('yt_thumbnail_wrapper');
        const headerSourceIcon = document.getElementById('header_source_icon');
        const headerSourceDesc = document.getElementById('header_source_desc');
        const sourceStatusPill = document.getElementById('source_status_pill');
        const inputUrl = document.getElementById('youtube_video_url');
        const iframe = document.getElementById('yt_iframe');
        const r2VideoPlayer = document.getElementById('r2_video_player');
        const radioYt = document.querySelector('input.video-source-radio[value="youtube"]');
        const radioR2 = document.querySelector('input.video-source-radio[value="r2"]');

        if (type === 'r2') {
            if (radioR2) radioR2.checked = true;

            if (sectionYt) sectionYt.style.display = 'none';
            if (sectionR2) sectionR2.style.display = 'block';
            if (ytThumbWrapper) ytThumbWrapper.style.display = 'none';
            if (inputUrl) inputUrl.removeAttribute('required');

            if (headerSourceIcon) {
                headerSourceIcon.className = 'avatar avatar-md bg-orange-lt rounded-circle';
                headerSourceIcon.innerHTML = '<i class="ti ti-cloud-upload fs-2 text-orange"></i>';
            }
            if (headerSourceDesc) {
                headerSourceDesc.textContent = 'Video lưu trữ bảo mật trên Cloudflare R2 (tối đa 200MB)';
            }
            if (sourceStatusPill) {
                sourceStatusPill.className = 'badge bg-orange text-white fs-13 px-3 py-2 fw-semibold';
                sourceStatusPill.innerHTML = '<i class="ti ti-cloud"></i> Cloudflare R2';
            }

            if (iframe) {
                try { iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*'); } catch (e) {}
            }
        } else {
            if (radioYt) radioYt.checked = true;

            if (sectionYt) sectionYt.style.display = 'block';
            if (sectionR2) sectionR2.style.display = 'none';
            if (ytThumbWrapper) ytThumbWrapper.style.display = 'block';
            if (inputUrl) inputUrl.setAttribute('required', 'required');

            if (headerSourceIcon) {
                headerSourceIcon.className = 'avatar avatar-md bg-danger-lt rounded-circle';
                headerSourceIcon.innerHTML = '<i class="ti ti-brand-youtube fs-2 text-danger"></i>';
            }
            if (headerSourceDesc) {
                headerSourceDesc.textContent = 'Hệ thống tự động phát hiện YouTube ID và tính toán thời lượng video';
            }
            if (sourceStatusPill) {
                if (inputUrl && inputUrl.value.trim() && parseYouTubeId(inputUrl.value.trim())) {
                    sourceStatusPill.className = 'badge bg-success fs-13 px-3 py-2 fw-semibold';
                    sourceStatusPill.innerHTML = '<i class="ti ti-check"></i> Đã nhận diện YouTube';
                } else {
                    sourceStatusPill.className = 'badge bg-secondary-lt fs-13 px-3 py-2 fw-semibold';
                    sourceStatusPill.innerHTML = '<i class="ti ti-link"></i> Chờ nhập link';
                }
            }

            if (r2VideoPlayer) {
                r2VideoPlayer.pause();
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const r2FileInput = document.getElementById('r2_video_file');
        const r2Dropzone = document.getElementById('r2_dropzone');
        const r2DropzoneDefault = document.getElementById('r2_dropzone_default');
        const r2FileInfoCard = document.getElementById('r2_file_info_card');
        const r2Filename = document.getElementById('r2_filename');
        const r2Filesize = document.getElementById('r2_filesize');
        const r2DetectedDuration = document.getElementById('r2_detected_duration');
        const r2PreviewContainer = document.getElementById('r2_preview_container');
        const r2VideoPlayer = document.getElementById('r2_video_player');
        const r2MetaResolution = document.getElementById('r2_meta_resolution');
        const btnRemoveR2File = document.getElementById('btn_remove_r2_file');

        const inputUrl = document.getElementById('youtube_video_url');
        const previewContainer = document.getElementById('yt_preview_container');
        const iframe = document.getElementById('yt_iframe');
        const placeholder = document.getElementById('yt_player_placeholder');
        const metaId = document.getElementById('yt_meta_id');
        const watchExternal = document.getElementById('btn_watch_external');
        const btnClear = document.getElementById('btn_clear_yt');
        const btnFetch = document.getElementById('btn_fetch_yt');
        const btnApplyTitle = document.getElementById('btn_apply_title');
        const btnApplyTitleBadge = document.getElementById('btn_apply_title_badge');
        const inputTitle = document.getElementById('video_title');
        const titleCounter = document.getElementById('video_title_counter');

        const calcM = document.getElementById('calc_minutes');
        const calcS = document.getElementById('calc_seconds');
        const durationSec = document.getElementById('duration_seconds');

        // Init duration
        if (durationSec && durationSec.value > 0) {
            updateDurationInputs(durationSec.value);
        }

        if (calcM) calcM.addEventListener('input', syncFromCalcInputs);
        if (calcS) calcS.addEventListener('input', syncFromCalcInputs);
        if (durationSec) {
            durationSec.addEventListener('input', function () {
                updateDurationInputs(this.value);
            });
        }

        // Initialize active source on load
        const checkedRadio = document.querySelector('.video-source-radio:checked');
        const initialSource = checkedRadio ? checkedRadio.value : 'youtube';
        window.switchVideoSource(initialSource);

        if (r2VideoPlayer && r2VideoPlayer.src && initialSource === 'r2') {
            r2VideoPlayer.onloadedmetadata = function () {
                if (r2MetaResolution && r2VideoPlayer.videoWidth) {
                    r2MetaResolution.textContent = `${r2VideoPlayer.videoWidth} x ${r2VideoPlayer.videoHeight}`;
                }
            };
        }

        function handleR2File(file) {
            if (!file) return;

            const maxBytes = 200 * 1024 * 1024;
            if (file.size > maxBytes) {
                alert(`Dung lượng video vượt quá 200MB (${(file.size / (1024 * 1024)).toFixed(1)} MB). Vui lòng chọn file dung lượng nhỏ hơn.`);
                if (r2FileInput) r2FileInput.value = '';
                return;
            }

            const sizeMb = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            if (r2Filename) r2Filename.textContent = file.name;
            if (r2Filesize) r2Filesize.textContent = sizeMb;

            if (r2DropzoneDefault) r2DropzoneDefault.style.display = 'none';
            if (r2FileInfoCard) r2FileInfoCard.style.display = 'block';

            const objectUrl = URL.createObjectURL(file);
            if (r2VideoPlayer) {
                r2VideoPlayer.src = objectUrl;
                if (r2PreviewContainer) r2PreviewContainer.style.display = 'block';

                r2VideoPlayer.onloadedmetadata = function () {
                    const dur = Math.round(r2VideoPlayer.duration);
                    if (dur > 0) {
                        updateDurationInputs(dur);
                        if (r2DetectedDuration) r2DetectedDuration.textContent = formatSeconds(dur).split(' ')[0];
                    }
                    if (r2MetaResolution && r2VideoPlayer.videoWidth) {
                        r2MetaResolution.textContent = `${r2VideoPlayer.videoWidth} x ${r2VideoPlayer.videoHeight}`;
                    }
                };
            }
        }

        if (r2FileInput) {
            r2FileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    handleR2File(this.files[0]);
                }
            });
        }

        if (r2Dropzone) {
            ['dragenter', 'dragover'].forEach(evt => {
                r2Dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    r2Dropzone.style.borderColor = '#ea580c';
                    r2Dropzone.style.background = '#fff4e5';
                });
            });

            ['dragleave', 'drop'].forEach(evt => {
                r2Dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    r2Dropzone.style.borderColor = '#fb923c';
                    r2Dropzone.style.background = 'linear-gradient(180deg, #fffaf5 0%, #fff7ed 100%)';
                });
            });

            r2Dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                    const droppedFile = e.dataTransfer.files[0];
                    if (r2FileInput) {
                        r2FileInput.files = e.dataTransfer.files;
                    }
                    handleR2File(droppedFile);
                }
            });
        }

        if (btnRemoveR2File) {
            btnRemoveR2File.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (r2FileInput) r2FileInput.value = '';
                if (r2FileInfoCard) r2FileInfoCard.style.display = 'none';
                if (r2DropzoneDefault) r2DropzoneDefault.style.display = 'block';

                if (initialOriginalR2Url) {
                    if (r2VideoPlayer) {
                        r2VideoPlayer.src = initialOriginalR2Url;
                        r2VideoPlayer.load();
                    }
                    if (r2PreviewContainer) r2PreviewContainer.style.display = 'block';
                } else {
                    if (r2PreviewContainer) r2PreviewContainer.style.display = 'none';
                    if (r2VideoPlayer) {
                        r2VideoPlayer.pause();
                        r2VideoPlayer.removeAttribute('src');
                        r2VideoPlayer.load();
                    }
                }
            });
        }

        function updateTitleCounter() {
            if (titleCounter && inputTitle) {
                titleCounter.textContent = `${inputTitle.value.length}/300 ký tự`;
            }
        }

        function highlightTitleField() {
            if (!inputTitle) return;
            inputTitle.classList.add('is-valid');
            inputTitle.style.transition = 'all 0.3s ease';
            inputTitle.style.backgroundColor = '#ecfdf5';
            setTimeout(() => {
                inputTitle.style.backgroundColor = '';
                inputTitle.classList.remove('is-valid');
            }, 1200);
        }

        if (inputTitle) {
            inputTitle.addEventListener('input', function () {
                this.dataset.autoYt = 'false';
                updateTitleCounter();
            });
            updateTitleCounter();
        }

        function getInternalApiUrl(videoUrl) {
            let basePath = '';
            const adminIdx = window.location.pathname.indexOf('/admin/');
            if (adminIdx !== -1) {
                basePath = window.location.pathname.substring(0, adminIdx);
            }
            return `${window.location.origin}${basePath}/admin/videos/fetch-youtube-info?url=${encodeURIComponent(videoUrl)}`;
        }

        function applyDetectedTitle(title, force = false) {
            if (!title) return;
            detectedYtTitle = title.trim();
            if (btnApplyTitle) {
                btnApplyTitle.style.display = 'inline-flex';
                btnApplyTitle.setAttribute('title', `Tiêu đề: ${detectedYtTitle}`);
            }
            if (btnApplyTitleBadge) {
                btnApplyTitleBadge.style.display = 'inline-flex';
                const badgeText = document.getElementById('btn_apply_title_badge_text');
                if (badgeText) {
                    const shortTitle = detectedYtTitle.length > 30 ? detectedYtTitle.substring(0, 27) + '...' : detectedYtTitle;
                    badgeText.textContent = `Áp dụng: "${shortTitle}"`;
                }
            }
            const currentVal = inputTitle ? inputTitle.value.trim() : '';
            if (!currentVal || force || (inputTitle && inputTitle.dataset.autoYt === 'true')) {
                if (inputTitle) {
                    inputTitle.value = detectedYtTitle;
                    inputTitle.dataset.autoYt = 'true';
                }
                updateTitleCounter();
                highlightTitleField();
            }
        }

        function handleUrlChange(manualTrigger = false) {
            if (!inputUrl) return;
            const raw = inputUrl.value.trim();
            if (btnClear) btnClear.style.display = raw ? 'block' : 'none';

            const ytId = parseYouTubeId(raw);
            if (!ytId) {
                activeYtId = '';
                stopYouTubeProbe();
                if (metadataController) metadataController.abort();
                if (iframe) iframe.removeAttribute('src');
                if (previewContainer) previewContainer.style.display = 'none';
                const sourceStatusPill = document.getElementById('source_status_pill');
                if (sourceStatusPill) {
                    sourceStatusPill.className = 'badge bg-secondary-lt fs-13 px-3 py-2 fw-semibold';
                    sourceStatusPill.innerHTML = '<i class="ti ti-link"></i> Chờ nhập link';
                }
                if (btnApplyTitle) btnApplyTitle.style.display = 'none';
                if (btnApplyTitleBadge) btnApplyTitleBadge.style.display = 'none';
                inputUrl.classList.toggle('is-invalid', manualTrigger && raw.length > 0);
                if (manualTrigger && raw.length > 0) inputUrl.focus();
                return;
            }

            inputUrl.classList.remove('is-invalid');
            if (metaId) metaId.textContent = ytId;
            if (watchExternal) watchExternal.href = `https://www.youtube.com/watch?v=${ytId}`;
            if (previewContainer) previewContainer.style.display = 'block';
            const sourceStatusPill = document.getElementById('source_status_pill');
            if (sourceStatusPill) {
                sourceStatusPill.className = 'badge bg-success fs-13 px-3 py-2 fw-semibold';
                sourceStatusPill.innerHTML = '<i class="ti ti-check"></i> Đã nhận diện YouTube';
            }

            const sideThumb = document.getElementById('yt_auto_thumb_img');
            if (sideThumb) {
                sideThumb.src = `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`;
            }

            if (placeholder) placeholder.style.display = 'none';
            if (iframe) {
                iframe.style.display = 'block';
                iframe.src = `https://www.youtube.com/embed/${ytId}?enablejsapi=1&origin=${window.location.origin}`;
            }

            if (activeYtId !== ytId || manualTrigger) {
                activeYtId = ytId;
                fetchVideoMetadata(ytId, raw, manualTrigger);
            }
        }

        function fetchVideoMetadata(ytId, rawUrl, manualTrigger = false) {
            const ytDurText = document.getElementById('yt_meta_duration_text');
            if (ytDurText) ytDurText.textContent = 'Đang tự động đo thời lượng...';

            stopYouTubeProbe();
            if (metadataController) metadataController.abort();
            metadataController = new AbortController();

            const targetUrl = rawUrl || (inputUrl ? inputUrl.value.trim() : '') || `https://www.youtube.com/watch?v=${ytId}`;
            const apiUrl = getInternalApiUrl(targetUrl);
            fetch(apiUrl, { signal: metadataController.signal })
                .then(r => {
                    if (!r.ok) throw new Error('API status: ' + r.status);
                    return r.json();
                })
                .then(data => {
                    if (data && data.title) {
                        applyDetectedTitle(data.title, manualTrigger);
                    }
                    if (data && data.duration_seconds && data.duration_seconds > 0) {
                        updateDurationInputs(data.duration_seconds);
                        stopYouTubeProbe();
                    }
                })
                .catch(() => {
                    fetch(`https://noembed.com/embed?url=https://www.youtube.com/watch?v=${ytId}`, {
                        signal: metadataController.signal
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.title) {
                                applyDetectedTitle(data.title, manualTrigger);
                            }
                        })
                        .catch(() => {});
                });

            let retryCount = 0;
            function measureDurationWithYT() {
                if (!window.YT || !window.YT.Player) {
                    retryCount += 1;
                    if (retryCount < 20 && activeYtId === ytId) {
                        probeRetryTimer = setTimeout(measureDurationWithYT, 300);
                    } else if (activeYtId === ytId && ytDurText) {
                        ytDurText.textContent = 'Không thể tự đo thời lượng';
                    }
                    return;
                }

                if (ytProbePlayer) {
                    try { ytProbePlayer.destroy(); } catch (e) {}
                }

                const probeDiv = document.createElement('div');
                probeDiv.id = 'probe_' + Date.now();
                const probeContainer = document.getElementById('yt_hidden_probe');
                if (probeContainer) probeContainer.appendChild(probeDiv);

                ytProbePlayer = new YT.Player(probeDiv.id, {
                    height: '1',
                    width: '1',
                    videoId: ytId,
                    events: {
                        onReady: function (event) {
                            try {
                                const dur = Math.round(event.target.getDuration());
                                if (dur > 0) {
                                    updateDurationInputs(dur);
                                    setTimeout(stopYouTubeProbe, 0);
                                }
                                const vData = event.target.getVideoData();
                                if (vData && vData.title && !detectedYtTitle) {
                                    applyDetectedTitle(vData.title, manualTrigger);
                                }
                            } catch (e) {}
                        },
                        onStateChange: function (event) {
                            try {
                                const dur = Math.round(event.target.getDuration());
                                if (dur > 0) {
                                    updateDurationInputs(dur);
                                    setTimeout(stopYouTubeProbe, 0);
                                }
                                const vData = event.target.getVideoData();
                                if (vData && vData.title && !detectedYtTitle) {
                                    applyDetectedTitle(vData.title, manualTrigger);
                                }
                            } catch (e) {}
                        }
                    }
                });

                probeTimeoutTimer = setTimeout(function () {
                    stopYouTubeProbe();
                    if (activeYtId === ytId && durationSec && !parseInt(durationSec.value) && ytDurText) {
                        ytDurText.textContent = 'Vui lòng nhập thời lượng thủ công';
                    }
                }, 8000);
            }

            measureDurationWithYT();
        }

        function handleApplyTitleClick() {
            if (detectedYtTitle && inputTitle) {
                inputTitle.value = detectedYtTitle;
                inputTitle.dataset.autoYt = 'true';
                updateTitleCounter();
                inputTitle.focus();
                highlightTitleField();
            }
        }

        if (btnApplyTitle) btnApplyTitle.addEventListener('click', handleApplyTitleClick);
        if (btnApplyTitleBadge) btnApplyTitleBadge.addEventListener('click', handleApplyTitleClick);

        if (btnClear && inputUrl) {
            btnClear.addEventListener('click', function () {
                inputUrl.value = '';
                handleUrlChange();
                inputUrl.focus();
            });
        }

        if (btnFetch) {
            btnFetch.addEventListener('click', function () {
                handleUrlChange(true);
            });
        }

        if (inputUrl) {
            inputUrl.addEventListener('input', function () {
                clearTimeout(urlInputTimer);
                urlInputTimer = setTimeout(() => handleUrlChange(false), 450);
            });

            inputUrl.addEventListener('paste', function () {
                clearTimeout(urlInputTimer);
                urlInputTimer = setTimeout(() => handleUrlChange(true), 100);
            });
        }

        window.addEventListener('beforeunload', stopYouTubeProbe);

        if (initialSource === 'youtube' && inputUrl && inputUrl.value.trim()) {
            const ytId = parseYouTubeId(inputUrl.value.trim());
            if (ytId) {
                activeYtId = ytId;
                fetchVideoMetadata(ytId, inputUrl.value.trim(), false);
            }
        }
    });
</script>
@endpush
