<script>
$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content') 
                   || $('meta[name="X-TOKEN"]').attr('content') 
                   || '{{ csrf_token() }}';

    // Cập nhật giao diện thống kê quota thiết bị
    function updateDeviceQuotaUI(activeCount, maxAllowed) {
        $('#active-device-count').text(activeCount);
        $('#max-device-count').text(maxAllowed);

        const badge = $('#device-quota-badge');
        const statusText = $('#device-quota-status-text');
        const wrapRevokeAll = $('#wrap-revoke-all-btn');

        if (activeCount >= maxAllowed) {
            badge.removeClass('bg-success text-white').addClass('bg-danger text-white');
            statusText.html('<span class="text-danger fw-semibold">{{ __("Đã đạt giới hạn tối đa. Cần giải phóng thiết bị cũ trước khi liên kết máy mới.") }}</span>');
        } else {
            badge.removeClass('bg-danger text-white').addClass('bg-success text-white');
            const available = maxAllowed - activeCount;
            statusText.html('<span class="text-success fw-semibold">{{ __("Còn trống :count slot liên kết thiết bị.", ["count" => "__COUNT__"]) }}</span>'.replace('__COUNT__', available));
        }

        // Cập nhật badge trên Tab header
        $('#devices-info-tab .badge').text(activeCount + '/' + maxAllowed);

        // Ẩn/hiện nút giải phóng toàn bộ
        if (activeCount > 0) {
            wrapRevokeAll.show();
        } else {
            wrapRevokeAll.hide();
        }
    }

    // Sự kiện CLICK: Giải phóng 1 thiết bị đơn lẻ
    $(document).on('click', '.btn-revoke-device', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const url = btn.data('url');
        const deviceId = btn.data('device-id');
        const deviceName = btn.data('device-name') || 'Thiết bị này';

        Swal.fire({
            title: '{{ __("Giải phóng thiết bị?") }}',
            html: '{{ __("Bạn có chắc chắn muốn giải phóng thiết bị") }} <strong>' + deviceName + '</strong>?<br><small class="text-muted">{{ __("Thiết bị sẽ bị đăng xuất khỏi tài khoản ngay lập tức và slot liên kết sẽ được mở lại.") }}</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d63939',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-unlink me-1"></i> {{ __("Đồng ý giải phóng") }}',
            cancelButtonText: '{{ __("Hủy bỏ") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Hiển thị trạng thái loading trên nút
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>{{ __("Đang xử lý...") }}');

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    data: {
                        _token: csrfToken
                    },
                    success: function (res) {
                        if (res && res.success) {
                            if (typeof msgSuccess === 'function') {
                                msgSuccess(res.message || '{{ __("Đã giải phóng thiết bị thành công.") }}');
                            } else {
                                Swal.fire('{{ __("Thành công") }}', res.message, 'success');
                            }

                            // Cập nhật DOM dòng thiết bị vừa giải phóng
                            $('#device-avatar-' + deviceId)
                                .removeClass('bg-primary-lt text-primary')
                                .addClass('bg-secondary-lt text-secondary');

                            $('#device-status-col-' + deviceId).html(
                                '<span class="badge bg-secondary-lt px-2 py-1 badge-device-status"><i class="ti ti-ban me-1"></i>{{ __("Đã giải phóng") }}</span>'
                            );

                            $('#device-action-col-' + deviceId).html(
                                '<span class="text-muted fs-12 fst-italic">{{ __("Không khả dụng") }}</span>'
                            );

                            // Cập nhật số lượng thiết bị
                            const activeCount = res.data && typeof res.data.active_count !== 'undefined' ? res.data.active_count : 0;
                            const maxAllowed = res.data && typeof res.data.max_allowed !== 'undefined' ? res.data.max_allowed : 1;
                            updateDeviceQuotaUI(activeCount, maxAllowed);
                        } else {
                            const errorMsg = (res && res.message) ? res.message : '{{ __("Thực hiện thất bại.") }}';
                            if (typeof msgError === 'function') {
                                msgError(errorMsg);
                            } else {
                                Swal.fire('{{ __("Lỗi") }}', errorMsg, 'error');
                            }
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function (xhr) {
                        const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                                       ? xhr.responseJSON.message 
                                       : '{{ __("Có lỗi xảy ra khi kết nối máy chủ. Vui lòng thử lại.") }}';
                        if (typeof msgError === 'function') {
                            msgError(errorMsg);
                        } else {
                            Swal.fire('{{ __("Lỗi") }}', errorMsg, 'error');
                        }
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });

    // Sự kiện CLICK: Giải phóng TOÀN BỘ thiết bị
    $(document).on('click', '.btn-revoke-all-devices', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const url = btn.data('url');
        const userName = btn.data('user-name') || 'khách hàng';

        Swal.fire({
            title: '{{ __("Giải phóng TOÀN BỘ thiết bị?") }}',
            html: '{{ __("Bạn có chắc chắn muốn giải phóng toàn bộ thiết bị của") }} <strong>' + userName + '</strong>?<br><small class="text-danger fw-semibold">{{ __("Người dùng sẽ bị đăng xuất trên mọi thiết bị và phải đăng nhập lại từ đầu.") }}</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d63939',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-device-mobile-off me-1"></i> {{ __("Đồng ý giải phóng tất cả") }}',
            cancelButtonText: '{{ __("Hủy bỏ") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>{{ __("Đang xử lý...") }}');

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    data: {
                        _token: csrfToken
                    },
                    success: function (res) {
                        if (res && res.success) {
                            if (typeof msgSuccess === 'function') {
                                msgSuccess(res.message || '{{ __("Đã giải phóng toàn bộ thiết bị thành công.") }}');
                            } else {
                                Swal.fire('{{ __("Thành công") }}', res.message, 'success');
                            }

                            // Đổi trạng thái toàn bộ các dòng trong bảng sang "Đã giải phóng"
                            $('#table-user-devices tbody tr').each(function () {
                                const row = $(this);
                                row.find('.avatar').removeClass('bg-primary-lt text-primary').addClass('bg-secondary-lt text-secondary');
                                row.find('.badge-device-status').parent().html(
                                    '<span class="badge bg-secondary-lt px-2 py-1 badge-device-status"><i class="ti ti-ban me-1"></i>{{ __("Đã giải phóng") }}</span>'
                                );
                                row.find('.btn-revoke-device').parent().html(
                                    '<span class="text-muted fs-12 fst-italic">{{ __("Không khả dụng") }}</span>'
                                );
                            });

                            // Cập nhật quota về 0
                            const maxAllowed = res.data && typeof res.data.max_allowed !== 'undefined' ? res.data.max_allowed : 1;
                            updateDeviceQuotaUI(0, maxAllowed);
                        } else {
                            const errorMsg = (res && res.message) ? res.message : '{{ __("Thực hiện thất bại.") }}';
                            if (typeof msgError === 'function') {
                                msgError(errorMsg);
                            } else {
                                Swal.fire('{{ __("Lỗi") }}', errorMsg, 'error');
                            }
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function (xhr) {
                        const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                                       ? xhr.responseJSON.message 
                                       : '{{ __("Có lỗi xảy ra khi kết nối máy chủ. Vui lòng thử lại.") }}';
                        if (typeof msgError === 'function') {
                            msgError(errorMsg);
                        } else {
                            Swal.fire('{{ __("Lỗi") }}', errorMsg, 'error');
                        }
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });

    // Tự động khôi phục tab đang chọn từ URL Hash (Ví dụ: #devicesInfo)
    const hash = window.location.hash;
    if (hash) {
        const targetTabBtn = $('button[data-bs-target="' + hash + '"]');
        if (targetTabBtn.length) {
            targetTabBtn.tab('show');
        }
    }

    // Ghi nhận hash khi chuyển tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');
        if (target && history.replaceState) {
            history.replaceState(null, null, target);
        }
    });
});
</script>
