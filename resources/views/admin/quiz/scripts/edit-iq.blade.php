<script>
    $(document).ready(function () {
        const questionsUrl = "{{ route('admin.question.type') }}";
        const questionsUrlIDS = "{{ route('admin.question.by-ids') }}";
        let selectedQuestionIds = @json($selected_questions->pluck('id'));
        let searchDebounceTimer;
        let updateDebounceTimer;
        let firstLoad = true;
        let load = firstLoad;

        function updateAllCounters() {
            const count = selectedQuestionIds.length;
            const type = $('#type-select').val();
            const isIQ = (type === 'iq' || type === 'IQ' || type == '{{ \App\Enums\Question\QuestionType::IQ->value }}');

            $('#header-checked-count').text(count);
            $('#sidebar-selected-count').text(count);
            $('#estimated-time').text(Math.round(count * 1.5));

            if (isIQ) {
                if (count < 15) {
                    const diff = 15 - count;
                    $('#checked-count')
                        .attr('class', 'badge bg-warning text-dark fw-bold')
                        .html(`<i class="ti ti-alert-triangle me-1"></i>${count}/15 câu (thiếu ${diff} câu)`);
                    
                    $('#iq-rule-alert')
                        .attr('class', 'alert alert-warning d-flex align-items-center py-2 px-3 mb-3 border-0 bg-warning-lt rounded-3')
                        .find('i').attr('class', 'ti ti-alert-triangle fs-3 me-2 text-warning flex-shrink-0');
                    
                    $('#iq-validation-notice')
                        .attr('class', 'ms-2 fw-bold text-warning')
                        .html(`— ⚠️ Hiện tại mới chọn <strong>${count}/15</strong> câu (cần thêm <strong>${diff}</strong> câu)`);

                    $('#sidebar-validation-badge')
                        .attr('class', 'badge bg-warning-lt text-warning fw-bold fs-11')
                        .html(`<i class="ti ti-alert-triangle me-1"></i>Thiếu ${diff} câu`);

                    $('#sidebar-status-text')
                        .attr('class', 'stat-sub text-warning fw-semibold')
                        .html(`<i class="ti ti-alert-circle me-1"></i>Cần chọn thêm ${diff} câu để đủ 15 câu`);
                } else if (count === 15) {
                    $('#checked-count')
                        .attr('class', 'badge bg-success fw-bold')
                        .html(`<i class="ti ti-check me-1"></i>Đã đủ 15/15 câu chuẩn`);

                    $('#iq-rule-alert')
                        .attr('class', 'alert alert-success d-flex align-items-center py-2 px-3 mb-3 border-0 bg-success-lt rounded-3')
                        .find('i').attr('class', 'ti ti-circle-check fs-3 me-2 text-success flex-shrink-0');

                    $('#iq-validation-notice')
                        .attr('class', 'ms-2 fw-bold text-success')
                        .html(`— ✅ Tuyệt vời! Đã chọn đủ đúng <strong>15/15</strong> câu hỏi.`);

                    $('#sidebar-validation-badge')
                        .attr('class', 'badge bg-success-lt text-success fw-bold fs-11')
                        .html(`<i class="ti ti-check me-1"></i>Chuẩn 15/15`);

                    $('#sidebar-status-text')
                        .attr('class', 'stat-sub text-success fw-semibold')
                        .html(`<i class="ti ti-circle-check me-1"></i>Đã đủ số lượng câu hỏi quy định`);
                } else {
                    const diff = count - 15;
                    $('#checked-count')
                        .attr('class', 'badge bg-danger fw-bold')
                        .html(`<i class="ti ti-alert-circle me-1"></i>${count}/15 câu (thừa ${diff} câu)`);

                    $('#iq-rule-alert')
                        .attr('class', 'alert alert-danger d-flex align-items-center py-2 px-3 mb-3 border-0 bg-danger-lt rounded-3')
                        .find('i').attr('class', 'ti ti-alert-circle fs-3 me-2 text-danger flex-shrink-0');

                    $('#iq-validation-notice')
                        .attr('class', 'ms-2 fw-bold text-danger')
                        .html(`— ❌ Đang vượt quá <strong>${diff}</strong> câu (cần bỏ bớt để đủ 15 câu)`);

                    $('#sidebar-validation-badge')
                        .attr('class', 'badge bg-danger-lt text-danger fw-bold fs-11')
                        .html(`<i class="ti ti-x me-1"></i>Thừa ${diff} câu`);

                    $('#sidebar-status-text')
                        .attr('class', 'stat-sub text-danger fw-semibold')
                        .html(`<i class="ti ti-alert-circle me-1"></i>Cần bỏ bớt ${diff} câu để đủ 15 câu`);
                }
            } else {
                $('#checked-count').attr('class', 'badge bg-success fw-bold').text(count + ' câu');
            }

            if (count === 0) {
                $('#empty-selected-box').show();
                $('#selected-questions').hide();
            } else {
                $('#empty-selected-box').hide();
                $('#selected-questions').show();
            }
        }

        function reindexSequenceNumbers() {
            $('#selected-questions .quiz-selected-item').each(function (index) {
                const seq = (index + 1).toString().padStart(2, '0');
                $(this).find('.seq-num').text(seq);
            });
        }

        function init() {
            if (firstLoad) {
                $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
                firstLoad = false;
            }

            // Real-time search with debounce
            $('#search-keyword, #search-age').on('input', function () {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function () {
                    const type = $('#type-select').val();
                    const keyword = $('#search-keyword').val();
                    const age = $('#search-age').val();
                    fetchQuestions(type, keyword, age);
                }, 350);
            });

            // Clear filter button
            $('#clear-button').on('click', function () {
                $('#search-keyword').val('');
                $('#search-age').val('');
                fetchQuestions($('#type-select').val());
            });

            // Toggle selection from available cards
            $('#questions-container').on('click', '.quiz-question-card', function (e) {
                if ($(e.target).is('a')) return;
                const id = Number($(this).data('id'));
                const isCurrentlySelected = selectedQuestionIds.includes(id);
                updateSelections(id, !isCurrentlySelected);
            });

            // Remove question from selected list
            $('#selected-questions').on('click', '.btn-remove', function (e) {
                e.stopPropagation();
                const idToRemove = Number($(this).data('id'));
                selectedQuestionIds = selectedQuestionIds.filter(id => id !== idToRemove);
                
                // Update card state in left column if visible
                $(`#card-${idToRemove}`).removeClass('is-selected').find('.btn-add-indicator').html('<i class="ti ti-plus"></i>');
                
                syncInput();
                updateAllCounters();
                
                // Animate removal from DOM
                $(this).closest('.quiz-selected-item').slideUp(150, function () {
                    $(this).remove();
                    reindexSequenceNumbers();
                    updateAllCounters();
                });
            });

            // Intercept form submission for instant client-side validation
            $('form').on('submit', function (e) {
                const type = $('#type-select').val();
                const isIQ = (type === 'iq' || type === 'IQ' || type == '{{ \App\Enums\Question\QuestionType::IQ->value }}');
                if (isIQ) {
                    const count = selectedQuestionIds.length;
                    if (count !== 15) {
                        e.preventDefault();
                        e.stopPropagation();

                        const msg = count < 15
                            ? `Bài kiểm tra IQ phải có đúng 15 câu hỏi. Hiện tại bạn mới chọn ${count}/15 câu (thiếu ${15 - count} câu).`
                            : `Bài kiểm tra IQ phải có đúng 15 câu hỏi. Hiện tại bạn đang chọn ${count}/15 câu (thừa ${count - 15} câu).`;

                        if (typeof msgWarning === 'function') {
                            msgWarning(msg);
                        } else if (typeof msgError === 'function') {
                            msgError(msg);
                        } else {
                            alert(msg);
                        }

                        // Focus & smooth scroll to question rule banner
                        $('#selected-panel-body').addClass('border border-warning shadow-sm');
                        $('html, body').animate({
                            scrollTop: $('#iq-rule-alert').offset().top - 100
                        }, 300);
                        setTimeout(() => {
                            $('#selected-panel-body').removeClass('border border-warning shadow-sm');
                        }, 2500);

                        return false;
                    }
                }
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
                    if (response.data && response.data.length) {
                        $('#available-count').text(response.data.length);
                        renderQuestions(response.data, container);
                    } else {
                        $('#available-count').text(0);
                        container.html(`
                            <div class="quiz-empty-box py-4">
                                <i class="ti ti-file-search"></i>
                                <div class="empty-text">${__('Không tìm thấy câu hỏi phù hợp.')}</div>
                            </div>
                        `);
                    }
                },
                error: function () {
                    $('#loading').hide();
                    container.html(`
                        <div class="quiz-empty-box text-danger py-4">
                            <i class="ti ti-alert-circle"></i>
                            <div class="empty-text">${__('Lỗi khi tải danh sách câu hỏi.')}</div>
                        </div>
                    `);
                }
            });
        }

        function renderQuestions(questions, container) {
            container.empty();

            questions.forEach(q => {
                const isSelected = selectedQuestionIds.includes(q.id);
                const selectedClass = isSelected ? 'is-selected' : '';
                const actionIcon = isSelected ? '<i class="ti ti-check text-success fs-5"></i>' : '<i class="ti ti-plus text-primary fs-5"></i>';
                const ageText = q.age ? `${q.age} tuổi` : '';

                const html = `
                    <div class="quiz-question-card ${selectedClass}" id="card-${q.id}" data-id="${q.id}">
                        <div class="card-top-meta">
                            <span class="code-badge">#${q.code}</span>
                            <div class="d-flex align-items-center gap-1">
                                ${ageText ? `<span class="age-badge">${ageText}</span>` : ''}
                                <span class="btn-add-indicator ms-1">${actionIcon}</span>
                            </div>
                        </div>
                        <p class="question-text">${q.question}</p>
                    </div>
                `;
                container.append(html);
            });

            if (window.MathJax) MathJax.typesetPromise();
        }

        function updateSelections(id, isChecked) {
            const card = $(`#card-${id}`);
            if (isChecked && !selectedQuestionIds.includes(id)) {
                selectedQuestionIds.push(id);
                card.addClass('is-selected').find('.btn-add-indicator').html('<i class="ti ti-check text-success fs-5"></i>');
            } else if (!isChecked) {
                selectedQuestionIds = selectedQuestionIds.filter(qid => qid !== id);
                card.removeClass('is-selected').find('.btn-add-indicator').html('<i class="ti ti-plus text-primary fs-5"></i>');
            }
            syncInput();
            updateAllCounters();
            debounceUpdateSelectedQuestions();
        }

        function debounceUpdateSelectedQuestions() {
            clearTimeout(updateDebounceTimer);
            updateDebounceTimer = setTimeout(updateSelectedQuestions, 400);
        }

        function syncInput() {
            $('#selected_questions_input').val(JSON.stringify(selectedQuestionIds));
        }

        function updateSelectedQuestions() {
            const container = $('#selected-questions');
            const loader = $('#loading-indicator');
            
            if (!selectedQuestionIds.length) {
                container.empty();
                updateAllCounters();
                loader.hide();
                return;
            }

            loader.show();
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
                    container.empty();

                    if (response.data && response.data.length) {
                        response.data.forEach((q, index) => {
                            const seq = (index + 1).toString().padStart(2, '0');
                            const detailUrl = `${urlHome}/admin/question/edit/iq/${q.id}`;
                            const ageInfo = q.age ? `<span class="badge bg-light text-muted">${q.age} tuổi</span>` : '';

                            const item = $(`
                                <div class="quiz-selected-item" data-id="${q.id}">
                                    <div class="drag-handle" title="Kéo để đổi vị trí">
                                        <i class="ti ti-grip-vertical"></i>
                                    </div>
                                    <div class="seq-num">${seq}</div>
                                    <div class="item-content">
                                        <div class="item-meta">
                                            <a href="${detailUrl}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                #${q.code}
                                            </a>
                                            ${ageInfo}
                                        </div>
                                        <div class="item-text" title="${q.question}">${q.question}</div>
                                    </div>
                                    <button type="button" class="btn-remove" data-id="${q.id}" title="Xóa khỏi bài test">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            `);
                            container.append(item);
                        });

                        // Init Sortable.js
                        new Sortable(container[0], {
                            handle: '.drag-handle',
                            animation: 180,
                            ghostClass: 'bg-primary-lt',
                            onEnd: function () {
                                selectedQuestionIds = container.find('.quiz-selected-item').map(function () {
                                    return Number($(this).data('id'));
                                }).get();
                                syncInput();
                                reindexSequenceNumbers();
                            }
                        });
                    }

                    loader.hide();
                    updateAllCounters();
                    if (window.MathJax) MathJax.typesetPromise();
                },
                error: function (err) {
                    console.error("Lỗi khi tải câu hỏi đã chọn:", err);
                    loader.hide();
                }
            });
        }

        init();
    });
</script>
