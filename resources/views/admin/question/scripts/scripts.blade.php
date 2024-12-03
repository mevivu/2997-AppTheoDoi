<script>
    $(document).ready(function() {
        $('#question_type').change(function() {
            let value = $(this).val();
            if (value == 'iq') {
                $('#question_iq_group').removeClass('d-none');
            } else {
                $('#question_iq_group').addClass('d-none');
            }
        });


        $('#add_wrong_answer').click(function() {
            let html = `
                <div class="d-flex align-items-center justify-content-between position-relative mb-3">
                    <x-input type="text" name="wrong_answers[]" :placeholder="'Nhập câu trả lời sai'" :required="true" />
                    <button type="button" class="btn btn-sm btn-danger position-absolute end-0 me-2" id="remove_wrong_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                </div>
            `;
            $('#wrong_answers').append(html);
        });

        $(document).on('click', '#remove_wrong_answer', function() {
            $(this).parent().remove();
        });
    })
</script>
