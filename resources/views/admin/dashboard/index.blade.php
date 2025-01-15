@extends('admin.layouts.master')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Dashboard') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h2>{{ __('Dashboard') }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Transaction-->
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="iconModuleMevivu ti ti-award"></span>
                                                </div>
                                                <div class="col">
                                                    <x-link :href="route('admin.expected.index')" title="Thông tin dự kiến"
                                                            class="font-weight-medium">
                                                    </x-link>
                                                    <div class="text-secondary">
                                                        Số lượng: {{ $rowCountExpected }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--Product--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                {{--User--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                {{--VaccinationSchedule--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                {{--Children--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                {{--Education--}}
                                <div class="col-sm-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
                                    <div class="card card-sm">
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
