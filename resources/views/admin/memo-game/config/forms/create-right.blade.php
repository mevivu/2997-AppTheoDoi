@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-md-4 col-xl-3">
    <!-- Card 1: Cài đặt & Trạng thái -->
    <div class="card custom-shadow mb-3">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Cài đặt & Trạng thái') }}</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Trạng thái') }}: <span class="text-danger">*</span></label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="old('status') == $key" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <!-- Card 2: 📱 Mô phỏng Bài test trên App (Live App Simulation) -->
    <div class="card custom-shadow mb-3 border border-primary border-opacity-25">
        <div class="card-header bg-primary-lt py-2.5">
            <h4 class="card-title mb-0 text-primary d-flex align-items-center fs-14">
                <i class="ti ti-device-mobile me-1.5 fs-18"></i> {{ __('Mô phỏng hiển thị trên App') }}
            </h4>
        </div>
        <div class="card-body p-3">
            <div class="mb-2 pb-2 border-bottom">
                <div class="text-muted fs-11 text-uppercase fw-semibold">{{ __('Đối tượng áp dụng') }}</div>
                <div class="fw-bold text-dark fs-13 mt-0.5" id="simAge">--</div>
            </div>

            <div class="mb-2 pb-2 border-bottom">
                <div class="text-muted fs-11 text-uppercase fw-semibold">{{ __('Quy chuẩn thẻ bài') }}</div>
                <div class="fw-bold text-dark fs-13 mt-0.5" id="simGrid">--</div>
            </div>

            <div class="mb-2 pb-2 border-bottom">
                <div class="text-muted fs-11 text-uppercase fw-semibold">{{ __('Thời lượng 1 ván') }}</div>
                <div class="fw-bold text-azure fs-13 mt-0.5" id="simDuration">--</div>
            </div>

            <div class="mb-2 pb-2 border-bottom">
                <div class="text-muted fs-11 text-uppercase fw-semibold">{{ __('Số ván chơi') }}</div>
                <div class="fw-bold text-purple fs-13 mt-0.5" id="simRounds">--</div>
            </div>

            <div class="mb-2 pb-2 border-bottom">
                <div class="text-muted fs-11 text-uppercase fw-semibold">{{ __('Tổng thời gian Game') }}</div>
                <div class="fw-bold text-success fs-13 mt-0.5" id="simTotalGame">--</div>
            </div>

            <div class="mt-3 p-2.5 rounded bg-primary text-white text-center shadow-sm">
                <div class="fs-11 opacity-75">15p trắc nghiệm + <span id="simGameMinutes">0</span>p game</div>
                <div class="fs-11 fw-bold text-uppercase mt-0.5">{{ __('Tổng bài test IQ trên App:') }}</div>
                <div class="fs-20 fw-bolder mt-0.5 letter-spacing-1" id="simTotalAssessment">--</div>
            </div>
            <small class="text-muted fs-11 d-block text-center mt-2">
                <i class="ti ti-info-circle me-0.5"></i> {{ __('Tự động tính theo thông số bên trái') }}
            </small>
        </div>
    </div>

    <x-admin.form-actions
        :submit-title="__('Lưu cấu hình')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::MEMO_AGE_CONFIG_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
