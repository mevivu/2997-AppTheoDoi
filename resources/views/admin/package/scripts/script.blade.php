<script>
    $(document).ready(function() {
        // Description dynamic repeater
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

        // ==========================================
        // Discount & Live Price Preview Logic
        // ==========================================
        const $discountType = $('#package_discount_type');
        const $valueWrapper = $('#discount_value_wrapper');
        const $percentGroup = $('#discount_percent_group');
        const $fixedGroup = $('#discount_fixed_group');
        const $percentInput = $('#package_discount_percent_input');
        const $fixedDisplay = $('#package_discount_fixed_display');
        const $valueHidden = $('#package_discount_value_hidden');

        const $previewCard = $('#price_preview_card');
        const $previewOriginal = $('#preview_original_price');
        const $previewBadgeWrap = $('#preview_discount_badge_wrap');
        const $previewDiscountText = $('#preview_discount_text');
        const $previewFinal = $('#preview_final_price');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
        }

        function getOriginalPrice() {
            const hiddenVal = $('#price-hidden').val();
            if (hiddenVal && !isNaN(hiddenVal)) {
                return parseFloat(hiddenVal);
            }
            const displayVal = $('input[data-format="price"]').val() || $('input[name="price"]').val() || '0';
            const cleanVal = displayVal.toString().replace(/[^0-9]/g, '');
            return parseFloat(cleanVal) || 0;
        }

        function updateDiscountUI() {
            const type = $discountType.val();

            if (type === 'percent') {
                $valueWrapper.show();
                $percentGroup.show();
                $fixedGroup.hide();
                $valueHidden.val($percentInput.val() || 0);
            } else if (type === 'fixed') {
                $valueWrapper.show();
                $percentGroup.hide();
                $fixedGroup.show();
                $valueHidden.val($valueHidden.val() || 0);
            } else {
                // none
                $valueWrapper.hide();
                $percentGroup.hide();
                $fixedGroup.hide();
                $valueHidden.val(0);
            }

            calculatePrice();
        }

        function calculatePrice() {
            const type = $discountType.val();
            const originalPrice = getOriginalPrice();
            let discountValue = parseFloat($valueHidden.val()) || 0;
            let finalPrice = originalPrice;
            let discountAmount = 0;

            if (type === 'percent' && discountValue > 0) {
                if (discountValue > 100) {
                    discountValue = 100;
                    $percentInput.val(100);
                    $valueHidden.val(100);
                }
                discountAmount = originalPrice * (discountValue / 100);
                finalPrice = Math.max(0, originalPrice - discountAmount);

                $previewBadgeWrap.show();
                $previewDiscountText.text('-' + Math.round(discountValue) + '%');
            } else if (type === 'fixed' && discountValue > 0) {
                discountAmount = discountValue;
                finalPrice = Math.max(0, originalPrice - discountAmount);

                $previewBadgeWrap.show();
                $previewDiscountText.text('-' + formatCurrency(discountAmount));
            } else {
                $previewBadgeWrap.hide();
            }

            $previewOriginal.text(formatCurrency(originalPrice));
            $previewFinal.text(formatCurrency(finalPrice));
        }

        // Event listeners
        $discountType.on('change', updateDiscountUI);

        $percentInput.on('input change', function() {
            let val = parseFloat($(this).val()) || 0;
            if (val > 100) val = 100;
            if (val < 0) val = 0;
            $valueHidden.val(val);
            calculatePrice();
        });

        $fixedDisplay.on('input change', function() {
            const rawVal = $(this).val().replace(/[^0-9]/g, '');
            const num = parseFloat(rawVal) || 0;
            $valueHidden.val(num);
            if (rawVal) {
                $(this).val(new Intl.NumberFormat('vi-VN').format(num));
            } else {
                $(this).val('');
            }
            calculatePrice();
        });

        // Listen on price input change
        $(document).on('input change', 'input[data-format="price"], input[name="price"], #price-hidden', function() {
            setTimeout(calculatePrice, 50);
        });

        // Initialize display value for fixed discount if already set
        const initVal = parseFloat($valueHidden.val()) || 0;
        if (initVal > 0) {
            $fixedDisplay.val(new Intl.NumberFormat('vi-VN').format(initVal));
        }

        // Trigger on load
        updateDiscountUI();
    });
</script>
