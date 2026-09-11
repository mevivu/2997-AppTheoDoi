<script>
    $(document).ready(function () {
        let withdrawCurrentBalance = 0;

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

        function syncQuickWithdrawAmountActiveState(currentAmount) {
            $('.quick-withdraw-amount-btn').each(function () {
                var btnAmount = $(this).data('amount');
                if (btnAmount === 'all') {
                    if (currentAmount > 0 && currentAmount === withdrawCurrentBalance) {
                        $(this).removeClass('btn-outline-danger').addClass('btn-danger text-white');
                    } else {
                        $(this).removeClass('btn-danger text-white').addClass('btn-outline-danger');
                    }
                } else {
                    var parsed = parseInt(btnAmount, 10);
                    if (parsed === currentAmount && currentAmount > 0) {
                        $(this).removeClass('btn-outline-secondary').addClass('btn-danger text-white border-danger');
                    } else {
                        $(this).removeClass('btn-danger text-white border-danger').addClass('btn-outline-secondary');
                    }
                }
            });
        }

        function syncQuickWithdrawReasonActiveState(currentReason) {
            $('.quick-withdraw-reason-chip').each(function () {
                var chipReason = $(this).data('reason');
                if (chipReason === currentReason && currentReason) {
                    $(this).removeClass('btn-outline-secondary').addClass('btn-primary text-white border-primary');
                } else {
                    $(this).removeClass('btn-primary text-white border-primary').addClass('btn-outline-secondary');
                }
            });
        }

        function updateWithdrawPreview() {
            var subAmount = parseMoney($('#withdrawAmountInput').val());
            var remaining = withdrawCurrentBalance - subAmount;
            
            $('#withdrawPreviewOldBalance').text(formatMoney(withdrawCurrentBalance) + ' đ');
            $('#withdrawPreviewSubtractAmount').text((subAmount > 0 ? '- ' : '') + formatMoney(subAmount) + ' đ');
            $('#withdrawPreviewNewBalance').text(formatMoney(remaining >= 0 ? remaining : 0) + ' đ');

            // Kiểm tra số dư vượt mức
            var isOverBalance = subAmount > withdrawCurrentBalance;
            if (isOverBalance) {
                $('#withdrawOverBalanceAlert').removeClass('d-none');
                $('#btnSubmitWithdraw').prop('disabled', true);
            } else {
                $('#withdrawOverBalanceAlert').addClass('d-none');
                $('#btnSubmitWithdraw').prop('disabled', false);
            }

            // Hiển thị hoặc ẩn nút xóa nhanh số tiền
            if (subAmount > 0) {
                $('#btnClearWithdrawAmount').removeClass('d-none');
            } else {
                $('#btnClearWithdrawAmount').addClass('d-none');
            }

            syncQuickWithdrawAmountActiveState(subAmount);
        }

        // Mở modal Rút tiền khi click nút
        $(document).on('click', '.open-modal-withdraw', function (e) {
            e.preventDefault();
            var btn = $(this);
            var userId = btn.data('id');
            var fullname = btn.data('fullname') || '---';
            var code = btn.data('code') || ('#' + userId);
            withdrawCurrentBalance = parseFloat(btn.data('balance') || 0);

            $('#withdrawUserId').val(userId);
            $('#withdrawUserFullname').text(fullname);
            $('#withdrawUserAvatarInitials').text(getUserInitials(fullname));
            $('#withdrawUserCode').text(code);
            $('#withdrawUserCurrentBalance').text(formatMoney(withdrawCurrentBalance) + ' đ');

            $('#withdrawAmountInput').val('');
            $('#withdrawAdminNote').val('');
            $('#withdrawSendNotification').prop('checked', true);
            $('#withdrawErrorAlert').addClass('d-none').text('');
            $('#withdrawOverBalanceAlert').addClass('d-none');

            // Reset trạng thái active
            $('.quick-withdraw-amount-btn').each(function () {
                if ($(this).data('amount') === 'all') {
                    $(this).removeClass('btn-danger text-white').addClass('btn-outline-danger');
                } else {
                    $(this).removeClass('btn-danger text-white border-danger').addClass('btn-outline-secondary');
                }
            });
            $('.quick-withdraw-reason-chip').removeClass('btn-primary text-white border-primary').addClass('btn-outline-secondary');
            $('#btnClearWithdrawAmount').addClass('d-none');

            // Reset nút submit về trạng thái ban đầu
            var submitBtn = $('#btnSubmitWithdraw');
            submitBtn.prop('disabled', false);
            submitBtn.find('.button-text').text('{{ __("Xác nhận rút tiền") }}');
            submitBtn.find('.spinner-border').addClass('d-none');

            updateWithdrawPreview();

            var modal = new bootstrap.Modal(document.getElementById('withdrawModal'));
            modal.show();
        });

        // Xử lý nút chọn nhanh số tiền (50k, 100k, 200k, ... hoặc "all")
        $(document).on('click', '.quick-withdraw-amount-btn', function () {
            var amount = $(this).data('amount');
            if (amount === 'all') {
                if (withdrawCurrentBalance <= 0) {
                    $('#withdrawAmountInput').val('');
                } else {
                    $('#withdrawAmountInput').val(formatMoney(withdrawCurrentBalance));
                }
            } else {
                $('#withdrawAmountInput').val(formatMoney(amount));
            }
            updateWithdrawPreview();
        });

        // Nút xóa nhanh số tiền đã nhập
        $(document).on('click', '#btnClearWithdrawAmount', function () {
            $('#withdrawAmountInput').val('');
            updateWithdrawPreview();
            $('#withdrawAmountInput').focus();
        });

        // Tự động định dạng số tiền khi gõ
        $(document).on('input', '#withdrawAmountInput', function () {
            var val = parseMoney($(this).val());
            if (val > 0) {
                $(this).val(formatMoney(val));
            } else {
                $(this).val('');
            }
            updateWithdrawPreview();
        });

        // Xử lý chọn nhanh lý do rút tiền
        $(document).on('click', '.quick-withdraw-reason-chip', function () {
            var reason = $(this).data('reason');
            $('#withdrawAdminNote').val(reason);
            syncQuickWithdrawReasonActiveState(reason);
        });

        // Cập nhật active chip khi người dùng tự gõ lý do
        $(document).on('input', '#withdrawAdminNote', function () {
            syncQuickWithdrawReasonActiveState($(this).val().trim());
        });

        // Xử lý sự kiện CLICK rút tiền
        $(document).on('click', '#btnSubmitWithdraw', function (e) {
            e.preventDefault();

            var form = $('#formWithdrawWallet');
            var amount = parseMoney($('#withdrawAmountInput').val());
            var note = $.trim($('#withdrawAdminNote').val());
            var errorAlert = $('#withdrawErrorAlert');
            var submitBtn = $(this);
            var btnText = submitBtn.find('.button-text');
            var spinner = submitBtn.find('.spinner-border');

            errorAlert.addClass('d-none').text('');

            function resetBtn() {
                submitBtn.prop('disabled', false);
                btnText.text('{{ __("Xác nhận rút tiền") }}');
                spinner.addClass('d-none');
            }

            if (amount < 1000) {
                resetBtn();
                errorAlert.removeClass('d-none').text('{{ __("Số tiền rút tối thiểu là 1.000đ.") }}');
                $('#withdrawAmountInput').focus();
                return;
            }

            if (amount > withdrawCurrentBalance) {
                resetBtn();
                errorAlert.removeClass('d-none').text('{{ __("Số tiền rút vượt quá số dư hiện tại của thành viên.") }}');
                $('#withdrawAmountInput').focus();
                return;
            }

            if (!note || note.length < 3) {
                resetBtn();
                errorAlert.removeClass('d-none').text('{{ __("Vui lòng nhập lý do rút tiền (tối thiểu 3 ký tự).") }}');
                $('#withdrawAdminNote').focus();
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
                    user_id: $('#withdrawUserId').val(),
                    amount: amount,
                    admin_note: note,
                    send_notification: $('#withdrawSendNotification').is(':checked') ? 1 : 0
                },
                dataType: 'json',
                success: function (res) {
                    resetBtn();

                    // Đóng modal
                    var modalEl = document.getElementById('withdrawModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }

                    // Thông báo thành công
                    var successMessage = res.message || '{{ __("Rút tiền từ ví thành công!") }}';
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

                    var errMsg = '{{ __("Có lỗi xảy ra khi rút tiền từ ví. Vui lòng thử lại.") }}';
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

        // Ngăn chặn submit form mặc định nếu user gõ enter
        $('#formWithdrawWallet').on('submit', function (e) {
            e.preventDefault();
            $('#btnSubmitWithdraw').trigger('click');
        });
    });
</script>
