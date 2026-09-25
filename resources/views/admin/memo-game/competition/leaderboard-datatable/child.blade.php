@php
    $childObj = $child ?? ($entry?->child ?? null);
    $childName = is_object($childObj) ? ($childObj->fullname ?? 'Thí sinh') : ($childObj['fullname'] ?? 'Thí sinh');
    $avatar = is_object($childObj) ? ($childObj->avatar ? asset($childObj->avatar) : asset('images/avatar-default.png')) : asset('images/avatar-default.png');
    $genderVal = is_object($childObj) ? ($childObj->gender instanceof \BackedEnum ? $childObj->gender->value : (string) $childObj->gender) : ($childObj['gender'] ?? null);
    $isMale = ($genderVal === 'male');
    $childAge = is_object($childObj) ? ($childObj->age ?? null) : ($childObj['age'] ?? null);
@endphp
<div class="d-flex align-items-center gap-2.5">
    <img src="{{ $avatar }}" alt="{{ $childName }}" class="avatar avatar-md rounded-circle border shadow-2xs flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
    <div class="min-w-0">
        <div class="fw-bold text-dark fs-13 text-truncate" title="{{ $childName }}">{{ $childName }}</div>
        <div class="text-muted fs-11 d-flex align-items-center gap-1 mt-0.5 flex-wrap">
            @if ($genderVal)
                <span class="badge {{ $isMale ? 'bg-blue-lt border border-blue-subtle text-blue' : 'bg-pink-lt border border-pink-subtle text-pink' }} py-0.5 px-1.5" style="font-size: 10px;">
                    <i class="ti {{ $isMale ? 'ti-gender-male' : 'ti-gender-female' }} me-0.5"></i>{{ $isMale ? 'Bé trai' : 'Bé gái' }}
                </span>
            @endif
            @if ($childAge)
                <span class="badge bg-light text-muted border py-0.5 px-1.5" style="font-size: 10px;">
                    {{ $childAge }} tuổi
                </span>
            @endif
        </div>
    </div>
</div>
