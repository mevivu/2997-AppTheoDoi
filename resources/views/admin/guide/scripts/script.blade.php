<script>
    document.addEventListener('DOMContentLoaded', () => {
        let stepIndex = {{ count($instance->steps ?? []) }}; // Tạo biến stepIndex từ số bước hiện tại, bắt đầu từ 0
        const stepsContainer = document.getElementById('steps-list');
        const addStepButton = document.getElementById('add-step-btn');

        // Thêm bước mới
        addStepButton.addEventListener('click', () => {
            const newStep = document.createElement('div');
            newStep.classList.add('step-item', 'border', 'rounded', 'p-3', 'mb-3');
            newStep.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="control-label">@lang('Tiêu đề bước')</label>
                    <button type="button" class="btn btn-danger remove-step">@lang('Xóa bước')</button>
                </div>
                <x-input name="steps[${stepIndex}][title]" :required="true" :placeholder="__('Tiêu đề bước')" />
                <br>
                <div class="mb-3">
                    <label class="control-label d-block text-start">@lang('Mô tả bước')</label>
                    <textarea name="steps[${stepIndex}][description]" class="form-control" placeholder="@lang('Mô tả bước')"></textarea>
                </div>

               <div class="mb-3">
                    <label class="control-label d-block text-start">@lang('Thứ tự')</label>
                    <span class="form-control-plaintext text-start" id="order-${stepIndex}">${stepIndex + 1}</span>
                </div>
            `;

            stepsContainer.appendChild(newStep);
            stepIndex++; // Tăng chỉ số bước khi thêm bước mới
        });

        // Xóa bước
        stepsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-step')) {
                const stepItem = e.target.closest('.step-item');
                stepItem.remove();

                // Cập nhật lại chỉ số thứ tự của các bước còn lại
                const allSteps = document.querySelectorAll('.step-item');
                allSteps.forEach((step, index) => {
                    const orderInput = step.querySelector('input[name^="steps"][name$="[order]"]');
                    if (orderInput) {
                        orderInput.value = index + 1; // Cập nhật lại giá trị thứ tự
                    }
                });
            }
        });
    });
</script>
