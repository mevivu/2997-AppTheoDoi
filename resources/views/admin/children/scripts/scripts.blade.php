<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');

        $('#is_born').change(function() {
            const isBorn = $('#is_born').val();
            if (isBorn === 'born') {

                $('#date_birthday').removeClass('d-none');
                $('#date_birthday input').attr('required', true);

                $('#due_date').addClass('d-none');
                $('#due_date input').removeAttr('required');
            } else {
                $('#date_birthday').addClass('d-none');
                $('#date_birthday input').removeAttr('required');

                $('#due_date').removeClass('d-none');
                $('#due_date input').attr('required', true);
            }
        });
    });
</script>
