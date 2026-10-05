<script>
    $(document).ready(function() {
        select2LoadData($('#subject_id').data('url'), '#subject_id');

        // ===== Cấu hình đánh giá từng môn (class_subject pivot) =====
        const METHODS = @json($methodOptions ?? []);
        const LEVEL_LABELS = @json($educationLevels ?? []);
        const INITIAL_ROWS = @json($subjectConfigs ?? []);

        const $subject = $('#subject_id');
        const $level = $('#education_level');
        const $tbody = $('#subject-config-table tbody');
        const $empty = $('#subject-config-empty');
        const $badge = $('#subject-config-level-badge');

        // id (string) => { id, name, evaluation_method, is_required, sort_order, unconfigured }
        const state = new Map();
        INITIAL_ROWS.forEach(function(r) {
            state.set(String(r.id), {
                id: String(r.id),
                name: r.name,
                evaluation_method: r.evaluation_method,
                is_required: r.is_required !== false,
                sort_order: r.sort_order,
                unconfigured: !r.evaluation_method,
            });
        });

        function currentLevel() {
            return $level.val() || Object.keys(METHODS)[0];
        }

        function allowedValues() {
            return (METHODS[currentLevel()] || []).map(function(m) { return m.value; });
        }

        function defaultMethod() {
            return allowedValues()[0] || '';
        }

        function nextSortOrder() {
            let max = 0;
            state.forEach(function(r) { max = Math.max(max, parseInt(r.sort_order, 10) || 0); });
            return max + 1;
        }

        // Đồng bộ state với danh sách môn đang chọn ở select2
        function syncFromSelect() {
            const ids = ($subject.val() || []).map(String);
            state.forEach(function(_, id) {
                if (!ids.includes(id)) state.delete(id);
            });
            ids.forEach(function(id) {
                if (state.has(id)) return;
                const name = $subject.find('option[value="' + id + '"]').text() || ('#' + id);
                state.set(id, {
                    id: id,
                    name: name,
                    evaluation_method: defaultMethod(),
                    is_required: true,
                    sort_order: nextSortOrder(),
                    unconfigured: false,
                });
            });
            render();
        }

        function render() {
            const allowed = allowedValues();
            const rows = Array.from(state.values()).sort(function(a, b) {
                const sa = parseInt(a.sort_order, 10), sb = parseInt(b.sort_order, 10);
                return (isNaN(sa) ? 9999 : sa) - (isNaN(sb) ? 9999 : sb);
            });

            $badge.text(LEVEL_LABELS[currentLevel()] || '');
            $tbody.empty();
            $empty.toggle(rows.length === 0);

            rows.forEach(function(r, index) {
                if (!allowed.includes(r.evaluation_method)) {
                    r.evaluation_method = defaultMethod();
                }
                const base = 'subject_config[' + r.id + ']';
                const $tr = $('<tr>').attr('data-id', r.id);

                $tr.append($('<td class="text-muted">').text(index + 1));

                const $name = $('<td>').append($('<span class="fw-semibold">').text(r.name));
                if (r.unconfigured) {
                    $name.append(' ').append(
                        $('<span class="badge bg-yellow-lt ms-1">').text('Chưa cấu hình – sẽ lưu theo lựa chọn bên cạnh')
                    );
                }
                $tr.append($name);

                const $method = $('<select class="form-select form-select-sm cfg-method">').attr('name', base + '[evaluation_method]');
                (METHODS[currentLevel()] || []).forEach(function(m) {
                    $method.append($('<option>').val(m.value).text(m.label).prop('selected', m.value === r.evaluation_method));
                });
                $tr.append($('<td>').append($method));

                const $req = $('<td class="text-center">')
                    .append($('<input type="hidden" value="0">').attr('name', base + '[is_required]'))
                    .append(
                        $('<label class="form-check form-switch d-inline-flex justify-content-center m-0">').append(
                            $('<input type="checkbox" value="1" class="form-check-input cfg-required">')
                                .attr('name', base + '[is_required]')
                                .prop('checked', r.is_required)
                        )
                    );
                $tr.append($req);

                $tr.append($('<td>').append(
                    $('<input type="number" min="0" max="999" step="1" class="form-control form-control-sm cfg-order">')
                        .attr('name', base + '[sort_order]')
                        .val(r.sort_order ?? '')
                ));

                $tbody.append($tr);
            });
        }

        $tbody.on('change', '.cfg-method', function() {
            const r = state.get(String($(this).closest('tr').data('id')));
            if (r) { r.evaluation_method = $(this).val(); r.unconfigured = false; }
        });
        $tbody.on('change', '.cfg-required', function() {
            const r = state.get(String($(this).closest('tr').data('id')));
            if (r) r.is_required = $(this).is(':checked');
        });
        $tbody.on('change', '.cfg-order', function() {
            const r = state.get(String($(this).closest('tr').data('id')));
            if (r) { r.sort_order = $(this).val(); render(); }
        });

        $subject.on('change', syncFromSelect);
        $level.on('change', render);

        render();
    });
</script>
