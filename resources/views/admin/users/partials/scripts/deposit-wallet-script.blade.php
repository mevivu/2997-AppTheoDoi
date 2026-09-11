<script>
    $(document).ready(function () {
        let depositCurrentBalance = 0;

        function formatMoney(n) {
            return new Intl.NumberFormat('vi-VN').format(n);
        }

        function parseMoney(val) {
            if (!val) return 0;
            var clean = ('' + val).replace(/[^\d]/g, '');
            return clean ? parseInt(clean, 10) : 0;
        }

        function updateDepositPreview() {
            var addAmount = parseMoney($('#depositAmountInput').val());
            var newTotal = depositCurrentBalance + addAmount;
            $('#depositPreviewNewBalance').text(formatMoney(newTotal) + ' đ');
        }

        // Mở modal Nạp tiền khi click nút ví
        $(document).on('click', '.open-modal-deposit', function (e) {
            e.preventDefault();
            var btn = $(this);
            var userId = btn.data('id');
            var fullname = btn.data('fullname') || '---';
            var code = btn.data('code') || ('#' + userId);
            depositCurrentBalance = parseFloat(btn.data('balance') || 0);

            $('#depositUserId').val(userId);
            $('#depositUserFullname').text(fullname);
            $('#depositUserCode').text(code);
            $('#depositUserCurrentBalance').text(formatMoney(depositCurrentBalance) + ' đ');

            $('#depositAmountInput').val('');
            $('#depositAdminNote').val('');
            $('#depositSendNotification').prop('checked', true);
            $('#depositErrorAlert').addClass('d-none').text('');

            updateDepositPreview();

            var modal = new bootstrap.Modal(document.getElementById('depositModal'));
            modal.show();
        });

        // Xử lý nút chọn nhanh số tiền (+50k, +100k, ...)
        $(document).on('click', '.quick-amount-btn', function () {
            var amount = $(this).data('amount');
            $('#depositAmountInput').val(formatMoney(amount));
            updateDepositPreview();
        });

        // Tự động định dạng số tiền khi nhập
        $(document).on('input', '#depositAmountInput', function () {
            var val = parseMoney($(this).val());
            if (val > 0) {
                $(this).val(formatMoney(val));
            } else {
                $(this).val('');
            }
            updateDepositPreview();
        });

        // Xử lý chọn nhanh lý do nạp tiền
        $(document).on('click', '.quick-reason-chip', function () {
            var reason = $(this).data('reason');
            $('#depositAdminNote').val(reason);
        });

        // Submit form nạp tiền bằng AJAX
        $('#formDepositWallet').on('submit', function (e) {
            e.preventDefault();

            var form = $(this);
            var amount = parseMoney($('#depositAmountInput').val());
            var note = $.trim($('#depositAdminNote').val());
            var errorAlert = $('#depositErrorAlert');
            var submitBtn = $('#btnSubmitDeposit');
            var btnText = submitBtn.find('.button-text');
            var spinner = submitBtn.find('.spinner-border');

            errorAlert.addClass('d-none').text('');

            if (amount < 1000) {
                errorAlert.removeClass('d-none').text('{{ __("Số tiền nạp tối thiểu là 1.000đ.") }}');
                $('#depositAmountInput').focus();
                return;
            }

            if (!note || note.length < 3) {
                errorAlert.removeClass('d-none').text('{{ __("Vui lòng nhập lý do nạp tiền (tối thiểu 3 ký tự).") }}');
                $('#depositAdminNote').focus();
                return;
            }

            // Hiển thị trạng thái đang xử lý
            submitBtn.prop('disabled', true);
            btnText.text('{{ __("Đang xử lý...") }}');
            spinner.removeClass('d-none');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: $('#depositUserId').val(),
                    amount: amount,
                    admin_note: note,
                    send_notification: $('#depositSendNotification').is(':checked') ? 1 : 0
                },
                dataType: 'json',
                success: function (res) {
                    submitBtn.prop('disabled', false);
                    btnText.text('{{ __("Xác nhận nạp tiền") }}');
                    spinner.addClass('d-none');

                    // Đóng modal
                    var modalEl = document.getElementById('depositModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }

                    // Thông báo thành công
                    var successMessage = res.message || '{{ __("Nạp tiền vào ví thành công!") }}';
                    if (typeof msgSuccess === 'function') {
                        msgSuccess(successMessage);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Thành công!") }}',
                            text: successMessage,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        alert(successMessage);
                    }

                    // Tự động cập nhật DataTable nếu đang ở trang danh sách
                    if (window.LaravelDataTables && window.LaravelDataTables['userTable']) {
                        window.LaravelDataTables['userTable'].ajax.reload(null, false);
                    }

                    // Tự động cập nhật hiển thị số dư nếu đang ở trang sửa hồ sơ
                    if ($('.user-wallet-balance-display').length && res.data && res.data.new_balance_formatted) {
                        $('.user-wallet-balance-display').text(res.data.new_balance_formatted);
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false);
                    btnText.text('{{ __("Xác nhận nạp tiền") }}');
                    spinner.addClass('d-none');

                    var errMsg = '{{ __("Có lỗi xảy ra khi nạp tiền vào ví. Vui lòng thử lại.") }}';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var firstErr = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(firstErr)) {
                                errMsg = firstErr[0];
                            }
                        }
                    }
                    errorAlert.removeClass('d-none').text(errMsg);
                }
            });
        });
    });
</script>
