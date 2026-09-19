<div class="col-12 col-lg-8 col-xl-9">
    <div class="quiz-form-card">
        <div class="card-header">
            <h5 class="header-title">
                <i class="ti ti-info-circle text-primary fs-4"></i>
                {{ __('Thông tin chung bài kiểm tra') }}
            </h5>
            <span class="badge bg-primary-lt fs-12 fw-bold">
                {{ __('Tạo mới') }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @if (request()->routeIs('admin.quiz.createIq') || (isset($selectedType) && ($selectedType == \App\Enums\Question\QuestionType::IQ->value || $selectedType == \App\Enums\Question\QuestionType::IQ)))
                    <!-- title -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark">@lang('title') <span class="text-danger">*</span></label>
                        <x-input name="title" :value="old('title')" :required="true" :placeholder="__('Nhập tên bài kiểm tra...')" />
                    </div>

                    <!-- age -->
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold text-dark">@lang('age') (tuổi) <span class="text-danger">*</span></label>
                        <x-input name="age" type="number" :value="old('age')" :required="true" :placeholder="__('Độ tuổi (VD: 1, 2, 3...)')" min="1" max="100" />
                    </div>

                    <!-- game_plays -->
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold text-dark">{{ __('Số lần chơi game') }} <span class="text-danger">*</span></label>
                        <x-input name="game_plays" type="number" :value="old('game_plays', 3)" :required="true" :placeholder="__('Mặc định: 3')" min="1" max="20" />
                        <small class="form-hint text-muted">{{ __('1 lần = 1 điểm (Mặc định: 3)') }}</small>
                    </div>
                @else
                    <!-- title -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">@lang('title') <span class="text-danger">*</span></label>
                        <x-input name="title" :value="old('title')" :required="true" :placeholder="__('Nhập tên bài kiểm tra...')" />
                    </div>
                @endif

                <!-- type -->
                <input type="hidden" name="type" value="{{ $selectedType }}">

                <!-- description -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">@lang('description')</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="{{ __('Nhập mô tả hoặc hướng dẫn làm bài kiểm tra cho người dùng...') }}">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
