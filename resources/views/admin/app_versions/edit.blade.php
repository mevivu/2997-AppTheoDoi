@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="versions"
                :title="__('Cấu hình Phiên bản Ứng dụng')"
                :subtitle="__('Quản lý phiên bản bắt buộc, thông báo và đường dẫn cập nhật app: ') . ucfirst($appVersion->platform) . ' (' . ucfirst($appVersion->app_type) . ')'"
                :back-route="route(RouteAdminSystem::APP_VERSION_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::APP_VERSION_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$appVersion->id" />
                <div class="row g-4 justify-content-center">
                    <!-- Cột trái: Thông tin cấu hình -->
                    <div class="col-12 col-lg-8 col-xl-9">
                        <div class="card border-0 custom-shadow rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom px-4 py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                                    <i class="ti ti-adjustments-horizontal text-primary me-2 fs-4"></i>
                                    {{ __('Thông tin cấu hình Phiên bản') }}
                                </h5>
                            </div>
                            <div class="row card-body p-4 g-3">
                                <!-- Nền tảng -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-device-mobile"></span> {{ __('Nền tảng') }}:</label>
                                        <x-input name="platform_display" :value="ucfirst($appVersion->platform)" :disabled="true" />
                                    </div>
                                </div>
                                <!-- Loại App -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-apps"></span> {{ __('Loại ứng dụng') }}:</label>
                                        <x-input name="app_type_display" :value="ucfirst($appVersion->app_type)" :disabled="true" />
                                    </div>
                                </div>
                                <!-- Trạng thái hoạt động -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-status-change"></span> {{ __('Trạng thái') }}: <span class="text-danger">*</span></label>
                                        <x-select name="is_active" :required="true">
                                            <x-select-option :value="1" :title="__('Hoạt động')" :option="$appVersion->is_active ? 1 : 0" />
                                            <x-select-option :value="0" :title="__('Tạm khóa')" :option="$appVersion->is_active ? 1 : 0" />
                                        </x-select>
                                    </div>
                                </div>

                                 <!-- Phiên bản thông báo (Notify) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold"><span class="ti ti-versions"></span> {{ __('Phiên bản thông báo (Notify)') }}: <span class="text-danger">*</span></label>
                                         <x-input name="notify" :value="old('notify', $appVersion->notify)" :required="true" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>
                                 <!-- Phiên bản bắt buộc (Required) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold"><span class="ti ti-alert-triangle"></span> {{ __('Phiên bản bắt buộc (Required)') }}: <span class="text-danger">*</span></label>
                                         <x-input name="required" :value="old('required', $appVersion->required)" :required="true" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>
                                 <!-- Phiên bản kiểm tra (Checking) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold"><span class="ti ti-checklist"></span> {{ __('Phiên bản kiểm tra (Checking)') }}:</label>
                                         <x-input name="checking_version" :value="old('checking_version', $appVersion->checking_version)" :required="false" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>

                                <!-- Link cập nhật -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-link"></span> {{ __('Đường dẫn tải/cập nhật ứng dụng') }}: <span class="text-danger">*</span></label>
                                        <x-input name="update_url" :value="old('update_url', $appVersion->update_url)" :required="true" placeholder="e.g. https://play.google.com/store" />
                                    </div>
                                </div>

                                <!-- Nhật ký phát hành (Tiếng Việt) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-notes"></span> {{ __('Nhật ký phát hành (Tiếng Việt)') }}: <span class="text-danger">*</span></label>
                                        <textarea name="release_notes_vi" class="form-control" rows="4" required>{{ old('release_notes_vi', $appVersion->release_notes['vi'] ?? '') }}</textarea>
                                    </div>
                                </div>
                                <!-- Nhật ký phát hành (Tiếng Anh) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-notes"></span> {{ __('Nhật ký phát hành (Tiếng Anh)') }}: <span class="text-danger">*</span></label>
                                        <textarea name="release_notes_en" class="form-control" rows="4" required>{{ old('release_notes_en', $appVersion->release_notes['en'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Thao tác đăng -->
                    <div class="col-12 col-lg-4 col-xl-3">
                        {{-- Floating Form Actions --}}
                        <x-admin.form-actions
                            :submit-title="__('Lưu thay đổi')"
                            submit-icon="ti ti-device-floppy"
                            :back-route="route(RouteAdminSystem::APP_VERSION_INDEX)"
                            :back-title="__('Quay lại')"
                        />
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection
