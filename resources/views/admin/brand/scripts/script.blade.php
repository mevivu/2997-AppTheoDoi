<script>
    $(document).ready(function() {
        $('#add-description').on('click', function() {
            const newDescription = `
            <div class="d-flex align-items-strech gap-1">
                <textarea name="description[]" class="form-control" rows="2" placeholder="{{ __('description') }}"></textarea>
                <button type="button" class="btn btn-danger remove-description">
                    <i class="ti ti-x fs-1"></i>
                </button>
            </div>`;

            $('#description-container').append(newDescription);
        });

        $('#description-container').on('click', '.remove-description', function() {
            $(this).parent().remove();
        });
    });
</script>
