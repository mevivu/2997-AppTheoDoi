<script>
    $(document).ready(function() {
        $('#question_type').change(function() {
            let value = $(this).val();
            if (value == 'iq') {
                $('#question_iq_group').removeClass('d-none');
                $('#question_aq_eq_group').addClass('d-none');
            } else if (value == 'aq' || value == 'eq') {
                $('#question_iq_group').addClass('d-none');
                $('#question_aq_eq_group').removeClass('d-none');
            }
        });

        $('#add_wrong_answer').click(function() {
            let html = `
                <div class="d-flex align-items-center justify-content-start gap-2 position-relative mb-3">
                        <input type="hidden" name="answer[is_correct][]" value="0"/>
                        <input type="checkbox" name="answer[is_correct][]" class="form-check-input" value="1" onchange="toggleCheckbox(this)"/>
                        <x-input type="text" name="answer[iq_answers][]" :placeholder="'Nhập nội dung câu trả lời'" />
                     <button type="button" class="btn btn-danger remove_wrong_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                </div>
            `;
            $('#wrong_answers').append(html);
        });

        $(document).on('click', '.remove_wrong_answer', function() {
            $(this).parent().remove();
        });

        $('#add_answer').click(function() {
            let html = `
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                    <x-input type="text" name="answer[answers][]" class="flex-grow-1" :placeholder="'Nhập câu trả lời'" :required="true" />
                    <x-input type="number" min="1" max="5" name="answer[scores][]" class="flex-grow-1" :placeholder="'Nhập điểm đánh giá'" :required="true" />
                    <button type="button" class="btn btn-danger remove_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                </div>
            `;
            $('#answers').append(html);
        });

        $(document).on('click', '.remove_answer', function() {
            $(this).parent().remove();
        });
    });

    function toggleCheckbox(checkbox) {
        const hiddenInput = checkbox.previousElementSibling;
        hiddenInput.disabled = checkbox.checked;
    }
</script>
