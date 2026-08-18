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

    $predHeight = $heightPrediction['predicting_adult_height'] ?? 0;

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
                :subtitle="__('Quản lý chi tiết thông tin của trẻ, phụ huynh, dự báo chiều cao và toàn bộ chỉ số đánh giá')"
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

                            <div class="min-w-0 flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                    <h4 class="mb-0 fw-bold text-dark fs-16">{{ $children->fullname }}</h4>
                                    <span class="user-code-badge">#{{ $children->id }}</span>
                                    @if($children->gender)
                                        <span class="badge {{ $children->gender->value == 1 ? 'bg-blue-lt' : 'bg-pink-lt' }} px-2 py-1 fs-12">
                                            {{ $children->gender->description() }}
                                        </span>
                                    @endif
                                    @if($children->is_born)
                                        <span class="badge bg-light text-muted px-2 py-1 fs-12">{{ $children->is_born->description() }}</span>
                                    @endif
                                    @if($children->status)
                                        <span class="badge {{ $children->status->badge() }} px-2 py-1 fs-12">{{ $children->status->description() }}</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 text-muted fs-12">
                                    @if($parentUser)
                                        <span class="d-inline-flex align-items-center" title="Phụ huynh">
                                            <i class="ti ti-users text-primary me-1"></i> 
                                            {{ __('Phụ huynh:') }} 
                                            <a href="{{ route('admin.user.edit', $parentUser->id) }}" class="text-primary text-decoration-none fw-bold ms-1" target="_blank">
                                                {{ $parentUser->fullname }}
                                            </a>
                                        </span>
                                    @endif
                                    @if($decryptedParentPhone)
                                        <span class="d-inline-flex align-items-center ms-1" title="Số điện thoại phụ huynh">
                                            <i class="ti ti-phone text-success me-1"></i> {{ $decryptedParentPhone }}
                                        </span>
                                    @endif
                                    @if($children->birthday)
                                        <span class="d-inline-flex align-items-center ms-1" title="Ngày sinh">
                                            <i class="ti ti-calendar text-muted me-1"></i> {{ format_date($children->birthday, 'd/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Assessment Stat Mini-Cards -->
                    <div class="col-12 col-xl-6">
                        <div class="child-hero-stats-grid">
                            <!-- Dự báo chiều cao -->
                            <div class="child-stat-card">
                                <div class="child-stat-icon" style="background: #e0f2fe; color: #0284c7;">
                                    <i class="ti ti-ruler-2"></i>
                                </div>
                                <div class="child-stat-content">
                                    <div class="child-stat-label">{{ __('DỰ BÁO CC') }}</div>
                                    <div class="child-stat-val text-info">{{ ($latestPq && $predHeight > 0) ? $predHeight . ' cm' : '--' }}</div>
                                </div>
                            </div>

                            <!-- IQ Score -->
                            <div class="child-stat-card">
                                <div class="child-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="ti ti-brain"></i>
                                </div>
                                <div class="child-stat-content">
                                    <div class="child-stat-label">{{ __('IQ') }}</div>
                                    <div class="child-stat-val text-purple">
                                        {{ ($latestIq && $latestIq->score !== null && $latestIq->score !== '') ? $latestIq->score . ' đ' : '--' }}
                                    </div>
                                </div>
                            </div>

                            <!-- EQ Score -->
                            <div class="child-stat-card">
                                <div class="child-stat-icon" style="background: #fdf2f8; color: #db2777;">
                                    <i class="ti ti-heart"></i>
                                </div>
                                <div class="child-stat-content">
                                    <div class="child-stat-label">{{ __('EQ') }}</div>
                                    <div class="child-stat-val text-pink">
                                        {{ ($latestEq && $latestEq->score !== null && $latestEq->score !== '') ? $latestEq->score . ' đ' : '--' }}
                                    </div>
                                </div>
                            </div>

                            <!-- PQ Score -->
                            <div class="child-stat-card">
                                <div class="child-stat-icon" style="background: #fff7ed; color: #ea580c;">
                                    <i class="ti ti-activity"></i>
                                </div>
                                <div class="child-stat-content">
                                    <div class="child-stat-label">{{ __('PQ') }}</div>
                                    <div class="child-stat-val text-orange">
                                        {{ ($latestPq && $latestPq->height) ? $latestPq->height . ' cm' : (($latestPq && $latestPq->bmi) ? 'BMI ' . $latestPq->bmi : '--') }}
                                    </div>
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
                    @include('admin.children.forms.edit-left', ['children' => $children, 'heightPrediction' => $heightPrediction])
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
