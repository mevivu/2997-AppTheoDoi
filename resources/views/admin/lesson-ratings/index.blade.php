@extends('admin.layouts.master')

@push('libs-css')
@endpush

@push('custom-css')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            {{-- Header --}}
            <div class="card custom-shadow mb-3">
                <x-admin.page-header :title="__('Thống kê đánh giá độ khó bài học')"
                                     :subtitle="__('Theo dõi phản hồi từ phụ huynh về mức độ dễ, có trợ giúp hoặc khó của từng bài học')"
                                     icon="ti ti-star" />
            </div>

            {{-- 4 Dashboard Overview Cards --}}
            <div class="row g-3 mb-3">
                {{-- Card 1: Tổng đánh giá --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-sm custom-shadow stat-card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded-circle bg-primary-lt text-primary">
                                        <i class="ti ti-message-2-star fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-muted fs-12 text-uppercase fw-semibold">{{ __('Tổng lượt đánh giá') }}</div>
                                    <div class="h2 mb-0 fw-bold text-dark">{{ number_format($stats['total_ratings']) }}</div>
                                    <div class="text-muted fs-12 mt-1">
                                        {{ __('Trên') }} <strong class="text-dark">{{ $stats['rated_lessons_count'] }}/{{ $stats['total_lessons'] }}</strong> {{ __('bài học') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Dễ --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-sm custom-shadow stat-card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded-circle bg-green-lt text-green fs-24">
                                        😊
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-muted fs-12 text-uppercase fw-semibold">{{ __('Độ khó: Dễ') }}</div>
                                    <div class="h2 mb-0 fw-bold text-success">{{ $stats['easy_pct'] }}%</div>
                                    <div class="text-muted fs-12 mt-1">
                                        <strong class="text-success">{{ number_format($stats['easy_count']) }}</strong> {{ __('lượt đánh giá') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Có trợ giúp --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-sm custom-shadow stat-card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded-circle bg-warning-lt text-warning fs-24">
                                        👋
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-muted fs-12 text-uppercase fw-semibold">{{ __('Cần có trợ giúp') }}</div>
                                    <div class="h2 mb-0 fw-bold text-warning">{{ $stats['with_help_pct'] }}%</div>
                                    <div class="text-muted fs-12 mt-1">
                                        <strong class="text-warning">{{ number_format($stats['with_help_count']) }}</strong> {{ __('lượt đánh giá') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Khó --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-sm custom-shadow stat-card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded-circle bg-danger-lt text-danger fs-24">
                                        🔥
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-muted fs-12 text-uppercase fw-semibold">{{ __('Độ khó: Khó') }}</div>
                                    <div class="h2 mb-0 fw-bold text-danger">{{ $stats['hard_pct'] }}%</div>
                                    <div class="text-muted fs-12 mt-1">
                                        <strong class="text-danger">{{ number_format($stats['hard_count']) }}</strong> {{ __('lượt đánh giá') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DataTable Card --}}
            <div class="card custom-shadow">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="ti ti-table text-primary"></i>
                        <span>{{ __('Bảng thống kê đánh giá theo bài học') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    @include('admin.lesson-ratings.partials.detail-modal')
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

    <script>
        $(document).ready(function () {
            const modalEl = document.getElementById('lessonRatingModal');
            const modal = new bootstrap.Modal(modalEl);

            $(document).on('click', '.btn-rating-detail', function (e) {
                e.preventDefault();
                const lessonId = $(this).data('id');
                const lessonName = $(this).data('name') || 'Bài học';

                // Reset modal state
                $('#modal_lesson_name').text(lessonName);
                $('#modal_lesson_age_group').text('—');
                $('#modal_lesson_thumb').attr('src', '');
                $('#modal_loading').show();
                $('#modal_content').hide();
                $('#modal_empty_ratings').hide();
                $('#modal_ratings_tbody').empty();

                modal.show();

                // Fetch detail data via AJAX
                $.ajax({
                    url: '{{ url("/admin/lesson-ratings/detail") }}/' + lessonId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        $('#modal_loading').hide();

                        if (res.status === 200 && res.data) {
                            const data = res.data;
                            const lesson = data.lesson;
                            const summary = data.summary;
                            const ratings = data.ratings || [];

                            // Render lesson header
                            $('#modal_lesson_name').text(lesson.name);
                            $('#modal_lesson_age_group').text('Độ tuổi: ' + lesson.age_group);
                            $('#modal_lesson_thumb').attr('src', lesson.thumbnail);
                            $('#modal_total_badge').text(summary.total + ' lượt đánh giá');

                            // Render summary breakdown
                            $('#modal_easy_count').text(summary.easy.count);
                            $('#modal_easy_pct').text(summary.easy.pct + '%');

                            $('#modal_with_help_count').text(summary.with_help.count);
                            $('#modal_with_help_pct').text(summary.with_help.pct + '%');

                            $('#modal_hard_count').text(summary.hard.count);
                            $('#modal_hard_pct').text(summary.hard.pct + '%');

                            // Render ratings list
                            if (ratings.length > 0) {
                                let rowsHtml = '';
                                ratings.forEach(function (item, index) {
                                    rowsHtml += `
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">${index + 1}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">${item.user_name}</div>
                                                <small class="text-muted"><i class="ti ti-phone fs-12 me-1"></i>${item.user_phone}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-purple-lt text-purple px-2 py-1">
                                                    <i class="ti ti-baby-carriage me-1"></i>${item.child_name}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge ${item.difficulty_badge} px-2 py-1">
                                                    ${item.difficulty_icon} ${item.difficulty_label}
                                                </span>
                                            </td>
                                            <td class="text-center text-muted fs-12">${item.created_at}</td>
                                        </tr>
                                    `;
                                });
                                $('#modal_ratings_tbody').html(rowsHtml);
                                $('#modal_empty_ratings').hide();
                            } else {
                                $('#modal_empty_ratings').show();
                            }

                            $('#modal_content').fadeIn(150);
                        } else {
                            alert('Không thể tải dữ liệu chi tiết đánh giá.');
                            modal.hide();
                        }
                    },
                    error: function (xhr) {
                        $('#modal_loading').hide();
                        alert('Đã xảy ra lỗi khi tải dữ liệu chi tiết.');
                        modal.hide();
                    }
                });
            });
        });
    </script>
@endpush
