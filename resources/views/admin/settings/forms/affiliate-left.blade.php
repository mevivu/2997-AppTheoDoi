@php
    $settingsByKey = $settings->keyBy('setting_key');
@endphp

<div class="card custom-shadow">
    <div class="card-header border-bottom-0 pb-0">
        <ul class="nav nav-tabs card-header-tabs" id="affiliateTabs" role="tablist">
            {{-- Tab Cài đặt chung --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab" aria-controls="tab-general" aria-selected="true">
                    <i class="ti ti-settings text-muted me-1"></i>
                    {{ __('Cài đặt chung') }}
                </button>
            </li>

            {{-- Tab Mẹ Đồng --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="bronze-tab" data-bs-toggle="tab" data-bs-target="#tab-bronze" type="button" role="tab" aria-controls="tab-bronze" aria-selected="false">
                    <i class="ti ti-medal me-1" style="color: #CD7F32;"></i>
                    <span style="color: #CD7F32;">{{ __('Mẹ Đồng') }}</span>
                    <span class="badge bg-orange-lt ms-1">Cấp 1</span>
                </button>
            </li>

            {{-- Tab Mẹ Bạc --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="silver-tab" data-bs-toggle="tab" data-bs-target="#tab-silver" type="button" role="tab" aria-controls="tab-silver" aria-selected="false">
                    <i class="ti ti-award me-1" style="color: #6C757D;"></i>
                    <span style="color: #6C757D;">{{ __('Mẹ Bạc') }}</span>
                    <span class="badge bg-secondary-lt ms-1">Cấp 2</span>
                </button>
            </li>

            {{-- Tab Mẹ Vàng --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="gold-tab" data-bs-toggle="tab" data-bs-target="#tab-gold" type="button" role="tab" aria-controls="tab-gold" aria-selected="false">
                    <i class="ti ti-crown me-1" style="color: #E6A100;"></i>
                    <span style="color: #E6A100;">{{ __('Mẹ Vàng') }}</span>
                    <span class="badge bg-yellow-lt ms-1">Cấp 3</span>
                </button>
            </li>

            {{-- Tab Mẹ Kim Cương --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="diamond-tab" data-bs-toggle="tab" data-bs-target="#tab-diamond" type="button" role="tab" aria-controls="tab-diamond" aria-selected="false">
                    <i class="ti ti-diamond me-1" style="color: #00B4D8;"></i>
                    <span style="color: #00B4D8;">{{ __('Mẹ Kim Cương') }}</span>
                    <span class="badge bg-cyan-lt ms-1">Cấp 4</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body pt-4">
        <div class="tab-content" id="affiliateTabContent">
            {{-- ========================================================================= --}}
            {{-- TAB 1: CÀI ĐẶT CHUNG --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="general-tab">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar avatar-sm bg-primary-lt rounded-circle me-2">
                        <i class="ti ti-adjustments-alt fs-3"></i>
                    </span>
                    <div>
                        <h3 class="card-title mb-0">{{ __('Cấu hình chung hệ thống Affiliate') }}</h3>
                        <div class="text-muted small">{{ __('Thiết lập kích hoạt chương trình và mức thưởng cho người mới tham gia') }}</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        @include('admin.settings.forms.partials.setting-field', [
                            'setting' => $settingsByKey->get('affiliate_active'),
                            'label' => __('Bật / Tắt chương trình tiếp thị liên kết (Affiliate)'),
                            'hint' => __('Bật để cho phép người dùng nhận thưởng khi giới thiệu thành viên mới.')
                        ])
                    </div>

                    <div class="col-12 col-md-6">
                        @include('admin.settings.forms.partials.setting-field', [
                            'setting' => $settingsByKey->get('affiliate_reward_referee'),
                            'label' => __('Thưởng chào mừng người mới đăng ký (VNĐ)'),
                            'hint' => __('Số tiền cộng ngay vào ví của người mới khi đăng ký qua mã giới thiệu hợp lệ.')
                        ])
                    </div>

                    <div class="col-12 col-md-6">
                        @include('admin.settings.forms.partials.setting-field', [
                            'setting' => $settingsByKey->get('affiliate_reward_referrer'),
                            'label' => __('Thưởng người giới thiệu mặc định dự phòng (VNĐ)'),
                            'hint' => __('Định mức dự phòng khi không xác định được cấp bậc.')
                        ])
                    </div>

                    {{-- Render các cài đặt khác trong nhóm Affiliate chưa được phân loại --}}
                    @php
                        $knownKeys = [
                            'affiliate_active', 'affiliate_reward_referee', 'affiliate_reward_referrer',
                            'affiliate_sales_bronze', 'affiliate_sales_silver', 'affiliate_sales_gold', 'affiliate_sales_diamond',
                            'affiliate_users_bronze', 'affiliate_users_silver', 'affiliate_users_gold', 'affiliate_users_diamond',
                            'affiliate_commission_bronze', 'affiliate_commission_silver', 'affiliate_commission_gold', 'affiliate_commission_diamond',
                            'affiliate_reward_user_bronze', 'affiliate_reward_user_silver', 'affiliate_reward_user_gold', 'affiliate_reward_user_diamond',
                        ];
                        $otherSettings = $settings->whereNotIn('setting_key', $knownKeys);
                    @endphp

                    @if($otherSettings->isNotEmpty())
                        <div class="col-12"><hr class="my-2"></div>
                        @foreach($otherSettings as $otherSetting)
                            <div class="col-12 col-md-6">
                                @include('admin.settings.forms.partials.setting-field', ['setting' => $otherSetting])
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 2: MẸ ĐỒNG (BRONZE) --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="tab-bronze" role="tabpanel" aria-labelledby="bronze-tab">
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background-color: #FFF4E6; border: 1px solid #FFE8CC;">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded-circle me-3" style="background-color: #FFE8CC; color: #CD7F32;">
                            <i class="ti ti-medal fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title mb-0" style="color: #CD7F32;">{{ __('Cấp 1: Mẹ Đồng (Bronze)') }}</h3>
                            <div class="text-muted small">{{ __('Cấp bậc mặc định khi thành viên bắt đầu tham gia giới thiệu') }}</div>
                        </div>
                    </div>
                    <span class="badge bg-orange text-white fs-6 px-3 py-2">{{ __('Cấp Mặc Định') }}</span>
                </div>

                <div class="alert alert-info py-2 mb-3" role="alert">
                    <i class="ti ti-info-circle me-1"></i>
                    {{ __('Mẹ Đồng là cấp khởi đầu. Tất cả người dùng mới đều bắt đầu ở cấp này với mốc điều kiện từ 0đ và 0 user.') }}
                </div>

                <div class="row g-4">
                    {{-- Khối 1: Chính sách quyền lợi --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-gift text-primary me-1"></i> {{ __('🎁 Chính Sách Trả Thưởng & Hoa Hồng') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_reward_user_bronze'),
                                    'label' => __('Thưởng F1 mới đăng ký (VNĐ)'),
                                    'hint' => __('Tiền thưởng cộng vào ví khi có F1 mới tạo tài khoản (Mặc định: 1.000đ).')
                                ])

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_commission_bronze'),
                                    'label' => __('Tỷ lệ hoa hồng mua gói (%)'),
                                    'hint' => __('Tỷ lệ % chiết khấu nhận được khi F1 thanh toán mua gói VIP (Mặc định: 10%).')
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Khối 2: Điều kiện nâng hạng --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-target text-success me-1"></i> {{ __('🎯 Điều Kiện Đạt Cấp (HOẶC)') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_users_bronze'),
                                    'label' => __('Số lượng User F1 tối thiểu (Người)'),
                                    'hint' => __('Mốc 0 user (cấp mặc định cho người mới).')
                                ])

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_sales_bronze'),
                                    'label' => __('Doanh số F1 tích lũy tối thiểu (VNĐ)'),
                                    'hint' => __('Mốc 0đ (cấp mặc định cho người mới).')
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 3: MẸ BẠC (SILVER) --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="tab-silver" role="tabpanel" aria-labelledby="silver-tab">
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background-color: #F8F9FA; border: 1px solid #DEE2E6;">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded-circle me-3" style="background-color: #E9ECEF; color: #6C757D;">
                            <i class="ti ti-award fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title mb-0" style="color: #6C757D;">{{ __('Cấp 2: Mẹ Bạc (Silver)') }}</h3>
                            <div class="text-muted small">{{ __('Cấp bậc sơ cấp khi đạt mốc giới thiệu đầu tiên') }}</div>
                        </div>
                    </div>
                    <span class="badge bg-secondary text-white fs-6 px-3 py-2">{{ __('Cấp 2') }}</span>
                </div>

                <div class="alert alert-success py-2 mb-3" role="alert">
                    <i class="ti ti-bulb me-1"></i>
                    {{ __('Quy tắc HOẶC: Người dùng đạt 6.000 User F1 HOẶC đạt 10.000.000đ Doanh số F1 sẽ tự động được thăng hạng lên Mẹ Bạc.') }}
                </div>

                <div class="row g-4">
                    {{-- Khối 1: Chính sách quyền lợi --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-gift text-primary me-1"></i> {{ __('🎁 Chính Sách Trả Thưởng & Hoa Hồng') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_reward_user_silver'),
                                    'label' => __('Thưởng F1 mới đăng ký: Mẹ Bạc (VNĐ)'),
                                    'hint' => __('Tiền thưởng cộng vào ví khi có F1 mới tạo tài khoản (Khuyến nghị: 3.000đ).')
                                ])

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_commission_silver'),
                                    'label' => __('Tỷ lệ hoa hồng mua gói: Mẹ Bạc (%)'),
                                    'hint' => __('Tỷ lệ % chiết khấu nhận được khi F1 thanh toán mua gói VIP (Khuyến nghị: 30%).')
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Khối 2: Điều kiện nâng hạng --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-target text-success me-1"></i> {{ __('🎯 Điều Kiện Nâng Hạng (Quy tắc HOẶC)') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_users_silver'),
                                    'label' => __('Số lượng User F1 tối thiểu (Người)'),
                                    'hint' => __('Nhánh 1: Giới thiệu đủ mốc user này sẽ được thăng cấp (Khuyến nghị: 6.000 user).')
                                ])

                                <div class="text-center fw-bold text-muted my-1">— HOẶC —</div>

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_sales_silver'),
                                    'label' => __('Doanh số F1 tích lũy tối thiểu (VNĐ)'),
                                    'hint' => __('Nhánh 2: F1 mua gói đạt mốc doanh số này sẽ được thăng cấp (Khuyến nghị: 10.000.000đ).')
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 4: MẸ VÀNG (GOLD) --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="tab-gold" role="tabpanel" aria-labelledby="gold-tab">
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background-color: #FFF9DB; border: 1px solid #FFE066;">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded-circle me-3" style="background-color: #FFF3BF; color: #E6A100;">
                            <i class="ti ti-crown fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title mb-0" style="color: #E6A100;">{{ __('Cấp 3: Mẹ Vàng (Gold)') }}</h3>
                            <div class="text-muted small">{{ __('Cấp bậc trung cấp với chính sách hoa hồng và thưởng vượt trội') }}</div>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">{{ __('Cấp 3') }}</span>
                </div>

                <div class="alert alert-success py-2 mb-3" role="alert">
                    <i class="ti ti-bulb me-1"></i>
                    {{ __('Quy tắc HOẶC: Người dùng đạt 8.000 User F1 HOẶC đạt 15.000.000đ Doanh số F1 sẽ tự động được thăng hạng lên Mẹ Vàng.') }}
                </div>

                <div class="row g-4">
                    {{-- Khối 1: Chính sách quyền lợi --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-gift text-primary me-1"></i> {{ __('🎁 Chính Sách Trả Thưởng & Hoa Hồng') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_reward_user_gold'),
                                    'label' => __('Thưởng F1 mới đăng ký: Mẹ Vàng (VNĐ)'),
                                    'hint' => __('Tiền thưởng cộng vào ví khi có F1 mới tạo tài khoản (Khuyến nghị: 4.000đ).')
                                ])

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_commission_gold'),
                                    'label' => __('Tỷ lệ hoa hồng mua gói: Mẹ Vàng (%)'),
                                    'hint' => __('Tỷ lệ % chiết khấu nhận được khi F1 thanh toán mua gói VIP (Khuyến nghị: 40%).')
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Khối 2: Điều kiện nâng hạng --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-target text-success me-1"></i> {{ __('🎯 Điều Kiện Nâng Hạng (Quy tắc HOẶC)') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_users_gold'),
                                    'label' => __('Số lượng User F1 tối thiểu (Người)'),
                                    'hint' => __('Nhánh 1: Giới thiệu đủ mốc user này sẽ được thăng cấp (Khuyến nghị: 8.000 user).')
                                ])

                                <div class="text-center fw-bold text-muted my-1">— HOẶC —</div>

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_sales_gold'),
                                    'label' => __('Doanh số F1 tích lũy tối thiểu (VNĐ)'),
                                    'hint' => __('Nhánh 2: F1 mua gói đạt mốc doanh số này sẽ được thăng cấp (Khuyến nghị: 15.000.000đ).')
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 5: MẸ KIM CƯƠNG (DIAMOND) --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade" id="tab-diamond" role="tabpanel" aria-labelledby="diamond-tab">
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background-color: #E3FAFC; border: 1px solid #99E9F2;">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md rounded-circle me-3" style="background-color: #C5F6FA; color: #00B4D8;">
                            <i class="ti ti-diamond fs-2"></i>
                        </span>
                        <div>
                            <h3 class="card-title mb-0" style="color: #00B4D8;">{{ __('Cấp 4: Mẹ Kim Cương (Diamond)') }}</h3>
                            <div class="text-muted small">{{ __('Cấp bậc danh giá cao nhất với đặc quyền hoa hồng tối đa 50%') }}</div>
                        </div>
                    </div>
                    <span class="badge bg-cyan text-white fs-6 px-3 py-2">{{ __('Cấp Cao Nhất') }}</span>
                </div>

                <div class="alert alert-success py-2 mb-3" role="alert">
                    <i class="ti ti-bulb me-1"></i>
                    {{ __('Quy tắc HOẶC: Người dùng đạt 10.000 User F1 HOẶC đạt 20.000.000đ Doanh số F1 sẽ tự động được thăng hạng lên Mẹ Kim Cương.') }}
                </div>

                <div class="row g-4">
                    {{-- Khối 1: Chính sách quyền lợi --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-gift text-primary me-1"></i> {{ __('🎁 Chính Sách Trả Thưởng & Hoa Hồng') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_reward_user_diamond'),
                                    'label' => __('Thưởng F1 mới đăng ký: Mẹ Kim Cương (VNĐ)'),
                                    'hint' => __('Tiền thưởng cộng vào ví khi có F1 mới tạo tài khoản (Khuyến nghị: 5.000đ).')
                                ])

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_commission_diamond'),
                                    'label' => __('Tỷ lệ hoa hồng mua gói: Mẹ Kim Cương (%)'),
                                    'hint' => __('Tỷ lệ % chiết khấu nhận được khi F1 thanh toán mua gói VIP (Khuyến nghị: 50%).')
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- Khối 2: Điều kiện nâng hạng --}}
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border shadow-none bg-light-lt">
                            <div class="card-header bg-white py-2">
                                <h4 class="card-title mb-0 text-dark">
                                    <i class="ti ti-target text-success me-1"></i> {{ __('🎯 Điều Kiện Nâng Hạng (Quy tắc HOẶC)') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_users_diamond'),
                                    'label' => __('Số lượng User F1 tối thiểu (Người)'),
                                    'hint' => __('Nhánh 1: Giới thiệu đủ mốc user này sẽ được thăng cấp (Khuyến nghị: 10.000 user).')
                                ])

                                <div class="text-center fw-bold text-muted my-1">— HOẶC —</div>

                                @include('admin.settings.forms.partials.setting-field', [
                                    'setting' => $settingsByKey->get('affiliate_sales_diamond'),
                                    'label' => __('Doanh số F1 tích lũy tối thiểu (VNĐ)'),
                                    'hint' => __('Nhánh 2: F1 mua gói đạt mốc doanh số này sẽ được thăng cấp (Khuyến nghị: 20.000.000đ).')
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
