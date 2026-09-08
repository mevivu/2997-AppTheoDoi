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
                                        <div class="form-text text-muted fs-11 mt-1">
                                            <i class="ti ti-info-circle text-primary me-1"></i>{{ __('Bật để áp dụng kiểm tra phiên bản trên nền tảng này.') }}
                                        </div>
                                    </div>
                                </div>

                                 <!-- Phiên bản thông báo (Notify) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                             <span><span class="ti ti-versions text-primary me-1"></span> {{ __('Phiên bản thông báo (Notify)') }}: <span class="text-danger">*</span></span>
                                             <span class="badge bg-green-lt fs-11">{{ __('Gợi ý') }}</span>
                                         </label>
                                         <x-input name="notify" :value="old('notify', $appVersion->notify)" :required="true" placeholder="e.g. 1.0.0" />
                                         <div class="form-text text-muted fs-11 mt-1">
                                             <i class="ti ti-bell-ringing text-success me-1"></i>{{ __('Bản mới nhất. Máy < Notify sẽ nhận popup gợi ý cập nhật (hoãn 24h).') }}
                                         </div>
                                     </div>
                                 </div>
                                 <!-- Phiên bản bắt buộc (Required) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                             <span><span class="ti ti-alert-triangle text-danger me-1"></span> {{ __('Phiên bản bắt buộc (Required)') }}: <span class="text-danger">*</span></span>
                                             <span class="badge bg-danger-lt fs-11">{{ __('Khóa màn hình') }}</span>
                                         </label>
                                         <x-input name="required" :value="old('required', $appVersion->required)" :required="true" placeholder="e.g. 1.0.0" />
                                         <div class="form-text text-muted fs-11 mt-1">
                                             <i class="ti ti-lock text-danger me-1"></i>{{ __('Bản tối thiểu. Máy < Required sẽ bị khóa màn hình, buộc cập nhật mới dùng được.') }}
                                         </div>
                                     </div>
                                 </div>
                                 <!-- Phiên bản kiểm tra (Checking) -->
                                 <div class="col-12 col-md-4">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                             <span><span class="ti ti-checklist text-info me-1"></span> {{ __('Phiên bản kiểm tra (Checking)') }}:</span>
                                             <span class="badge bg-blue-lt fs-11">{{ __('Duyệt Store') }}</span>
                                         </label>
                                         <x-input name="checking_version" :value="old('checking_version', $appVersion->checking_version)" :required="false" placeholder="e.g. 1.0.1 (để trống nếu không duyệt)" />
                                         <div class="form-text text-muted fs-11 mt-1">
                                             <i class="ti ti-shield-check text-info me-1"></i>{{ __('Chỉ điền khi đang nộp bản build lên Store để ẩn popup cho Reviewer. Xóa khi duyệt xong.') }}
                                         </div>
                                     </div>
                                 </div>

                                <!-- Link cập nhật -->
                                <div class="col-12 col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold d-flex align-items-center justify-content-between flex-wrap gap-1">
                                            <span><span class="ti ti-link text-primary me-1"></span> {{ __('Đường dẫn tải/cập nhật ứng dụng (Store URL)') }}: <span class="text-danger">*</span></span>
                                            @if(!empty($appVersion->update_url))
                                                <a href="{{ $appVersion->update_url }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 fs-11 text-decoration-none">
                                                    <i class="ti ti-external-link me-1"></i>{{ __('Mở thử link Store') }}
                                                </a>
                                            @endif
                                        </label>
                                        <x-input name="update_url" :value="old('update_url', $appVersion->update_url)" :required="true" placeholder="e.g. https://play.google.com/store hoặc https://apps.apple.com" />
                                        <div class="form-text text-muted fs-11 mt-1">
                                            <i class="ti ti-info-circle text-primary me-1"></i>{{ __('Khi người dùng nhấn "Cập nhật ngay" trên ứng dụng mobile, app sẽ tự động mở link này.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Nhật ký phát hành (Tiếng Việt) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-notes text-secondary me-1"></span> {{ __('Nhật ký phát hành (Tiếng Việt)') }}: <span class="text-danger">*</span></label>
                                        <textarea name="release_notes_vi" class="form-control" rows="5" required placeholder="- Thêm tính năng mới&#10;- Sửa lỗi và tối ưu hiệu năng">{{ old('release_notes_vi', $appVersion->release_notes['vi'] ?? '') }}</textarea>
                                        <div class="form-text text-muted fs-11 mt-1">
                                            <i class="ti ti-device-mobile text-secondary me-1"></i>{{ __('Nội dung hiển thị trên popup cập nhật của thiết bị tiếng Việt.') }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Nhật ký phát hành (Tiếng Anh) -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><span class="ti ti-notes text-secondary me-1"></span> {{ __('Nhật ký phát hành (Tiếng Anh)') }}: <span class="text-danger">*</span></label>
                                        <textarea name="release_notes_en" class="form-control" rows="5" required placeholder="- Add new features&#10;- Bug fixes and performance improvements">{{ old('release_notes_en', $appVersion->release_notes['en'] ?? '') }}</textarea>
                                        <div class="form-text text-muted fs-11 mt-1">
                                            <i class="ti ti-device-mobile text-secondary me-1"></i>{{ __('Nội dung hiển thị trên popup cập nhật của thiết bị tiếng Anh / ngôn ngữ khác.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Thao tác đăng & Cẩm nang cấu hình -->
                    <div class="col-12 col-lg-4 col-xl-3">
                        <!-- Card Cẩm nang Cấu hình -->
                        <div class="card border-0 custom-shadow rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom px-4 py-3">
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                                    <i class="ti ti-book text-primary me-2 fs-4"></i>
                                    {{ __('Cẩm nang Cấu hình') }}
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                {{-- Quy trình 4 bước --}}
                                <div class="mb-3">
                                    <span class="badge bg-primary-lt px-2 py-1 mb-2 fw-bold d-inline-flex align-items-center">
                                        <i class="ti ti-list-check me-1"></i>{{ __('Quy trình 4 bước phát hành') }}
                                    </span>
                                    <ol class="text-secondary fs-12 ps-3 mb-0" style="line-height: 1.6;">
                                        <li class="mb-1"><strong>{{ __('Phát hành Store:') }}</strong> {{ __('Nộp build mới lên Google Play / App Store và đợi duyệt hoàn tất.') }}</li>
                                        <li class="mb-1"><strong>{{ __('Link Store:') }}</strong> {{ __('Copy link ứng dụng chính xác trên Store dán vào ô "Đường dẫn tải/cập nhật".') }}</li>
                                        <li class="mb-1"><strong>{{ __('Đặt Version:') }}</strong> {{ __('Cập nhật "Notify" (bản mới nhất) và "Required" (bản tối thiểu).') }}</li>
                                        <li><strong>{{ __('Nhật ký:') }}</strong> {{ __('Điền tóm tắt cải tiến/sửa lỗi rồi nhấn "Lưu thay đổi".') }}</li>
                                    </ol>
                                </div>

                                <hr class="my-3 text-muted">

                                {{-- Quy tắc vàng --}}
                                <div class="mb-3">
                                    <span class="badge bg-warning-lt px-2 py-1 mb-2 fw-bold text-warning d-inline-flex align-items-center">
                                        <i class="ti ti-alert-triangle me-1"></i>{{ __('Quy tắc logic vàng') }}
                                    </span>
                                    <ul class="text-secondary fs-12 ps-3 mb-0" style="line-height: 1.6;">
                                        <li class="mb-1 text-danger fw-semibold">
                                            {{ __('Luôn đảm bảo:') }} <code>Required &le; Notify</code>.
                                        </li>
                                        <li class="mb-1">
                                            <strong>Checking:</strong> {{ __('Chỉ điền khi đang nộp duyệt để Reviewer không bị chặn. Sau khi app đã Live thì xóa ô này.') }}
                                        </li>
                                        <li>
                                            <strong>Trạng thái:</strong> {{ __('Nếu "Tạm khóa", app sẽ bỏ qua kiểm tra phiên bản trên nền tảng này.') }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="bg-light p-2 rounded border text-muted fs-11">
                                    <i class="ti ti-info-circle text-primary me-1"></i>
                                    {{ __('Mẹo: Có thể bấm "Mở thử link Store" để kiểm tra đường dẫn có hoạt động tốt trước khi lưu.') }}
                                </div>
                            </div>
                        </div>

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
