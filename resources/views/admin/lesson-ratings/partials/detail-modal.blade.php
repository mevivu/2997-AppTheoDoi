{{-- Modal Chi tiết Đánh giá Bài học --}}
<div class="modal fade" id="lessonRatingModal" tabindex="-1" aria-labelledby="lessonRatingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm bg-primary-lt rounded-circle">
                        <i class="ti ti-chart-bar fs-4 text-primary"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="lessonRatingModalLabel">
                            {{ __('Chi tiết đánh giá bài học') }}
                        </h5>
                        <small class="text-muted" id="modal_lesson_age_group">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Loading Spinner --}}
                <div id="modal_loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <div class="text-muted mt-2 fs-14">{{ __('Đang tải dữ liệu đánh giá...') }}</div>
                </div>

                {{-- Content Container --}}
                <div id="modal_content" style="display: none;">
                    {{-- Lesson Header & Quick Stats --}}
                    <div class="card bg-light border-0 mb-3 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-3">
                                <img id="modal_lesson_thumb" src="" alt="" class="rounded shadow-sm" style="width: 72px; height: 48px; object-fit: cover;">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="fw-bold mb-1 text-dark text-truncate" id="modal_lesson_name">—</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-lt fs-12 px-2 py-0" id="modal_total_badge">0 đánh giá</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Breakdown Bars --}}
                            <div class="mt-3 pt-3 border-top">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white border border-success-subtle">
                                            <div class="fs-18 mb-1">😊</div>
                                            <div class="fw-bold text-success fs-15" id="modal_easy_pct">0%</div>
                                            <small class="text-muted fs-12"><span id="modal_easy_count">0</span> lượt (Dễ)</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white border border-warning-subtle">
                                            <div class="fs-18 mb-1">👋</div>
                                            <div class="fw-bold text-warning fs-15" id="modal_with_help_pct">0%</div>
                                            <small class="text-muted fs-12"><span id="modal_with_help_count">0</span> lượt (Cần giúp)</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white border border-danger-subtle">
                                            <div class="fs-18 mb-1">🔥</div>
                                            <div class="fw-bold text-danger fs-15" id="modal_hard_pct">0%</div>
                                            <small class="text-muted fs-12"><span id="modal_hard_count">0</span> lượt (Khó)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ratings History List --}}
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="ti ti-history me-1 text-primary"></i>{{ __('Lịch sử đánh giá gần nhất') }}</span>
                        <small class="text-muted fw-normal fs-12" id="modal_history_note">{{ __('Tối đa 50 lượt mới nhất') }}</small>
                    </h6>

                    <div class="table-responsive rounded border">
                        <table class="table table-hover table-striped align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>{{ __('Phụ huynh') }}</th>
                                    <th>{{ __('Bé') }}</th>
                                    <th class="text-center">{{ __('Mức đánh giá') }}</th>
                                    <th class="text-center">{{ __('Thời gian') }}</th>
                                </tr>
                            </thead>
                            <tbody id="modal_ratings_tbody">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Empty State --}}
                    <div id="modal_empty_ratings" class="text-center py-4 text-muted" style="display: none;">
                        <i class="ti ti-mood-empty fs-1 text-secondary mb-2"></i>
                        <p class="mb-0 fs-13">{{ __('Chưa có phụ huynh nào đánh giá bài học này.') }}</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 border-top">
                <button type="button" class="btn btn-secondary px-4 shadow-none" data-bs-dismiss="modal">
                    {{ __('Đóng') }}
                </button>
            </div>
        </div>
    </div>
</div>
