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
        let selectedQuestionIds = @json($selected_questions->pluck('id'));
        console.log(selectedQuestionIds)
        // Pre-selected questions from server
        let debounceTimer; // Timer for debouncing updates to selected questions
        let firstLoad = true;

        function loadSelectedFirstQuestions() {
            if (firstLoad) {
                $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
                firstLoad = false;
            }

        }

        function init() {
            loadSelectedFirstQuestions();
            // Handle type change and search button click events
            $('#type-select').on('change', function () {
                fetchQuestions($(this).val());
            });

            $('#clear-button').on('click', function () {
                $('#search-keyword').val('');
                fetchQuestions($('#type-select').val());
            });

            $('#search-button').on('click', function (event) {
                event.preventDefault();
                const type = $('#type-select').val();
                const keyword = $('#search-keyword').val();
                fetchQuestions(type, keyword);
            });

            // Handle question selection
            $('#questions-container').on('change', 'input[name="question_ids[]"]', function () {
                const questionId = $(this).val();
                const isChecked = $(this).is(':checked');
                updateSelections(questionId, isChecked); // Update immediately
            });

            $('#selected-questions').on('click', '.remove-question', function () {
                const idToRemove = parseInt($(this).data('id'), 10);
                selectedQuestionIds = selectedQuestionIds.filter(id => id !== idToRemove);
                $(`#question-${idToRemove}`).prop('checked', false);
                $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
                updateSelectedQuestions();
            });

            loadInitialQuestions();
            updateSelectedQuestions();// Load initial set of questions
        }

        // Fetch initial questions when page loads
        function loadInitialQuestions() {
            const selectedType = $('#type-select').val();
            if (selectedType) {
                fetchQuestions(selectedType);
            }
        }

        // Fetch questions based on type and search keyword
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
                console.log(error)
                $('#loading').hide();
                questionContainer.html('<div>Lỗi khi tải câu hỏi.</div>');
            }
        }

        function restrictMultipleGroupSelection() {
            const selectedGroups = {};
            $('input[name="question_ids[]"]:checked').each(function() {
                const groupId = $(this).data('group-id');
                if (selectedGroups[groupId]) {
                    $(this).prop('checked', false);
                    alert('Chỉ được chọn một câu hỏi trong mỗi nhóm.');
                } else {
                    selectedGroups[groupId] = true;
                }
            });
        }

        // Render questions in the container
        function renderQuestions(questions, container) {
            const selectedType = $('#type-select').val();
            questions.forEach(function (question) {
                console.log(question);
                const isChecked = selectedQuestionIds.includes(Number(question.id)) ? "checked" : "";

                let groupNameHtml = '';
                if (selectedType === 'eq' || selectedType === 'aq') {
                    groupNameHtml = `<b>${question.group?.name ?? 'Chưa có'}</b>`;
                }

                const questionHtml = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="question_ids[]" value="${question.id}" id="question-${question.id}" ${isChecked} data-group-id="${question.question_group_id}">
                <label class="form-check-label" for="question-${question.id}">
                    ${question.question} ${groupNameHtml}
                </label>
            </div>
        `;
                container.append(questionHtml);
            });
        }


        // Update selected questions based on checkbox input
        function updateSelections(questionId, isChecked) {
            const groupId = $(`#question-${questionId}`).data('group-id'); // Get the group ID of the selected question
            const selectedType = $('#type-select').val(); // Get the selected question type

            // Apply the restriction only if the selected type is 'eq'
            if (selectedType === 'eq' && isChecked) {
                // Check if a question from the same group is already selected
                if (selectedQuestionIds.some(id => $(`#question-${id}`).data('group-id') === groupId)) {
                    alert('Chỉ được chọn một câu hỏi trong mỗi nhóm.');
                    $(`#question-${questionId}`).prop('checked', false); // Uncheck the current question
                    return; // Prevent further execution
                }
                // Add question if not already selected
                if (!selectedQuestionIds.includes(Number(questionId))) {
                    selectedQuestionIds.push(Number(questionId)); // Ensure the ID is number type
                }
            } else {
                // If selected type is not 'eq' or if unchecked, just add/remove without restriction
                if (isChecked) {
                    // Add question if not already selected
                    if (!selectedQuestionIds.includes(Number(questionId))) {
                        selectedQuestionIds.push(Number(questionId)); // Ensure the ID is number type
                    }
                } else {
                    // Remove question if it was previously selected
                    selectedQuestionIds = selectedQuestionIds.filter(id => id !== Number(questionId)); // Ensure the ID is number type
                }
            }

            $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds)); // Update selected questions input
            debounceUpdateSelectedQuestions(); // Debounce the update of selected questions to avoid frequent requests
        }



        // Debounce the selected questions update to avoid frequent requests
        function debounceUpdateSelectedQuestions() {
            clearTimeout(debounceTimer); // Clear the previous timer
            debounceTimer = setTimeout(updateSelectedQuestions, 700); // Delay the API call
        }

        // Update selected questions on the right side after selection change
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
                        data: {ids: selectedQuestionIds}
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
                    console.error("Failed to fetch selected questions:", error.responseText);
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
