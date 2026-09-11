<?php

return [
    'reminder_confirmation' => [
        'title' => 'Lịch nhắc hẹn',
        'message' => '#MESSAGE#'
    ],

    'package_purchase_pending' => [
        'title' => 'Gói Dịch Vụ Chờ Phê Duyệt',
        'message' => 'Tài khoản {fullname} của bạn đã mua gói dịch vụ và đang chờ phê duyệt. Chúng tôi sẽ thông báo cho bạn ngay khi quá trình phê duyệt hoàn tất. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!'
    ],

    'admin_approval_required' => [
        'title' => 'Yêu cầu phê duyệt gói dịch vụ mới',
        'message' => 'Gói dịch vụ được mua bởi {fullname} ({email}) cần được phê duyệt. Vui lòng xem xét và phê duyệt gói dịch vụ để hoàn tất quá trình mua hàng.'
    ],


    'package_approved_and_paid' => [
        'title' => 'Xác nhận và Thanh toán Thành Công',
        'message' => 'Yêu cầu phê duyệt gói của bạn đã được xác nhận và thanh toán thành công. Bạn có thể bắt đầu sử dụng các dịch vụ của chúng tôi ngay bây giờ. Cảm ơn bạn đã lựa chọn chúng tôi!'
    ],
    'payment_success' => [
        'title' => 'Thanh Toán Thành Công - Vui Lòng Đăng Nhập Lại',
        'message' => 'Chào {fullname}! Giao dịch mua gói "{package_name}" của bạn đã được xử lý thành công. Để kích hoạt gói dịch vụ mới, vui lòng đăng xuất và đăng nhập lại ứng dụng. Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi!'
    ],
    'payment_refunded' => [
        'title' => 'Hoàn Tiền Thành Công',
        'message' => 'Xin chào {fullname}! Khoản thanh toán cho gói "{package_name}" của bạn đã được hoàn lại thành công. Số tiền sẽ sớm được ghi có về phương thức thanh toán ban đầu. Cảm ơn bạn đã đồng hành cùng chúng tôi!',
    ],
    'user_locked' => [
        'title' => 'Tài khoản đã bị khóa',
        'message' => 'Tài khoản của bạn đã bị khóa do không hoạt động hoặc vi phạm điều khoản.'
    ],
    'login_another_device' => [
        'title' => 'Tài khoản của bạn hiện đang được đăng nhập trên một thiết bị khác.',
        'message' => "Nếu bạn muốn sử dụng tài khoản đồng thời trên nhiều thiết bị để cùng vợ hoặc chồng chia sẻ hành trình chăm sóc con với ứng dụng CHĂM CON 360, hãy nâng cấp lên \"Gói VIP 1 năm\".\n\nĐể đăng ký, vui lòng vào Tài khoản → Gói thành viên → Gói VIP 1 năm."
    ],
    'affiliate_reward_referrer' => [
        'title' => 'Bạn nhận được {amount} hoa hồng giới thiệu!',
        'message' => 'Chúc mừng bạn! {new_user_name} vừa đăng ký tài khoản thành công qua mã giới thiệu của bạn. Số tiền {amount} đã được cộng vào ví.',
    ],
    'affiliate_package_commission' => [
        'title' => '💰 Bạn nhận được {amount} hoa hồng mua gói!',
        'message' => 'Chúc mừng bạn! Thành viên {f1_name} vừa thanh toán thành công gói "{package_name}". Bạn được cộng {amount} ({percent}%) vào ví hoa hồng.',
    ],
    'affiliate_rank_upgrade' => [
        'title' => '🎉 Chúc mừng bạn đã thăng cấp {rank_name}!',
        'message' => 'Xin chúc mừng {fullname}! Với tổng doanh số giới thiệu tích lũy đạt {total_sales}, bạn đã chính thức đạt danh hiệu {rank_name} của CHĂM CON 360 với nhiều quyền lợi ưu đãi hấp dẫn.',
    ],
    'affiliate_new_referral' => [
        'title' => 'Bạn có thành viên giới thiệu mới!',
        'message' => 'Chúc mừng bạn! {new_user_name} vừa tạo tài khoản thành công qua mã giới thiệu của bạn.',
    ],
    'welcome_user' => [
        'title' => 'Chào mừng bạn đến với CHĂM CON 360!',
        'message' => "Xin chào {fullname}!\n\nChúc mừng bạn đã gia nhập đại gia đình CHĂM CON 360 – người bạn đồng hành tin cậy trên hành trình nuôi dưỡng và chăm sóc bé yêu phát triển toàn diện.\n\nTại đây, bạn có thể dễ dàng theo dõi các chỉ số tăng trưởng, lịch tiêm chủng, cẩm nang sức khỏe và nhận nhiều ưu đãi hấp dẫn. Chúc bạn và bé yêu luôn có những trải nghiệm thật tuyệt vời cùng CHĂM CON 360!",
    ],
    'affiliate_withdraw_approved' => [
        'title' => 'Chi trả hoa hồng thành công',
        'message' => 'Lệnh rút tiền {code} ({amount}) của bạn đã được chuyển khoản thành công vào tài khoản {bank_name} ({bank_account_number}).{note}',
    ],
    'affiliate_withdraw_rejected' => [
        'title' => 'Yêu cầu rút tiền bị từ chối',
        'message' => 'Lệnh rút tiền {code} ({amount}) đã bị từ chối. Lý do: {reason}. Số tiền {amount} đã được tự động hoàn lại vào ví hoa hồng của bạn.',
    ],
    'affiliate_withdraw_requested_admin' => [
        'title' => 'Yêu cầu rút tiền hoa hồng mới',
        'message' => 'Đối tác {fullname} vừa tạo yêu cầu rút tiền hoa hồng {amount} về {bank_name} (STK: {bank_account_number}). Mã GD: {code}.',
    ],
    'admin_deposit_wallet' => [
        'title' => 'Biến động số dư ví',
        'message' => 'Ví của bạn vừa được cộng +{amount} từ Ban Quản Trị. Lý do: {reason}. Số dư hiện tại: {balance}.',
    ],
    'admin_withdraw_wallet' => [
        'title' => 'Biến động số dư ví',
        'message' => 'Ví của bạn vừa bị trừ -{amount} từ Ban Quản Trị. Lý do: {reason}. Số dư hiện tại: {balance}.',
    ],
    'admin_kyc_submitted' => [
        'title' => 'Yêu cầu xác minh CCCD & MST mới',
        'message' => 'Đối tác {fullname} vừa gửi hồ sơ xác minh CCCD và Mã số thuế. Vui lòng kiểm tra và phê duyệt.',
    ],
];
