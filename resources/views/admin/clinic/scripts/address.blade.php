<script>
    $(document).ready(function(e) {
        select2LoadData($('#province_id').data('url'), '#province_id');

        let provinceId = $('#province_id').val();
        let urlWard = "{{ route('admin.search.select.ward') }}";
        select2LoadData(urlWard + (provinceId ? '?province_id=' + provinceId : ''), '#ward_id');
    });

    $(document).on('change', 'select[name="province_id"]', function(e) {
        let provinceId = $(this).val();
        let url = "{{ route('admin.search.select.ward') }}";
        select2LoadData(url + (provinceId ? '?province_id=' + provinceId : ''), '#ward_id');
        $('#ward_id').val(null).trigger('change');
    });
</script>
