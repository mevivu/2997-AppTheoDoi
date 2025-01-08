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

        $('#add_answer').on('click', function() {
            $('#answer').append(html(isImage));
        });

        $('#answer').on('click', '.remove_answer', function() {
            $(this).parent().remove();
        });


    });
</script>
