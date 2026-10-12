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

        {{-- Section Cấu hình Video hướng dẫn Đánh giá (UI/UX Pro Max) --}}
        @if ($evaluationGuideVideo || $evaluationGuideNote)
            <div class="col-12 mt-4">
                <div class="card border border-primary-subtle bg-light-subtle rounded-3 overflow-hidden shadow-xs">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white p-2 rounded-2">
                            <i class="ti ti-video fs-5"></i>
                        </span>
                        <div>
                            <h4 class="mb-0 fw-bold fs-16 text-dark">{{ __('Video & Hướng dẫn Đánh giá (Dành cho App)') }}</h4>
                            <p class="text-muted fs-13 mb-0">{{ __('Cấu hình video hướng dẫn phụ huynh trước khi thực hiện các bài đánh giá (EQ, AQ, IQ,...)') }}</p>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        @if ($evaluationGuideVideo)
                            <div class="mb-4">
                                <label for="evaluation_guide_video" class="form-label fw-bold d-flex align-items-center gap-1">
                                    {{ $evaluationGuideVideo->setting_name }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="ti ti-link text-primary"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="evaluation_guide_video"
                                           name="evaluation_guide_video"
                                           value="{{ $evaluationGuideVideo->plain_value }}"
                                           placeholder="https://pub-...r2.dev/video.mp4 hoặc link video"
                                           oninput="handleEvaluationVideoUrlChange(this.value)">
                                    <button type="button" class="btn btn-outline-primary" onclick="openEvaluationVideoFinder()">
                                        <i class="ti ti-folder me-1"></i> {{ __('Chọn từ thư viện') }}
                                    </button>
                                </div>
                                <div class="form-text fs-12 text-muted">
                                    <i class="ti ti-info-circle"></i> {{ __('Hỗ trợ link video MP4, WebM, Cloudflare R2 hoặc URL trực tiếp.') }}
                                </div>

                                {{-- Khung Preview Video trực tiếp --}}
                                <div class="mt-3 p-3 bg-white border rounded-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-13 fw-semibold text-secondary"><i class="ti ti-eye me-1"></i> {{ __('Xem trước video') }}:</span>
                                        <span id="video_status_badge" class="badge bg-success-subtle text-success fs-11">{{ __('Sẵn sàng') }}</span>
                                    </div>
                                    <div class="ratio ratio-16x9 rounded-2 overflow-hidden bg-dark d-flex align-items-center justify-content-center" style="max-height: 320px;">
                                        <video id="evaluation_video_preview"
                                               controls
                                               preload="metadata"
                                               class="w-100 h-100"
                                               src="{{ $evaluationGuideVideo->plain_value }}"
                                               style="{{ empty($evaluationGuideVideo->plain_value) ? 'display: none;' : '' }}">
                                            {{ __('Trình duyệt không hỗ trợ thẻ video.') }}
                                        </video>
                                        <div id="evaluation_video_placeholder" class="text-center text-white-50 p-4" style="{{ !empty($evaluationGuideVideo->plain_value) ? 'display: none;' : '' }}">
                                            <i class="ti ti-video-off fs-1 d-block mb-2"></i>
                                            <span>{{ __('Chưa có video được chọn') }}</span>
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
    function handleEvaluationVideoUrlChange(url) {
        const videoEl = document.getElementById('evaluation_video_preview');
        const placeholderEl = document.getElementById('evaluation_video_placeholder');
        const badgeEl = document.getElementById('video_status_badge');

        if (!videoEl || !placeholderEl) return;

        const trimmedUrl = url ? url.trim() : '';
        if (trimmedUrl.length > 5) {
            videoEl.src = trimmedUrl;
            videoEl.style.display = 'block';
            placeholderEl.style.display = 'none';
            if (badgeEl) {
                badgeEl.className = 'badge bg-success-subtle text-success fs-11';
                badgeEl.innerText = 'Đã nhận link';
            }
        } else {
            videoEl.src = '';
            videoEl.style.display = 'none';
            placeholderEl.style.display = 'block';
            if (badgeEl) {
                badgeEl.className = 'badge bg-secondary-subtle text-secondary fs-11';
                badgeEl.innerText = 'Trống';
            }
        }
    }

    function openEvaluationVideoFinder() {
        if (typeof CKFinder !== 'undefined') {
            CKFinder.modal({
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function(finder) {
                    finder.on('files:choose', function(evt) {
                        var file = evt.data.files.first();
                        var inputEl = document.getElementById('evaluation_guide_video');
                        if (inputEl) {
                            inputEl.value = file.getUrl();
                            handleEvaluationVideoUrlChange(file.getUrl());
                        }
                    });
                }
            });
        } else {
            alert('Trình quản lý tệp tin chưa được tải. Vui lòng dán trực tiếp đường dẫn video vào ô nhập.');
        }
    }
</script>
