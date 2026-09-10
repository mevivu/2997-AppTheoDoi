@php
    use App\AES\AESHelper;
    $referrer = $user->referrer;
    $referrals = $user->referrals()->with(['userPackages.package'])->latest()->get();
    $histories = $user->affiliateHistories()->with(['sourceUser'])->latest()->take(50)->get();
@endphp

<div class="row g-4">
    <!-- Stat Overview: Số dư ví hoa hồng & Thống kê affiliate -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm border border-light-subtle rounded-3 shadow-none bg-primary-lt">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar rounded-3">
                                    <i class="ti ti-wallet fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small fw-medium">{{ __('Số dư ví hoa hồng') }}</div>
                                <div class="fs-2 fw-bold text-primary">
                                    {{ number_format($user->wallet_balance ?? 0, 0, ',', '.') }} đ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card card-sm border border-light-subtle rounded-3 shadow-none bg-green-lt">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar rounded-3">
                                    <i class="ti ti-users fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small fw-medium">{{ __('Thành viên đã giới thiệu') }}</div>
                                <div class="fs-2 fw-bold text-success">
                                    {{ $referrals->count() }} <span class="fs-4 text-muted fw-normal">thành viên</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="card card-sm border border-light-subtle rounded-3 shadow-none bg-purple-lt">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-purple text-white avatar rounded-3">
                                    <i class="ti ti-share fs-2"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small fw-medium">{{ __('Mã Affiliate cá nhân') }}</div>
                                <div class="fs-2 fw-bold text-purple">
                                    {{ $user->affiliate_code ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 1: Người giới thiệu tài khoản này -->
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 shadow-none">
            <div class="card-header bg-light-subtle py-3">
                <h6 class="card-title m-0 fw-bold d-flex align-items-center">
                    <i class="ti ti-user-check text-primary me-2 fs-3"></i>
                    {{ __('Người giới thiệu tài khoản này') }}
                </h6>
            </div>
            <div class="card-body p-4">
                @if($referrer)
                    @php
                        $refEmail = $referrer->email ? AESHelper::decrypt($referrer->email) : '-';
                        $refPhone = $referrer->phone ? AESHelper::decrypt($referrer->phone) : '-';
                    @endphp
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 bg-light rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md rounded-circle bg-primary-lt fw-bold fs-3">
                                {{ mb_strtoupper(mb_substr($referrer->fullname ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold fs-3 text-dark">
                                    {{ $referrer->fullname }}
                                    @if($referrer->affiliate_code)
                                        <span class="badge bg-purple-lt ms-1">{{ $referrer->affiliate_code }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1 d-flex gap-3 flex-wrap">
                                    <span><i class="ti ti-mail text-primary"></i> {{ $refEmail }}</span>
                                    <span><i class="ti ti-phone text-success"></i> {{ $refPhone }}</span>
                                    <span><i class="ti ti-calendar text-muted"></i> {{ format_date($referrer->created_at, 'd/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.user.edit', $referrer->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-external-link"></i>
                            <span>{{ __('Xem hồ sơ') }}</span>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-user-x fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                        <span class="fw-medium">{{ __('Tài khoản này tự đăng ký trực tiếp, không qua mã giới thiệu.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Danh sách thành viên được tài khoản này giới thiệu -->
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 shadow-none">
            <div class="card-header bg-light-subtle py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title m-0 fw-bold d-flex align-items-center">
                    <i class="ti ti-users text-success me-2 fs-3"></i>
                    {{ __('Danh sách thành viên đã giới thiệu') }}
                    <span class="badge bg-success ms-2">{{ $referrals->count() }}</span>
                </h6>
                <div class="text-muted small">
                    {{ __('Mã giới thiệu của tài khoản:') }} <strong class="text-purple">{{ $user->affiliate_code ?? '-' }}</strong>
                </div>
            </div>
            <div class="card-body p-0">
                @if($referrals->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="w-1 text-center">#</th>
                                    <th>{{ __('Khách hàng') }}</th>
                                    <th>{{ __('Mã affiliate') }}</th>
                                    <th>{{ __('Số điện thoại / Email') }}</th>
                                    <th>{{ __('Gói đang dùng') }}</th>
                                    <th>{{ __('Ngày tham gia') }}</th>
                                    <th>{{ __('Trạng thái') }}</th>
                                    <th class="text-center">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referrals as $index => $refUser)
                                    @php
                                        $f1Email = $refUser->email ? AESHelper::decrypt($refUser->email) : '';
                                        $f1Phone = $refUser->phone ? AESHelper::decrypt($refUser->phone) : '';
                                        $f1Package = $refUser->userPackages->first()?->package?->name;
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $refUser->fullname }}</div>
                                            <div class="small text-muted">ID: #{{ $refUser->id }} ({{ $refUser->code }})</div>
                                        </td>
                                        <td>
                                            @if($refUser->affiliate_code)
                                                <span class="badge bg-purple-lt">{{ $refUser->affiliate_code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($f1Phone)
                                                <div><i class="ti ti-phone text-success fs-5"></i> {{ $f1Phone }}</div>
                                            @endif
                                            @if($f1Email)
                                                <div class="small text-muted"><i class="ti ti-mail text-primary fs-5"></i> {{ $f1Email }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($f1Package)
                                                <span class="badge bg-green-lt">{{ $f1Package }}</span>
                                            @else
                                                <span class="badge bg-secondary-lt">{{ __('Chưa có') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ format_date($refUser->created_at, 'd/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $refUser->status?->badge() ?? 'bg-secondary' }}">
                                                {{ $refUser->status?->description() ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.user.edit', $refUser->id) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="{{ __('Xem chi tiết') }}">
                                                <i class="ti ti-eye fs-3"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-users-minus fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                        <span class="fw-medium">{{ __('Tài khoản này chưa giới thiệu thành viên nào.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 3: Lịch sử nhận thưởng hoa hồng Affiliate -->
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 shadow-none">
            <div class="card-header bg-light-subtle py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title m-0 fw-bold d-flex align-items-center">
                    <i class="ti ti-receipt text-warning me-2 fs-3"></i>
                    {{ __('Lịch sử nhận thưởng Affiliate') }}
                    <span class="badge bg-warning ms-2">{{ $histories->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @if($histories->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="w-1 text-center">#</th>
                                    <th>{{ __('Thời gian') }}</th>
                                    <th>{{ __('Loại thưởng') }}</th>
                                    <th>{{ __('Thành viên kích hoạt') }}</th>
                                    <th>{{ __('Diễn giải') }}</th>
                                    <th class="text-end">{{ __('Số tiền') }}</th>
                                    <th class="text-end">{{ __('Số dư sau') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($histories as $hIndex => $history)
                                    <tr>
                                        <td class="text-center text-muted">{{ $hIndex + 1 }}</td>
                                        <td class="small">{{ format_date($history->created_at, 'd/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($history->type === 'referral_register')
                                                <span class="badge bg-green-lt">{{ __('Thưởng giới thiệu F1') }}</span>
                                            @elseif($history->type === 'welcome_register')
                                                <span class="badge bg-blue-lt">{{ __('Thưởng chào mừng F1') }}</span>
                                            @else
                                                <span class="badge bg-secondary-lt">{{ $history->type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($history->sourceUser)
                                                <a href="{{ route('admin.user.edit', $history->sourceUser->id) }}" class="fw-medium text-dark text-decoration-none">
                                                    {{ $history->sourceUser->fullname }}
                                                </a>
                                                <div class="small text-muted">ID: #{{ $history->sourceUser->id }}</div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $history->description }}</td>
                                        <td class="text-end fw-bold text-success">
                                            +{{ number_format($history->amount, 0, ',', '.') }} đ
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            {{ number_format($history->balance_after, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-receipt-off fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                        <span class="fw-medium">{{ __('Chưa có lịch sử nhận thưởng hoa hồng nào.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
