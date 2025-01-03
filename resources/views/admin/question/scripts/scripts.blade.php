    <script>
        $(document).ready(function() {
            const $answerType = $('#answer_type');
            const $questionType = $('#question_type');
            const $wrongAnswers = $('#wrong_answers');
            const $answers = $('#answers');
            const $answerTypeAQEQ = $('#answer_type_aq_eq');
            let showImageIndex = 1;

            function toggleAnswerType(value) {
                if (value === 'image') {
                    $('.default').addClass('d-none');
                    $('.img-ckfinder').removeClass('d-none');
                } else {
                    $('.default').removeClass('d-none');
                    $('.img-ckfinder').addClass('d-none');
                }
            }

            function toggleQuestionType(value) {
                if (value === 'iq') {
                    $('#question_iq_group').removeClass('d-none');
                    $('#question_aq_eq_group').addClass('d-none');
                } else if (value === 'aq' || value === 'eq') {
                    $('#question_iq_group').addClass('d-none');
                    $('#question_aq_eq_group').removeClass('d-none');
                }
            }

            function toggleAnswerTypeAQEQ(value) {
                if (value === 'image') {
                    $('.default-aqeq').addClass('d-none');
                    $('.img-ckfinder-eqaq').removeClass('d-none');
                } else {
                    $('.default-aqeq').removeClass('d-none');
                    $('.img-ckfinder-eqaq').addClass('d-none');
                }
            }

            $answerType.change(function() {
                toggleAnswerType($(this).val());
            });

            $questionType.change(function() {
                toggleQuestionType($(this).val());
            });

            $answerTypeAQEQ.change(function() {
                toggleAnswerTypeAQEQ($(this).val());
            });

            $('#add_wrong_answer').click(function() {
                showImageIndex++;
                const html = `
                    <div class="d-flex align-items-center justify-content-start gap-2 position-relative mb-3">
                        <input type="hidden" name="answer[is_correct][][${question_id}]" value="0" />
                        <input type="radio" name="answer[is_correct][][${question_id}]" class="form-check-input" value="1" onchange="toggleCheckbox(this)" />
                        <x-input type="text" name="answer[iq_answers][]" :placeholder="'Nhập nội dung câu trả lời'" class="default ${$answerType.val() == 'normal' ? '' : 'd-none'}" />
                        <div class="${$answerType.val() == 'image' ? '' : 'd-none'} img-ckfinder" style="width:200px; object-fit:cover;">
                            <x-input-image-ckfinder name="answer[image-iq][${showImageIndex}]" :value="old('image[0]')" showImage="answerImage-${showImageIndex}" />
                        </div>
                        <button type="button" class="btn btn-danger remove_wrong_answer">
                            <i class="ti ti-x fs-2"></i>
                        </button>
                    </div>
                `;
                $wrongAnswers.append(html);
            });

            let editAnswerTypeAQEQ = $('input[name="answer[answer_type_aq_eq]"]');
            $('#add_answer').click(function() {
                showImageIndex++;
                const html = `
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div class="w-50 default-aqeq ${editAnswerTypeAQEQ.val() == 'normal' ? '' : 'd-none'}">
                            <x-input type="text" name="answer[answers][]" class="flex-grow-1"
                                :value="old('answer[0]')" :placeholder="'Nhập câu trả lời'" />
                        </div>

                        <div class="w-50 img-ckfinder-eqaq ${editAnswerTypeAQEQ.val() == 'image' ? '' : 'd-none'}" style="width:200px; object-fit:cover;">
                            <x-input-image-ckfinder name="answer[image-eqaq][${showImageIndex}]" :value="old('image[0]')" showImage="answerImage-${showImageIndex}" />
                        </div>

                        <x-input type="number" min="1" max="5" name="answer[scores][]" class="flex-grow-1" :placeholder="'Nhập điểm đánh giá'" />
                        <button type="button" class="btn btn-danger remove_answer">
                            <i class="ti ti-x fs-2"></i>
                        </button>
                    </div>
                `;
                $answers.append(html);
            });

            $wrongAnswers.on('click', '.remove_wrong_answer', function() {
                $(this).parent().remove();
            });

            $answers.on('click', '.remove_answer', function() {
                $(this).parent().remove();
            });

            let answerTypeEdit = $('input[name="answer[answer_type]"]') ?? $answerType;
            $('#add_wrong_answer_edit').click(function() {
                const tempAnswerId = Date.now();
                const html = `
                    <div class="d-flex align-items-center justify-content-start gap-2 position-relative mb-3" id="answer_${tempAnswerId}">
                        <input type="radio" name="answer[is_correct][${question_id}]" class="form-check-input" value="${tempAnswerId}" onchange="toggleCheckbox(this)" />
                        <x-input type="text" name="answer[iq_answers][${tempAnswerId}]" :placeholder="'Nhập nội dung câu trả lời'"
                            class="default ${answerTypeEdit.val() == 'normal' ? '' : 'd-none'}"/>
                        <div class="img-ckfinder ${answerTypeEdit.val() == 'image' ? '' : 'd-none'}" style="width:200px; object-fit:cover;">
                            <x-input-image-ckfinder name="answer[image-iq][${tempAnswerId}]" :value="old('image[0]')" showImage="answerImage-${showImageIndex}" />
                        </div>
                        <button type="button" class="btn btn-danger remove_wrong_answer">
                            <i class="ti ti-x fs-2"></i>
                        </button>
                    </div>
                `;
                $wrongAnswers.append(html);
            });
        });

        function toggleCheckbox(checkbox) {
            const hiddenInput = checkbox.previousElementSibling;
            hiddenInput.disabled = checkbox.checked;
        }
    </script>
