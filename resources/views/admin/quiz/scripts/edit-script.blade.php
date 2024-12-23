<script>
    $(document).ready(function() {
        function updateCheckedCount() {
            var count = $('input[name="question_ids[]"]:checked').length;
            $('#checked-count').text(count);
        }

        updateCheckedCount();

        $(document).on('change', 'input[name="question_ids[]"]', function() {
            updateCheckedCount();
        });
    });
</script>
