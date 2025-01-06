

<script>
    $(document).ready(function(e) {
        try {
            select2LoadData($('#clinic_type_id').data('url'), '#clinic_type_id');
            select2LoadData($('#province_id').data('url'), '#province_id');
            select2LoadData($('#district_id').data('url'), '#district_id');
            select2LoadData($('#ward_id').data('url'), '#ward_id');

        } catch (error) {
            if (error.message.includes('setPosition')) {
                window.location.reload();
            } else {
                handleAjaxError(error);
            }
        }
    });
</script>
