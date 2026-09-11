@php
    $frontUrl = null;
    if (!empty($user->id_card_front)) {
        if (str_starts_with($user->id_card_front, 'http')) {
            $frontUrl = $user->id_card_front;
        } elseif (str_starts_with($user->id_card_front, 'public/')) {
            $frontUrl = asset($user->id_card_front);
        } else {
            $frontUrl = asset('storage/' . $user->id_card_front);
        }
    }

    $backUrl = null;
    if (!empty($user->id_card_back)) {
        if (str_starts_with($user->id_card_back, 'http')) {
            $backUrl = $user->id_card_back;
        } elseif (str_starts_with($user->id_card_back, 'public/')) {
            $backUrl = asset($user->id_card_back);
        } else {
            $backUrl = asset('storage/' . $user->id_card_back);
        }
    }
@endphp

<div class="d-flex align-items-center justify-content-center gap-2 py-1">
    {{-- Mặt trước --}}
    <div class="text-center">
        @if ($frontUrl)
            <div class="kyc-thumb-box position-relative" 
                 onclick="previewKycImage('{{ $frontUrl }}', '{{ __('CCCD Mặt trước - ') . ($user->fullname ?? '#' . $user->id) }}')"
                 title="{{ __('Bấm để xem ảnh phóng to') }}">
                <img src="{{ $frontUrl }}" alt="CCCD Mặt trước" class="rounded border shadow-2xs kyc-thumb-img">
                <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-50 translate-middle-x mb-0.5 px-1 py-0 fs-9 text-nowrap rounded-1">
                    {{ __('Mặt trước') }}
                </span>
            </div>
        @else
            <div class="kyc-thumb-empty rounded border border-dashed d-flex flex-column align-items-center justify-content-center text-muted fs-10">
                <i class="ti ti-photo-x fs-14 mb-0.5 text-secondary"></i>
                <span>{{ __('Thiếu') }}</span>
            </div>
        @endif
    </div>

    {{-- Mặt sau --}}
    <div class="text-center">
        @if ($backUrl)
            <div class="kyc-thumb-box position-relative" 
                 onclick="previewKycImage('{{ $backUrl }}', '{{ __('CCCD Mặt sau - ') . ($user->fullname ?? '#' . $user->id) }}')"
                 title="{{ __('Bấm để xem ảnh phóng to') }}">
                <img src="{{ $backUrl }}" alt="CCCD Mặt sau" class="rounded border shadow-2xs kyc-thumb-img">
                <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-50 translate-middle-x mb-0.5 px-1 py-0 fs-9 text-nowrap rounded-1">
                    {{ __('Mặt sau') }}
                </span>
            </div>
        @else
            <div class="kyc-thumb-empty rounded border border-dashed d-flex flex-column align-items-center justify-content-center text-muted fs-10">
                <i class="ti ti-photo-x fs-14 mb-0.5 text-secondary"></i>
                <span>{{ __('Thiếu') }}</span>
            </div>
        @endif
    </div>
</div>
