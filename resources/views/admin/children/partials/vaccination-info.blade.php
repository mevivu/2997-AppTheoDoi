@php
    $vaccinations = $children->vaccinationSchedules()->with('vaccinationType')->orderBy('performed_on', 'desc')->get();
@endphp

<div class="row g-3">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-vaccine text-primary fs-4"></i>
                    {{ __('Lịch Sử & Lộ Trình Tiêm Chủng Của Trẻ') }}
                </h5>
                <small class="text-muted">{{ __('Theo dõi danh sách các mũi tiêm phòng dịch bệnh đã thực hiện và dự kiến') }}</small>
            </div>
            <span class="badge bg-primary-lt px-3 py-2 fs-13 rounded-pill fw-bold">
                {{ __('Tổng số:') }} {{ $vaccinations->count() }} {{ __('mũi tiêm') }}
            </span>
        </div>
    </div>

    @forelse($vaccinations as $item)
        <div class="col-12">
            <div class="vaccination-item-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-blue-lt rounded-3 fs-3">
                        <i class="ti ti-vaccine text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark fs-14">
                            {{ $item->vaccinationType?->name ?? ($item->name ?? __('Mũi tiêm phòng')) }}
                        </h6>
                        <div class="d-flex align-items-center gap-3 text-muted fs-12 flex-wrap">
                            @if($item->performed_on)
                                <span><i class="ti ti-calendar-check text-success me-1"></i>{{ __('Ngày tiêm:') }} {{ format_date($item->performed_on, 'd/m/Y') }}</span>
                            @else
                                <span><i class="ti ti-calendar text-muted me-1"></i>{{ __('Chưa có ngày tiêm') }}</span>
                            @endif
                            @if($item->description)
                                <span><i class="ti ti-info-circle text-info me-1"></i>{{ $item->description }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    @if($item->vaccination_status)
                        <span class="badge {{ $item->vaccination_status->badge() }} px-3 py-2 fs-12">
                            {{ $item->vaccination_status->description() }}
                        </span>
                    @elseif($item->status)
                        <span class="badge {{ $item->status->badge() }} px-3 py-2 fs-12">
                            {{ $item->status->description() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted fs-1 text-opacity-50 mb-2">
                <i class="ti ti-vaccine-off"></i>
            </div>
            <h6 class="text-muted">{{ __('Chưa có dữ liệu tiêm chủng nào cho bé này.') }}</h6>
        </div>
    @endforelse
</div>
