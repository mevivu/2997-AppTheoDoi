@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .rank-tier-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            color: #1e293b !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .rank-tier-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }

        .rank-tier-card .card-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0.85rem 1.25rem;
            border-top-left-radius: 11px;
            border-top-right-radius: 11px;
        }

        .rank-tier-card .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a !important;
        }

        .rank-tier-card .form-label {
            color: #1e293b !important;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
            display: block;
        }

        .rank-tier-card .form-control {
            color: #0f172a !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
        }

        .rank-tier-card .form-hint {
            color: #64748b !important;
            font-size: 0.8rem;
            line-height: 1.45;
            margin-top: 0.35rem;
        }

        .weight-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="crown"
                :title="__('Cài đặt Phân hạng Bố mẹ')"
                :subtitle="__('Cấu hình tiêu chí tính điểm, mục tiêu tháng và ngưỡng phân hạng thành viên Bố mẹ')"
                :back-route="route('admin.dashboard')"
            />

            <x-form :action="route('admin.setting.update')" type="put" :validate="true">
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-lg-8 col-xl-9">
                        @include('admin.settings.forms.parent-rank-left')
                    </div>
                    @include('admin.settings.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('custom-js')
    <script>
        $(document).ready(function() {
            // Tính tổng trọng số và cảnh báo nếu khác 100%
            function checkWeights() {
                var wUsage = parseFloat($('#input_parent_rank_weight_usage').val()) || 0;
                var wFreq = parseFloat($('#input_parent_rank_weight_frequency').val()) || 0;
                var wAssess = parseFloat($('#input_parent_rank_weight_assessment').val()) || 0;
                var wLesson = parseFloat($('#input_parent_rank_weight_lesson_video').val()) || 0;
                var wChild = parseFloat($('#input_parent_rank_weight_child_score').val()) || 0;
                var total = wUsage + wFreq + wAssess + wLesson + wChild;

                $('#totalWeightDisplay').text(total.toFixed(0) + '%');
                if (Math.abs(total - 100) < 0.01) {
                    $('#totalWeightDisplay').removeClass('bg-danger').addClass('bg-success');
                    $('#weightWarning').addClass('d-none');
                } else {
                    $('#totalWeightDisplay').removeClass('bg-success').addClass('bg-danger');
                    $('#weightWarning').removeClass('d-none');
                }
            }

            $('.weight-input').on('input change', checkWeights);
            checkWeights();

            // Xem trước câu gợi ý chỉ số năng lực (thay biến mẫu)
            function renderCompetencyPreview() {
                var tpl = $('#input_parent_rank_suggest_text_competency').val() || '';
                var maxDisplay = parseInt($('#input_parent_rank_competency_max_display').val(), 10) || 2;
                var sample = ['EQ = 2', 'GPA = 3', 'AQ = 4', 'IQ = 4.5', 'PQ = 4.8'].slice(0, Math.max(1, Math.min(5, maxDisplay)));
                var text = tpl
                    .split('{child}').join('Bé An')
                    .split('{label}').join(sample.length > 1 ? 'các chỉ số' : 'chỉ số')
                    .split('{list}').join(sample.join(', '));
                $('#competencyPreview').text(text);
            }

            $('#input_parent_rank_suggest_text_competency, #input_parent_rank_competency_max_display')
                .on('input change', renderCompetencyPreview);
            renderCompetencyPreview();

            // Khôi phục câu mặc định
            $('.btn-reset-suggest').on('click', function() {
                var $target = $($(this).data('target'));
                $target.val($target.data('default')).trigger('input');
            });
        });
    </script>
@endpush
