@php use App\Enums\Question\QuestionType; @endphp
<div class="col-12 col-lg-8 col-xl-9">
    <!-- Card 1: Thông tin cơ bản bài kiểm tra -->
    <div class="quiz-form-card">
        <div class="card-header">
            <h5 class="header-title">
                <i class="ti ti-info-circle text-primary fs-4"></i>
                {{ __('Thông tin chung bài kiểm tra') }}
            </h5>
            <span class="badge bg-purple-lt fs-12 fw-bold">
                {{ $instance->type?->description() ?? $instance->type }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- title -->
                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold text-dark">@lang('title') <span class="text-danger">*</span></label>
                    <x-input type="text" name="title" :value="$instance->title" :required="true"
                             :placeholder="__('Nhập tên bài kiểm tra...')" />
                </div>

                @if ($instance->type == QuestionType::IQ || $instance->type->value == QuestionType::IQ->value)
                    <!-- age -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold text-dark">@lang('age') (tuổi) <span class="text-danger">*</span></label>
                        <x-input type="number" name="age" :value="$instance->age" :required="true"
                                 :placeholder="__('Độ tuổi (VD: 1, 2, 3...)')" min="1" max="100" />
                    </div>
                @endif

                <!-- type (hidden + display badge) -->
                <input type="hidden" name="type" id="type-select" value="{{ $selectedType }}">

                <!-- description -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">@lang('description')</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="{{ __('Nhập mô tả hoặc hướng dẫn làm bài kiểm tra cho người dùng...') }}">{{ $instance->description }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Quản lý danh sách câu hỏi (Split-Pane Questions Manager) -->
    <div class="quiz-form-card">
        <div class="card-header">
            <h5 class="header-title">
                <i class="ti ti-list-check text-primary fs-4"></i>
                {{ __('Quản lý bộ câu hỏi bài kiểm tra') }}
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-lt fs-13 py-2 px-3 fw-bold">
                    <span id="header-checked-count">{{ $selected_questions->count() }}</span> {{ __('câu hỏi đã chọn') }}
                </span>
            </div>
        </div>
        <div class="card-body p-3">
            <!-- Note banner & Quy định số lượng câu hỏi IQ -->
            <div id="iq-rule-alert" class="alert alert-info d-flex align-items-center py-2 px-3 mb-3 border-0 bg-info-lt rounded-3">
                <i class="ti ti-info-circle fs-3 me-2 text-info flex-shrink-0"></i>
                <div class="fs-13">
                    <strong>{{ __('Quy định số lượng:') }}</strong>
                    {{ __('Bài kiểm tra IQ bắt buộc phải có đúng') }} <strong class="text-primary fs-14">15</strong> {{ __('câu hỏi.') }}
                    <span id="iq-validation-notice" class="ms-2 fw-semibold"></span>
                </div>
            </div>

            <div class="row g-3">
                <!-- Cột Trái: Ngân hàng câu hỏi khả dụng -->
                <div class="col-12 col-md-6">
                    <div class="quiz-split-panel">
                        <div class="panel-top">
                            <div class="panel-title mb-2">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="ti ti-books text-primary"></i>
                                    {{ __('Ngân hàng câu hỏi') }}
                                </span>
                                <span id="available-count" class="badge bg-light text-muted fw-bold">0</span>
                            </div>
                            <!-- Thanh tìm kiếm & lọc -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" id="search-keyword" class="form-control border-start-0"
                                       placeholder="{{ __('Tìm mã hoặc nội dung...') }}">
                                <input type="number" id="search-age" class="form-control"
                                       placeholder="{{ __('Tuổi') }}" min="1" max="100"
                                       style="max-width: 65px;">
                                <button class="btn btn-outline-secondary" type="button" id="clear-button" title="{{ __('Tải lại') }}">
                                    <i class="ti ti-rotate-clockwise"></i>
                                </button>
                            </div>
                        </div>

                        <div class="panel-scroll-body" id="available-panel-body">
                            <div id="loading" class="text-center py-4" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <div class="text-muted fs-12 mt-2">{{ __('Đang tải câu hỏi...') }}</div>
                            </div>
                            <div id="questions-container"></div>
                        </div>
                    </div>
                </div>

                <!-- Cột Phải: Bộ câu hỏi đã chọn trong bài test -->
                <div class="col-12 col-md-6">
                    <div class="quiz-split-panel">
                        <div class="panel-top">
                            <div class="panel-title mb-1">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="ti ti-checklist text-success"></i>
                                    {{ __('Câu hỏi trong bài test') }}
                                </span>
                                <span id="checked-count" class="badge bg-success fw-bold">{{ $selected_questions->count() }}</span>
                            </div>
                            <small class="text-muted d-block fs-11">
                                <i class="ti ti-arrows-sort me-1"></i>{{ __('Kéo biểu tượng ⠿ để sắp xếp lại thứ tự câu hỏi') }}
                            </small>
                        </div>

                        <div class="panel-scroll-body" id="selected-panel-body">
                            <div id="loading-indicator" class="text-center py-4" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                <div class="text-muted fs-12 mt-2">{{ __('Đang cập nhật danh sách...') }}</div>
                            </div>
                            <input type="hidden" id="selected_questions_input" name="selected_questions">
                            <div id="selected-questions" class="sortable-list"></div>
                            <div id="empty-selected-box" class="quiz-empty-box" style="display: none;">
                                <i class="ti ti-clipboard-list"></i>
                                <div class="empty-text">{{ __('Chưa có câu hỏi nào trong bài test.') }}</div>
                                <small class="text-muted d-block mt-1">{{ __('Nhấp vào câu hỏi ở cột bên trái để thêm vào bài kiểm tra.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
