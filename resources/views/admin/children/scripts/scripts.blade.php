<script>
    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');

        function toggleDateInput() {
            const isBorn = $('#is_born').val();
            console.log("Initial isBorn value:", isBorn);  // Check initial value
            const dateInput = $('#date_input input[type="date"]');
            const dateLabel = $('#date_input label');

            // Check and set the appropriate input details based on isBorn
            if (isBorn === 'born') {
                dateLabel.text('{{ __("Ngày sinh") }}:');
                dateInput.attr('name', 'birthday').attr('placeholder', '{{ __("Ngày sinh") }}');
                dateInput.val('{{ old("birthday", $birthday ?? "") }}');  // Handle null case
            } else {
                dateLabel.text('{{ __("Ngày dự sinh") }}:');
                dateInput.attr('name', 'due_date').attr('placeholder', '{{ __("Ngày dự sinh") }}');
                dateInput.val('{{ old("due_date", $dueDate ?? "") }}');
            }
        }

        // Initial call to set the field correctly on page load
        toggleDateInput();

        // Update fields on dropdown change
        $('#is_born').change(function() {
            toggleDateInput();
        });
    });
</script>
