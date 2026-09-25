@php
    $childObj = $child ?? ($entry?->child ?? null);
    $userObj = is_object($childObj) ? ($childObj->user ?? null) : ($childObj['user'] ?? null);
    $parentName = is_object($userObj) ? ($userObj->fullname ?? '--') : ($userObj['fullname'] ?? '--');
    $rawPhone = is_object($userObj) ? ($userObj->decrypted_phone ?? $userObj->phone ?? null) : ($userObj['phone'] ?? null);
    $parentEmail = is_object($userObj) ? ($userObj->decrypted_email ?? $userObj->email ?? null) : ($userObj['email'] ?? null);

    $cleanPhone = $rawPhone ? preg_replace('/[^0-9]/', '', $rawPhone) : '';
    if (strlen($cleanPhone) === 10) {
        $formattedPhone = substr($cleanPhone, 0, 4) . ' ' . substr($cleanPhone, 4, 3) . ' ' . substr($cleanPhone, 7);
    } else {
        $formattedPhone = $rawPhone;
    }
@endphp
<div class="d-flex flex-column gap-1" style="min-width: 175px;">
    <div class="fw-bold text-dark fs-13 d-flex align-items-center gap-1">
        <i class="ti ti-user text-muted fs-13 flex-shrink-0"></i>
        <span class="text-truncate" style="max-width: 180px;" title="{{ $parentName }}">{{ $parentName }}</span>
    </div>
    @if ($formattedPhone)
        <div class="d-flex align-items-center gap-1 flex-nowrap">
            <a href="tel:{{ $cleanPhone ?: $rawPhone }}" 
               class="phone-badge text-nowrap flex-shrink-0" 
               style="white-space: nowrap !important;" 
               title="{{ __('Bấm để gọi: :p', ['p' => $formattedPhone]) }}">
                <i class="ti ti-phone-call fs-12"></i>
                <span style="white-space: nowrap !important;">{{ $formattedPhone }}</span>
            </a>
            <button type="button" 
                    class="btn btn-copy-phone flex-shrink-0" 
                    data-phone="{{ $cleanPhone ?: $rawPhone }}" 
                    title="{{ __('Sao chép số điện thoại') }}">
                <i class="ti ti-copy fs-12"></i>
            </button>
        </div>
    @elseif ($parentEmail)
        <div class="text-muted fs-11 d-flex align-items-center gap-1">
            <i class="ti ti-mail fs-12 flex-shrink-0"></i>
            <span class="text-truncate" style="max-width: 170px;" title="{{ $parentEmail }}">{{ $parentEmail }}</span>
        </div>
    @else
        <span class="text-muted fs-11">--</span>
    @endif
</div>
