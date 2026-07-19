@if($row->platform === 'android')
    <span class="badge bg-green"><i class="ti ti-brand-android me-1"></i>Android</span>
@elseif($row->platform === 'ios')
    <span class="badge bg-dark"><i class="ti ti-brand-apple me-1"></i>iOS</span>
@else
    <span class="badge bg-orange">{{ ucfirst($row->platform) }}</span>
@endif
