@php
    $children = $user->children;
@endphp

<div class="row g-3">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ __('Danh Sách Các Bé Trong Gia Đình') }}</h5>
                <small class="text-muted">{{ __('Tổng số:') }} {{ $children->count() }} {{ __('trẻ em đã đăng ký') }}</small>
            </div>
        </div>
    </div>

    @forelse($children as $child)
        <div class="col-12 col-md-6">
            <div class="child-grid-card">
                <img src="{{ $child->avatar ? asset($child->avatar) : asset('/public/admin/assets/images/default-avatar.png') }}"
                     alt="{{ $child->fullname }}"
                     class="child-avatar">

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h6 class="mb-0 fw-bold text-dark fs-14">{{ $child->fullname }}</h6>
                        @if($child->gender)
                            <span class="badge {{ $child->gender->value == 1 ? 'bg-blue-lt' : 'bg-pink-lt' }}">
                                {{ $child->gender->description() }}
                            </span>
                        @endif
                    </div>

                    <div class="text-muted fs-12 mb-1">
                        <i class="ti ti-calendar me-1"></i>
                        {{ __('Ngày sinh:') }} {{ $child->birthday ? format_date($child->birthday, 'd/m/Y') : __('Chưa cập nhật') }}
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if($child->status)
                            <span class="badge {{ $child->status->badge() }}">{{ $child->status->description() }}</span>
                        @endif
                        @if($child->is_born)
                            <span class="badge bg-light text-muted">{{ $child->is_born->description() }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted fs-1 text-opacity-50 mb-2">
                <i class="ti ti-baby-carriage"></i>
            </div>
            <h6 class="text-muted">{{ __('Chưa có thông tin trẻ em nào được liên kết với tài khoản này.') }}</h6>
        </div>
    @endforelse
</div>
