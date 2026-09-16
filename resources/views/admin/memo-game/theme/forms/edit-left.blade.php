<div class="col-12 col-md-8 col-xl-9">
    <div class="card custom-shadow">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Thông tin Chủ đề') }}</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Tên chủ đề') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="$response->name" :required="true" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Mã code chủ đề') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="code" :value="$response->code" :required="true" />
                        <small class="text-muted fs-12">{{ __('Mã duy nhất dùng để API đồng bộ với Mobile App') }}</small>
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
                        <label class="form-label fw-bold">{{ __('Ảnh mặt sau thẻ mặc định (Card Back)') }}:</label>
                        <x-input-image name="card_back" :value="$response->card_back" sub="{{ __('Tùy chọn: Dùng làm hình mặt sau của mọi thẻ trong chủ đề') }}" />
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
