<div class="row g-4">
    <!-- Cột Bố -->
    <div class="col-12 col-lg-6">
        <div class="parent-box-card">
            <div class="card-header-father d-flex align-items-center">
                <span class="badge bg-primary text-white p-2 me-2 rounded-circle">
                    <i class="ti ti-user-check fs-4"></i>
                </span>
                <div>
                    <h5 class="mb-0 fw-bold text-primary">{{ __('Thông Tin Bố') }}</h5>
                    <small class="text-muted">{{ __('Chỉ số và thông tin cá nhân của bố') }}</small>
                </div>
            </div>

            <div class="p-4">
                <!-- Tên của bố -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-user text-primary me-1"></i>
                        {{ __('Họ và tên của bố') }}:
                    </label>
                    <x-input name="father_name" :value="$user->father_name" placeholder="{{ __('Nhập họ và tên của bố') }}" />
                </div>

                <!-- Chiều cao của bố -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-ruler-2 text-primary me-1"></i>
                        {{ __('Chiều cao của bố (cm)') }}:
                    </label>
                    <div class="input-group">
                        <x-input type="number" name="father_height" :value="$user->father_height" placeholder="{{ __('Ví dụ: 172') }}" min="0" />
                        <span class="input-group-text bg-light text-muted">cm</span>
                    </div>
                </div>

                <!-- Ngày sinh của bố -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-calendar text-primary me-1"></i>
                        {{ __('Ngày sinh của bố') }}:
                    </label>
                    <x-input type="date" name="father_birthday" :value="$user->father_birthday ? format_date($user->father_birthday, 'Y-m-d') : null" />
                </div>
            </div>
        </div>
    </div>

    <!-- Cột Mẹ -->
    <div class="col-12 col-lg-6">
        <div class="parent-box-card">
            <div class="card-header-mother d-flex align-items-center">
                <span class="badge bg-danger text-white p-2 me-2 rounded-circle">
                    <i class="ti ti-user-heart fs-4"></i>
                </span>
                <div>
                    <h5 class="mb-0 fw-bold text-danger">{{ __('Thông Tin Mẹ') }}</h5>
                    <small class="text-muted">{{ __('Chỉ số và thông tin cá nhân của mẹ') }}</small>
                </div>
            </div>

            <div class="p-4">
                <!-- Tên của mẹ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-user text-danger me-1"></i>
                        {{ __('Họ và tên của mẹ') }}:
                    </label>
                    <x-input name="mother_name" :value="$user->mother_name" placeholder="{{ __('Nhập họ và tên của mẹ') }}" />
                </div>

                <!-- Chiều cao của mẹ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-ruler-2 text-danger me-1"></i>
                        {{ __('Chiều cao của mẹ (cm)') }}:
                    </label>
                    <div class="input-group">
                        <x-input type="number" name="mother_height" :value="$user->mother_height" placeholder="{{ __('Ví dụ: 160') }}" min="0" />
                        <span class="input-group-text bg-light text-muted">cm</span>
                    </div>
                </div>

                <!-- Ngày sinh của mẹ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-calendar text-danger me-1"></i>
                        {{ __('Ngày sinh của mẹ') }}:
                    </label>
                    <x-input type="date" name="mother_birthday" :value="$user->mother_birthday ? format_date($user->mother_birthday, 'Y-m-d') : null" />
                </div>
            </div>
        </div>
    </div>
</div>
