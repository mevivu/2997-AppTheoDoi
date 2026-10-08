<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Trang trung gian cho link giới thiệu (/invite).
 *
 * Vì sao cần: iOS không kích hoạt Universal Link trong trình duyệt nhúng của
 * Zalo / Facebook / Messenger..., nên OneLink coi như "chưa cài app" và đẩy
 * người dùng sang App Store dù app đã cài. OneLink template được cấu hình
 * af_ios_url = https://kids360growth.com/invite để chuyển về trang này, nơi
 * người dùng có nút mở app bằng URI scheme + sao chép mã giới thiệu.
 */
class InviteController extends Controller
{
    private const CODE_PATTERN = '/^[A-Z0-9_-]{3,20}$/';

    private const CODE_KEYS = ['deep_link_sub1', 'referral_code', 'referralCode', 'code', 'ref'];

    private const IN_APP_PATTERN = '/Zalo|FBAN|FBAV|FB_IAB|FBIOS|Messenger|Instagram|Line\/|musical_ly|BytedanceWebview|TikTok/i';

    public function show(Request $request)
    {
        $userAgent = (string) $request->userAgent();
        $isIos = (bool) preg_match('/iPhone|iPad|iPod/i', $userAgent);
        $isAndroid = Str::contains(Str::lower($userAgent), 'android');
        // WKWebView mặc định không có token "Safari/" như Safari/Chrome iOS.
        $isInApp = (bool) preg_match(self::IN_APP_PATTERN, $userAgent)
            || ($isIos && !Str::contains($userAgent, 'Safari/'));

        $code = $this->extractCode($request);
        $pid = $this->sanitizePid($request->query('pid'));
        $appStoreUrl = config('app_download.app_store_url');

        // Android: App Links trong Zalo/Facebook vẫn hoạt động → trả về OneLink để
        // giữ attribution của AppsFlyer như luồng hiện tại.
        if ($isAndroid) {
            return redirect()->away($this->buildOneLinkUrl($code, $pid));
        }

        // Safari/Chrome iOS đi thẳng từ OneLink tới đây nghĩa là Universal Link
        // không mở được app → app chưa cài → chuyển App Store như trước.
        // Ngoại lệ: via=inapp là khi người dùng bấm "Mở bằng trình duyệt" từ Zalo.
        if ($isIos && !$isInApp && $request->query('via') !== 'inapp') {
            return redirect()->away($appStoreUrl);
        }

        $scheme = config('app_download.app_scheme');
        $schemeQuery = $code ? '?' . http_build_query([
            'referral_code' => $code,
            'deep_link_sub1' => $code,
        ]) : '';

        $response = response()->view('public.invite', [
            'code' => $code,
            'isInApp' => $isInApp,
            'isIos' => $isIos,
            'appSchemeUrl' => $scheme . '://signup' . $schemeQuery,
            // Trong Safari, người dùng bấm trực tiếp vào OneLink sẽ kích hoạt
            // Universal Link; af_ios_url ghi đè để không quay vòng lại trang này.
            'oneLinkUrl' => $this->buildOneLinkUrl($code, $pid, $appStoreUrl),
            'storeUrl' => $appStoreUrl,
            'playStoreUrl' => config('app_download.play_store_url'),
        ]);

        // Nội dung phụ thuộc User-Agent và mã giới thiệu → không cache, không index.
        return $response
            ->header('Cache-Control', 'no-store, private')
            ->header('Vary', 'User-Agent')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function extractCode(Request $request): ?string
    {
        foreach (self::CODE_KEYS as $key) {
            $value = $request->query($key);
            if (!is_string($value)) {
                continue;
            }
            $value = Str::upper(trim($value));
            if (preg_match(self::CODE_PATTERN, $value)) {
                return $value;
            }
        }

        return null;
    }

    private function sanitizePid($pid): string
    {
        return is_string($pid) && preg_match('/^[A-Za-z0-9_.-]{1,50}$/', $pid) ? $pid : 'referral';
    }

    private function buildOneLinkUrl(?string $code, string $pid, ?string $iosFallbackUrl = null): string
    {
        $params = ['pid' => $pid];
        if ($code) {
            $params += [
                'deep_link_value' => 'signup',
                'deep_link_sub1' => $code,
                'referral_code' => $code,
            ];
        }
        if ($iosFallbackUrl) {
            $params['af_ios_url'] = $iosFallbackUrl;
        }

        return config('app_download.onelink_url') . '?' . http_build_query($params);
    }
}
