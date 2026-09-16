@extends('admin.layouts.master')

@push('libs-css')
<style>
    @keyframes spinLoader {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotate-spin {
        animation: spinLoader 0.9s linear infinite;
        display: inline-block;
    }
    .select-change-question-group {
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .select-change-question-group:focus {
        box-shadow: 0 0 0 0.15rem rgba(32, 107, 196, 0.2) !important;
        border-color: #206bc4 !important;
    }

    /* Popover xem trước đáp án khi hover */
    .question-answers-popover {
        --bs-popover-max-width: 400px;
        --bs-popover-border-color: #cbd5e1;
        --bs-popover-header-bg: #f8fafc;
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.16) !important;
        border-radius: 10px !important;
        border: 1px solid #cbd5e1 !important;
        z-index: 1060 !important;
    }
    .question-answers-popover .popover-body {
        padding: 12px 14px !important;
    }
    .question-popover-trigger {
        transition: color 0.15s ease;
        cursor: pointer;
    }
    .question-popover-trigger:hover {
        color: #1d4ed8 !important;
        text-decoration: underline !important;
    }
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card custom-shadow">
                <x-admin.page-header :title="__('Danh sách câu hỏi IQ (Trí tuệ)')"
                                     :subtitle="__('Ngân hàng câu hỏi trắc nghiệm đánh giá chỉ số IQ')"
                                     icon="ti ti-help"
                                     :addRoute="route('admin.question.createIq')"
                                     :addText="__('Thêm mới')" />
                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.question.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
    @include('admin.common.copy')

    <script>
        // Cập nhật nhanh nhóm câu hỏi trực tiếp trên từng dòng
        $(document).on('change', '.select-change-question-group', function () {
            const select = $(this);
            const questionId = select.data('id');
            const originalValue = select.data('original');
            const newValue = select.val();
            const indicator = $(`#status-indicator-${questionId}`);

            // Hiển thị trạng thái đang lưu
            indicator.html('<i class="ti ti-loader-2 rotate-spin text-primary" style="font-size: 16px;"></i>');
            select.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.question.quickUpdateGroup') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    question_id: questionId,
                    question_group_id: newValue
                },
                success: function (response) {
                    select.prop('disabled', false);
                    select.data('original', newValue);
                    if (newValue) {
                        select.removeClass('border-warning text-warning-emphasis bg-warning-subtle')
                              .addClass('border-primary-subtle text-dark');
                    } else {
                        select.removeClass('border-primary-subtle text-dark')
                              .addClass('border-warning text-warning-emphasis bg-warning-subtle');
                    }

                    // Biểu tượng thành công
                    indicator.html('<i class="ti ti-check text-success fs-5"></i>');
                    setTimeout(() => {
                        indicator.empty();
                    }, 1800);

                    // Thông báo Toast góc phải
                    if (typeof $.toast === 'function') {
                        $.toast({
                            heading: 'Thành công',
                            text: response.message || 'Đã cập nhật nhóm câu hỏi',
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            stack: 3,
                            hideAfter: 2000
                        });
                    }
                },
                error: function (xhr) {
                    select.prop('disabled', false);
                    select.val(originalValue);
                    indicator.html('<i class="ti ti-x text-danger fs-5"></i>');
                    setTimeout(() => {
                        indicator.empty();
                    }, 2500);

                    let message = 'Có lỗi xảy ra khi cập nhật nhóm câu hỏi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof $.toast === 'function') {
                        $.toast({
                            heading: 'Lỗi',
                            text: message,
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3500
                        });
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        alert(message);
                    }
                }
            });
        });

        // Hover vào câu hỏi để hiển thị xem trước đáp án (Popover)
        $(document).on('mouseenter', '.question-popover-trigger', function () {
            const $trigger = $(this);
            const $wrapper = $trigger.closest('.question-hover-wrapper');
            const $template = $wrapper.find('.question-preview-template');

            if (!$template.length) return;

            if (!$trigger.data('bs.popover')) {
                const popoverInstance = new bootstrap.Popover($trigger[0], {
                    html: true,
                    container: 'body',
                    trigger: 'manual',
                    placement: 'right',
                    fallbackPlacements: ['left', 'bottom', 'top'],
                    customClass: 'question-answers-popover',
                    content: function () {
                        return $template.html();
                    }
                });
                $trigger.data('bs.popover', popoverInstance);
            }

            const pop = $trigger.data('bs.popover');
            clearTimeout($trigger.data('hideTimer'));
            
            // Đóng các popover khác đang mở
            $('.question-popover-trigger').not($trigger).each(function () {
                const otherPop = $(this).data('bs.popover');
                if (otherPop) otherPop.hide();
            });

            pop.show();

            // Giữ popover mở khi rê chuột trực tiếp vào bên trong popover
            $('.question-answers-popover').off('mouseenter mouseleave')
                .on('mouseenter', function () {
                    clearTimeout($trigger.data('hideTimer'));
                })
                .on('mouseleave', function () {
                    pop.hide();
                });
        }).on('mouseleave', '.question-popover-trigger', function () {
            const $trigger = $(this);
            const pop = $trigger.data('bs.popover');
            if (pop) {
                const timer = setTimeout(function () {
                    pop.hide();
                }, 250);
                $trigger.data('hideTimer', timer);
            }
        });

        // Dọn dẹp popover khi bảng vẽ lại (chuyển trang, lọc, tìm kiếm)
        $('#iqQuestionTable').on('draw.dt', function () {
            $('.question-answers-popover').remove();
        });
    </script>
@endpush
