<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin nhóm tuổi') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tên nhóm tuổi') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" placeholder="Ví dụ: Thai giáo, 0-2 tuổi, 2-4 tuổi..." />
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Độ tuổi tối thiểu (tháng)') }}:</label>
                    <x-input type="number" name="min_months" :value="old('min_months')" min="0" placeholder="Để trống nếu là Thai giáo" />
                    <small class="text-muted">{{ __('Ví dụ: 0 tháng (đối với nhóm 0-2 tuổi)') }}</small>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Độ tuổi tối đa (tháng)') }}:</label>
                    <x-input type="number" name="max_months" :value="old('max_months')" min="0" placeholder="Để trống nếu là Thai giáo hoặc không giới hạn (14+)" />
                    <small class="text-muted">{{ __('Ví dụ: 24 tháng (đối với nhóm 0-2 tuổi)') }}</small>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Thứ tự sắp xếp') }}:</label>
                    <x-input type="number" name="sort_order" :value="old('sort_order', 0)" min="0" />
                </div>
            </div>
        </div>
    </div>
</div>
