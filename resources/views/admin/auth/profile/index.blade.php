@php
    use App\Traits\RouteAdminSystem;
    $roleName = $auth->roles->first()?->name ?? 'Quản trị viên';
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route(RouteAdminSystem::PROFILE_UPDATE)" type="put" enctype="multipart/form-data" :validate="true">
                <div class="row g-4">
                    {{-- Left Side: Profile Hero Card --}}
                    <div class="col-12 col-lg-4">
                        <div class="card custom-shadow h-100">
                            <div class="card-body text-center p-4">
                                {{-- Avatar Upload Section --}}
                                <div class="position-relative d-inline-block mb-3">
                                    @include('admin.partials.image-upload', ['name' => 'avatar', 'value' => $auth->avatar, 'showImage' => 'avatar'])
                                </div>

                                <h4 class="fw-bold text-dark mb-1">{{ $auth->fullname ?? 'Quản trị viên' }}</h4>
                                <div class="mb-3">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fs-12 fw-semibold">
                                        <i class="ti ti-shield-check me-1"></i>{{ $roleName }}
                                    </span>
                                </div>

                                <hr class="my-3 opacity-25">

                                {{-- Quick Info List --}}
                                <div class="text-start fs-13 text-secondary mb-4" style="line-height: 2.2;">
                                    <div class="d-flex align-items-center justify-content-between border-bottom py-1">
                                        <span><i class="ti ti-id me-2 text-primary"></i>Mã quản trị:</span>
                                        <span class="fw-bold text-dark">{{ $auth->code ?? '#' . $auth->id }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-bottom py-1">
                                        <span><i class="ti ti-mail me-2 text-primary"></i>Email:</span>
                                        <span class="fw-bold text-dark text-truncate ms-2" style="max-width: 180px;">{{ $auth->email ?? 'Chưa cập nhật' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between py-1">
                                        <span><i class="ti ti-phone me-2 text-primary"></i>Số điện thoại:</span>
                                        <span class="fw-bold text-dark">{{ $auth->phone ?? 'Chưa cập nhật' }}</span>
                                    </div>
                                </div>

                                {{-- Password Link Button --}}
                                <a href="{{ route(RouteAdminSystem::PASSWORD_INDEX) }}" class="btn btn-outline-primary w-100 rounded-pill fw-semibold py-2 text-decoration-none">
                                    <i class="ti ti-key me-1"></i> {{ __('Đổi mật khẩu bảo mật') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Profile Form Card --}}
                    <div class="col-12 col-lg-8">
                        <div class="card custom-shadow h-100">
                            {{-- Page Header Banner --}}
                            <x-admin.page-header
                                icon="user-check"
                                :title="__('Thông tin cá nhân')"
                                :subtitle="__('Quản lý hồ sơ cá nhân và cập nhật thông tin liên hệ tài khoản')"
                            />

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    {{-- Fullname --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-user text-primary me-1"></i> {{ __('Họ và tên') }} <span class="text-danger">*</span>
                                        </label>
                                        <x-input name="fullname" :value="$auth->fullname" :required="true" placeholder="{{ __('Nhập họ và tên đầy đủ') }}"/>
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-phone text-primary me-1"></i> {{ __('Số điện thoại') }} <span class="text-danger">*</span>
                                        </label>
                                        <x-input-phone name="phone" :value="$auth->phone" :required="true" />
                                    </div>

                                    {{-- Address --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-map-pin text-primary me-1"></i> {{ __('Địa chỉ') }}
                                        </label>
                                        <x-input name="address" :value="$auth->address" placeholder="{{ __('Nhập địa chỉ') }}"/>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions Footer --}}
                            <div class="card-footer bg-light border-top p-3 d-flex justify-content-end">
                                <x-button.submit :title="__('Cập nhật thông tin')" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection
