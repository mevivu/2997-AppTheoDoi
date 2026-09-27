@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    {{-- Card 0: Ảnh đại diện bài học --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện bài học') }}
            </h5>
        </div>
        <div class="card-body p-3 p-md-4">
            @php
                $lessonAvatarVal = old('image', !empty($instance->image) ? asset($instance->image) : ($instance->thumbnail_url ?? null));
            @endphp
            <x-input-image name="image" 
                           :value="$lessonAvatarVal" 
                           width="125px"
                           height="75px"
                           sub="{{ __('Ảnh avatar/bìa đại diện bài học (khuyến nghị 16:9, tối đa 5MB)') }}" />
        </div>
    </div>

    {{-- Card 1: Phân quyền truy cập --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-lock text-primary me-2 fs-4"></i>
                {{ __('Phân quyền gói học') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column gap-3">
                @php
                    $currentAccess = old('access_type', $instance->access_type?->value ?? 'free');
                @endphp
                @foreach ($accessTypes as $key => $label)
                    @php
                        $isVip = ($key === 'vip');
                        $isChecked = ($currentAccess === $key);
                    @endphp
                    <label class="access-type-card access-card-{{ $key }} {{ $isChecked ? 'is-active' : '' }} rounded-3 p-3 cursor-pointer d-flex align-items-center gap-3 mb-0"
                           for="access_type_{{ $key }}">
                        <input class="form-check-input access-type-radio mt-0" 
                               type="radio" 
                               name="access_type" 
                               id="access_type_{{ $key }}" 
                               value="{{ $key }}" 
                               {{ $isChecked ? 'checked' : '' }} 
                               required>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if($isVip)
                                    <span class="badge bg-yellow text-dark fw-bold px-2 py-1 shadow-xs">
                                        <i class="ti ti-crown me-1"></i>VIP
                                    </span>
                                @else
                                    <span class="badge bg-green text-white fw-bold px-2 py-1 shadow-xs">
                                        <i class="ti ti-gift me-1"></i>FREE
                                    </span>
                                @endif
                                <span class="fw-bold text-dark fs-14">{{ $label }}</span>
                            </div>
                            <div class="text-muted fs-11 lh-base">
                                {{ $isVip ? 'Chỉ tài khoản VIP mới xem được toàn bộ video.' : 'Mọi người dùng đều có thể truy cập bài học này.' }}
                            </div>
                        </div>
                        <div class="access-check-icon">
                            <i class="ti ti-circle-check-filled {{ $isVip ? 'text-warning' : 'text-success' }} fs-1"></i>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .access-type-card {
            position: relative;
            border: 2px solid #e2e8f0;
            background-color: #ffffff;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }
        .access-type-card:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
            transform: translateY(-1px);
        }
        .access-type-card .access-type-radio {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
            flex-shrink: 0;
        }
        .access-type-card .access-check-icon {
            display: none;
            flex-shrink: 0;
            animation: accessPopIn 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes accessPopIn {
            from { transform: scale(0.4); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* FREE Active */
        .access-card-free.is-active,
        .access-card-free:has(input:checked) {
            border-color: #2fb344 !important;
            background-color: #f0fdf4 !important;
            box-shadow: 0 4px 14px rgba(47, 179, 68, 0.16) !important;
            transform: translateY(-1px);
        }
        .access-card-free.is-active .access-check-icon,
        .access-card-free:has(input:checked) .access-check-icon {
            display: block;
        }

        /* VIP Active */
        .access-card-vip.is-active,
        .access-card-vip:has(input:checked) {
            border-color: #f59f00 !important;
            background-color: #fffbeb !important;
            box-shadow: 0 4px 14px rgba(245, 159, 0, 0.2) !important;
            transform: translateY(-1px);
        }
        .access-card-vip.is-active .access-check-icon,
        .access-card-vip:has(input:checked) .access-check-icon {
            display: block;
        }
    </style>

    {{-- Card 2: Trạng thái & Thứ tự --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái & Hiển thị') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Trạng thái hoạt động') }}:</label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="old('status', $instance->status?->value ?? $instance->status) == $key" />
                    @endforeach
                </x-select>
            </div>

            <div class="mb-0">
                <label class="form-label fw-bold text-dark fs-13 mb-1">{{ __('Thứ tự sắp xếp') }}:</label>
                <x-input type="number" name="sort_order" :value="old('sort_order', $instance->sort_order)" min="0" />
            </div>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Cập nhật')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::LESSON_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accessRadios = document.querySelectorAll('.access-type-radio');
    const accessCards = document.querySelectorAll('.access-type-card');

    function syncAccessCards() {
        accessCards.forEach(card => {
            const radio = card.querySelector('.access-type-radio');
            if (radio && radio.checked) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
    }

    accessRadios.forEach(radio => {
        radio.addEventListener('change', syncAccessCards);
    });

    syncAccessCards();
});
</script>
@endpush
