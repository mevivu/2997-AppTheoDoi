@php
    use App\Traits\RouteAdminSystem;
    use App\AES\AESHelper;
    $currentPackage = $user->userPackages()->where('status', 'active')->first();
    $totalChildren = $user->children()->count();
    $decryptedEmail = $user->email ? AESHelper::decrypt($user->email) : '';
    $decryptedPhone = $user->phone ? AESHelper::decrypt($user->phone) : '';

    // Generate user initials
    $nameParts = explode(' ', trim($user->fullname ?? ''));
    $initials = '';
    if (count($nameParts) >= 2) {
        $initials = mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1);
    } else {
        $initials = mb_substr($user->fullname ?? 'U', 0, 2);
    }
    $initials = mb_strtoupper($initials);
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-3"
                icon="user-edit"
                :title="__('Hồ sơ Khách hàng')"
                :subtitle="__('Quản lý chi tiết thông tin cá nhân, gia đình, gói dịch vụ và trẻ em')"
                :back-route="route(RouteAdminSystem::USER_INDEX)"
            />

            <!-- User Profile Hero Banner -->
            <div class="user-profile-hero">
                <div class="row align-items-center g-3">
                    <!-- Left: Profile Info -->
                    <div class="col-12 col-xl-6">
                        <div class="d-flex align-items-center gap-3">
                            @if($user->avatar && file_exists(public_path($user->avatar)))
                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->fullname }}" class="user-hero-avatar">
                            @else
                                <div class="user-avatar-initials">
                                    {{ $initials ?: 'KH' }}
                                </div>
                            @endif

                            <div>
                                <div class="user-hero-title">
                                    <span>{{ $user->fullname }}</span>
                                    <span class="user-code-badge">#{{ $user->id }}</span>
                                    @if($user->gender)
                                        <span class="badge bg-blue-lt">{{ $user->gender->description() }}</span>
                                    @endif
                                    <span class="badge {{ $user->status->badge() }}">{{ $user->status->description() }}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap mt-1">
                                    @if($decryptedEmail)
                                        <span class="user-meta-pill" title="Email"><i class="ti ti-mail text-primary"></i> {{ $decryptedEmail }}</span>
                                    @endif
                                    @if($decryptedPhone)
                                        <span class="user-meta-pill" title="Số điện thoại"><i class="ti ti-phone text-success"></i> {{ $decryptedPhone }}</span>
                                    @endif
                                    <span class="user-meta-pill" title="Ngày tham gia"><i class="ti ti-calendar text-muted"></i> {{ __('Tham gia: ') . format_date($user->created_at, 'd/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Stat Cards -->
                    <div class="col-12 col-xl-6">
                        <div class="user-hero-stats-grid">
                            <!-- Gói dịch vụ hiện tại -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon icon-package">
                                    <i class="ti ti-package"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('Gói Dịch Vụ') }}</div>
                                    <div class="fw-bold fs-13 text-dark text-truncate">{{ $currentPackage?->package?->name ?? __('Chưa kích hoạt') }}</div>
                                </div>
                            </div>

                            <!-- Số lượng con -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon icon-children">
                                    <i class="ti ti-baby-carriage"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('Trẻ Em') }}</div>
                                    <div class="fw-bold fs-13 text-dark text-truncate">{{ $totalChildren }} {{ __('bé') }}</div>
                                </div>
                            </div>

                            <!-- Lịch sử giao dịch -->
                            <a href="{{ route('admin.user.history', $user->id) }}" class="user-stat-card text-decoration-none" title="{{ __('Xem lịch sử giao dịch') }}">
                                <div class="user-stat-icon icon-orders">
                                    <i class="ti ti-receipt"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-1">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('Giao Dịch') }}</div>
                                    <div class="fw-bold fs-12 text-success d-inline-flex align-items-center gap-1 text-truncate">
                                        <span>{{ __('Xem chi tiết') }}</span> <i class="ti ti-arrow-right fs-12"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <x-form :action="route(RouteAdminSystem::USER_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$user->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.users.forms.edit-left', ['user' => $user])
                    @include('admin.users.forms.edit-right', ['user' => $user])
                </div>
            </x-form>
        </div>
    </div>
    @include('admin.users.partials.modal.modal-deposit')
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('public/libs/numeral/numeral.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.layouts.modal.modal-pick-address')
    @include('admin.scripts.google-map-input')
    @include('admin.users.partials.user-devices-script')
    @include('admin.users.partials.scripts.deposit-wallet-script')
@endpush
