@php
    use App\Traits\RouteAdminSystem;
    use App\AES\AESHelper;
    use App\Enums\Question\QuestionType;
    use App\Enums\Child\BornStatus;

    $parentUser = $children->user;
    $decryptedParentEmail = $parentUser?->email ? AESHelper::decrypt($parentUser->email) : '';
    $decryptedParentPhone = $parentUser?->phone ? AESHelper::decrypt($parentUser->phone) : '';
    $parentPackage = $parentUser?->userPackages()->where('status', 'active')->first();

    $latestIq = $children->ratings()->where('type', QuestionType::IQ)->latest()->first();
    $latestEq = $children->ratings()->where('type', QuestionType::EQ)->latest()->first();
    $latestAq = $children->ratings()->where('type', QuestionType::AQ)->latest()->first();
    $latestPq = $children->ratingPQs()->latest()->first();

    // Generate child initials
    $childNameParts = explode(' ', trim($children->fullname ?? ''));
    $childInitials = '';
    if (count($childNameParts) >= 2) {
        $childInitials = mb_substr($childNameParts[0], 0, 1) . mb_substr(end($childNameParts), 0, 1);
    } else {
        $childInitials = mb_substr($children->fullname ?? 'B', 0, 2);
    }
    $childInitials = mb_strtoupper($childInitials);
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-3"
                icon="baby-carriage"
                :title="__('Hồ sơ Trẻ em')"
                :subtitle="__('Quản lý chi tiết thông tin của trẻ, phụ huynh và toàn bộ chỉ số đánh giá phát triển')"
                :back-route="route(RouteAdminSystem::CHILDREN_INDEX)"
            />

            <!-- Child Profile Hero Banner -->
            <div class="child-hero-banner">
                <div class="row align-items-center g-3">
                    <!-- Left: Child & Parent Info -->
                    <div class="col-12 col-xl-6">
                        <div class="d-flex align-items-center gap-3">
                            @if($children->avatar && file_exists(public_path($children->avatar)))
                                <img src="{{ asset($children->avatar) }}" alt="{{ $children->fullname }}" class="child-hero-avatar">
                            @else
                                <div class="child-hero-initials">
                                    {{ $childInitials ?: 'BÉ' }}
                                </div>
                            @endif

                            <div>
                                <div class="user-hero-title">
                                    <span>{{ $children->fullname }}</span>
                                    <span class="user-code-badge">#{{ $children->id }}</span>
                                    @if($children->gender)
                                        <span class="badge {{ $children->gender->value == 1 ? 'bg-blue-lt' : 'bg-pink-lt' }}">
                                            {{ $children->gender->description() }}
                                        </span>
                                    @endif
                                    @if($children->is_born)
                                        <span class="badge bg-light text-muted">{{ $children->is_born->description() }}</span>
                                    @endif
                                    @if($children->status)
                                        <span class="badge {{ $children->status->badge() }}">{{ $children->status->description() }}</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center flex-wrap mt-1">
                                    @if($parentUser)
                                        <span class="user-meta-pill" title="Phụ huynh">
                                            <i class="ti ti-users text-primary"></i> 
                                            <strong>{{ __('Phụ huynh:') }}</strong> 
                                            <a href="{{ route('admin.user.edit', $parentUser->id) }}" class="text-primary text-decoration-none fw-bold ms-1" target="_blank">
                                                {{ $parentUser->fullname }}
                                            </a>
                                        </span>
                                    @endif
                                    @if($decryptedParentPhone)
                                        <span class="user-meta-pill" title="Số điện thoại phụ huynh">
                                            <i class="ti ti-phone text-success"></i> {{ $decryptedParentPhone }}
                                        </span>
                                    @endif
                                    @if($children->birthday)
                                        <span class="user-meta-pill" title="Ngày sinh">
                                            <i class="ti ti-calendar text-muted"></i> {{ format_date($children->birthday, 'd/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Assessment Stat Mini-Cards -->
                    <div class="col-12 col-xl-6">
                        <div class="user-hero-stats-grid">
                            <!-- IQ Score -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="ti ti-brain"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-1">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('IQ') }}</div>
                                    <div class="fw-bold fs-13 text-purple text-truncate">{{ $latestIq ? $latestIq->score . ' đ' : '--' }}</div>
                                </div>
                            </div>

                            <!-- EQ Score -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon" style="background: #fdf2f8; color: #db2777;">
                                    <i class="ti ti-heart"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-1">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('EQ') }}</div>
                                    <div class="fw-bold fs-13 text-pink text-truncate">{{ $latestEq ? $latestEq->score . ' đ' : '--' }}</div>
                                </div>
                            </div>

                            <!-- AQ Score -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="ti ti-leaf"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-1">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('AQ') }}</div>
                                    <div class="fw-bold fs-13 text-success text-truncate">{{ $latestAq ? $latestAq->score . ' đ' : '--' }}</div>
                                </div>
                            </div>

                            <!-- PQ Score -->
                            <div class="user-stat-card">
                                <div class="user-stat-icon" style="background: #fff7ed; color: #ea580c;">
                                    <i class="ti ti-activity"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-1">
                                    <div class="text-muted fs-11 fw-bold text-uppercase text-truncate">{{ __('PQ') }}</div>
                                    <div class="fw-bold fs-13 text-orange text-truncate">{{ $latestPq ? ($latestPq->score ?? ($latestPq->bmi ? 'BMI ' . $latestPq->bmi : '--')) : '--' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Edit -->
            <x-form id="notificationForm" :action="route(RouteAdminSystem::CHILDREN_UPDATE)" type="put" :validate="true">
                <input type="hidden" name="id" value="{{ $children->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.children.forms.edit-left', ['children' => $children])
                    @include('admin.children.forms.edit-right', ['children' => $children])
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
    <!-- button in datatable -->
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.children.scripts.scripts')
@endpush
