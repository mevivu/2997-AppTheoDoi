<script>
    $(document).ready(function () {
        $('#bike_C_Delivery_base_distance_input').on('input', function () {
            const distanceValue = $(this).val();
            $('#bike_C_Delivery_base_distance_text').text(distanceValue || '{{ $bikeC_Delivery_Settings['bike_C_Delivery_base_distance'] }}');  // Cập nhật giá trị hiển thị
        });
        $('#bike_C_Delivery_distance_to_discount_input').on('input', function () {
            const discountDistanceValue = $(this).val();
            $('#bike_C_Delivery_distance_to_discount_text').text(discountDistanceValue || '{{ $bikeC_Delivery_Settings['bike_C_Delivery_distance_to_discount'] }}');
        });
       
    });
</script>
