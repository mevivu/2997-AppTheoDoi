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

            return `<div class="d-flex align-items-center justify-content-start gap-2 mb-3">
                        <x-input type="number" name="answers[score][]" :required="true" min="1" max="5"
                            step="1" style="width:60px" />
                        <x-input class="answer_normal ${isImage ? 'd-none' : ''}" type="text" name="answers[answer][]" />
                        <div class="answer_image ${isImage ? '' : 'd-none'}" style="width: 200px;" data-answer="${index}">
                            <x-input-image-ckfinder name="answers[image][${index}]" :value="old('answers.image[0]')"
                                showImage="showAnswerImage-${index}" />
                        </div>
                        <button type="button" class="btn btn-danger remove_answer">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>`;
        };

        $('#add_answer').on('click', function() {
            if ($('#answer').find('.d-flex').length < 5) {
                $('#answer').append(html(isImage));
            }
        });

        $('#answer').on('click', '.remove_answer', function() {
            $(this).parent().remove();
        });

        function getImageArray() {
            let images = [];
            let imageElements = document.querySelectorAll('.answer_image img');
            imageElements.forEach((imageElement) => {
                images.push(imageElement.src);
            });

            return images;
        }

        $('#form_eq_aq').on('submit', function(event) {
            event.preventDefault();
            if (isImage) {
                let imageElements = document.querySelectorAll('.answer_image');
                let images = getImageArray();

                images.forEach((image, index) => {
                    let answerId = imageElements[index]?.dataset.answer;

                    if (answerId) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `answers[image][${answerId}]`;
                        input.value = image;
                        this.appendChild(input);
                    } else {
                        console.error('Không tìm thấy ID câu trả lời cho ảnh:', image);
                    }
                });
            }

            this.submit();
        });
    })
</script>
