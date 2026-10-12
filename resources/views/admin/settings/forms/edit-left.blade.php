@php
    $evaluationGuideVideo = $settings->firstWhere('setting_key', 'evaluation_guide_video');
    $evaluationGuideNote = $settings->firstWhere('setting_key', 'evaluation_guide_note');
    $generalSettingsList = $settings->whereNotIn('setting_key', ['evaluation_guide_video', 'evaluation_guide_note']);
@endphp

<div class="card h-100">
    <div class="card-header justify-content-between align-items-center">
        <h2 class="mb-0 fs-18 fw-bold">{{ $title ?? __('Thông tin cài đặt') }}</h2>
    </div>
    <div class="row card-body wrap-loop-input">
        @foreach ($generalSettingsList as $setting)
            <div class="col-12">
                <div class="mb-3">
                    <label for="{{ $setting->setting_key }}" class="form-label fw-semibold">{{ $setting->setting_name }} @if($setting->type_input != App\Enums\Setting\SettingTypeInput::Checkbox()) <span class="text-danger">*</span> @endif</label>
                    @if ($setting->type_input == App\Enums\Setting\SettingTypeInput::Checkbox())
                        <!-- Handling Checkbox Type -->
                        <div class="form-check form-switch">
                            <input type="hidden" name="{{ $setting->setting_key }}" value="0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="{{ $setting->setting_key }}"
                                   name="{{ $setting->setting_key }}" value="1"
                                {{ $setting->plain_value == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $setting->setting_key }}"></label>
                        </div>
                    @elseif ($setting->type_input == App\Enums\Setting\SettingTypeInput::Textarea())
                        <!-- Handling Textarea Type -->
                        <textarea name="{{ $setting->setting_key }}" class="ckeditor visually-hidden" placeholder="{{ $setting->setting_name }}">{{ $setting->plain_value }}</textarea>
                    @else
                        <!-- Handling Other Types -->
                        <x-dynamic-component
                            :component="$setting->getNameComponentTypeInput()"
                            :name="$setting->setting_key"
                            :value="$setting->plain_value"
                            showImage="{{ $setting->setting_key }}"
                            :required="true">
                        </x-dynamic-component>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Section Cấu hình Video hướng dẫn Đánh giá (UI/UX Pro Max - Cloudflare R2 & YouTube) --}}
        @if ($evaluationGuideVideo || $evaluationGuideNote)
            <div class="col-12 mt-4">
                <div class="card border border-primary-subtle bg-light-subtle rounded-3 overflow-hidden shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white p-2 rounded-2 shadow-xs">
                                <i class="ti ti-video fs-5"></i>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-bold fs-16 text-dark">{{ __('Video & Hướng Dẫn Đánh Giá (App & Phụ Huynh)') }}</h4>
                                <p class="text-muted fs-13 mb-0">{{ __('Video chuẩn hóa để phụ huynh quan sát bé trước các bài đánh giá (EQ, AQ, IQ,...)') }}</p>
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 px-2 py-1">
                            <i class="ti ti-cloud-check me-1"></i>Cloudflare R2 Direct
                        </span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        @if ($evaluationGuideVideo)
                            <div class="mb-4">
                                <label for="evaluation_guide_video" class="form-label fw-bold d-flex align-items-center gap-1">
                                    {{ $evaluationGuideVideo->setting_name }}
                                    <span class="text-danger">*</span>
                                </label>

                                {{-- Tab Mode Switcher (R2 Upload vs YouTube/Link) --}}
                                <ul class="nav nav-pills nav-fill mb-3 p-1 bg-white border rounded-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                                                id="tab-r2-upload-btn"
                                                data-bs-toggle="pill"
                                                data-bs-target="#tab-r2-upload"
                                                type="button"
                                                role="tab">
                                            <i class="ti ti-cloud-upload fs-5 text-primary"></i>
                                            <span>{{ __('Tải lên Cloudflare R2') }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                                                id="tab-youtube-link-btn"
                                                data-bs-toggle="pill"
                                                data-bs-target="#tab-youtube-link"
                                                type="button"
                                                role="tab">
                                            <i class="ti ti-brand-youtube fs-5 text-danger"></i>
                                            <span>{{ __('Dán link YouTube / Trực tiếp') }}</span>
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content mb-3">
                                    {{-- TAB 1: R2 Upload Zone --}}
                                    <div class="tab-pane fade show active" id="tab-r2-upload" role="tabpanel">
                                        <div id="r2_dropzone"
                                             class="border border-2 border-dashed rounded-3 p-4 text-center bg-white position-relative"
                                             style="cursor: pointer; transition: all 0.2s ease; border-color: #cbd5e1 !important;"
                                             onclick="document.getElementById('r2_video_file_input').click()">
                                            <input type="file"
                                                   id="r2_video_file_input"
                                                   accept="video/mp4,video/webm,video/quicktime,video/x-matroska"
                                                   class="d-none"
                                                   onchange="handleR2FileSelected(this.files)">
                                            
                                            <div class="py-2">
                                                <div class="mb-3 d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary">
                                                    <i class="ti ti-cloud-upload fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold mb-1 text-dark">{{ __('Kéo thả video vào đây hoặc bấm để chọn tệp') }}</h5>
                                                <p class="text-muted fs-13 mb-0">{{ __('Hỗ trợ: MP4, WebM, MOV, MKV (Tối đa 200MB). Video sẽ được đẩy trực tiếp lên Cloudflare R2.') }}</p>
                                            </div>
                                        </div>

                                        {{-- Progress Bar Container (Hidden by default) --}}
                                        <div id="r2_upload_progress_container" class="mt-3 p-3 bg-white border rounded-3 shadow-xs" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ti ti-movie fs-4 text-primary"></i>
                                                    <div>
                                                        <div id="r2_upload_filename" class="fw-bold text-dark fs-13 text-truncate" style="max-width: 250px;">video.mp4</div>
                                                        <small id="r2_upload_filesize" class="text-muted fs-11">0 MB</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span id="r2_upload_percentage" class="fw-bold text-primary fs-13">0%</span>
                                                    <span id="r2_upload_badge" class="badge bg-primary-subtle text-primary fs-11">{{ __('Đang tải lên...') }}</span>
                                                </div>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div id="r2_upload_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <div id="r2_upload_error_msg" class="text-danger fs-12 mt-2" style="display: none;"></div>
                                        </div>
                                    </div>

                                    {{-- TAB 2: YouTube / Direct URL Input --}}
                                    <div class="tab-pane fade" id="tab-youtube-link" role="tabpanel">
                                        <div class="p-3 bg-white border rounded-3">
                                            <label class="form-label fs-13 fw-semibold text-secondary">{{ __('Nhập liên kết YouTube hoặc Video trực tiếp') }}:</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="ti ti-brand-youtube text-danger"></i></span>
                                                <input type="text"
                                                       class="form-control"
                                                       id="custom_video_url_input"
                                                       placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/..."
                                                       value="{{ $evaluationGuideVideo->plain_value }}"
                                                       oninput="handleCustomUrlChange(this.value)">
                                                <button type="button" class="btn btn-outline-secondary" onclick="pasteFromClipboard()">
                                                    <i class="ti ti-clipboard me-1"></i>{{ __('Dán') }}
                                                </button>
                                            </div>
                                            <div class="form-text fs-12 text-muted mt-2">
                                                <i class="ti ti-info-circle text-primary me-1"></i>{{ __('Hệ thống tự động nhận diện liên kết YouTube (watch, youtu.be, shorts) hoặc file MP4 trực tiếp.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Main Setting Field (Đường dẫn video thực tế lưu vào DB) --}}
                                <div class="mb-3">
                                    <label class="form-label fs-13 fw-semibold text-muted d-flex align-items-center justify-content-between">
                                        <span><i class="ti ti-link me-1"></i>{{ __('Đường dẫn video hiện tại (Lưu vào Cài đặt)') }}:</span>
                                        <span class="fs-12 text-secondary">{{ __('Tự động đồng bộ khi tải lên hoặc dán link') }}</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control bg-light font-monospace fs-13"
                                               id="evaluation_guide_video"
                                               name="evaluation_guide_video"
                                               value="{{ $evaluationGuideVideo->plain_value }}"
                                               placeholder="Chưa có đường dẫn video"
                                               oninput="handleMainUrlChange(this.value)">
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyCurrentVideoUrl()" title="{{ __('Sao chép') }}">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="clearCurrentVideo()" title="{{ __('Xóa video') }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Khung Preview Video Trực Tiếp (Smart Player: YouTube iframe hoặc HTML5 Video) --}}
                                <div class="p-3 bg-white border rounded-3 shadow-xs">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-13 fw-bold text-dark d-flex align-items-center gap-1">
                                            <i class="ti ti-player-play text-primary"></i>
                                            {{ __('Trình phát video trực tiếp') }}
                                        </span>
                                        <span id="video_type_badge" class="badge bg-secondary-subtle text-secondary fs-11">
                                            {{ __('Chưa có video') }}
                                        </span>
                                    </div>
                                    <div class="ratio ratio-16x9 rounded-2 overflow-hidden bg-dark position-relative d-flex align-items-center justify-content-center" style="max-height: 340px;">
                                        {{-- 1. YouTube Iframe Player --}}
                                        <iframe id="evaluation_youtube_preview"
                                                class="w-100 h-100 border-0"
                                                style="display: none;"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>

                                        {{-- 2. HTML5 Video Player (R2 / MP4) --}}
                                        <video id="evaluation_video_preview"
                                               controls
                                               preload="metadata"
                                               class="w-100 h-100"
                                               style="display: none;">
                                            {{ __('Trình duyệt không hỗ trợ thẻ video.') }}
                                        </video>

                                        {{-- 3. Placeholder Empty State --}}
                                        <div id="evaluation_video_placeholder" class="text-center text-white-50 p-4">
                                            <i class="ti ti-video-off fs-1 d-block mb-2"></i>
                                            <span class="fw-semibold">{{ __('Chưa có video được chọn') }}</span>
                                            <p class="fs-12 text-white-50 mb-0 mt-1">{{ __('Tải file video lên Cloudflare R2 hoặc nhập link YouTube ở trên để xem trước') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($evaluationGuideNote)
                            <div class="mb-3">
                                <label for="evaluation_guide_note" class="form-label fw-bold">
                                    {{ $evaluationGuideNote->setting_name }}
                                </label>
                                <textarea id="evaluation_guide_note"
                                          name="evaluation_guide_note"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Nhập lưu ý hiển thị dưới video cho phụ huynh...">{{ $evaluationGuideNote->plain_value }}</textarea>
                                <div class="form-text fs-12 text-muted">
                                    <i class="ti ti-notes"></i> {{ __('Đoạn văn bản ngắn gọn nhắc nhở phụ huynh quan sát bé trước khi làm trắc nghiệm.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if (isset($bikeC_Intercity_Rush_Hour))
        @include('admin.settings.forms.c_intercity.setting_c_intercity')
    @elseif(isset($bikeC_Multi_Rush_Hour))
        @include('admin.settings.forms.c_multi.setting_c_multi')
    @endif
</div>

<script>
    // Trích xuất YouTube Video ID từ nhiều định dạng URL
    function extractYouTubeId(url) {
        if (!url) return null;
        const trimmed = url.trim();
        const regExp = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = trimmed.match(regExp);
        return (match && match[1]) ? match[1] : null;
    }

    // Cập nhật khung xem trước video thông minh (YouTube / R2 / Placeholder)
    function renderVideoPreview(url) {
        const youtubeIframe = document.getElementById('evaluation_youtube_preview');
        const html5Video = document.getElementById('evaluation_video_preview');
        const placeholder = document.getElementById('evaluation_video_placeholder');
        const badge = document.getElementById('video_type_badge');

        if (!youtubeIframe || !html5Video || !placeholder) return;

        const trimmedUrl = url ? url.trim() : '';

        if (!trimmedUrl) {
            youtubeIframe.style.display = 'none';
            youtubeIframe.src = '';
            html5Video.pause();
            html5Video.style.display = 'none';
            html5Video.src = '';
            placeholder.style.display = 'block';
            if (badge) {
                badge.className = 'badge bg-secondary-subtle text-secondary fs-11';
                badge.innerHTML = '<i class="ti ti-circle-x me-1"></i>Chưa có video';
            }
            return;
        }

        const ytId = extractYouTubeId(trimmedUrl);

        if (ytId) {
            // Hiển thị YouTube Player
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
            // Hiển thị HTML5 Player (R2 hoặc file video)
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
    }

    // Khi người dùng chỉnh sửa trong ô Main URL
    function handleMainUrlChange(val) {
        const customInput = document.getElementById('custom_video_url_input');
        if (customInput && customInput.value !== val) {
            customInput.value = val;
        }
        renderVideoPreview(val);
    }

    // Khi người dùng nhập trong tab YouTube/Ngoài
    function handleCustomUrlChange(val) {
        const mainInput = document.getElementById('evaluation_guide_video');
        if (mainInput) {
            mainInput.value = val;
        }
        renderVideoPreview(val);
    }

    // Xử lý kéo thả Dropzone R2
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('r2_dropzone');
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
                handleR2FileSelected(files);
            }, false);
        }

        const mainInput = document.getElementById('evaluation_guide_video');
        if (mainInput && mainInput.value) {
            renderVideoPreview(mainInput.value);
        }
    });

    // Khi chọn file video từ máy tính
    function handleR2FileSelected(files) {
        if (!files || files.length === 0) return;
        const file = files[0];

        // Kiểm tra dung lượng (tối đa 200MB)
        const maxSizeBytes = 200 * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert('Dung lượng video vượt quá 200MB. Vui lòng chọn file nhỏ hơn.');
            return;
        }

        uploadFileToR2(file);
    }

    // Upload file lên Cloudflare R2 qua AJAX XMLHttpRequest
    function uploadFileToR2(file) {
        const progressContainer = document.getElementById('r2_upload_progress_container');
        const progressBar = document.getElementById('r2_upload_progress_bar');
        const percentageText = document.getElementById('r2_upload_percentage');
        const filenameText = document.getElementById('r2_upload_filename');
        const filesizeText = document.getElementById('r2_upload_filesize');
        const badge = document.getElementById('r2_upload_badge');
        const errorMsg = document.getElementById('r2_upload_error_msg');
        const mainInput = document.getElementById('evaluation_guide_video');
        const customInput = document.getElementById('custom_video_url_input');

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

        // Theo dõi tiến trình tải lên
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

                        renderVideoPreview(publicUrl);
                    }
                } catch (e) {
                    handleUploadError('Không thể phân tích phản hồi từ máy chủ.');
                }
            } else {
                let msg = 'Lỗi upload lên Cloudflare R2.';
                try {
                    const err = JSON.parse(xhr.responseText);
                    if (err.message) msg = err.message;
                } catch(e) {}
                handleUploadError(msg);
            }
        };

        xhr.onerror = function() {
            handleUploadError('Mất kết nối tới máy chủ khi upload video.');
        };

        xhr.send(formData);

        function handleUploadError(message) {
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
    }

    // Sao chép link video hiện tại
    function copyCurrentVideoUrl() {
        const mainInput = document.getElementById('evaluation_guide_video');
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
    }

    // Dán link từ clipboard
    function pasteFromClipboard() {
        if (navigator.clipboard && navigator.clipboard.readText) {
            navigator.clipboard.readText().then(text => {
                if (text) {
                    const customInput = document.getElementById('custom_video_url_input');
                    const mainInput = document.getElementById('evaluation_guide_video');
                    if (customInput) customInput.value = text;
                    if (mainInput) mainInput.value = text;
                    renderVideoPreview(text);
                }
            });
        } else {
            const val = prompt('Dán đường dẫn video vào đây:');
            if (val) {
                handleCustomUrlChange(val);
            }
        }
    }

    // Xóa video hiện tại
    function clearCurrentVideo() {
        if (confirm('Bạn có chắc chắn muốn xóa video hướng dẫn này?')) {
            const mainInput = document.getElementById('evaluation_guide_video');
            const customInput = document.getElementById('custom_video_url_input');
            const fileInput = document.getElementById('r2_video_file_input');
            const progressContainer = document.getElementById('r2_upload_progress_container');

            if (mainInput) mainInput.value = '';
            if (customInput) customInput.value = '';
            if (fileInput) fileInput.value = '';
            if (progressContainer) progressContainer.style.display = 'none';

            renderVideoPreview('');
        }
    }
</script>
