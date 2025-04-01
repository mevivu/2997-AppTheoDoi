<script>
    $(document).ready(function() {
        $(document).on('click', '.copy-btn', function() {
            $('.check-icon').hide();
            $('.copy-btn').show();

            const copyText = $(this).data('value');
            navigator.clipboard.writeText(copyText).then(() => {
                const $btn = $(this);
                $btn.hide();
                const $checkIcon = $btn.siblings('.check-icon');
                $checkIcon.show();
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
    });
</script>
