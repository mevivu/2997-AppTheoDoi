<div class="col-12 col-lg-8">
    {{-- Card 1: Nguồn Video YouTube & Xem trước thời gian thực --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-danger-lt rounded-circle">
                    <i class="ti ti-brand-youtube fs-3 text-danger"></i>
                </span>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">{{ __('Nguồn Video YouTube & Xem trước') }}</h5>
                    <small class="text-muted">{{ __('Nhập liên kết YouTube để hệ thống tự động nhận diện video và thời lượng') }}</small>
                </div>
            </div>
            <span id="yt_status_pill" class="badge bg-secondary-lt fs-12 px-2 py-1">
                <i class="ti ti-link"></i> {{ __('Chờ nhập link') }}
            </span>
        </div>

        <div class="card-body p-4">
            {{-- Input URL --}}
            <div class="mb-3">
                <label class="form-label fw-bold" for="youtube_video_url">
                    {{ __('Đường dẫn Video YouTube') }}: <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-flat shadow-none">
                    <span class="input-group-text bg-danger text-white px-3">
                        <i class="ti ti-brand-youtube fs-2"></i>
                    </span>
                    <input type="text"
                           name="video_url"
                           id="youtube_video_url"
                           class="form-control form-control-lg fs-14 @error('video_url') is-invalid @enderror"
                           placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/..."
                           value="{{ old('video_url') }}"
                           required
                           autocomplete="off"
                           inputmode="url"
                           aria-describedby="youtube_url_help youtube_url_error" />
                    <button class="btn btn-outline-secondary px-3" type="button" id="btn_clear_yt" title="{{ __('Xóa link') }}" style="display: none;">
                        <i class="ti ti-x"></i>
                    </button>
                    <button class="btn btn-danger px-3 d-flex align-items-center gap-1" type="button" id="btn_fetch_yt" title="{{ __('Lấy thông tin tự động') }}">
                        <i class="ti ti-sparkles"></i>
                        <span>{{ __('Tự động nhận diện') }}</span>
                    </button>
                </div>
                <div id="youtube_url_error" class="invalid-feedback">@error('video_url'){{ $message }}@else{{ __('Vui lòng nhập một liên kết YouTube hợp lệ.') }}@enderror</div>
                <div id="youtube_url_help" class="form-text mt-1 text-muted d-flex align-items-center gap-1">
                    <i class="ti ti-info-circle text-primary"></i>
                    {{ __('Hỗ trợ: Link video chuẩn, link rút gọn youtu.be, link Shorts hoặc link Embed.') }}
                </div>
            </div>

            {{-- Live YouTube Preview Box --}}
            <div id="yt_preview_container" class="rounded-3 border overflow-hidden mt-3" style="display: none; background: #0f172a;">
                {{-- Player Wrapper (Responsive 16:9) --}}
                <div class="position-relative" style="padding-top: 56.25%; background: #000;">
                    <div id="yt_player_placeholder" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-3 text-center">
                        <div class="spinner-border text-danger mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small>{{ __('Đang tải trình phát video...') }}</small>
                    </div>
                    <iframe id="yt_iframe"
                            class="position-absolute top-0 start-0 w-100 h-100 border-0"
                            title="YouTube video player"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            style="display: none;">
                    </iframe>
                </div>

                {{-- Player Metadata Bar --}}
                <div class="p-3 bg-white border-top">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-danger text-white fs-12 px-2 py-1">
                                <i class="ti ti-brand-youtube"></i> YouTube
                            </span>
                            <span class="badge bg-light text-dark border fs-12 px-2 py-1">
                                ID: <code class="text-danger fw-bold" id="yt_meta_id">—</code>
                            </span>
                            {{-- Live Auto Duration Badge --}}
                            <span class="badge bg-success-lt text-success border border-success fs-12 px-3 py-1 fw-bold d-flex align-items-center gap-1" id="yt_meta_duration_pill">
                                <i class="ti ti-clock"></i>
                                <span id="yt_meta_duration_text">{{ __('Đang tính thời lượng...') }}</span>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" id="btn_apply_title" style="display: none;">
                                <i class="ti ti-writing"></i> {{ __('Áp dụng tiêu đề từ YouTube') }}
                            </button>
                            <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" id="btn_watch_external">
                                <i class="ti ti-external-link"></i> {{ __('Mở YouTube') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Thông tin chi tiết video --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-sm bg-primary-lt rounded-circle">
                <i class="ti ti-file-description fs-3 text-primary"></i>
            </span>
            <h5 class="mb-0 fw-bold text-dark">{{ __('Thông tin chi tiết video') }}</h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-3">
                {{-- Tiêu đề --}}
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" for="video_title">
                            {{ __('Tiêu đề video') }}: <span class="text-danger">*</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fs-12 align-items-center gap-1 shadow-sm" id="btn_apply_title_badge" style="display: none;" title="{{ __('Nhấn để điền tiêu đề từ YouTube') }}">
                            <i class="ti ti-sparkles text-warning"></i> <span id="btn_apply_title_badge_text">{{ __('Áp dụng tiêu đề YouTube') }}</span>
                        </button>
                    </div>
                    <input type="text"
                           name="title"
                           id="video_title"
                           class="form-control form-control-lg fs-14 @error('title') is-invalid @enderror"
                           placeholder="Ví dụ: Bắt bóng rèn luyện phản xạ cấp độ 1..."
                           value="{{ old('title') }}"
                           maxlength="300"
                           aria-describedby="video_title_counter"
                           required />
                    <div class="d-flex justify-content-between mt-1">
                        @error('title')<div class="text-danger fs-12">{{ $message }}</div>@else<span></span>@enderror
                        <small id="video_title_counter" class="text-muted field-counter">0/300</small>
                    </div>
                </div>

                {{-- Danh mục theo nhóm tuổi --}}
                <div class="col-12">
                    <label class="form-label fw-bold" for="video_category_id">
                        {{ __('Danh mục video (theo nhóm tuổi)') }}: <span class="text-danger">*</span>
                    </label>
                    <select name="video_category_id" id="video_category_id" class="form-select form-select-lg fs-14 @error('video_category_id') is-invalid @enderror" required>
                        <option value="">-- {{ __('Chọn danh mục phù hợp') }} --</option>
                        @isset($categoriesByAge)
                            @foreach ($categoriesByAge as $ageGroupName => $cats)
                                <optgroup label="👶 {{ __('Nhóm tuổi') }}: {{ $ageGroupName }}">
                                    @foreach ($cats as $cat)
                                        <option value="{{ $cat->id }}" {{ old('video_category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @else
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('video_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->ageGroup?->name ?? 'Tất cả' }})
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('video_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted mt-1 d-block">{{ __('Danh mục được nhóm theo độ tuổi tương ứng để phụ huynh dễ dàng tìm kiếm.') }}</small>
                </div>

                {{-- Thời lượng video --}}
                <div class="col-md-6 col-12">
                    <label class="form-label fw-bold d-flex justify-content-between align-items-center" for="duration_seconds">
                        <span>{{ __('Thời lượng video') }}:</span>
                        <span class="badge bg-blue-lt fs-11" id="duration_formatted_badge">00:00</span>
                    </label>
                    
                    {{-- Duration Converter UI --}}
                    <div class="p-3 bg-light rounded-2 border">
                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group input-group-sm">
                                    <input type="number" id="calc_minutes" class="form-control text-center fw-bold" min="0" placeholder="0" />
                                    <span class="input-group-text">{{ __('phút') }}</span>
                                </div>
                            </div>
                            <div class="col-1 text-center fw-bold text-muted">:</div>
                            <div class="col-5">
                                <div class="input-group input-group-sm">
                                    <input type="number" id="calc_seconds" class="form-control text-center fw-bold" min="0" max="59" placeholder="0" />
                                    <span class="input-group-text">{{ __('giây') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between">
                            <small class="text-muted">{{ __('Tổng số giây:') }}</small>
                            <div class="d-flex align-items-center gap-1" style="max-width: 140px;">
                                <input type="number"
                                       name="duration_seconds"
                                       id="duration_seconds"
                                       class="form-control form-control-sm text-end fw-bold text-primary"
                                       min="0"
                                       value="{{ old('duration_seconds', 0) }}" />
                                <small class="text-muted">s</small>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="ti ti-wand text-success"></i> {{ __('Tự động đếm và điền khi dán link YouTube. Có thể chỉnh sửa thủ công.') }}
                    </small>
                </div>

                {{-- Thứ tự sắp xếp --}}
                <div class="col-md-6 col-12">
                    <label class="form-label fw-bold" for="sort_order">
                        {{ __('Thứ tự sắp xếp') }}:
                    </label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="stepDownSortOrder()"><i class="ti ti-minus"></i></button>
                        <input type="number"
                               name="sort_order"
                               id="sort_order"
                               class="form-control text-center fw-bold"
                               min="0"
                               value="{{ old('sort_order', 0) }}" />
                        <button type="button" class="btn btn-outline-secondary" onclick="stepUpSortOrder()"><i class="ti ti-plus"></i></button>
                    </div>
                    <small class="text-muted mt-1 d-block">{{ __('Số nhỏ hơn sẽ hiển thị trước trên app mobile.') }}</small>
                </div>

                {{-- Mô tả nội dung --}}
                <div class="col-12 mt-3">
                    <label class="form-label fw-bold">
                        {{ __('Mô tả nội dung video') }}:
                    </label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden container for YouTube IFrame API audio/duration probe --}}
<div id="yt_hidden_probe" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;"></div>

@push('custom-js')
<script>
    // 1. YouTube IFrame API loader
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    let ytPlayer = null;
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
        document.getElementById('duration_seconds').value = totalSec;
        document.getElementById('calc_minutes').value = Math.floor(totalSec / 60);
        document.getElementById('calc_seconds').value = totalSec % 60;

        const formatted = formatSeconds(totalSec);
        document.getElementById('duration_formatted_badge').textContent = formatted.split(' ')[0];
        document.getElementById('yt_meta_duration_text').textContent = '⏱️ ' + formatted;
    }

    function syncFromCalcInputs() {
        const m = parseInt(document.getElementById('calc_minutes').value) || 0;
        const s = parseInt(document.getElementById('calc_seconds').value) || 0;
        const total = m * 60 + s;
        document.getElementById('duration_seconds').value = total;
        document.getElementById('duration_formatted_badge').textContent = formatSeconds(total).split(' ')[0];
        document.getElementById('yt_meta_duration_text').textContent = '⏱️ ' + formatSeconds(total);
    }

    function stepUpSortOrder() {
        const inp = document.getElementById('sort_order');
        inp.value = (parseInt(inp.value) || 0) + 1;
    }

    function stepDownSortOrder() {
        const inp = document.getElementById('sort_order');
        inp.value = Math.max(0, (parseInt(inp.value) || 0) - 1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputUrl = document.getElementById('youtube_video_url');
        const previewContainer = document.getElementById('yt_preview_container');
        const iframe = document.getElementById('yt_iframe');
        const placeholder = document.getElementById('yt_player_placeholder');
        const metaId = document.getElementById('yt_meta_id');
        const watchExternal = document.getElementById('btn_watch_external');
        const btnClear = document.getElementById('btn_clear_yt');
        const btnFetch = document.getElementById('btn_fetch_yt');
        const statusPill = document.getElementById('yt_status_pill');
        const btnApplyTitle = document.getElementById('btn_apply_title');
        const btnApplyTitleBadge = document.getElementById('btn_apply_title_badge');
        const inputTitle = document.getElementById('video_title');
        const titleCounter = document.getElementById('video_title_counter');

        const calcM = document.getElementById('calc_minutes');
        const calcS = document.getElementById('calc_seconds');
        const durationSec = document.getElementById('duration_seconds');

        // Init duration if pre-filled
        if (durationSec.value > 0) {
            updateDurationInputs(durationSec.value);
        }

        calcM.addEventListener('input', syncFromCalcInputs);
        calcS.addEventListener('input', syncFromCalcInputs);
        durationSec.addEventListener('input', function () {
            updateDurationInputs(this.value);
        });

        function updateTitleCounter() {
            if (titleCounter && inputTitle) {
                titleCounter.textContent = `${inputTitle.value.length}/300`;
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
            // Điền ngay nếu tiêu đề đang trống, hoặc force = true (bấm nhận diện / dán link), hoặc ô này từng do youtube điền
            const currentVal = inputTitle.value.trim();
            if (!currentVal || force || inputTitle.dataset.autoYt === 'true') {
                inputTitle.value = detectedYtTitle;
                inputTitle.dataset.autoYt = 'true';
                updateTitleCounter();
                highlightTitleField();
            }
        }

        function handleUrlChange(manualTrigger = false) {
            const raw = inputUrl.value.trim();
            btnClear.style.display = raw ? 'block' : 'none';

            const ytId = parseYouTubeId(raw);
            if (!ytId) {
                activeYtId = '';
                stopYouTubeProbe();
                if (metadataController) metadataController.abort();
                iframe.removeAttribute('src');
                previewContainer.style.display = 'none';
                statusPill.className = 'badge bg-secondary-lt fs-12 px-2 py-1';
                statusPill.innerHTML = '<i class="ti ti-link"></i> Chờ nhập link';
                if (btnApplyTitle) btnApplyTitle.style.display = 'none';
                if (btnApplyTitleBadge) btnApplyTitleBadge.style.display = 'none';
                const thumbEmpty = document.getElementById('yt_auto_thumb_empty');
                const thumbPreview = document.getElementById('yt_auto_thumb_preview');
                if (thumbEmpty) thumbEmpty.style.display = 'flex';
                if (thumbPreview) thumbPreview.style.display = 'none';
                inputUrl.classList.toggle('is-invalid', manualTrigger && raw.length > 0);
                if (manualTrigger && raw.length > 0) inputUrl.focus();
                return;
            }

            // Valid ID
            inputUrl.classList.remove('is-invalid');
            metaId.textContent = ytId;
            watchExternal.href = `https://www.youtube.com/watch?v=${ytId}`;
            previewContainer.style.display = 'block';
            statusPill.className = 'badge bg-success fs-12 px-2 py-1';
            statusPill.innerHTML = '<i class="ti ti-check"></i> Đã nhận diện YouTube';

            // Update thumbnail preview in sidebar if available
            const sideThumb = document.getElementById('yt_auto_thumb_img');
            if (sideThumb) {
                sideThumb.src = `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`;
                document.getElementById('yt_auto_thumb_empty').style.display = 'none';
                document.getElementById('yt_auto_thumb_preview').style.display = 'block';
            }

            // Load iframe
            placeholder.style.display = 'none';
            iframe.style.display = 'block';
            iframe.src = `https://www.youtube.com/embed/${ytId}?enablejsapi=1&origin=${window.location.origin}`;

            // Fetch video info & duration via internal API / YouTube probe
            if (activeYtId !== ytId || manualTrigger) {
                activeYtId = ytId;
                fetchVideoMetadata(ytId, raw, manualTrigger);
            }
        }

        function fetchVideoMetadata(ytId, rawUrl, manualTrigger = false) {
            document.getElementById('yt_meta_duration_text').textContent = 'Đang tự động đo thời lượng...';

            stopYouTubeProbe();
            if (metadataController) metadataController.abort();
            metadataController = new AbortController();

            // 1. Primary: internal API endpoint (server-to-server YouTube oEmbed, 100% reliable)
            const targetUrl = rawUrl || inputUrl.value.trim() || `https://www.youtube.com/watch?v=${ytId}`;
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
                    // Fallback to noembed
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

            // 2. Measure duration & title fallback using YouTube IFrame API Player
            let retryCount = 0;
            function measureDurationWithYT() {
                if (!window.YT || !window.YT.Player) {
                    retryCount += 1;
                    if (retryCount < 20 && activeYtId === ytId) {
                        probeRetryTimer = setTimeout(measureDurationWithYT, 300);
                    } else if (activeYtId === ytId) {
                        document.getElementById('yt_meta_duration_text').textContent = 'Không thể tự đo thời lượng';
                    }
                    return;
                }

                if (ytProbePlayer) {
                    try { ytProbePlayer.destroy(); } catch (e) {}
                }

                const probeDiv = document.createElement('div');
                probeDiv.id = 'probe_' + Date.now();
                document.getElementById('yt_hidden_probe').appendChild(probeDiv);

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
                    if (activeYtId === ytId && !parseInt(durationSec.value)) {
                        document.getElementById('yt_meta_duration_text').textContent = 'Vui lòng nhập thời lượng thủ công';
                    }
                }, 8000);
            }

            measureDurationWithYT();
        }

        function handleApplyTitleClick() {
            if (detectedYtTitle) {
                inputTitle.value = detectedYtTitle;
                inputTitle.dataset.autoYt = 'true';
                updateTitleCounter();
                inputTitle.focus();
                highlightTitleField();
            }
        }

        if (btnApplyTitle) btnApplyTitle.addEventListener('click', handleApplyTitleClick);
        if (btnApplyTitleBadge) btnApplyTitleBadge.addEventListener('click', handleApplyTitleClick);

        btnClear.addEventListener('click', function () {
            inputUrl.value = '';
            handleUrlChange();
            inputUrl.focus();
        });

        btnFetch.addEventListener('click', function () {
            handleUrlChange(true);
        });

        inputUrl.addEventListener('input', function () {
            clearTimeout(urlInputTimer);
            urlInputTimer = setTimeout(() => handleUrlChange(false), 450);
        });

        inputUrl.addEventListener('paste', function () {
            clearTimeout(urlInputTimer);
            urlInputTimer = setTimeout(() => handleUrlChange(true), 100);
        });

        window.addEventListener('beforeunload', stopYouTubeProbe);

        // Trigger on initial load if URL is present (e.g. old() value)
        if (inputUrl.value.trim()) {
            handleUrlChange(false);
        }
    });
</script>
@endpush
