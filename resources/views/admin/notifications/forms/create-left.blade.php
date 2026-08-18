@php
    use App\Enums\Notification\NotificationOption;
    use App\Enums\Notification\NotificationType;
    $selectedOption = old('option', NotificationOption::All->value);
@endphp

<div class="col-12 col-lg-7 col-xl-8">
    <!-- Card 1: Cấu hình Phạm vi & Đối tượng nhận -->
    <div class="notification-form-card">
        <div class="card-header">
            <h5 class="header-title">
                <i class="ti ti-users-group text-primary fs-4"></i>
                {{ __('Phạm vi & Đối tượng nhận thông báo') }}
            </h5>
            <span class="badge bg-primary-lt fs-12 fw-bold">
                {{ __('Bước 1') }}
            </span>
        </div>
        <div class="card-body p-4">
            <!-- Hidden inputs -->
            <input type="hidden" name="types" value="{{ NotificationType::Customer->value }}">
            <input type="hidden" id="notification_option_input" name="option" value="{{ $selectedOption }}">

            <!-- 3 Audience Segment Selection Cards -->
            <label class="form-label fw-bold text-dark mb-3">
                {{ __('Chọn đối tượng nhận tin:') }} <span class="text-danger">*</span>
            </label>
            <div class="row g-3 mb-4">
                <!-- Option 1: Tất cả phụ huynh -->
                <div class="col-12 col-md-4">
                    <div class="notification-audience-card {{ $selectedOption == NotificationOption::All->value ? 'active' : '' }}"
                         data-option="{{ NotificationOption::All->value }}"
                         data-summary="{{ __('Gửi toàn bộ phụ huynh') }}">
                        <div class="card-icon-wrap">
                            <i class="ti ti-world"></i>
                        </div>
                        <div class="card-text-wrap">
                            <div class="card-main-title">{{ __('Tất cả phụ huynh') }}</div>
                            <p class="card-sub-desc">{{ __('Phát thông báo đại trà đến toàn bộ người dùng app') }}</p>
                        </div>
                        <div class="card-radio-indicator"></div>
                    </div>
                </div>

                <!-- Option 2: Chọn phụ huynh cụ thể -->
                <div class="col-12 col-md-4">
                    <div class="notification-audience-card {{ $selectedOption == NotificationOption::One->value ? 'active' : '' }}"
                         data-option="{{ NotificationOption::One->value }}"
                         data-summary="{{ __('Chọn phụ huynh cụ thể') }}">
                        <div class="card-icon-wrap">
                            <i class="ti ti-user-check"></i>
                        </div>
                        <div class="card-text-wrap">
                            <div class="card-main-title">{{ __('Chọn cụ thể') }}</div>
                            <p class="card-sub-desc">{{ __('Tìm kiếm & chọn một hoặc nhiều phụ huynh chỉ định') }}</p>
                        </div>
                        <div class="card-radio-indicator"></div>
                    </div>
                </div>

                <!-- Option 3: Nhập file Excel -->
                <div class="col-12 col-md-4">
                    <div class="notification-audience-card {{ $selectedOption == NotificationOption::Excel->value ? 'active' : '' }}"
                         data-option="{{ NotificationOption::Excel->value }}"
                         data-summary="{{ __('Nhập từ file Excel') }}">
                        <div class="card-icon-wrap">
                            <i class="ti ti-file-spreadsheet"></i>
                        </div>
                        <div class="card-text-wrap">
                            <div class="card-main-title">{{ __('Nhập file Excel') }}</div>
                            <p class="card-sub-desc">{{ __('Tải lên danh sách người nhận theo file excel mẫu') }}</p>
                        </div>
                        <div class="card-radio-indicator"></div>
                    </div>
                </div>
            </div>

            <!-- Khung chọn khách hàng (Option 2) -->
            <div id="notification-customer-select" class="p-3 bg-light rounded-3 border mb-3" style="{{ $selectedOption == NotificationOption::One->value ? '' : 'display: none;' }}">
                <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-user-search text-primary"></i>
                    {{ __('Tìm kiếm & Chọn khách hàng nhận tin') }} <span class="text-danger">*</span>
                </label>
                <x-select name="user_id[]" class="select2-bs5-ajax" :data-url="route('admin.search.select.user')" id="user_id" multiple="multiple">
                </x-select>
                <small class="text-muted d-block mt-2">
                    <i class="ti ti-info-circle me-1"></i>{{ __('Nhập tên, số điện thoại hoặc email phụ huynh để tìm kiếm và chọn.') }}
                </small>
            </div>

            <!-- Khung upload file Excel (Option 3) -->
            <div id="notification-excel-file-wrapper" class="excel-upload-container mb-3" style="{{ $selectedOption == NotificationOption::Excel->value ? '' : 'display: none;' }}">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-lt text-success p-2 rounded-2 fs-14">
                            <i class="ti ti-file-spreadsheet"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark fs-14">{{ __('Tải lên danh sách từ file Excel') }} <span class="text-danger">*</span></div>
                            <small class="text-muted fs-12">{{ __('Định dạng hỗ trợ: .xlsx, .xls, .csv') }}</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.notification.downloadTemplate') }}" class="btn btn-sm btn-primary-subtle d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-2">
                        <i class="ti ti-download fs-5"></i>
                        <span>{{ __('Tải file Excel mẫu (.xlsx)') }}</span>
                    </a>
                </div>

                <!-- Custom Drag & Drop Dropzone Box -->
                <div class="excel-dropzone-box" id="excel-dropzone">
                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" class="excel-file-input">
                    
                    <!-- State 1: Chưa chọn file -->
                    <div class="dropzone-content" id="dropzone-empty-state">
                        <div class="dropzone-icon-wrap">
                            <i class="ti ti-cloud-upload"></i>
                        </div>
                        <div class="dropzone-text">
                            <span class="main-text">{{ __('Kéo & thả file Excel vào đây hoặc') }} <strong class="text-primary">{{ __('Chọn tệp từ máy tính') }}</strong></span>
                            <span class="sub-text">{{ __('Hỗ trợ file .xlsx, .xls, .csv dung lượng tối đa 10MB') }}</span>
                        </div>
                    </div>

                    <!-- State 2: Đã chọn file -->
                    <div class="dropzone-file-selected" id="dropzone-selected-state" style="display: none;">
                        <div class="file-icon-wrap">
                            <i class="ti ti-file-spreadsheet"></i>
                        </div>
                        <div class="file-info-wrap">
                            <div class="file-name" id="selected-file-name">danh_sach_phu_huynh.xlsx</div>
                            <div class="file-meta">
                                <span class="file-size" id="selected-file-size">0 KB</span>
                                <span class="badge bg-success-lt text-success fw-bold ms-2">
                                    <i class="ti ti-check me-1"></i>{{ __('Đã chọn sẵn sàng') }}
                                </span>
                            </div>
                        </div>
                        <button type="button" class="btn-remove-file" id="btn-remove-excel-file" title="{{ __('Hủy và chọn file khác') }}">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Soạn thảo Nội dung Thông báo -->
    <div class="notification-form-card">
        <div class="card-header">
            <h5 class="header-title">
                <i class="ti ti-message-2-share text-primary fs-4"></i>
                {{ __('Soạn thảo nội dung thông báo') }}
            </h5>
            <span class="badge bg-primary-lt fs-12 fw-bold">
                {{ __('Bước 2') }}
            </span>
        </div>
        <div class="card-body p-4">
            <!-- Mẫu thông báo nhanh (Quick Message Templates) -->
            <div class="mb-4">
                <label class="form-label fw-bold text-dark d-flex align-items-center gap-1 mb-2">
                    <i class="ti ti-wand text-primary"></i>
                    {{ __('Gợi ý mẫu thông báo nhanh:') }}
                </label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="quick-template-pill"
                            data-title="Chào mừng bạn đến với Chăm Con! 🎉"
                            data-message="Ứng dụng Chăm Con đồng hành cùng ba mẹ theo dõi sức khỏe, thể chất và giáo dục toàn diện cho bé yêu mỗi ngày.">
                        <i class="ti ti-sparkles"></i> {{ __('Chào mừng') }}
                    </button>
                    <button type="button" class="quick-template-pill"
                            data-title="Đã có bản cập nhật mới! 🚀"
                            data-message="Phiên bản mới mang đến nhiều tính năng cải tiến vượt trội và giao diện tối ưu hơn. Hãy cập nhật ngay hôm nay!">
                        <i class="ti ti-bell-ringing"></i> {{ __('Cập nhật App') }}
                    </button>
                    <button type="button" class="quick-template-pill"
                            data-title="Nhắc nhở lịch tiêm chủng định kỳ 💉"
                            data-message="Ba mẹ đừng quên kiểm tra và theo dõi lịch tiêm chủng sắp tới của bé để đảm bảo bé được tiêm phòng đúng lịch nhé!">
                        <i class="ti ti-vaccine"></i> {{ __('Nhắc tiêm chủng') }}
                    </button>
                    <button type="button" class="quick-template-pill"
                            data-title="Ưu đãi đặc biệt dành riêng cho bạn! 🎁"
                            data-message="Khám phá các tính năng cao cấp không giới hạn cùng gói Chăm Con VIP với mức giá ưu đãi nhất tháng này.">
                        <i class="ti ti-gift"></i> {{ __('Ưu đãi VIP') }}
                    </button>
                </div>
            </div>

            <!-- Title -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label fw-bold text-dark mb-0">
                        {{ __('Tiêu đề thông báo') }} <span class="text-danger">*</span>
                    </label>
                    <span class="char-counter" id="title-char-count">0/100</span>
                </div>
                <input type="text" id="notification_title" name="title" class="form-control form-control-lg"
                       value="{{ old('title') }}" required maxlength="100"
                       placeholder="{{ __('Nhập tiêu đề thông báo ngắn gọn, hấp dẫn...') }}">
                <small class="text-muted d-block mt-1 fs-12">
                    <i class="ti ti-device-mobile me-1"></i>{{ __('Khuyến nghị: Tiêu đề dưới 50 ký tự để hiển thị trọn vẹn trên màn hình khóa.') }}
                </small>
            </div>

            <!-- Message -->
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label fw-bold text-dark mb-0">
                        {{ __('Nội dung chi tiết') }} <span class="text-danger">*</span>
                    </label>
                    <span class="char-counter" id="message-char-count">0/300</span>
                </div>
                <textarea id="notification_message" name="message" class="form-control" rows="5"
                          required maxlength="300"
                          placeholder="{{ __('Nhập nội dung thông điệp muốn truyền tải đến người dùng...') }}">{{ old('message') }}</textarea>
                <small class="text-muted d-block mt-1 fs-12">
                    <i class="ti ti-device-mobile me-1"></i>{{ __('Khuyến nghị: Nội dung từ 80 - 200 ký tự mang lại tỷ lệ đọc cao nhất.') }}
                </small>
            </div>
        </div>
    </div>
</div>
