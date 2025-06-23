
<script>
    $(document).ready(function () {
        const questionsUrl = "{{ route('admin.question.type') }}";
        const questionsUrlIDS = "{{ route('admin.question.by-ids') }}";
        let selectedQuestionIds = @json($selected_questions->pluck('id'));
        let debounceTimer;
        let firstLoad = true;
        let load = firstLoad;

        function init() {
            if (firstLoad) {
                $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
                firstLoad = false;
            }

            $('#type-select').on('change', function () {
                fetchQuestions($(this).val());
            });

            $('#clear-button').on('click', function () {
                $('#search-keyword').val('');
                fetchQuestions($('#type-select').val());
            });

            $('#search-button').on('click', function (e) {
                e.preventDefault();
                const type = $('#type-select').val();
                const keyword = $('#search-keyword').val();
                const age = $('#search-age').val();
                fetchQuestions(type, keyword,age);
            });

            $('#questions-container').on('change', 'input[name="question_ids[]"]', function () {
                const id = Number($(this).val());
                const isChecked = $(this).is(':checked');
                updateSelections(id, isChecked);
            });

            $('#selected-questions').on('click', '.remove-question', function () {
                const idToRemove = Number($(this).data('id'));
                selectedQuestionIds = selectedQuestionIds.filter(id => id !== idToRemove);
                $(`#question-${idToRemove}`).prop('checked', false);
                syncInput();
                updateSelectedQuestions();
            });

            loadInitial();
            updateSelectedQuestions();
        }

        function loadInitial() {
            const type = $('#type-select').val();
            if (type) fetchQuestions(type);
        }

        function fetchQuestions(type, keyword = '', age = '') {
            const container = $('#questions-container');
            container.empty();
            $('#loading').show();

            $.ajax({
                url: questionsUrl,
                type: 'GET',
                data: { type, keyword, age },
                success: function (response) {
                    $('#loading').hide();
                    if (response.data?.length) {
                        renderQuestions(response.data, container);
                        const panelBody = $('#question-panel-body');
                        if (panelBody.is(':hidden')) {
                            panelBody.slideDown(200);
                            $('#toggle-question-panel').text('Ẩn');
                        }
                    } else {
                        container.html('<div>Không có câu hỏi nào.</div>');
                    }
                    $('#checked-count').text(selectedQuestionIds.length);
                },
                error: function () {
                    $('#loading').hide();
                    container.html('<div>Lỗi khi tải câu hỏi.</div>');
                }
            });
        }

        function renderQuestions(questions, container) {
            container.empty();

            questions.forEach(q => {
                const isChecked = selectedQuestionIds.includes(q.id);
                const checkedAttr = isChecked ? 'checked' : '';
                const selectedClass = isChecked ? 'bg-selected' : '';
                const html = `
            <div class="card mb-2 border shadow-sm ${selectedClass}" id="card-${q.id}" data-id="${q.id}">
                <div class="card-body d-flex align-items-start py-2 px-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="question_ids[]" value="${q.id}" id="question-${q.id}" ${checkedAttr}>
                        <label class="form-check-label ms-2" for="question-${q.id}">
                          <strong>${q.code}</strong> - ${q.question}
                        </label>
                    </div>
                </div>
            </div>
        `;
                container.append(html);
            });

            // ✅ Sự kiện đổi màu khi checkbox thay đổi
            container.find('input[type="checkbox"]').off('change').on('change', function () {
                const card = $(this).closest('.card');
                card.toggleClass('bg-selected', this.checked);
            });

            // ✅ Click vào toàn bộ card (trừ checkbox & label) cũng check được
            container.find('.card').off('click').on('click', function (e) {
                if (!$(e.target).is('input[type="checkbox"], label')) {
                    const checkbox = $(this).find('input[type="checkbox"]');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });

            if (window.MathJax) MathJax.typesetPromise();
        }



        function updateSelections(id, isChecked) {
            if (isChecked && !selectedQuestionIds.includes(id)) {
                selectedQuestionIds.push(id);
            } else if (!isChecked) {
                selectedQuestionIds = selectedQuestionIds.filter(qid => qid !== id);
            }
            syncInput();
            debounceUpdateSelectedQuestions();
        }

        function debounceUpdateSelectedQuestions() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateSelectedQuestions, 1000);
        }

        function syncInput() {
            $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
        }

        function updateSelectedQuestions() {
            const container = $('#selected-questions');
            const loader = $('#loading-indicator');
            container.empty();
            loader.show();

            if (!selectedQuestionIds.length) {
                $('#checked-count').text(0);
                loader.hide();
                return;
            }
            const quizId = $('input[name="id"]').val();
            $.ajax({
                url: questionsUrlIDS,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                data: {
                    ids: selectedQuestionIds,
                    quiz_id: quizId,
                    load: load
                },
                success: function (response) {
                    load = false;
                    const list = $('<ul class="list-group"></ul>');
                    response.data.forEach(q => {
                        const detailUrl = `${urlHome}/admin/question/edit/iq/${q.id}`;
                        const item = $(`
                        <li class="list-group-item d-flex justify-content-between align-items-start" data-id="${q.id}">
                            <div class="question-text">
                                <div class="fw-semibold text-dark mb-1">
                                    <i class="ti ti-hash text-muted me-1"></i>
                                    <a href="${detailUrl}" target="_blank" class="text-decoration-none link-primary">
                                        ${q.code}
                                    </a>
                                </div>
                                <div class="text-secondary small">${q.question}</div>
                            </div>
                          <button type="button"
                                class="btn btn-outline-danger btn-icon btn-rounded remove-question"
                                data-id="${q.id}" title="Xoá câu hỏi"
                                style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-trash fs-5"></i>
                        </button>

                        </li>
                    `);
                        list.append(item);
                    });



                    container.append(list);
                    loader.hide();
                    $('#checked-count').text(selectedQuestionIds.length);
                    if (window.MathJax) MathJax.typesetPromise();

                    // Sortable Init
                    new Sortable(list[0], {
                        animation: 150,
                        onEnd: function () {
                            selectedQuestionIds = list.find('.list-group-item').map(function () {
                                return Number($(this).data('id'));
                            }).get();
                            syncInput();
                        }
                    });
                },
                error: function (err) {
                    console.error("Lỗi khi tải câu hỏi:", err);
                    loader.hide();
                }
            });
        }

        init();

        $(document).ready(function () {
            $('#toggle-question-panel').on('click', function () {
                $('#question-panel-body').slideToggle(200);

                const isHidden = $(this).text().trim() === 'Ẩn';
                $(this).text(isHidden ? 'Hiển thị' : 'Ẩn');
            });
        });

        $('#toggle-selected-panel').on('click', function () {
            const panel = $('#selected-panel-body');
            const isHidden = panel.is(':hidden');
            panel.slideToggle(200);
            $(this).text(isHidden ? 'Ẩn' : 'Hiển thị');
        });
    });
</script>
