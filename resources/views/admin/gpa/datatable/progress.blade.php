@if($completed)
    <span class="badge bg-success-lt text-success"><i class="ti ti-circle-check me-1"></i>Hoàn tất</span>
@elseif($count > 0)
    <span class="badge bg-warning-lt text-warning"><i class="ti ti-clock me-1"></i>{{ $count }}/3 kỳ</span>
@else
    <span class="badge bg-secondary-lt text-secondary"><i class="ti ti-minus me-1"></i>Chưa cập nhật</span>
@endif
