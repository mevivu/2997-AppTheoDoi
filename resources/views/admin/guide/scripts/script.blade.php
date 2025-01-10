<script>
    document.addEventListener('DOMContentLoaded', () => {
        let stepIndex = {{ count($instance->steps ?? []) }}; // Tạo biến stepIndex từ số bước hiện tại
        const stepsContainer = document.getElementById('steps-container');
        const addStepButton = document.getElementById('add-step-btn');

        // Thêm bước mới
        addStepButton.addEventListener('click', () => {
            const newStep = document.createElement('div');
            newStep.classList.add('step-item', 'border', 'rounded', 'p-3', 'mb-3');
            newStep.innerHTML = `
            <div class="step-item border rounded p-3 mb-3" id="step-${stepIndex}">
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
                <x-input type="number" name="steps[${stepIndex}][order]" :required="true" />
            </div>
        </div>
        `;

            stepsContainer.appendChild(newStep);
            stepIndex++; // Tăng chỉ số bước khi thêm bước mới
        });

        // Xóa bước
        stepsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-step')) {
                e.target.closest('.step-item').remove();
            }
        });
    });
</script>
