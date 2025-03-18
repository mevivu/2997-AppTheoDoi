<script>
    $(document).ready(function() {
        function updateCheckedCount() {
            const count = $('input[name="question_ids[]"]:checked').length;
            $('#checked-count').text(count);
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
            updateCheckedCount();
        }

        updateCheckedCount();

        $(document).on('change', 'input[name="question_ids[]"]', function() {
            const selectedType = $('input[name="type"]').val();
            if (selectedType === 'eq') {
                restrictMultipleGroupSelection();
            }
        });
    });
</script>
