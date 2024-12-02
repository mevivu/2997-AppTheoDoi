<script>
    $(document).ready(function () {
        function loadDistricts(provinceCode, selectedDistrictCode) {
            var $districtSelect = $('select[name="district"]');

            $districtSelect.prop('disabled', true);
            // $wardSelect.prop('disabled', true).html('<option value="">-- Chọn Phường/Xã --</option>');

            if (!provinceCode) {
                $districtSelect.html('<option value="">-- Chọn Quận/Huyện --</option>').prop('disabled', false);
                return;
            }
            $.ajax({
                url: urlHome + '/admin/address/districts',
                type: 'GET',
                dataType: 'json',
                data: {province_code: provinceCode},
            })
                .done(function (districtData) {
                    console.log(1)
                    var districtOptions = districtData.map(function (district) {
                        return '<option value="' + district.code + '"' + (district.code === selectedDistrictCode ? ' selected' : '') + '>' + district.name + '</option>';
                    }).join('');
                    $districtSelect.html(districtOptions).prop('disabled', false).trigger('change');
                })
                .fail(function () {
                    $districtSelect.html('<option value="">Lỗi khi tải quận huyện</option>').prop('disabled', false);
                });
        }

        function loadWards(districtCode) {
            var $wardSelect = $('select[name="ward"]');

            $wardSelect.prop('disabled', true);

            if (!districtCode || districtCode === '') {
                $wardSelect.html('<option value="">-- Chọn Phường/Xã --</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: urlHome + '/admin/address/wards',
                type: 'GET',
                dataType: 'json',
                data: {district_code: districtCode},
            })
                .done(function (wardData) {
                    var wardOptions = wardData.map(function (ward) {
                        return '<option value="' + ward.code + '">' + ward.name + '</option>';
                    }).join('');
                    $wardSelect.html(wardOptions).prop('disabled', false);
                })
                .fail(function () {
                    $wardSelect.html('<option value="">Lỗi khi tải phường/xã</option>').prop('disabled', false);
                });
        }


        $(document).on('change', 'select[name="province"]', function (event) {
            event.preventDefault();
            loadDistricts($(this).val());
        });

        $(document).on('change', 'select[name="district"]', function (event) {
            event.preventDefault();
            loadWards($(this).val());
        });


        $(document).ready(function () {
            const selectedProvinceCode = $('select[name="province"]').val();
            const selectedDistrictCode = $('select[name="district"]').data('district-code');
            const selectedWardCode = $('select[name="ward"]').data('ward-code');
            if (selectedProvinceCode) {
                loadDistricts(selectedProvinceCode, selectedDistrictCode);
            }
            if (selectedDistrictCode) {
                loadWards(selectedDistrictCode, selectedWardCode);
            }
        });
    });


</script>
