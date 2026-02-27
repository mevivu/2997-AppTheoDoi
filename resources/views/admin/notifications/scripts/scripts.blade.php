<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');

        // Khi chọn loại thông báo (option: Tất cả / Một người)
        $('.notification-option-select-value').change(function() {
            const selectedOption = $(this).val();
            if (selectedOption == {{ \App\Enums\Notification\NotificationOption::One }}) {
                $('#notification-customer-select').show();
            } else {
                $('#notification-customer-select').hide();
            }
        });
    });
</script>
