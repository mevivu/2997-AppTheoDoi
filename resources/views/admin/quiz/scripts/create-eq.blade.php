<style>
    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .question-text {
        flex-grow: 1;
    }
    .remove-question {
        margin-left: 10px;
    }
</style>
<script>
    $(document).ready(function () {
        const questionsUrl = "{{ route('admin.question.type') }}";
        const questionsUrlIDS = "{{ route('admin.question.by-ids') }}";
        let selectedQuestionIds = [];
        let debounceTimer; // Timer for debouncing updates to selected questions

        function init() {
            $('#type-select').on('change', function () {
                fetchQuestions($(this).val());
            });

            $('#search-button').on('click', function (event) {
                event.preventDefault();
                const type = $('#type-select').val();
                const keyword = $('#search-keyword').val();
                fetchQuestions(type, keyword);
            });

            $('#clear-button').on('click', function () {
                $('#search-keyword').val('');
                fetchQuestions($('#type-select').val());
            });

            $('#questions-container').on('change', 'input[name="question_ids[]"]', function () {
                const questionId = parseInt($(this).val(), 10);
                const isChecked = $(this).is(':checked');
                updateSelections(questionId, isChecked);
            });

            $('#selected-questions').on('click', '.remove-question', function () {
                const idToRemove = parseInt($(this).data('id'), 10);
                selectedQuestionIds = selectedQuestionIds.filter(id => id !== idToRemove);
                $(`#question-${idToRemove}`).prop('checked', false);
                $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
                updateSelectedQuestions();
            });

            fetchInitialQuestions(); // Fetch initial set of questions if needed
        }

        function fetchInitialQuestions() {
            const selectedType = $('#type-select').val();
            fetchQuestions(selectedType);
        }

        async function fetchQuestions(type, keyword = '') {
            const questionContainer = $('#questions-container');
            questionContainer.empty();
            $('#loading').show();
            const selectedAgeGroup = $('#filter_age').val();
            try {
                const response = await $.ajax({
                    url: questionsUrl,
                    type: 'GET',
                    data: {type, keyword, age_group: selectedAgeGroup}
                });
                $('#loading').hide();
                if (response.data && response.data.length > 0) {
                    renderQuestions(response.data, questionContainer);
                } else {
                    questionContainer.html('<div>Không có câu hỏi nào.</div>');
                }
                $('#checked-count').text(selectedQuestionIds.length);
            } catch (error) {
                $('#loading').hide();
                questionContainer.html('<div>Lỗi khi tải câu hỏi.</div>');
            }
        }

        function renderQuestions(questions, container) {
            questions.forEach(function (question) {
                const isChecked = selectedQuestionIds.includes(question.id) ? "checked" : "";
                const questionHtml = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="question_ids[]" value="${question.id}" id="question-${question.id}" ${isChecked}>
                    <label class="form-check-label" for="question-${question.id}">
                        ${question.question}
                    </label>
                </div>
            `;
                container.append(questionHtml);
            });
        }

        function updateSelections(questionId, isChecked) {
            if (isChecked) {
                if (!selectedQuestionIds.includes(questionId)) {
                    selectedQuestionIds.push(questionId);
                }
            } else {
                selectedQuestionIds = selectedQuestionIds.filter(id => id !== questionId);
            }
            $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
            debounceUpdateSelectedQuestions();
        }

        function debounceUpdateSelectedQuestions() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateSelectedQuestions, 700);
        }

        async function updateSelectedQuestions() {
            const selectedQuestionsContainer = $('#selected-questions');
            const loadingIndicator = $('#loading-indicator');
            selectedQuestionsContainer.empty();
            loadingIndicator.show();

            if (selectedQuestionIds.length > 0) {
                try {
                    const response = await $.ajax({
                        url: questionsUrlIDS,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: { ids: selectedQuestionIds }
                    });

                    const list = $('<ul class="list-group"></ul>');
                    response.data.forEach(function (question) {
                        if ($('#selected-questions li').find(`button[data-id="${question.id}"]`).length === 0) {
                            const listItem = $(`<li class="list-group-item">
                        ${question.question}
                        <button type="button" class="btn btn-sm btn-danger remove-question" data-id="${question.id}">Xoá</button>
                    </li>`);
                            list.append(listItem);
                        }
                    });
                    selectedQuestionsContainer.append(list);
                    $('#checked-count').text(selectedQuestionIds.length); // Update the count of selected questions
                    loadingIndicator.hide();
                } catch (error) {
                    console.error("Failed to fetch questions:", error.responseText);
                    loadingIndicator.hide();
                }
            } else {
                $('#checked-count').text(0);
                loadingIndicator.hide();
            }
        }

        init();
    });

</script>
