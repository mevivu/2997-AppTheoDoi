<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');

        $('#is_born').change(function() {
            const isBorn = $('#is_born').val();
            if (isBorn === 'born') {
                $('#date_birthday').removeClass('d-none');
                $('#date_birthday input').attr('required', true);
            } else {
                $('#date_birthday').addClass('d-none');
                $('#date_birthday input').removeAttr('required');
            }
        });
    });
</script>
