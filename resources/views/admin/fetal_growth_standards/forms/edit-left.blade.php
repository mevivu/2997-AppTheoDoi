<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin tiêu chuẩn tuần thai') }}</h2>
        </div>
        <div class="row card-body">
            <!-- Tuần thai (week) -->
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-calendar"></span>
                        {{ __('Tuần thai') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="number" name="week" :value="old('week', $response->week)" :required="true" min="1" max="50" placeholder="{{ __('VD: 20') }}" />
                </div>
            </div>

            <!-- Chiều dài chuẩn (length - cm) -->
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-ruler"></span>
                        {{ __('Chiều dài chuẩn (cm)') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="number" step="0.01" name="length" :value="old('length', $response->length)" :required="true" min="0" placeholder="{{ __('VD: 25.6') }}" />
                </div>
            </div>

            <!-- Cân nặng chuẩn (weight - g) -->
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-scale"></span>
                        {{ __('Cân nặng chuẩn (g)') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="number" step="0.01" name="weight" :value="old('weight', $response->weight)" :required="true" min="0" placeholder="{{ __('VD: 300') }}" />
                </div>
            </div>

            <!-- Chu vi đầu (head_circumference - cm) -->
            <div class="col-12 col-md-4">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-circle"></span>
                        {{ __('Chu vi đầu (cm)') }}:
                    </label>
                    <x-input type="number" step="0.01" name="head_circumference" :value="old('head_circumference', $response->head_circumference)" min="0" placeholder="{{ __('VD: 17.5') }}" />
                </div>
            </div>

            <!-- Mô tả phát triển tuần thai (description) -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-notes"></span>
                        {{ __('Mô tả sự phát triển trong tuần') }}:
                    </label>
                    <textarea name="description" class="form-control" rows="4" placeholder="{{ __('Mô tả ngắn gọn về đặc điểm của thai nhi trong tuần này...') }}">{{ old('description', $response->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
