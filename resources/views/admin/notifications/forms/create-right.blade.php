@php
    use App\Traits\ImageSystem;
    use App\Traits\RouteAdminSystem;
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $settings = $settingRepository->getAll();
    $siteLogo = $settings->where('setting_key', 'site_logo')->first()?->plain_value ?? ImageSystem::DEFAULT_IMAGE;
    $siteName = $settings->where('setting_key', 'site_name')->first()?->plain_value ?? 'Chăm Con';
@endphp

<div class="col-12 col-lg-5 col-xl-4">
    <!-- Card 1: Live Mobile Push Preview -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.02rem;">
                <i class="ti ti-device-mobile text-primary me-2 fs-4"></i>
                {{ __('Mô phỏng hiển thị trên Mobile') }}
            </h5>
            <span class="badge bg-success-lt fw-bold fs-11">
                <i class="ti ti-eye me-1"></i>{{ __('Live Preview') }}
            </span>
        </div>
        <div class="card-body p-3 bg-light rounded-bottom-3">
            <!-- Smartphone Lockscreen Preview -->
            <div class="mobile-push-preview-container">
                <div class="mobile-push-preview-notch"></div>
                <div class="mobile-push-preview-clock" id="preview-clock">09:41</div>
                <div class="mobile-push-preview-date" id="preview-date">{{ date('l, d/m/Y') }}</div>

                <!-- Push Notification Card -->
                <div class="mobile-push-notification-banner">
                    <div class="banner-header">
                        <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="banner-app-icon"
                             onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%233b82f6\'><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'">
                        <span class="banner-app-name">{{ $siteName }}</span>
                        <span class="banner-time">{{ __('Vừa xong') }}</span>
                    </div>
                    <div class="banner-title" id="preview-notification-title">
                        {{ old('title', __('Tiêu đề thông báo của bạn')) }}
                    </div>
                    <div class="banner-body" id="preview-notification-body">
                        {{ old('message', __('Nội dung thông báo sẽ xuất hiện trực tiếp tại đây khi bạn soạn thảo ở bên trái...')) }}
                    </div>
                </div>
            </div>
            <small class="text-muted text-center d-block fs-11">
                <i class="ti ti-sparkles me-1"></i>{{ __('Hình ảnh mô phỏng thông báo đẩy trên thanh trạng thái / màn hình khóa smartphone.') }}
            </small>
        </div>
    </div>

    <!-- Card 2: Tóm tắt thông tin phát thông báo -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.02rem;">
                <i class="ti ti-info-circle text-primary me-2 fs-4"></i>
                {{ __('Thông tin phát thông báo') }}
            </h5>
        </div>
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                <span class="text-muted fs-13">{{ __('Kênh phát:') }}</span>
                <span class="badge bg-primary-lt fw-bold fs-12">
                    <i class="ti ti-brand-firebase me-1"></i>{{ __('Firebase Cloud Messaging') }}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between py-2">
                <span class="text-muted fs-13">{{ __('Phạm vi nhận:') }}</span>
                <span id="summary-target-badge" class="badge bg-success-lt fw-bold fs-12">
                    <i class="ti ti-world me-1"></i>{{ __('Tất cả phụ huynh') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Card 3: Floating Form Actions -->
    <x-admin.form-actions
        :submit-title="__('Gửi thông báo ngay')"
        submit-icon="ti ti-send"
        :back-route="route(RouteAdminSystem::NOTIFICATION_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
