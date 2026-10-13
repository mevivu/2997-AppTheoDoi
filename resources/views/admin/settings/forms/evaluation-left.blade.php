@php
    $items = [
        'eq' => [
            'key' => 'eq',
            'name' => 'Đánh giá Cảm xúc (EQ)',
            'short' => 'EQ',
            'icon' => 'ti ti-heart',
            'badge_class' => 'bg-danger text-white',
            'border_class' => 'border-danger-subtle',
            'bg_light' => '#fff1f2',
            'theme_color' => '#e11d48',
            'desc' => 'Đo lường năng lực nhận diện, thấu hiểu và quản lý cảm xúc của trẻ.',
            'video' => $settings->firstWhere('setting_key', 'evaluation_guide_video_eq'),
            'desc_setting' => $settings->firstWhere('setting_key', 'evaluation_guide_desc_eq'),
        ],
        'aq' => [
            'key' => 'aq',
            'name' => 'Đánh giá Vượt khó (AQ)',
            'short' => 'AQ',
            'icon' => 'ti ti-shield',
            'badge_class' => 'bg-info text-white',
            'border_class' => 'border-info-subtle',
            'bg_light' => '#f0f9ff',
            'theme_color' => '#0284c7',
            'desc' => 'Đo lường bản lĩnh, ý chí kiên trì và cách vượt qua trở ngại, nghịch cảnh.',
            'video' => $settings->firstWhere('setting_key', 'evaluation_guide_video_aq'),
            'desc_setting' => $settings->firstWhere('setting_key', 'evaluation_guide_desc_aq'),
        ],
        'iq' => [
            'key' => 'iq',
            'name' => 'Kiểm tra Trí tuệ (IQ)',
            'short' => 'IQ',
            'icon' => 'ti ti-brain',
            'badge_class' => 'bg-purple text-white',
            'border_class' => 'border-purple-subtle',
            'bg_light' => '#f5f3ff',
            'theme_color' => '#7c3aed',
            'desc' => 'Đánh giá tư duy logic, nhận thức không gian và năng lực phân tích của trẻ.',
            'video' => $settings->firstWhere('setting_key', 'evaluation_guide_video_iq'),
            'desc_setting' => $settings->firstWhere('setting_key', 'evaluation_guide_desc_iq'),
        ],
        'common' => [
            'key' => 'common',
            'name' => 'Mặc định Dùng chung (Fallback)',
            'short' => 'Chung',
            'icon' => 'ti ti-settings-cog',
            'badge_class' => 'bg-secondary text-white',
            'border_class' => 'border-secondary-subtle',
            'bg_light' => '#f8fafc',
            'theme_color' => '#64748b',
            'desc' => 'Video và bài viết hướng dẫn dự phòng nếu một bài đánh giá chưa được cấu hình riêng.',
            'video' => $settings->firstWhere('setting_key', 'evaluation_guide_video'),
            'desc_setting' => $settings->firstWhere('setting_key', 'evaluation_guide_note'),
        ],
    ];
@endphp

<div class="card border border-primary-subtle bg-light-subtle rounded-3 overflow-hidden shadow-sm">
    {{-- Header --}}
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary text-white p-2 rounded-2 shadow-xs">
                <i class="ti ti-video fs-5"></i>
            </span>
            <div>
                <h4 class="mb-0 fw-bold fs-16 text-dark">{{ __('Cấu hình Video & Hướng Dẫn Từng Loại Đánh Giá') }}</h4>
                <p class="text-muted fs-13 mb-0">{{ __('Tách riêng video và bài viết hướng dẫn (soạn thảo chi tiết) cho EQ, AQ, IQ hiển thị trên App') }}</p>
            </div>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 px-2 py-1">
            <i class="ti ti-cloud-check me-1"></i>Cloudflare R2 Direct
        </span>
    </div>

    <div class="card-body p-3 p-md-4">
        {{-- Navigation Tabs theo từng loại đánh giá --}}
        <ul class="nav nav-pills nav-fill mb-4 p-1 bg-white border rounded-3 shadow-xs" id="evalTabs" role="tablist">
            @foreach($items as $tKey => $item)
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 px-3 fw-semibold d-flex align-items-center justify-content-center gap-2 {{ $loop->first ? 'active' : '' }}"
                            id="tab-eval-{{ $tKey }}-btn"
                            data-bs-toggle="pill"
                            data-bs-target="#tab-eval-{{ $tKey }}"
                            type="button"
                            role="tab"
                            aria-controls="tab-eval-{{ $tKey }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="{{ $item['icon'] }} fs-5"></i>
                        <span>{{ $item['name'] }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content" id="evalTabsContent">
            @foreach($items as $tKey => $item)
                @php
                    $vSetting = $item['video'];
                    $vKey = $vSetting ? $vSetting->setting_key : ($tKey === 'common' ? 'evaluation_guide_video' : "evaluation_guide_video_{$tKey}");
                    $vVal = $vSetting ? $vSetting->plain_value : '';

                    $descSetting = $item['desc_setting'];
                    $descKey = $descSetting ? $descSetting->setting_key : ($tKey === 'common' ? 'evaluation_guide_note' : "evaluation_guide_desc_{$tKey}");
                    $descVal = $descSetting ? $descSetting->plain_value : '';
                @endphp

                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="tab-eval-{{ $tKey }}"
                     role="tabpanel"
                     aria-labelledby="tab-eval-{{ $tKey }}-btn">

                    {{-- Banner tóm tắt loại đánh giá --}}
                    <div class="p-3 mb-4 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2"
                         style="background-color: {{ $item['bg_light'] }}; border-color: {{ $item['theme_color'] }}33 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $item['badge_class'] }} p-2 rounded-2">
                                <i class="{{ $item['icon'] }} fs-5"></i>
                            </span>
                            <div>
                                <h5 class="mb-0 fw-bold fs-15 text-dark">{{ $item['name'] }}</h5>
                                <p class="text-muted fs-12 mb-0">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        <span class="badge bg-white text-dark border fs-12 px-2 py-1">
                            Mã: <code class="text-primary">{{ $tKey }}</code>
                        </span>
                    </div>

                    {{-- 1. KHUNG CẤU HÌNH VIDEO --}}
                    <div class="card border rounded-3 mb-4 shadow-2xs">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold fs-14 text-dark d-flex align-items-center gap-1">
                                <i class="ti ti-movie fs-5 text-primary"></i>
                                <span>1. Video hướng dẫn (Cloudflare R2 / YouTube)</span>
                            </span>
                            <span id="video_type_badge_{{ $tKey }}" class="badge bg-secondary-subtle text-secondary fs-11">
                                <i class="ti ti-circle-x me-1"></i>Chưa nhận diện
                            </span>
                        </div>
                        <div class="card-body p-3">
                            {{-- Tab chọn nguồn video --}}
                            <ul class="nav nav-pills nav-fill mb-3 p-1 bg-light border rounded-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1-5 fw-semibold d-flex align-items-center justify-content-center gap-1 fs-13"
                                            id="tab-r2-{{ $tKey }}-btn"
                                            data-bs-toggle="pill"
                                            data-bs-target="#tab-r2-{{ $tKey }}"
                                            type="button"
                                            role="tab">
                                        <i class="ti ti-cloud-upload fs-5 text-primary"></i>
                                        <span>{{ __('Tải lên Cloudflare R2') }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1-5 fw-semibold d-flex align-items-center justify-content-center gap-1 fs-13"
                                            id="tab-yt-{{ $tKey }}-btn"
                                            data-bs-toggle="pill"
                                            data-bs-target="#tab-yt-{{ $tKey }}"
                                            type="button"
                                            role="tab">
                                        <i class="ti ti-brand-youtube fs-5 text-danger"></i>
                                        <span>{{ __('Dán link YouTube / Trực tiếp') }}</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mb-3">
                                {{-- TAB UPLOAD R2 --}}
                                <div class="tab-pane fade show active" id="tab-r2-{{ $tKey }}" role="tabpanel">
                                    <div id="r2_dropzone_{{ $tKey }}"
                                         class="border border-2 border-dashed rounded-3 p-3 text-center bg-white position-relative"
                                         style="cursor: pointer; transition: all 0.2s ease; border-color: #cbd5e1 !important;"
                                         onclick="document.getElementById('r2_video_file_input_{{ $tKey }}').click()">
                                        <input type="file"
                                               id="r2_video_file_input_{{ $tKey }}"
                                               accept="video/mp4,video/webm,video/quicktime,video/x-matroska"
                                               class="d-none"
                                               onchange="VideoUploadManager.handleFileSelected('{{ $tKey }}', this.files)">
                                        
                                        <div class="py-2">
                                            <div class="mb-2 d-inline-flex p-2 rounded-circle bg-primary-subtle text-primary">
                                                <i class="ti ti-cloud-upload fs-3"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">{{ __('Kéo thả video vào đây hoặc bấm để chọn tệp') }}</h6>
                                            <p class="text-muted fs-12 mb-0">{{ __('Hỗ trợ: MP4, WebM, MOV, MKV (Tối đa 200MB). Đẩy trực tiếp lên Cloudflare R2.') }}</p>
                                        </div>
                                    </div>

                                    {{-- Progress Bar Container --}}
                                    <div id="r2_upload_progress_container_{{ $tKey }}" class="mt-3 p-3 bg-white border rounded-3 shadow-2xs" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-movie fs-4 text-primary"></i>
                                                <div>
                                                    <div id="r2_upload_filename_{{ $tKey }}" class="fw-bold text-dark fs-13 text-truncate" style="max-width: 250px;">video.mp4</div>
                                                    <small id="r2_upload_filesize_{{ $tKey }}" class="text-muted fs-11">0 MB</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span id="r2_upload_percentage_{{ $tKey }}" class="fw-bold text-primary fs-13">0%</span>
                                                <span id="r2_upload_badge_{{ $tKey }}" class="badge bg-primary-subtle text-primary fs-11">{{ __('Đang tải lên...') }}</span>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div id="r2_upload_progress_bar_{{ $tKey }}" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div id="r2_upload_error_msg_{{ $tKey }}" class="text-danger fs-12 mt-2" style="display: none;"></div>
                                    </div>
                                </div>

                                {{-- TAB LINK YOUTUBE / TRỰC TIẾP --}}
                                <div class="tab-pane fade" id="tab-yt-{{ $tKey }}" role="tabpanel">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-danger">
                                            <i class="ti ti-brand-youtube fs-5"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control border-start-0 ps-1"
                                               id="custom_video_url_input_{{ $tKey }}"
                                               placeholder="Ví dụ: https://www.youtube.com/watch?v=... hoặc file .mp4"
                                               value="{{ $vVal }}"
                                               oninput="VideoUploadManager.handleCustomUrlChange('{{ $tKey }}', this.value)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="VideoUploadManager.pasteFromClipboard('{{ $tKey }}')">
                                            <i class="ti ti-clipboard me-1"></i>{{ __('Dán') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Ô Input chính thức lưu vào DB --}}
                            <div class="mb-3">
                                <label class="form-label fs-12 text-muted fw-semibold d-flex align-items-center justify-content-between mb-1">
                                    <span><i class="ti ti-link me-1"></i>{{ __('Đường dẫn video hiện tại (Lưu vào Cài đặt):') }}</span>
                                    <span class="text-secondary fs-11">{{ __('Tự động đồng bộ khi tải lên hoặc dán link') }}</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text"
                                           class="form-control bg-light fs-12 font-monospace"
                                           id="{{ $vKey }}"
                                           name="{{ $vKey }}"
                                           value="{{ $vVal }}"
                                           placeholder="https://..."
                                           onchange="VideoUploadManager.handleMainUrlChange('{{ $tKey }}', this.value)">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" title="Sao chép" onclick="VideoUploadManager.copyUrl('{{ $tKey }}')">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" type="button" title="Xóa video này" onclick="VideoUploadManager.clearVideo('{{ $tKey }}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Trình phát Video Xem Trước (Cinema Player) --}}
                            <div class="border rounded-3 overflow-hidden bg-dark position-relative shadow-sm" style="min-height: 240px;">
                                {{-- YouTube Player --}}
                                <iframe id="evaluation_youtube_preview_{{ $tKey }}"
                                        src=""
                                        class="w-100"
                                        style="height: 320px; border: none; display: none;"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>

                                {{-- HTML5 / R2 Video Player --}}
                                <video id="evaluation_video_preview_{{ $tKey }}"
                                       src=""
                                       class="w-100"
                                       style="height: 320px; object-fit: contain; display: none; background: #000;"
                                       controls
                                       preload="metadata"></video>

                                {{-- Placeholder khi chưa có video --}}
                                <div id="evaluation_video_placeholder_{{ $tKey }}"
                                     class="d-flex flex-column align-items-center justify-content-center text-center p-4 text-white"
                                     style="min-height: 240px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                                    <div class="p-3 rounded-circle bg-white-10 mb-2">
                                        <i class="ti ti-player-play fs-2 text-white-50"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-white">{{ __('Khung xem trước video') }}</h6>
                                    <p class="text-white-50 fs-12 mb-0" style="max-width: 320px;">
                                        {{ __('Tải lên file video R2 hoặc dán liên kết YouTube để xem thử trước khi lưu.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. BÀI VIẾT HƯỚNG DẪN ĐÁNH GIÁ (TRÌNH SOẠN THẢO BÀI VIẾT) --}}
                    <div class="card border rounded-3 mb-3 shadow-2xs">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold fs-14 text-dark d-flex align-items-center gap-1">
                                <i class="ti ti-article fs-5 text-primary"></i>
                                <span>2. Bài viết hướng dẫn đánh giá (Soạn thảo chi tiết hiển thị trên App)</span>
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">
                                <i class="ti ti-edit me-1"></i>Trình soạn thảo bài viết
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <label for="{{ $descKey }}" class="form-label fw-semibold fs-13 d-flex align-items-center justify-content-between mb-2">
                                    <span>
                                        <i class="ti ti-file-text text-primary me-1"></i>
                                        Nội dung bài viết hướng dẫn {{ $item['name'] }}
                                        <span class="text-danger">*</span>
                                    </span>
                                    <span class="text-muted fs-12 fw-normal">Hỗ trợ tiêu đề, định dạng in đậm, danh sách, hình ảnh,...</span>
                                </label>
                                <textarea class="ckeditor visually-hidden"
                                          id="{{ $descKey }}"
                                          name="{{ $descKey }}"
                                          placeholder="Soạn nội dung bài viết hướng dẫn đánh giá {{ $item['name'] }}...">{{ $descVal }}</textarea>
                                <div class="form-text fs-12 text-muted mt-2">
                                    {{ __('Nội dung bài viết được định dạng đầy đủ và hiển thị trực quan bên dưới video hướng dẫn trên ứng dụng di động cho phụ huynh đọc.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    /**
     * Quản lý Upload & Preview Video Đánh Giá Thông Minh
     * Hỗ trợ Cloudflare R2 Direct Upload & YouTube Embed Parser cho từng loại (EQ, AQ, IQ, Fallback)
     */
    const VideoUploadManager = {
        // Trích xuất YouTube Video ID từ nhiều định dạng URL
        extractYouTubeId: function(url) {
            if (!url) return null;
            const trimmed = url.trim();
            const regExp = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
            const match = trimmed.match(regExp);
            return (match && match[1]) ? match[1] : null;
        },

        // Render xem trước video cho một loại cụ thể
        renderPreview: function(type, url) {
            const youtubeIframe = document.getElementById('evaluation_youtube_preview_' + type);
            const html5Video = document.getElementById('evaluation_video_preview_' + type);
            const placeholder = document.getElementById('evaluation_video_placeholder_' + type);
            const badge = document.getElementById('video_type_badge_' + type);

            if (!youtubeIframe || !html5Video || !placeholder) return;

            const trimmedUrl = url ? url.trim() : '';

            if (!trimmedUrl) {
                youtubeIframe.style.display = 'none';
                youtubeIframe.src = '';
                html5Video.pause();
                html5Video.style.display = 'none';
                html5Video.src = '';
                placeholder.style.display = 'flex';
                if (badge) {
                    badge.className = 'badge bg-secondary-subtle text-secondary fs-11';
                    badge.innerHTML = '<i class="ti ti-circle-x me-1"></i>Chưa có video';
                }
                return;
            }

            const ytId = this.extractYouTubeId(trimmedUrl);

            if (ytId) {
                // YouTube Player
                html5Video.pause();
                html5Video.style.display = 'none';
                html5Video.src = '';

                youtubeIframe.src = `https://www.youtube-nocookie.com/embed/${ytId}?rel=0`;
                youtubeIframe.style.display = 'block';
                placeholder.style.display = 'none';

                if (badge) {
                    badge.className = 'badge bg-danger text-white fs-11';
                    badge.innerHTML = `<i class="ti ti-brand-youtube me-1"></i>YouTube (${ytId})`;
                }
            } else {
                // HTML5 Player (Cloudflare R2 hoặc file trực tiếp)
                youtubeIframe.style.display = 'none';
                youtubeIframe.src = '';

                html5Video.src = trimmedUrl;
                html5Video.style.display = 'block';
                placeholder.style.display = 'none';

                if (badge) {
                    const isR2 = trimmedUrl.includes('r2.dev') || trimmedUrl.includes('r2.cloudflarestorage.com');
                    badge.className = isR2 ? 'badge bg-info text-white fs-11' : 'badge bg-primary text-white fs-11';
                    badge.innerHTML = isR2
                        ? '<i class="ti ti-cloud me-1"></i>Cloudflare R2'
                        : '<i class="ti ti-video me-1"></i>Video Trực Tiếp';
                }
            }
        },

        // Khi thay đổi giá trị trong ô Input chính
        handleMainUrlChange: function(type, val) {
            const customInput = document.getElementById('custom_video_url_input_' + type);
            if (customInput && customInput.value !== val) {
                customInput.value = val;
            }
            this.renderPreview(type, val);
        },

        // Khi thay đổi giá trị trong tab YouTube
        handleCustomUrlChange: function(type, val) {
            const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
            const mainInput = document.getElementById(mainKey);
            if (mainInput) {
                mainInput.value = val;
            }
            this.renderPreview(type, val);
        },

        // Sao chép link video
        copyUrl: function(type) {
            const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
            const mainInput = document.getElementById(mainKey);
            if (!mainInput || !mainInput.value) {
                alert('Chưa có link video để sao chép.');
                return;
            }
            navigator.clipboard.writeText(mainInput.value).then(() => {
                alert('Đã sao chép đường dẫn video vào bộ nhớ tạm!');
            }).catch(() => {
                mainInput.select();
                document.execCommand('copy');
                alert('Đã sao chép!');
            });
        },

        // Xóa link video
        clearVideo: function(type) {
            if (confirm('Bạn có chắc chắn muốn xóa video này?')) {
                const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
                const mainInput = document.getElementById(mainKey);
                const customInput = document.getElementById('custom_video_url_input_' + type);
                const fileInput = document.getElementById('r2_video_file_input_' + type);
                const progressContainer = document.getElementById('r2_upload_progress_container_' + type);

                if (mainInput) mainInput.value = '';
                if (customInput) customInput.value = '';
                if (fileInput) fileInput.value = '';
                if (progressContainer) progressContainer.style.display = 'none';

                this.renderPreview(type, '');
            }
        },

        // Dán từ clipboard
        pasteFromClipboard: function(type) {
            const self = this;
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(text => {
                    if (text) {
                        const customInput = document.getElementById('custom_video_url_input_' + type);
                        const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
                        const mainInput = document.getElementById(mainKey);
                        if (customInput) customInput.value = text;
                        if (mainInput) mainInput.value = text;
                        self.renderPreview(type, text);
                    }
                });
            } else {
                const val = prompt('Dán đường dẫn video vào đây:');
                if (val) {
                    self.handleCustomUrlChange(type, val);
                }
            }
        },

        // Xử lý khi chọn file video
        handleFileSelected: function(type, files) {
            if (!files || files.length === 0) return;
            const file = files[0];

            // Tối đa 200MB
            const maxSizeBytes = 200 * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                alert('Dung lượng video vượt quá 200MB. Vui lòng chọn file nhỏ hơn.');
                return;
            }

            this.uploadToR2(type, file);
        },

        // Upload trực tiếp lên R2 qua AJAX
        uploadToR2: function(type, file) {
            const self = this;
            const progressContainer = document.getElementById('r2_upload_progress_container_' + type);
            const progressBar = document.getElementById('r2_upload_progress_bar_' + type);
            const percentageText = document.getElementById('r2_upload_percentage_' + type);
            const filenameText = document.getElementById('r2_upload_filename_' + type);
            const filesizeText = document.getElementById('r2_upload_filesize_' + type);
            const badge = document.getElementById('r2_upload_badge_' + type);
            const errorMsg = document.getElementById('r2_upload_error_msg_' + type);
            const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
            const mainInput = document.getElementById(mainKey);
            const customInput = document.getElementById('custom_video_url_input_' + type);

            if (!progressContainer) return;

            // Hiển thị thanh tiến trình
            progressContainer.style.display = 'block';
            if (filenameText) filenameText.innerText = file.name;
            if (filesizeText) filesizeText.innerText = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
            }
            if (percentageText) percentageText.innerText = '0%';
            if (badge) {
                badge.className = 'badge bg-primary-subtle text-primary fs-11';
                badge.innerText = 'Đang tải lên R2...';
            }
            if (errorMsg) errorMsg.style.display = 'none';

            const formData = new FormData();
            formData.append('video', file);
            formData.append('_token', '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.setting.upload_video") }}', true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    if (progressBar) progressBar.style.width = percentComplete + '%';
                    if (percentageText) percentageText.innerText = percentComplete + '%';
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.url) {
                            const publicUrl = response.data.url;
                            if (mainInput) mainInput.value = publicUrl;
                            if (customInput) customInput.value = publicUrl;

                            if (progressBar) {
                                progressBar.style.width = '100%';
                                progressBar.className = 'progress-bar bg-success';
                            }
                            if (percentageText) percentageText.innerText = '100%';
                            if (badge) {
                                badge.className = 'badge bg-success-subtle text-success fs-11';
                                badge.innerText = 'Đã tải lên R2!';
                            }

                            self.renderPreview(type, publicUrl);
                        }
                    } catch (e) {
                        handleErr('Không thể phân tích phản hồi từ máy chủ.');
                    }
                } else {
                    let msg = 'Lỗi upload lên Cloudflare R2.';
                    try {
                        const err = JSON.parse(xhr.responseText);
                        if (err.message) msg = err.message;
                    } catch(e) {}
                    handleErr(msg);
                }
            };

            xhr.onerror = function() {
                handleErr('Mất kết nối tới máy chủ khi upload video.');
            };

            xhr.send(formData);

            function handleErr(message) {
                if (badge) {
                    badge.className = 'badge bg-danger-subtle text-danger fs-11';
                    badge.innerText = 'Thất bại';
                }
                if (progressBar) {
                    progressBar.className = 'progress-bar bg-danger';
                }
                if (errorMsg) {
                    errorMsg.innerText = message;
                    errorMsg.style.display = 'block';
                }
            }
        },

        // Khởi tạo kéo thả và preview ban đầu cho tất cả các loại
        init: function() {
            const types = ['eq', 'aq', 'iq', 'common'];
            const self = this;

            types.forEach(function(type) {
                // Dropzone
                const dropzone = document.getElementById('r2_dropzone_' + type);
                if (dropzone) {
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.style.borderColor = '#0d6efd';
                            dropzone.style.backgroundColor = '#eff6ff';
                        }, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.style.borderColor = '#cbd5e1';
                            dropzone.style.backgroundColor = '#ffffff';
                        }, false);
                    });

                    dropzone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        self.handleFileSelected(type, files);
                    }, false);
                }

                // Render Preview ban đầu
                const mainKey = (type === 'common') ? 'evaluation_guide_video' : 'evaluation_guide_video_' + type;
                const mainInput = document.getElementById(mainKey);
                if (mainInput && mainInput.value) {
                    self.renderPreview(type, mainInput.value);
                }
            });

            // Đảm bảo updateElement khi submit form
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    if (window.CKEDITOR && CKEDITOR.instances) {
                        for (let inst in CKEDITOR.instances) {
                            try {
                                CKEDITOR.instances[inst].updateElement();
                            } catch(e) {}
                        }
                    }
                });
            }

            // Tự động resize CKEditor khi chuyển Tab để không bị vỡ giao diện
            document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(function(tabBtn) {
                tabBtn.addEventListener('shown.bs.tab', function () {
                    if (window.CKEDITOR && CKEDITOR.instances) {
                        for (let inst in CKEDITOR.instances) {
                            if (CKEDITOR.instances[inst]) {
                                try {
                                    CKEDITOR.instances[inst].resize();
                                } catch(e) {}
                            }
                        }
                    }
                });
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        VideoUploadManager.init();
    });
</script>
