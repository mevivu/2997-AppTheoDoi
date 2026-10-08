<?php

/*
|--------------------------------------------------------------------------
| App download / invite landing configuration
|--------------------------------------------------------------------------
| Dùng cho trang trung gian /invite (link giới thiệu mở từ Zalo, Facebook...).
| iOS không chạy Universal Link trong trình duyệt nhúng của các app này, nên
| OneLink chuyển người dùng iOS về trang /invite (cấu hình af_ios_url).
*/

return [
    'app_store_url' => env('APP_STORE_URL', 'https://apps.apple.com/vn/app/ch%C4%83m-con-360/id6753282605'),
    'play_store_url' => env('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.mevivu.theodoi'),
    'onelink_url' => env('APPSFLYER_ONELINK_URL', 'https://chamcon360.onelink.me/oBCi'),
    'app_scheme' => env('APP_URL_SCHEME', 'chamcon360'),
];
