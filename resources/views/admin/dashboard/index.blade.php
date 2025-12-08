@extends('admin.layouts.master')

@section('content')
    <style>
        :root {
            /* Primary Colors */
            --health-color: #10B981;
            --health-bg: rgba(16, 185, 129, 0.1);
            --education-color: #4573F9;
            --education-bg: rgba(69, 115, 249, 0.1);
            --system-color: #8B5CF6;
            --system-bg: rgba(139, 92, 246, 0.1);
            --user-color: #F59E0B;
            --user-bg: rgba(245, 158, 11, 0.1);
        }

        .page-body {
            background: #F8FAFC;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .dashboard-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1a1f36;
        }

        .card-sm {
            background: white;
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        /* Health Category */
        .card-sm[data-category="health"] {
            border-left: 4px solid var(--health-color);
        }
        .card-sm[data-category="health"] .iconModuleMevivu {
            background: var(--health-bg);
            color: var(--health-color);
        }
        .card-sm[data-category="health"]:hover .iconModuleMevivu {
            background: var(--health-color);
            color: white;
        }
        .card-sm[data-category="health"]:hover {
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
        }

        /* Education Category */
        .card-sm[data-category="education"] {
            border-left: 4px solid var(--education-color);
        }
        .card-sm[data-category="education"] .iconModuleMevivu {
            background: var(--education-bg);
            color: var(--education-color);
        }
        .card-sm[data-category="education"]:hover .iconModuleMevivu {
            background: var(--education-color);
            color: white;
        }
        .card-sm[data-category="education"]:hover {
            box-shadow: 0 8px 16px rgba(69, 115, 249, 0.2);
        }

        /* System Category */
        .card-sm[data-category="system"] {
            border-left: 4px solid var(--system-color);
        }
        .card-sm[data-category="system"] .iconModuleMevivu {
            background: var(--system-bg);
            color: var(--system-color);
        }
        .card-sm[data-category="system"]:hover .iconModuleMevivu {
            background: var(--system-color);
            color: white;
        }
        .card-sm[data-category="system"]:hover {
            box-shadow: 0 8px 16px rgba(139, 92, 246, 0.2);
        }

        /* User Category */
        .card-sm[data-category="user"] {
            border-left: 4px solid var(--user-color);
        }
        .card-sm[data-category="user"] .iconModuleMevivu {
            background: var(--user-bg);
            color: var(--user-color);
        }
        .card-sm[data-category="user"]:hover .iconModuleMevivu {
            background: var(--user-color);
            color: white;
        }
        .card-sm[data-category="user"]:hover {
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
        }

        .card-sm:hover {
            transform: translateY(-4px);
        }

        .card-sm .card-body {
            padding: 1.5rem;
        }

        .iconModuleMevivu {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .card-sm:hover .iconModuleMevivu {
            transform: scale(1.1);
        }

        .font-weight-medium {
            font-size: 1.1rem;
            color: #1E293B;
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .font-weight-medium:hover {
            opacity: 0.9;
        }

        .text-secondary {
            font-size: 0.875rem;
            color: #64748B;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .custom-shadow {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid #E5E7EB;
            padding: 1.5rem;
        }

        .row.g-3 {
            margin: 0 -0.75rem;
        }

        .col-sm-6.col-lg-3.mb-3 {
            padding: 0.75rem;
        }
    </style>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card custom-shadow">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <h2 class="dashboard-title">{{ __('Dashboard') }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                {{--User--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-users"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.user.index')" title="Khách hàng"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountUser}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Children--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-baby-carriage"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.children.index')" title="Trẻ em"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountChildren}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Transaction-->
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="system">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-calendar-dollar"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.transaction.index')" title="Giao dịch"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountTransaction }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                {{--GPA--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="education">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-calendar-dollar"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.gpa.index')" title="GPA"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountGPA }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Guide--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="system">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-calendar-dollar"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.guide.index')" title="Hướng dẫn"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountGuide }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Journal--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-external-link"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.journal.prescription')" title="Nhật ký"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountJournal }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Pregnancy--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="health">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-pennant"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.pregnancy.index')" title="Thai kỳ"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountPregnancy }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Notification Card -->
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-bell"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.notification.index')" title="Thông báo"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountNotification }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--  slider--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="system">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-photo"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.slider.index')" title="Slider"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountSlider }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--  Package--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="education">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-package-import"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.package.index')" title="Gói"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountPackage }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--  Exercise--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="health">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-book"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.exercise.physical')" title="Bài tập"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountExercise }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Post--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-article"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.post.index')" title="Bài viết"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountPost }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--BMI--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="health">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-info-circle"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.bmi.index')" title="BMI"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountBMI }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Expected--}}
{{--                                <div class="col-sm-6 col-lg-3 mb-3">--}}
{{--                                    <div class="card card-sm">--}}
{{--                                        <div class="card-body">--}}
{{--                                            <div class="row align-items-center">--}}
{{--                                                <div class="col-auto">--}}
{{--                                                    <span class="iconModuleMevivu ti ti-award"></span>--}}
{{--                                                </div>--}}
{{--                                                <div class="col">--}}
{{--                                                    <x-link :href="route('admin.expected.index')" title="Thông tin dự kiến"--}}
{{--                                                            class="font-weight-medium">--}}
{{--                                                    </x-link>--}}
{{--                                                    <div class="text-secondary">--}}
{{--                                                        Số lượng: {{ $rowCountExpected }}--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                {{--Product--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="education">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-brand-producthunt"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.product.index')" title="Sản phẩm"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountProduct }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Question--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-help-hexagon"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.question.iq')" title="Câu hỏi"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountQuestion }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Quiz--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="system">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-award"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.quiz.iq')" title="Bài kiểm tra"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountQuiz }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--VaccinationSchedule--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="health">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-calendar-bolt"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.vaccination.admin')" title="Lịch tiêm chủng"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountVaccinationSchedule}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Education--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="education">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-star"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.quality.index')" title="Khung giáo dục"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountEducation}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Clinic--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="health">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-mushroom"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.clinic.index')" title="Phòng khám"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountClinic}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Role--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-user-check"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.role.index')" title="Vai trò"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountRole}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Support--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-lifebuoy"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.support.help-center')" title="Hỗ trợ khách hàng"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountSupport}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Admin Card--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm" data-category="user">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-user-shield"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.admin.index')" title="Quản trị viên"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{$rowCountAdmin}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
