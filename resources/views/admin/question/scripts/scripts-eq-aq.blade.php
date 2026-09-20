<script>
    $(document).ready(function() {
        let isImage = false;
        let type = $('#answer_type').val();

        if (type == 'image') {
            isImage = true;
        }

        $('#answer_type').on('change', function() {
            if ($(this).val() == 'image') {
                isImage = true;
                $('#answer').find('.answer_image').removeClass('d-none');
                $('#answer').find('.answer_normal').addClass('d-none');
            } else {
                isImage = false;
                $('#answer').find('.answer_image').addClass('d-none');
                $('#answer').find('.answer_normal').removeClass('d-none');
            }
        });

        const html = function(isImage) {
            index++;

            return `
                <div class="answer-card-item d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-1 flex-shrink-0" style="min-width: 120px;">
                        <span class="badge bg-primary-lt px-2 py-1 fs-12 fw-bold">Điểm:</span>
                        <input type="number" name="answers[score][]" required min="1" max="5" step="1" class="form-control" style="width: 65px;" value="1" />
                    </div>
                    
                    <div class="flex-grow-1">
                        <input class="answer_normal form-control ${isImage ? 'd-none' : ''}" type="text" name="answers[answer][]" placeholder="Nhập nội dung câu trả lời..." />
                        <div class="answer_image ${isImage ? '' : 'd-none'}" style="width: 220px;" data-answer="${index}">
                            <x-input-image-ckfinder name="answers[image][${index}]" :value="old('answers.image[0]')" showImage="showAnswerImage-${index}" />
                        </div>
                    </div>

                    <button type="button" class="btn btn-delete-answer remove_answer" title="Xóa mục này">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </div>
            `;
        };

        $('#add_answer').on('click', function() {
            if ($('#answer').find('.answer-card-item').length < 10) {
                $('#answer').append(html(isImage));
            } else {
                alert('Tối đa 10 phương án lựa chọn.');
            }
        });

        $('#answer').on('click', '.remove_answer', function() {
            if ($('#answer').find('.answer-card-item').length <= 2) {
                alert('Mỗi câu hỏi cần tối thiểu 2 phương án lựa chọn.');
                return;
            }
            $(this).closest('.answer-card-item').remove();
        });

        // Xử lý bộ đếm ký tự cho ô câu hỏi
        const $questionContent = $('#question_content');
        const $charCount = $('#question_char_count');
        const $charWrapper = $('#question_char_count_wrapper');
        const $charWarning = $('#question_char_warning');
        const maxQuestionChars = 2000;

        function updateQuestionCharCount() {
            if (!$questionContent.length) return;
            const currentLength = $questionContent.val().length;
            $charCount.text(currentLength);

            if (currentLength >= maxQuestionChars) {
                $charWrapper.addClass('text-danger fw-bold').removeClass('text-muted text-warning');
                $charWarning.removeClass('d-none');
                $questionContent.addClass('is-invalid');
            } else if (currentLength >= maxQuestionChars * 0.9) {
                $charWrapper.addClass('text-warning fw-semibold').removeClass('text-muted text-danger fw-bold');
                $charWarning.addClass('d-none');
                $questionContent.removeClass('is-invalid');
            } else {
                $charWrapper.addClass('text-muted').removeClass('text-danger text-warning fw-bold fw-semibold');
                $charWarning.addClass('d-none');
                $questionContent.removeClass('is-invalid');
            }
        }

        if ($questionContent.length) {
            $questionContent.on('input propertychange', updateQuestionCharCount);
            updateQuestionCharCount();
        }
    });
</script>
