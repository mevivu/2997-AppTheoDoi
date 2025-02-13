

<script>
    $(document).ready(function(e) {
        try {
            select2LoadData($('#clinic_type_id').data('url'), '#clinic_type_id');
            select2LoadData($('#province_id').data('url'), '#province_id');
            select2LoadData($('#district_id').data('url'), '#district_id');
            select2LoadData($('#ward_id').data('url'), '#ward_id');

            $('input[name="opening_time"], input[name="closing_time"]').change(function() {
                var openingTime = $('input[name="opening_time"]').val();
                var closingTime = $('input[name="closing_time"]').val();

                if (openingTime && closingTime) {
                    if (openingTime >= closingTime) {
                        alert('Giờ mở cửa phải nhỏ hơn giờ đóng cửa.');
                        $('input[name="closing_time"]').val('');
                    }
                }
            });

        } catch (error) {
            if (error.message.includes('setPosition')) {
                window.location.reload();
            } else {
                handleAjaxError(error);
            }
        }
    });
</script>
