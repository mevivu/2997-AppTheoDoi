<div class="col-12 col-lg-8">
    <div class="card custom-shadow">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Thông tin Chủ đề') }}</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Tên chủ đề') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="$response->name" :required="true" />
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Mã code chủ đề') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="code" :value="$response->code" :required="true" />
                        <small class="text-muted fs-12">{{ __('Mã duy nhất dùng để API đồng bộ') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Độ tuổi áp dụng') }}: <span class="text-danger">*</span></label>
                        <x-input type="number" name="age" :value="$response->age ?? 1" min="1" max="20" :required="true" placeholder="1" />
                        <small class="text-muted fs-12">{{ __('Tuổi làm bài test (VD: 1, 2...)') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Ảnh đại diện chủ đề (Icon / Banner)') }}:</label>
                        <x-input-image name="icon" :value="$response->icon" sub="{{ __('Ảnh icon hoặc biểu tượng của chủ đề (khuyến nghị vuông 1:1, tối đa 5MB)') }}" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Loại ảnh mặt úp ban đầu trên app') }}: <span class="text-danger">*</span></label>
                        <select name="card_back_type" class="form-select">
                            <option value="theme" {{ old('card_back_type', $response->card_back_type ?? 'theme') === 'theme' ? 'selected' : '' }}>🎨 {{ __('Ảnh chủ đề (Dùng ảnh mặt sau bên dưới)') }}</option>
                            <option value="logo" {{ old('card_back_type', $response->card_back_type ?? 'theme') === 'logo' ? 'selected' : '' }}>🏷️ {{ __('Ảnh logo (Dùng logo mặc định của app)') }}</option>
                        </select>
                        <small class="text-muted fs-12">{{ __('Chọn hiển thị ảnh Logo hay ảnh Chủ đề khi thẻ ở trạng thái úp') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Ảnh mặt sau thẻ mặc định (Card Back)') }}:</label>
                        <x-input-image name="card_back" :value="$response->card_back" sub="{{ __('Tùy chọn: Dùng làm hình mặt sau của mọi thẻ khi chọn loại Ảnh chủ đề') }}" />
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Mô tả chủ đề') }}:</label>
                        <textarea name="description" class="form-control" rows="3">{{ $response->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
