<div class="text-start fs-12" style="line-height: 1.5;">
    <div class="text-nowrap">
        <i class="ti ti-calendar-event text-success me-1"></i><span class="text-muted">{{ __('BĐ:') }}</span> <strong>{{ !empty($start_at) ? \Carbon\Carbon::parse($start_at)->format('d/m/Y H:i') : '---' }}</strong>
    </div>
    <div class="text-nowrap mt-1">
        <i class="ti ti-calendar-off text-danger me-1"></i><span class="text-muted">{{ __('KT:') }}</span> <strong>{{ !empty($end_at) ? \Carbon\Carbon::parse($end_at)->format('d/m/Y H:i') : '---' }}</strong>
    </div>
</div>
