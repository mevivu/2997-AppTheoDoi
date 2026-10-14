@php
    use App\Traits\RouteAdminSystem;
@endphp
<div class="col-12 col-xl-3 col-lg-4">
    {{-- Card 1: Status Card --}}
    <div class="iq-card">
        <div class="iq-card-header">
            <h3 class="iq-card-title">
                <i class="ti ti-toggle-right text-primary fs-3"></i>
                <span>{{ __('Trạng thái áp dụng') }}</span>
                <span class="text-danger">*</span>
            </h3>
        </div>
        <div class="card-body p-3">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->status->value" />
                @endforeach
            </x-select>
            <div class="text-muted fs-12 mt-2">
                <i class="ti ti-shield-check text-success me-1"></i>
                {{ __('Chỉ mốc chuẩn ở trạng thái "Hoạt động" mới được dùng để tính chỉ số IQ Wechsler cho trẻ.') }}
            </div>
        </div>
    </div>

    {{-- Card 2: Wechsler 7-Tier Reference Card --}}
    <div class="iq-card">
        <div class="iq-card-header">
            <h3 class="iq-card-title">
                <i class="ti ti-list-details text-azure fs-3"></i>
                <span>{{ __('Thang Wechsler chuẩn') }}</span>
            </h3>
            <span class="badge bg-purple-lt fs-11">7 cấp độ</span>
        </div>
        <div class="card-body p-3">
            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #7c3aed;"></span>
                    <span class="fw-bold text-dark">≥ 130</span>
                </div>
                <span class="text-purple fw-semibold">Rất ưu tú</span>
                <span class="text-muted fs-11">2.2%</span>
            </div>

            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #2563eb;"></span>
                    <span class="fw-bold text-dark">120 - 129</span>
                </div>
                <span class="text-primary fw-semibold">Ưu tú</span>
                <span class="text-muted fs-11">6.7%</span>
            </div>

            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #06b6d4;"></span>
                    <span class="fw-bold text-dark">110 - 119</span>
                </div>
                <span class="text-azure fw-semibold">Trên TB</span>
                <span class="text-muted fs-11">16.1%</span>
            </div>

            <div class="wechsler-ref-item" style="background: #f0fdf4; border-color: #bbf7d0;">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #16a34a;"></span>
                    <span class="fw-bold text-dark">90 - 109</span>
                </div>
                <span class="text-success fw-bold">Trung bình</span>
                <span class="text-muted fs-11 fw-bold">50.0%</span>
            </div>

            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #d97706;"></span>
                    <span class="fw-bold text-dark">80 - 89</span>
                </div>
                <span class="text-warning fw-semibold">Dưới TB</span>
                <span class="text-muted fs-11">16.1%</span>
            </div>

            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #ea580c;"></span>
                    <span class="fw-bold text-dark">70 - 79</span>
                </div>
                <span class="text-orange fw-semibold">Giới hạn chậm</span>
                <span class="text-muted fs-11">6.7%</span>
            </div>

            <div class="wechsler-ref-item">
                <div class="d-flex align-items-center">
                    <span class="wechsler-dot" style="background: #dc2626;"></span>
                    <span class="fw-bold text-dark">&lt; 70</span>
                </div>
                <span class="text-danger fw-semibold">Rất kém</span>
                <span class="text-muted fs-11">2.2%</span>
            </div>
        </div>
    </div>

    {{-- Card 3: Audit & Metadata --}}
    <div class="iq-card">
        <div class="iq-card-header">
            <h3 class="iq-card-title text-muted fs-13">
                <i class="ti ti-info-circle text-muted fs-4"></i>
                <span>{{ __('Thông tin bản ghi') }}</span>
            </h3>
        </div>
        <div class="card-body p-3">
            <div class="d-flex justify-content-between py-1 border-bottom fs-12">
                <span class="text-muted">{{ __('Mã bản ghi:') }}</span>
                <span class="fw-bold text-dark">#{{ $response->id }}</span>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom fs-12">
                <span class="text-muted">{{ __('Ngày khởi tạo:') }}</span>
                <span class="text-dark">{{ format_datetime($response->created_at) }}</span>
            </div>
            <div class="d-flex justify-content-between py-1 fs-12">
                <span class="text-muted">{{ __('Cập nhật lần cuối:') }}</span>
                <span class="text-dark">{{ format_datetime($response->updated_at) }}</span>
            </div>
        </div>
    </div>

    {{-- Common Action Component (Floating Bottom Bar) --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::IQ_NORM_INDEX)"
        :back-title="__('Quay lại')"
    >
        <button type="button" class="btn btn-delete-settings d-inline-flex align-items-center gap-1"
                data-bs-toggle="modal" data-bs-target="#modalDeleteNorm"
                title="{{ __('Xóa mốc độ tuổi này') }}">
            <i class="ti ti-trash fs-5"></i>
            <span>{{ __('Xóa') }}</span>
        </button>
    </x-admin.form-actions>
</div>
