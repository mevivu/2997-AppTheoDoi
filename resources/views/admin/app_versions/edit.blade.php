@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-form :action="route('admin.app-version.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$appVersion->id" />
                <div class="row justify-content-center">
                    <!-- Cột trái: Thông tin cấu hình -->
                    <div class="col-12 col-md-9">
                        <div class="card custom-shadow mb-3">
                            <div class="card-header">
                                <h2 class="mb-0">{{ __('Thông tin cấu hình Phiên bản') }}</h2>
                            </div>
                            <div class="row card-body">
                                <!-- Nền tảng -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-device-mobile"></span> {{ __('Nền tảng') }}:</label>
                                        <x-input name="platform_display" :value="ucfirst($appVersion->platform)" :disabled="true" />
                                    </div>
                                </div>
                                <!-- Loại App -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-apps"></span> {{ __('Loại ứng dụng') }}:</label>
                                        <x-input name="app_type_display" :value="ucfirst($appVersion->app_type)" :disabled="true" />
                                    </div>
                                </div>
                                <!-- Trạng thái hoạt động -->
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-status-change"></span> {{ __('Trạng thái') }}:</label>
                                        <x-select name="is_active" :required="true">
                                            <x-select-option :value="1" :title="__('Hoạt động')" :option="$appVersion->is_active ? 1 : 0" />
                                            <x-select-option :value="0" :title="__('Tạm khóa')" :option="$appVersion->is_active ? 1 : 0" />
                                        </x-select>
                                    </div>
                                </div>

                                 <!-- Phiên bản thông báo (Notify) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="control-label"><span class="ti ti-versions"></span> {{ __('Phiên bản thông báo (Notify)') }}:</label>
                                         <x-input name="notify" :value="old('notify', $appVersion->notify)" :required="true" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>
                                 <!-- Phiên bản bắt buộc (Required) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="control-label"><span class="ti ti-alert-triangle"></span> {{ __('Phiên bản bắt buộc (Required)') }}:</label>
                                         <x-input name="required" :value="old('required', $appVersion->required)" :required="true" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>
                                 <!-- Phiên bản kiểm tra (Checking) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="control-label"><span class="ti ti-checklist"></span> {{ __('Phiên bản kiểm tra (Checking)') }}:</label>
                                         <x-input name="checking_version" :value="old('checking_version', $appVersion->checking_version)" :required="false" placeholder="e.g. 1.0.0" />
                                     </div>
                                 </div>

                                <!-- Link cập nhật -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-link"></span> {{ __('Đường dẫn tải/cập nhật ứng dụng') }}:</label>
                                        <x-input name="update_url" :value="old('update_url', $appVersion->update_url)" :required="true" placeholder="e.g. https://play.google.com/store" />
                                    </div>
                                </div>

                                <!-- Nhật ký phát hành (Tiếng Việt) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-notes"></span> {{ __('Nhật ký phát hành (Tiếng Việt)') }}:</label>
                                        <textarea name="release_notes_vi" class="form-control" rows="4" required>{{ old('release_notes_vi', $appVersion->release_notes['vi'] ?? '') }}</textarea>
                                    </div>
                                </div>
                                <!-- Nhật ký phát hành (Tiếng Anh) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="control-label"><span class="ti ti-notes"></span> {{ __('Nhật ký phát hành (Tiếng Anh)') }}:</label>
                                        <textarea name="release_notes_en" class="form-control" rows="4" required>{{ old('release_notes_en', $appVersion->release_notes['en'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Thao tác đăng -->
                    <div class="col-12 col-md-3">
                        <div class="card mb-3 custom-shadow">
                            <div class="card-header">
                                <span class="ti ti-upload me-1"></span>
                                {{ __('Đăng') }}
                            </div>
                            <div class="card-body p-2">
                                <div class="w-100 d-flex align-items-center h-100 gap-2">
                                    <x-button.submit :title="__('Lưu thay đổi')" class="btn btn-primary flex-grow-1" />
                                    <x-link :href="route('admin.app-version.index')" class="btn btn-outline w-50">
                                        {{ __('Quay lại') }}
                                    </x-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection
