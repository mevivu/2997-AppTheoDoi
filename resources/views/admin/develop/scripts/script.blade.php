<script>
    $(document).ready(function() {
        let stepIndex = {{ count($instance->steps ?? []) }}; // Số bước hiện tại từ dữ liệu
        const stepsContainer = $('#steps-list');
        const addStepButton = $('#add-step-btn');

        // Thêm bước mới
        addStepButton.on('click', function() {
            const newStep = $(`
            <div class="step-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <label class="control-label d-block text-start">@lang('Thứ tự')</label>
                        <input type="hidden" name="steps[${stepIndex}][order]" value="${stepIndex + 1}" class="step-order" />
                        <span class="form-control-plaintext text-start">${stepIndex + 1}</span>
                    </div>
                    <button type="button" class="btn btn-danger remove-step">@lang('Xóa tháng')</button>
                </div>
                <div class="mb-3">
                    <label class="control-label d-block text-start">@lang('Tiêu đề cho tháng')</label>
                    <x-input name="steps[${stepIndex}][title]" :required="true" :placeholder="__('Tiêu đề tháng')" />
                </div>
                <div class="mb-3">
                    <label class="control-label d-block text-start">@lang('Mô tả tháng ')</label>
                    <textarea name="steps[${stepIndex}][description]" class="form-control ckeditor visually-hidden" placeholder="@lang('Mô tả tháng')"></textarea>
                </div>
            </div>
        `);

            stepsContainer.append(newStep);
            stepIndex++; // Tăng chỉ số bước khi thêm bước mới

            // Khởi tạo CKEditor cho textarea mới
            CKEDITOR.replace(newStep.find('textarea')[0]);
            updateOrderNumbers(); // Cập nhật lại số thứ tự
        });

        // Xóa bước
        stepsContainer.on('click', '.remove-step', function() {
            const stepItem = $(this).closest('.step-item');
            stepItem.remove();
            updateOrderNumbers(); // Cập nhật lại số thứ tự sau khi xóa bước
        });

        // Hàm cập nhật lại số thứ tự
        function updateOrderNumbers() {
            $('.step-item').each(function(index) {
                const order = index + 1;
                $(this).find('.form-control-plaintext').text(order); // Hiển thị số thứ tự
                $(this).find('.step-order').val(order); // Cập nhật giá trị của input ẩn
            });
        }
    });
</script>
