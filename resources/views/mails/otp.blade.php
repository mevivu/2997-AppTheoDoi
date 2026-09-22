<!DOCTYPE html>
<html lang="vi" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Mã xác thực OTP - Chăm Con 360</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* CSS Reset & Base */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            border-collapse: collapse !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        p {
            margin: 0;
            padding: 0;
        }
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .content-padding {
                padding: 24px 18px !important;
            }
            .otp-code {
                font-size: 30px !important;
                letter-spacing: 6px !important;
            }
            .header-padding {
                padding: 28px 16px 24px 16px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    @php
        $appUrl = rtrim(config('app.url', 'https://kids360growth.com'), '/');
        $logoPath = public_path('assets/images/logo.png');
        $logoSrc = (isset($message) && file_exists($logoPath)) 
            ? $message->embed($logoPath) 
            : $appUrl . '/assets/images/logo.png';
    @endphp

    <!-- Preheader preview text for mobile lockscreen & email clients -->
    <div style="display: none; max-height: 0px; overflow: hidden; mso-hide: all; font-size: 1px; line-height: 1px; color: #ffffff; opacity: 0;">
        Mã xác thực OTP của bạn là {{ $otp }}. Mã có hiệu lực trong {{ $validity ?? 15 }} phút. Vui lòng không chia sẻ mã này.
        &#847;&zwnj;&nbsp;&#8199;&shy;&#847;&zwnj;&nbsp;&#8199;&shy;&#847;&zwnj;&nbsp;&#8199;&shy;&#847;&zwnj;&nbsp;&#8199;&shy;&#847;&zwnj;&nbsp;&#8199;&shy;
    </div>

    <!-- Outer Wrapper -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 10px 40px 10px;">
        <tr>
            <td align="center" valign="top">
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 560px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(31, 122, 128, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" class="header-padding" style="background: linear-gradient(135deg, #1F7A80 0%, #1695A3 100%); background-color: #1F7A80; padding: 36px 24px 30px 24px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <!-- Logo Badge -->
                                        <div style="background-color: #ffffff; padding: 10px; border-radius: 18px; display: inline-block; box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12); margin-bottom: 12px;">
                                            <img src="{{ $logoSrc }}" alt="Chăm Con 360" width="68" height="68" style="display: block; width: 68px; height: 68px; object-fit: contain;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                            CHĂM CON 360
                                        </h1>
                                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #d8f3e7; font-weight: 500;">
                                            Đồng hành phát triển toàn diện cho bé
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content-padding" style="padding: 36px 32px 28px 32px; background-color: #ffffff;">
                            
                            <!-- Greeting -->
                            <h2 style="margin: 0 0 14px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Xin chào Quý phụ huynh! 👋
                            </h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                                Cảm ơn bạn đã đăng ký và tin tưởng sử dụng nền tảng <strong>Chăm Con 360</strong>. Để hoàn tất xác nhận địa chỉ email của bạn, vui lòng nhập mã xác thực (OTP) dưới đây:
                            </p>

                            <!-- OTP Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 24px 0;">
                                <tr>
                                    <td align="center" style="background: linear-gradient(180deg, #F0FAF8 0%, #E6F5F3 100%); background-color: #F0FAF8; border: 2px dashed #1F7A80; border-radius: 16px; padding: 26px 20px 22px 20px;">
                                        <div style="font-size: 12px; font-weight: 800; color: #1F7A80; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                            MÃ XÁC THỰC CỦA BẠN
                                        </div>
                                        <div class="otp-code" style="font-size: 38px; font-weight: 900; letter-spacing: 10px; color: #1F7A80; font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, Consolas, 'Courier New', monospace; line-height: 1.2; text-indent: 10px;">
                                            {{ $otp }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-top: 8px;">
                                            (Mã gồm 6 chữ số • Chạm giữ để sao chép)
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiration Pill -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 0 20px 0;">
                                <tr>
                                    <td align="center" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px;">
                                        <p style="margin: 0; font-size: 13.5px; color: #475569; font-weight: 600;">
                                            ⏱ Mã OTP này có hiệu lực trong vòng <strong style="color: #0f172a;">{{ $validity ?? 15 }} phút</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Warning Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 0 26px 0;">
                                <tr>
                                    <td style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 10px 10px 0; padding: 14px 16px;">
                                        <p style="margin: 0; font-size: 13px; line-height: 1.55; color: #92400e;">
                                            <strong>🔒 Lưu ý bảo mật:</strong> Tuyệt đối không chia sẻ mã này cho bất kỳ ai (kể cả quản trị viên hoặc nhân viên hỗ trợ). Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email để đảm bảo an toàn cho tài khoản.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Sign Off -->
                            <p style="margin: 0; font-size: 14.5px; line-height: 1.6; color: #334155;">
                                Trân trọng,<br>
                                <strong style="color: #1F7A80; font-size: 15px;">Đội ngũ Chăm Con 360</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 26px 24px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #1F7A80;">
                                Ứng dụng Chăm Con 360
                            </p>
                            <p style="margin: 0 0 14px 0; font-size: 12px; color: #64748b; line-height: 1.5;">
                                Theo dõi thể chất WHO • Đánh giá đa trí tuệ IQ/EQ/AQ • Sổ tiêm chủng & Nhật ký dinh dưỡng
                            </p>
                            <div style="font-size: 12px; color: #94a3b8; line-height: 1.6;">
                                Website: <a href="{{ $appUrl }}" target="_blank" style="color: #1F7A80; text-decoration: none; font-weight: 600;">kids360growth.com</a>
                                <br>
                                © {{ date('Y') }} Chăm Con 360. Bảo lưu mọi quyền.
                                <br>
                                <span style="font-size: 11px; color: #cbd5e1;">(Email này được gửi tự động, vui lòng không trả lời trực tiếp)</span>
                            </div>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->
            </td>
        </tr>
    </table>
</body>
</html>
