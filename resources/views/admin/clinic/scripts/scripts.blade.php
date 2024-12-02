

<script>
    $(document).ready(function(e) {
        try {
            select2LoadData($('#clinic_type_id').data('url'), '#clinic_type_id');

        } catch (error) {
            if (error.message.includes('setPosition')) {
                window.location.reload();
            } else {
                handleAjaxError(error);
            }
        }
    });
</script>
