<script>
    $(document).ready(function () {
        const questionsUrl = "{{ route('admin.question.type') }}";

        $('#type-select').on('change', function () {
            const selectedType = $(this).val();
            const questionContainer = $('#questions-container');
            if (selectedType) {
                questionContainer.empty();
                $('#loading').show();
                $.ajax({
                    url: questionsUrl,
                    type: 'GET',
                    data: {type: selectedType},
                    success: function (response) {
                        $('#loading').hide();
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function (question) {
                                const questionHtml = `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="question_ids[]" value="${question.id}" id="question-${question.id}">
                                    <label class="form-check-label" for="question-${question.id}">
                                        ${question.question}
                                    </label>
                                </div>
                            `;
                                questionContainer.append(questionHtml);
                            });
                        } else {
                            questionContainer.html('<div>Không có câu hỏi nào.</div>');
                        }
                    },
                    error: function (error) {
                        $('#loading').hide();
                        questionContainer.html('<div>Lỗi khi tải câu hỏi.</div>');

                    }
                });
            }
        });
    });
</script>
