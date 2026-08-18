@php
    use App\Traits\RouteAdminSystem;
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route(RouteAdminSystem::PASSWORD_UPDATE)" type="put" enctype="multipart/form-data" :validate="true">
                <div class="row g-4">
                    {{-- Left Side: Security Info Card --}}
                    <div class="col-12 col-lg-4">
                        <div class="card custom-shadow h-100">
                            <div class="card-body p-4 text-center">
                                <div class="ph-icon-box mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem; border-radius: 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; color: #fff;">
                                    <i class="ti ti-shield-lock"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">{{ __('Bảo mật tài khoản') }}</h5>
                                <p class="text-secondary fs-13 mb-4" style="line-height: 1.6;">
                                    Đổi mật khẩu định kỳ giúp bảo vệ tài khoản quản trị của bạn khỏi các truy cập trái phép.
                                </p>

                                <div class="text-start bg-light rounded-3 p-3 mb-4 border">
                                    <div class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center">
                                        <i class="ti ti-info-circle text-primary me-1 fs-5"></i> Mẹo tạo mật khẩu mạnh:
                                    </div>
                                    <ul class="text-secondary fs-12 mb-0 ps-3 space-y-1" style="line-height: 1.8;">
                                        <li>Sử dụng ít nhất 8 ký tự</li>
                                        <li>Kết hợp chữ hoa, chữ thường và chữ số</li>
                                        <li>Bao gồm ký tự đặc biệt (VD: @, #, $, !)</li>
                                        <li>Không dùng mật khẩu từng sử dụng trước đây</li>
                                    </ul>
                                </div>

                                <a href="{{ route(RouteAdminSystem::PROFILE_INDEX) }}" class="btn btn-outline-secondary w-100 rounded-pill fw-semibold py-2 text-decoration-none">
                                    <i class="ti ti-arrow-left me-1"></i> {{ __('Về Thông tin cá nhân') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Password Change Form Card --}}
                    <div class="col-12 col-lg-8">
                        <div class="card custom-shadow h-100">
                            {{-- Page Header Banner --}}
                            <x-admin.page-header
                                icon="key"
                                :title="__('Đổi mật khẩu')"
                                :subtitle="__('Cập nhật mật khẩu mới để bảo vệ an toàn cho tài khoản quản trị')"
                            />

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <!-- Mật khẩu cũ -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-lock text-primary me-1"></i> {{ __('Mật khẩu hiện tại') }} <span class="text-danger">*</span>
                                        </label>
                                        <x-input-password name="old_password" :required="true" placeholder="{{ __('Nhập mật khẩu hiện tại') }}"/>
                                    </div>

                                    <!-- Mật khẩu mới -->
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-key text-primary me-1"></i> {{ __('Mật khẩu mới') }} <span class="text-danger">*</span>
                                        </label>
                                        <x-input-password name="password" :required="true" placeholder="{{ __('Nhập mật khẩu mới') }}"/>
                                    </div>

                                    <!-- Xác nhận mật khẩu mới -->
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold text-dark fs-13">
                                            <i class="ti ti-check text-primary me-1"></i> {{ __('Xác nhận mật khẩu mới') }} <span class="text-danger">*</span>
                                        </label>
                                        <x-input-password name="password_confirmation" :required="true" data-parsley-equalto="input[name='password']" data-parsley-equalto-message="{{ __('Mật khẩu xác nhận không khớp.') }}" placeholder="{{ __('Nhập lại mật khẩu mới') }}"/>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions Footer --}}
                            <div class="card-footer bg-light border-top p-3 d-flex justify-content-end">
                                <x-button.submit :title="__('Đổi mật khẩu')" class="btn btn-success rounded-pill px-4 py-2 fw-bold" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection
