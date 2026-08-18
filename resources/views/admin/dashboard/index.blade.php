@extends('admin.layouts.master')

@section('content')
    <div class="page-body pt-3 pb-5">
        <div class="container-fluid">
            <!-- 🌟 HERO WELCOME BANNER -->
            <div class="dashboard-hero-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="dashboard-hero-title d-flex align-items-center gap-2">
                        <span>👋 {{ __('Xin chào') }}, {{ auth('admin')->user()->fullname ?? auth('admin')->user()->username ?? 'Admin' }}!</span>
                    </h2>
                    <p class="dashboard-hero-desc">
                        {{ __('Chào mừng bạn trở lại Trung tâm Quản trị Hệ thống Chăm Con. Dưới đây là tổng quan các chỉ số vận hành mới nhất.') }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @if (Route::has('admin.firebase.report'))
                        <a href="{{ route('admin.firebase.report') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm fw-bold px-3 py-2">
                            <i class="ti ti-chart-bar fs-4"></i>
                            <span>{{ __('Báo cáo Firebase') }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- 🌟 TOP 4 CORE KPI CARDS (Vị trí #1: Khách hàng, Vị trí #2: Trẻ em) -->
            <div class="row g-3 mb-4">
                <!-- 🥇 TOP 1: KHÁCH HÀNG (PHỤ HUYNH) -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card-featured kpi-users">
                        <div>
                            <div class="kpi-top">
                                <div class="kpi-icon-wrap">
                                    <i class="ti ti-users"></i>
                                </div>
                                <span class="kpi-rank-badge bg-warning-lt text-warning fw-bold">
                                    <i class="ti ti-crown me-1"></i>{{ __('Vị trí #1') }}
                                </span>
                            </div>
                            <div class="kpi-label">{{ __('Khách hàng (Phụ huynh)') }}</div>
                            <div class="kpi-number">{{ number_format($rowCountUser) }}</div>
                            <div class="kpi-subtitle">{{ __('Tài khoản phụ huynh đang hoạt động') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('admin.user.index') }}" class="kpi-action-link w-100 justify-content-between">
                                <span>{{ __('Quản lý khách hàng') }}</span>
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 🥈 TOP 2: HỒ SƠ TRẺ EM -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card-featured kpi-children">
                        <div>
                            <div class="kpi-top">
                                <div class="kpi-icon-wrap">
                                    <i class="ti ti-baby-carriage"></i>
                                </div>
                                <span class="kpi-rank-badge bg-success-lt text-success fw-bold">
                                    <i class="ti ti-star me-1"></i>{{ __('Vị trí #2') }}
                                </span>
                            </div>
                            <div class="kpi-label">{{ __('Hồ sơ Trẻ em') }}</div>
                            <div class="kpi-number">{{ number_format($rowCountChildren) }}</div>
                            <div class="kpi-subtitle">{{ __('Hồ sơ trẻ đang được theo dõi phát triển') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('admin.children.index') }}" class="kpi-action-link w-100 justify-content-between">
                                <span>{{ __('Danh sách trẻ em') }}</span>
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 🥉 TOP 3: GIAO DỊCH DỊCH VỤ -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card-featured kpi-transactions">
                        <div>
                            <div class="kpi-top">
                                <div class="kpi-icon-wrap">
                                    <i class="ti ti-receipt-2"></i>
                                </div>
                                <span class="kpi-rank-badge bg-purple-lt text-purple fw-bold">
                                    {{ __('Tài chính') }}
                                </span>
                            </div>
                            <div class="kpi-label">{{ __('Giao dịch hệ thống') }}</div>
                            <div class="kpi-number">{{ number_format($rowCountTransaction) }}</div>
                            <div class="kpi-subtitle">{{ __('Tổng đơn thanh toán dịch vụ / gói') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('admin.transaction.index') }}" class="kpi-action-link w-100 justify-content-between">
                                <span>{{ __('Quản lý giao dịch') }}</span>
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TOP 4: BÀI KIỂM TRA & CÂU HỎI -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card-featured kpi-quiz">
                        <div>
                            <div class="kpi-top">
                                <div class="kpi-icon-wrap">
                                    <i class="ti ti-brain"></i>
                                </div>
                                <span class="kpi-rank-badge bg-primary-lt text-primary fw-bold">
                                    {{ __('Đánh giá IQ') }}
                                </span>
                            </div>
                            <div class="kpi-label">{{ __('Bài kiểm tra IQ') }}</div>
                            <div class="kpi-number">{{ number_format($rowCountQuiz) }}</div>
                            <div class="kpi-subtitle">{{ __('Đang có ') }} <strong>{{ number_format($rowCountQuestion) }}</strong> {{ __('câu hỏi trong kho') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('admin.quiz.iq') }}" class="kpi-action-link w-100 justify-content-between">
                                <span>{{ __('Quản lý bài test IQ') }}</span>
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
                 PHÂN NHÓM CHUYÊN MÔN THEO LĨNH VỰC NGHIỆP VỤ
                 ============================================ -->

            <!-- 🏥 KHỐI 1: THEO DÕI SỨC KHỎE & THỂ CHẤT TRẺ EM -->
            <div class="dashboard-category-header">
                <span class="category-icon bg-emerald-lt text-success">
                    <i class="ti ti-heart-rate-monitor"></i>
                </span>
                <h3 class="category-title">{{ __('Theo dõi Sức khỏe & Thể chất Trẻ em') }}</h3>
                <span class="category-count">5 {{ __('module') }}</span>
            </div>
            <div class="row g-3 mb-4">
                <!-- Lịch tiêm chủng -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.vaccination.admin') }}" class="dashboard-module-card">
                        <div class="module-icon bg-success-lt text-success">
                            <i class="ti ti-vaccine"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Lịch tiêm chủng') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountVaccinationSchedule) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Đánh giá BMI -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.bmi.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-primary-lt text-primary">
                            <i class="ti ti-scale"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Chỉ số BMI') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountBMI) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Theo dõi Thai kỳ -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.pregnancy.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-pink-lt text-pink">
                            <i class="ti ti-flower"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Theo dõi Thai kỳ') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountPregnancy) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Nhật ký & Đơn thuốc -->
                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                    <a href="{{ route('admin.journal.prescription') }}" class="dashboard-module-card">
                        <div class="module-icon bg-cyan-lt text-cyan">
                            <i class="ti ti-notebook"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Nhật ký & Thuốc') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountJournal) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Phòng khám -->
                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                    <a href="{{ route('admin.clinic.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-azure-lt text-azure">
                            <i class="ti ti-building-hospital"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Phòng khám liên kết') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountClinic) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- 🎓 KHỐI 2: GIÁO DỤC, TRÍ TUỆ & ĐÁNH GIÁ TRẺ -->
            <div class="dashboard-category-header">
                <span class="category-icon bg-primary-lt text-primary">
                    <i class="ti ti-school"></i>
                </span>
                <h3 class="category-title">{{ __('Giáo dục, Trí tuệ & Đánh giá Trẻ') }}</h3>
                <span class="category-count">5 {{ __('module') }}</span>
            </div>
            <div class="row g-3 mb-4">
                <!-- Học lực GPA -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.gpa.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-indigo-lt text-indigo">
                            <i class="ti ti-certificate"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Học lực GPA') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountGPA) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Bài kiểm tra IQ -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.quiz.iq') }}" class="dashboard-module-card">
                        <div class="module-icon bg-blue-lt text-blue">
                            <i class="ti ti-brain"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Bài kiểm tra IQ') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountQuiz) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Ngân hàng câu hỏi -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.question.iq') }}" class="dashboard-module-card">
                        <div class="module-icon bg-teal-lt text-teal">
                            <i class="ti ti-help-circle"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Ngân hàng câu hỏi') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountQuestion) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Bài tập phát triển -->
                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                    <a href="{{ route('admin.exercise.physical') }}" class="dashboard-module-card">
                        <div class="module-icon bg-orange-lt text-orange">
                            <i class="ti ti-run"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Bài tập phát triển') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountExercise) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Khung giáo dục chuẩn -->
                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                    <a href="{{ route('admin.quality.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-yellow-lt text-yellow">
                            <i class="ti ti-star"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Khung giáo dục chuẩn') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountEducation) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- 📦 KHỐI 3: DỊCH VỤ, NỘI DUNG & TƯƠNG TÁC -->
            <div class="dashboard-category-header">
                <span class="category-icon bg-purple-lt text-purple">
                    <i class="ti ti-box"></i>
                </span>
                <h3 class="category-title">{{ __('Dịch vụ, Nội dung & Tương tác') }}</h3>
                <span class="category-count">6 {{ __('module') }}</span>
            </div>
            <div class="row g-3 mb-4">
                <!-- Gói dịch vụ -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.package.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-purple-lt text-purple">
                            <i class="ti ti-package"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Gói dịch vụ') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountPackage) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Sản phẩm -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.product.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-cyan-lt text-cyan">
                            <i class="ti ti-shopping-bag"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Sản phẩm & Khóa học') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountProduct) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Bài viết tin tức -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.post.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-blue-lt text-blue">
                            <i class="ti ti-article"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Bài viết & Tin tức') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountPost) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Hướng dẫn phụ huynh -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.guide.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-green-lt text-green">
                            <i class="ti ti-compass"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Hướng dẫn phụ huynh') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountGuide) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Slider & Banner -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.slider.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-azure-lt text-azure">
                            <i class="ti ti-slideshow"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Slider & Banner') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountSlider) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Thông báo đẩy -->
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('admin.notification.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-amber-lt text-amber">
                            <i class="ti ti-bell-ringing"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Thông báo đẩy') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountNotification) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- ⚙️ KHỐI 4: QUẢN TRỊ HỆ THỐNG & PHÂN QUYỀN -->
            <div class="dashboard-category-header">
                <span class="category-icon bg-secondary-lt text-secondary">
                    <i class="ti ti-settings"></i>
                </span>
                <h3 class="category-title">{{ __('Quản trị Hệ thống, Nhân sự & Hỗ trợ') }}</h3>
                <span class="category-count">3 {{ __('module') }}</span>
            </div>
            <div class="row g-3">
                <!-- Quản trị viên -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.admin.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-dark-lt text-dark">
                            <i class="ti ti-user-shield"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Quản trị viên hệ thống') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountAdmin) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Vai trò & Phân quyền -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.role.index') }}" class="dashboard-module-card">
                        <div class="module-icon bg-purple-lt text-purple">
                            <i class="ti ti-shield-check"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Vai trò & Phân quyền') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountRole) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>

                <!-- Trung tâm hỗ trợ khách hàng -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('admin.support.help-center') }}" class="dashboard-module-card">
                        <div class="module-icon bg-danger-lt text-danger">
                            <i class="ti ti-headset"></i>
                        </div>
                        <div class="module-info">
                            <div class="module-name">{{ __('Trung tâm hỗ trợ khách hàng') }}</div>
                            <div class="module-count">{{ __('Số lượng:') }} <strong>{{ number_format($rowCountSupport) }}</strong></div>
                        </div>
                        <i class="ti ti-chevron-right module-arrow"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
