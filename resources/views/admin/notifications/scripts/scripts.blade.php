<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');

        function toggleRecipientOptions(selectedOption) {
            if (selectedOption == {{ \App\Enums\Notification\NotificationOption::One->value }}) {
                $('#notification-customer-select').show();
                $('#notification-excel-file-wrapper').hide();
            } else if (selectedOption == {{ \App\Enums\Notification\NotificationOption::Excel->value }}) {
                $('#notification-customer-select').hide();
                $('#notification-excel-file-wrapper').show();
            } else {
                $('#notification-customer-select').hide();
                $('#notification-excel-file-wrapper').hide();
            }
        }

        // Khi chọn loại thông báo (option: Tất cả / Một người / Excel)
        $('.notification-option-select-value').change(function() {
            toggleRecipientOptions($(this).val());
        });

        // Trigger on load (e.g. if old value exists)
        toggleRecipientOptions($('.notification-option-select-value').val());
    });
</script>
