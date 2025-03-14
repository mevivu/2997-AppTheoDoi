<script>
    $(document).ready(function() {
        const questionsUrl = "{{ route('admin.question.type') }}";

        init();

        function init() {
            const selectedType = $('#type-select').val();
            if (selectedType) {
                fetchQuestions(selectedType);
            }

            $('#type-select').on('change', handleTypeChange);
        }

        function handleTypeChange() {
            const selectedType = $(this).val();
            fetchQuestions(selectedType);
        }

        function fetchQuestions(type) {
            const questionContainer = $('#questions-container');
            const checkCount = $('#checked-count');

            if (!type) {
                questionContainer.empty();
                checkCount.text(0);
                return;
            }

            questionContainer.empty();
            $('#loading').show();

            $.ajax({
                url: questionsUrl,
                type: 'GET',
                data: {
                    type
                },
                success: function(response) {
                    $('#loading').hide();
                    renderQuestions(response.data, questionContainer, checkCount);
                },
                error: function() {
                    $('#loading').hide();
                    questionContainer.html('<div>Lỗi khi tải câu hỏi.</div>');
                    checkCount.text(0);
                }
            });
        }

        function renderQuestions(questions, container, checkCountElement) {
            const selectedType = $('#type-select').val();

            if (questions && questions.length > 0) {
                questions.forEach(function(question) {
                    const questionGroupName = selectedType === 'aq' ? '' : ` <span class="fw-bold">(${question.question_group_name})</span>`;
                    const questionHtml = `
                <div class="form-check" data-question-group-id="${question.question_group_id}">
                    <input class="form-check-input" type="checkbox" name="question_ids[]" value="${question.id}" id="question-${question.id}">
                    <input type="hidden" name="question_group_ids[]" value="${question.question_group_id}">
                    <label class="form-check-label" for="question-${question.id}">
                          ${question.question}${questionGroupName}
                    </label>
                </div>
            `;
                    container.append(questionHtml);
                });

                function restrictMultipleGroupSelection() {
                    const selectedGroups = {};
                    $('input[name="question_ids[]"]:checked').each(function() {
                        const groupId = $(this).closest('.form-check').data('question-group-id');
                        if (selectedGroups[groupId]) {
                            $(this).prop('checked', false);
                            alert('Chỉ được chọn một câu hỏi trong mỗi nhóm.');
                        } else {
                            selectedGroups[groupId] = true;
                        }
                    });
                    updateCheckedCount();
                }

                function updateCheckedCount() {
                    const checkedCount = $('input[name="question_ids[]"]:checked').length;
                    checkCountElement.text(checkedCount);
                }

                $('input[name="question_ids[]"]').on('change', function() {
                    restrictMultipleGroupSelection();
                    updateCheckedCount();
                });

            } else {
                container.html('<div>Không có câu hỏi nào.</div>');
                checkCountElement.text(0);
            }
        }

    });
</script>
