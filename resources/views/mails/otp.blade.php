<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận OTP</title>
</head>
<body>
<div class="container">
    <h2>Xin chào!</h2>
    <p>Cảm ơn bạn đã đăng ký. Dưới đây là mã OTP của bạn để xác nhận địa chỉ email:</p>
    <p class="otp-code">{{ $otp }}</p>
    <p>Mã OTP này sẽ hết hạn sau 1 phút.</p>
    <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
    <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
</div>
</body>
</html>
