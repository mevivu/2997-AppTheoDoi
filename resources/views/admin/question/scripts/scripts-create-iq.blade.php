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

        function getLetter(index) {
            return String.fromCharCode(65 + (index % 26));
        }

        function reIndexAnswers() {
            $('#answer .answer-card-item').each(function(idx) {
                $(this).find('.answer-letter-badge').text(getLetter(idx));
                $(this).find('.radio-correct-answer').val(idx);
                $(this).find('.answer_image').attr('data-answer', idx);
            });
        }

        const html = function(isImage, newIndex) {
            const letter = getLetter(newIndex);
            return `
                <div class="answer-card-item d-flex align-items-center gap-3">
                    <div class="answer-letter-badge">${letter}</div>
                    
                    <div class="flex-grow-1">
                        <input class="answer_normal form-control ${isImage ? 'd-none' : ''}" type="text" name="answers[answer][]"
                               placeholder="Nhập nội dung đáp án ${letter}..." required />
                        <div class="answer_image ${isImage ? '' : 'd-none'}" style="width: 220px;" data-answer="${newIndex}">
                            <x-input-image-ckfinder name="answers[image][${newIndex}]" :value="old('answers.image[0]')" showImage="showAnswerImage-${newIndex}" />
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="correct-option-pill mb-0">
                            <input class="d-none radio-correct-answer" type="radio" name="answers[is_correct]" value="${newIndex}">
                            <i class="ti ti-check-circle fs-5"></i>
                            <span class="correct-text">{{ __('Đáp án đúng') }}</span>
                        </label>
                        
                        <button type="button" class="btn btn-delete-answer remove_answer" title="{{ __('Xóa đáp án này') }}">
                            <i class="ti ti-trash fs-5"></i>
                        </button>
                    </div>
                </div>
            `;
        };

        $('#add_answer').on('click', function() {
            const currentCount = $('#answer .answer-card-item').length;
            if (currentCount >= 10) {
                alert('Tối đa 10 câu trả lời cho một câu hỏi.');
                return;
            }
            $('#answer').append(html(isImage, currentCount));
            reIndexAnswers();
        });

        $('#answer').on('click', '.remove_answer', function() {
            const currentCount = $('#answer .answer-card-item').length;
            if (currentCount <= 2) {
                alert('Mỗi câu hỏi cần tối thiểu 2 đáp án lựa chọn.');
                return;
            }
            const isChecked = $(this).closest('.answer-card-item').find('.radio-correct-answer').is(':checked');
            $(this).closest('.answer-card-item').remove();
            reIndexAnswers();

            if (isChecked) {
                const firstCard = $('#answer .answer-card-item').first();
                firstCard.find('.radio-correct-answer').prop('checked', true).trigger('change');
            }
        });

        // Handle active style on correct answer change
        $('#answer').on('change', '.radio-correct-answer', function() {
            $('#answer .answer-card-item').removeClass('is-correct-card');
            $('#answer .correct-option-pill').removeClass('active');

            if ($(this).is(':checked')) {
                $(this).closest('.answer-card-item').addClass('is-correct-card');
                $(this).closest('.correct-option-pill').addClass('active');
            }
        });

        // Direct click on correct-option-pill
        $('#answer').on('click', '.correct-option-pill', function(e) {
            const radio = $(this).find('.radio-correct-answer');
            radio.prop('checked', true).trigger('change');
        });
    });
</script>
