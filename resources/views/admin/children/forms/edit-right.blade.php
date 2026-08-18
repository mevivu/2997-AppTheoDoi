@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Card 1: Trạng thái hoạt động -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái hoạt động') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$children->status?->value ?? $children->status" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Card 2: Tình trạng sinh -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-heartbeat text-primary me-2 fs-4"></i>
                {{ __('Tình trạng sinh') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name='is_born' id="is_born">
                @foreach ($born as $key => $value)
                    <x-select-option :option="$children->is_born?->value ?? $children->is_born" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Card 3: Ảnh đại diện -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="avatar" showImage="avatar" class="img-fluid" :value="$children->avatar" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Hỗ trợ định dạng JPG, PNG, WEBP. Nhấp vào ảnh để thay đổi qua CKFinder.') }}</p>
        </div>
    </div>

    <!-- Card 4: Thao tác & Lối tắt nhanh -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-bolt text-warning me-2 fs-4"></i>
                {{ __('Lối tắt theo dõi') }}
            </h5>
        </div>
        <div class="card-body p-3 d-flex flex-column gap-2">
            @if($children->user_id)
                <a href="{{ route('admin.user.edit', $children->user_id) }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 py-2" target="_blank">
                    <i class="ti ti-user-check fs-4"></i>
                    <span>{{ __('Hồ sơ Cha / Mẹ') }}</span>
                </a>
            @endif

            <a href="{{ route('admin.rating.iq', ['child_id' => $children->id]) }}" class="btn btn-outline-purple d-flex align-items-center justify-content-center gap-2 py-2" target="_blank">
                <i class="ti ti-brain fs-4"></i>
                <span>{{ __('Đánh giá IQ') }}</span>
            </a>

            <a href="{{ route('admin.rating.eq', ['child_id' => $children->id]) }}" class="btn btn-outline-pink d-flex align-items-center justify-content-center gap-2 py-2" target="_blank">
                <i class="ti ti-heart fs-4"></i>
                <span>{{ __('Đánh giá EQ') }}</span>
            </a>

            <a href="{{ route('admin.rating.aq', ['child_id' => $children->id]) }}" class="btn btn-outline-teal d-flex align-items-center justify-content-center gap-2 py-2" target="_blank">
                <i class="ti ti-leaf fs-4"></i>
                <span>{{ __('Đánh giá AQ') }}</span>
            </a>

            <a href="{{ route('admin.ratingPQ.index', ['child_id' => $children->id]) }}" class="btn btn-outline-orange d-flex align-items-center justify-content-center gap-2 py-2" target="_blank">
                <i class="ti ti-activity fs-4"></i>
                <span>{{ __('Thể chất PQ') }}</span>
            </a>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::CHILDREN_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
