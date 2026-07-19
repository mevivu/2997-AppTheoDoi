@if($row->is_active)
    <span class="badge bg-success">{{ __('Hoạt động') }}</span>
@else
    <span class="badge bg-danger">{{ __('Tạm khóa') }}</span>
@endif
