{{-- Modal Chi tiết Đánh giá Bài học --}}
<div class="modal fade" id="lessonRatingModal" tabindex="-1" aria-labelledby="lessonRatingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Header --}}
            <div class="modal-header px-4 py-3 bg-white border-bottom" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar avatar-md rounded-circle bg-primary-lt text-primary shadow-sm">
                        <i class="ti ti-chart-pie fs-2"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold mb-1 text-dark fs-18" id="lessonRatingModalLabel">
                            {{ __('Chi tiết đánh giá bài học') }}
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-blue-lt text-blue rounded-pill px-2.5 py-0.5 fs-12">
                                <i class="ti ti-calendar me-1"></i><span id="modal_lesson_age_group">—</span>
                            </span>
                            <span class="badge bg-secondary-lt text-secondary rounded-pill px-2.5 py-0.5 fs-12" id="modal_total_badge">
                                0 đánh giá
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4 bg-light-subtle">
                {{-- Loading Spinner --}}
                <div id="modal_loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <div class="text-muted mt-3 fs-14 fw-medium">{{ __('Đang tải dữ liệu đánh giá...') }}</div>
                </div>

                {{-- Content Container --}}
                <div id="modal_content" style="display: none;">
                    {{-- Lesson Header Card --}}
                    <div class="card border border-light-subtle rounded-3 p-3 mb-3 shadow-sm bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <img id="modal_lesson_thumb" src="" alt="" class="rounded-3 shadow-sm border" style="width: 84px; height: 56px; object-fit: cover; background: #f8fafc;" onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}';">
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                    <i class="ti ti-book me-1 text-primary"></i>{{ __('Tên bài học') }}
                                </div>
                                <h6 class="fw-bold mb-0 text-dark fs-16 text-truncate" id="modal_lesson_name">—</h6>
                            </div>
                        </div>
                    </div>

                    {{-- 3 Upgraded Pastel Stat Cards --}}
                    <div class="row g-3 mb-4">
                        {{-- Card Dễ --}}
                        <div class="col-4">
                            <div class="p-3 rounded-3 h-100 position-relative overflow-hidden shadow-sm" 
                                 style="background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-white text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold fs-11 shadow-none">
                                        😊 {{ __('DỄ') }}
                                    </span>
                                    <span class="fs-18">🌟</span>
                                </div>
                                <div class="h2 fw-bold text-success mb-1" id="modal_easy_pct">0%</div>
                                <div class="text-success-emphasis fs-12 fw-medium">
                                    <span id="modal_easy_count" class="fw-bold">0</span> {{ __('lượt đánh giá') }}
                                </div>
                                <div class="progress mt-2 rounded-pill bg-white shadow-none" style="height: 6px;">
                                    <div id="modal_easy_bar" class="progress-bar bg-success" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Có trợ giúp --}}
                        <div class="col-4">
                            <div class="p-3 rounded-3 h-100 position-relative overflow-hidden shadow-sm" 
                                 style="background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-white text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold fs-11 shadow-none">
                                        👋 {{ __('CẦN TRỢ GIÚP') }}
                                    </span>
                                    <span class="fs-18">🤝</span>
                                </div>
                                <div class="h2 fw-bold text-warning mb-1" id="modal_with_help_pct">0%</div>
                                <div class="text-warning-emphasis fs-12 fw-medium">
                                    <span id="modal_with_help_count" class="fw-bold">0</span> {{ __('lượt đánh giá') }}
                                </div>
                                <div class="progress mt-2 rounded-pill bg-white shadow-none" style="height: 6px;">
                                    <div id="modal_with_help_bar" class="progress-bar bg-warning" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Khó --}}
                        <div class="col-4">
                            <div class="p-3 rounded-3 h-100 position-relative overflow-hidden shadow-sm" 
                                 style="background: linear-gradient(145deg, #fff1f2 0%, #ffe4e6 100%); border: 1px solid #fecdd3;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-white text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold fs-11 shadow-none">
                                        🔥 {{ __('KHÓ') }}
                                    </span>
                                    <span class="fs-18">⚡</span>
                                </div>
                                <div class="h2 fw-bold text-danger mb-1" id="modal_hard_pct">0%</div>
                                <div class="text-danger-emphasis fs-12 fw-medium">
                                    <span id="modal_hard_count" class="fw-bold">0</span> {{ __('lượt đánh giá') }}
                                </div>
                                <div class="progress mt-2 rounded-pill bg-white shadow-none" style="height: 6px;">
                                    <div id="modal_hard_bar" class="progress-bar bg-danger" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ratings History Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="avatar avatar-xs rounded bg-primary-lt text-primary">
                                <i class="ti ti-list-check fs-13"></i>
                            </span>
                            <span>{{ __('Danh sách phản hồi từ phụ huynh') }}</span>
                        </h6>
                        <small class="text-muted fw-normal fs-12" id="modal_history_note">
                            <i class="ti ti-clock me-1"></i>{{ __('Tối đa 50 lượt gần nhất') }}
                        </small>
                    </div>

                    {{-- Ratings History Table Card --}}
                    <div class="card border border-light-subtle rounded-3 overflow-hidden mb-0 shadow-sm bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="text-center py-2.5 text-muted fw-bold" style="width: 45px;">#</th>
                                        <th class="py-2.5 text-muted fw-bold">{{ __('PHỤ HUYNH') }}</th>
                                        <th class="py-2.5 text-muted fw-bold">{{ __('BÉ') }}</th>
                                        <th class="text-center py-2.5 text-muted fw-bold">{{ __('MỨC ĐÁNH GIÁ') }}</th>
                                        <th class="text-center py-2.5 text-muted fw-bold">{{ __('THỜI GIAN') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="modal_ratings_tbody">
                                    {{-- Populated by JS --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Empty State --}}
                        <div id="modal_empty_ratings" class="text-center py-5 text-muted" style="display: none;">
                            <div class="avatar avatar-lg rounded-circle bg-light text-secondary mb-3">
                                <i class="ti ti-mood-empty fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ __('Chưa có phản hồi nào') }}</h6>
                            <p class="mb-0 fs-13 text-secondary">{{ __('Bài học này hiện chưa nhận được đánh giá độ khó từ phụ huynh.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-between align-items-center">
                <div class="text-muted fs-12 d-flex align-items-center gap-1">
                    <i class="ti ti-info-circle text-primary fs-14"></i>
                    <span>{{ __('Dữ liệu đánh giá từ phụ huynh qua ứng dụng mobile') }}</span>
                </div>
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-none fw-semibold" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>{{ __('Đóng') }}
                </button>
            </div>
        </div>
    </div>
</div>
