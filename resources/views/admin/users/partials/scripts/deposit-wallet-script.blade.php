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

        function getUserInitials(name) {
            if (!name || name === '---') return 'U';
            var parts = name.trim().split(/\s+/);
            if (parts.length === 1) {
                return parts[0].substring(0, 2).toUpperCase();
            }
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        function syncQuickAmountActiveState(currentAmount) {
            $('.quick-amount-btn').each(function () {
                var btnAmount = parseInt($(this).data('amount'), 10);
                if (btnAmount === currentAmount && currentAmount > 0) {
                    $(this).removeClass('btn-outline-secondary').addClass('btn-success text-white border-success');
                } else {
                    $(this).removeClass('btn-success text-white border-success').addClass('btn-outline-secondary');
                }
            });
        }

        function syncQuickReasonActiveState(currentReason) {
            $('.quick-reason-chip').each(function () {
                var chipReason = $(this).data('reason');
                if (chipReason === currentReason && currentReason) {
                    $(this).removeClass('btn-outline-secondary').addClass('btn-primary text-white border-primary');
                } else {
                    $(this).removeClass('btn-primary text-white border-primary').addClass('btn-outline-secondary');
                }
            });
        }

        function updateDepositPreview() {
            var addAmount = parseMoney($('#depositAmountInput').val());
            var newTotal = depositCurrentBalance + addAmount;
            
            $('#previewOldBalance').text(formatMoney(depositCurrentBalance) + ' đ');
            $('#previewAddAmount').text((addAmount > 0 ? '+ ' : '') + formatMoney(addAmount) + ' đ');
            $('#depositPreviewNewBalance').text(formatMoney(newTotal) + ' đ');

            // Hiển thị hoặc ẩn nút xóa nhanh số tiền
            if (addAmount > 0) {
                $('#btnClearDepositAmount').removeClass('d-none');
            } else {
                $('#btnClearDepositAmount').addClass('d-none');
            }

            syncQuickAmountActiveState(addAmount);
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
            $('#depositUserAvatarInitials').text(getUserInitials(fullname));
            $('#depositUserCode').text(code);
            $('#depositUserCurrentBalance').text(formatMoney(depositCurrentBalance) + ' đ');

            $('#depositAmountInput').val('');
            $('#depositAdminNote').val('');
            $('#depositSendNotification').prop('checked', true);
            $('#depositErrorAlert').addClass('d-none').text('');

            // Reset trạng thái active
            $('.quick-amount-btn').removeClass('btn-success text-white border-success').addClass('btn-outline-secondary');
            $('.quick-reason-chip').removeClass('btn-primary text-white border-primary').addClass('btn-outline-secondary');
            $('#btnClearDepositAmount').addClass('d-none');

            // Reset nút submit về trạng thái ban đầu
            var submitBtn = $('#btnSubmitDeposit');
            submitBtn.prop('disabled', false);
            submitBtn.find('.button-text').text('{{ __("Xác nhận nạp tiền") }}');
            submitBtn.find('.spinner-border').addClass('d-none');

            updateDepositPreview();

            var modal = new bootstrap.Modal(document.getElementById('depositModal'));
            modal.show();
        });

        // Xử lý nút chọn nhanh số tiền (50k, 100k, 200k, ...)
        $(document).on('click', '.quick-amount-btn', function () {
            var amount = $(this).data('amount');
            $('#depositAmountInput').val(formatMoney(amount));
            updateDepositPreview();
        });

        // Nút xóa nhanh số tiền đã nhập
        $(document).on('click', '#btnClearDepositAmount', function () {
            $('#depositAmountInput').val('');
            updateDepositPreview();
            $('#depositAmountInput').focus();
        });

        // Tự động định dạng số tiền khi gõ
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
            syncQuickReasonActiveState(reason);
        });

        // Cập nhật active chip khi người dùng tự gõ lý do
        $(document).on('input', '#depositAdminNote', function () {
            syncQuickReasonActiveState($(this).val().trim());
        });

        // Xử lý sự kiện CLICK nạp tiền
        $(document).on('click', '#btnSubmitDeposit', function (e) {
            e.preventDefault();

            var form = $('#formDepositWallet');
            var amount = parseMoney($('#depositAmountInput').val());
            var note = $.trim($('#depositAdminNote').val());
            var errorAlert = $('#depositErrorAlert');
            var submitBtn = $(this);
            var btnText = submitBtn.find('.button-text');
            var spinner = submitBtn.find('.spinner-border');

            errorAlert.addClass('d-none').text('');

            function resetBtn() {
                submitBtn.prop('disabled', false);
                btnText.text('{{ __("Xác nhận nạp tiền") }}');
                spinner.addClass('d-none');
            }

            if (amount < 1000) {
                resetBtn();
                errorAlert.removeClass('d-none').text('{{ __("Số tiền nạp tối thiểu là 1.000đ.") }}');
                $('#depositAmountInput').focus();
                return;
            }

            if (!note || note.length < 3) {
                resetBtn();
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
                    resetBtn();

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
                    resetBtn();

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

        // Ngăn chặn submit form thông thường nếu user gõ enter
        $('#formDepositWallet').on('submit', function (e) {
            e.preventDefault();
            $('#btnSubmitDeposit').trigger('click');
        });
    });
</script>
