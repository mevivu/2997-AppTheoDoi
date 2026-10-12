<div class="col-12 col-lg-8">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h3 class="card-title fw-bold">{{ __('Thông tin cột mốc') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Name -->
                <div class="col-md-8 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-flag text-primary me-1"></i>{{ __('Tên cột mốc') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('VD: Biết lẫy, Biết đi...') }}"/>
                </div>

                <!-- Slug -->
                <div class="col-md-4 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-link text-primary me-1"></i>{{ __('Slug (định danh)') }}:
                    </label>
                    <x-input name="slug" :value="old('slug')" placeholder="{{ __('Tự động tạo nếu để trống') }}"/>
                </div>

                <!-- Age Range (from - to in months) -->
                <div class="col-md-6 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-calendar text-primary me-1"></i>{{ __('Từ tháng tuổi') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="number" name="age_from_month" :value="old('age_from_month', 0)" :required="true" min="0"/>
                </div>

                <div class="col-md-6 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-calendar text-primary me-1"></i>{{ __('Đến tháng tuổi') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input type="number" name="age_to_month" :value="old('age_to_month', 12)" :required="true" min="0"/>
                </div>

                <!-- Position -->
                <div class="col-md-6 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-arrows-sort text-primary me-1"></i>{{ __('Thứ tự sắp xếp') }}:
                    </label>
                    <x-input type="number" name="position" :value="old('position', 0)" min="0"/>
                </div>

                <!-- Icon (optional URL or path) -->
                <div class="col-md-6 col-12">
                    <label class="control-label fw-bold">
                        <i class="ti ti-photo text-primary me-1"></i>{{ __('Icon (tuỳ chọn)') }}:
                    </label>
                    <x-input name="icon" :value="old('icon')" placeholder="{{ __('Đường dẫn icon / để trống dùng mặc định') }}"/>
                </div>
            </div>
        </div>
    </div>
</div>
