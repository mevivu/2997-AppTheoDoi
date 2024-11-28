<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 10px;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #1d273b;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }

        .email-body {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }

        .email-footer {
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        .email-footer p {
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{ $title }}
        </div>
        <div class="email-body">
            <p>{{ $body }}</p>
        </div>
        <div class="email-footer">
            <a href="{{ $url }}" class="button">Chi tiết >></a>
            <p>Click để xem chi tiết thông báo</p>
        </div>
    </div>
</body>
</html>
