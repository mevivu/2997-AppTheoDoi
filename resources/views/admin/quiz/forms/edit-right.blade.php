@php use App\Enums\Question\QuestionType; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Thống kê nhanh bài test -->
    <div class="quiz-summary-stat-box" id="sidebar-summary-box">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="stat-label mb-0">
                <i class="ti ti-award me-1"></i>{{ __('Tổng câu hỏi đã chọn') }}
            </div>
            <span id="sidebar-validation-badge" class="badge bg-success-lt fw-bold fs-11">
                <i class="ti ti-check me-1"></i>{{ __('15/15 câu') }}
            </span>
        </div>
        <div class="d-flex align-items-baseline gap-2">
            <span class="stat-count" id="sidebar-selected-count">{{ $selected_questions->count() }}</span>
            <span class="fs-13 fw-semibold text-primary">/ 15 {{ __('câu') }}</span>
        </div>
        <div class="stat-sub" id="sidebar-status-text">
            <i class="ti ti-clock me-1"></i>{{ __('Thời gian làm bài: ~') }}<span id="estimated-time">{{ round($selected_questions->count() * 1.5) }}</span> {{ __('phút') }}
        </div>
    </div>

    <!-- Trạng thái -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.02rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-3">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :selected="$instance->status->value == $key"/>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route($route)"
        :back-title="__('Quay lại')"
    />
</div>
