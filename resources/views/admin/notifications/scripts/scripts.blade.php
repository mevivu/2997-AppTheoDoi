<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');
        let selectedValue = null;

        $('.notification-type').change(function() {
            selectedValue = $(this).val();
            $('#notification-customer-select').hide();
            $('#notification-option-select').hide();

            if (selectedValue == {{ \App\Enums\Notification\NotificationType::Customer }}) {
                $('#notification-option-select').show();
            }
        });

        $('.notification-option-select-value').change(function() {
            const selectedOption = $(this).val();
            $('#notification-customer-select')
                .hide();

            if (selectedValue == {{ \App\Enums\Notification\NotificationType::Customer }}) {
                if (selectedOption == {{ \App\Enums\Notification\NotificationOption::One }}) {
                    $('#notification-customer-select').show();
                }
            }
        });
    });
</script>
