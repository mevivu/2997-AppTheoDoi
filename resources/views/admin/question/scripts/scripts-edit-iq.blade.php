<script>
    $(document).ready(function () {
        let isImage = false;
        const answerType = $('#answer_type');
        const answer = $('#answer');

        let answerTypeValue = answerType.val();

        if (answerTypeValue === 'image') {
            isImage = true;
        }

        answerType.on('change', function () {

            answer.empty();
            reIndexAnswers();

            if ($(this).val() === 'image') {
                isImage = true;
                answer.find('.answer_image').removeClass('d-none');
                answer.find('.answer_normal').addClass('d-none');
            } else {
                isImage = false;
                answer.find('.answer_image').addClass('d-none');
                answer.find('.answer_normal').removeClass('d-none');
            }
        });

        function reIndexAnswers() {
            answer.find('.form-check').each(function (index) {
                $(this).find('input[type="radio"]').val(index);
            });
        }


        const html = function (isImage, index) {
            return `<div class="form-check d-flex align-items-center justify-content-start gap-2 mb-3">
                <input class="form-check-input" type="radio" name="answers[is_correct]" value="${index}">
                <x-input class="answer_normal ${isImage ? 'd-none' : ''}" type="text" name="answers[answer][]"
                    placeholder="Nhập câu trả lời" />
                <div class="answer_image ${isImage ? '' : 'd-none'}" style="width: 200px;" data-answer="${index}">
                    <x-input-image-ckfinder name="answers[image][${index}]" :value="old('answers.image[0]')" showImage="showAnswerImage-${index}" />
                </div>
                <button type="button" class="btn btn-danger remove_answer">
                    <i class="ti ti-x"></i>
                </button>
            </div>`;
        };

        $('#add_answer').on('click', function () {
            const newIndex = answer.find('.form-check').length;
            answer.append(html(isImage, newIndex));
            reIndexAnswers();
        });

        answer.on('click', '.remove_answer', function () {
            $(this).parent().remove();
            reIndexAnswers();
            $('input[type="radio"][name="answers[is_correct]"]:first').prop('checked', true);
        });


    });
</script>
