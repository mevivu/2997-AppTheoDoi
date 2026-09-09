<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Get system settings as key-value array with fallbacks.
     */
    protected function getSettings(): array
    {
        $keys = [
            'site_name',
            'site_logo',
            'email',
            'hotline',
            'address',
            'website',
            'facebook',
            'zalo',
            'introduce',
            'clause', // Chính sách bảo mật
            'policy', // Điều khoản sử dụng
        ];

        $settings = Setting::whereIn('setting_key', $keys)->pluck('plain_value', 'setting_key')->toArray();

        return array_merge([
            'site_name' => 'Chăm Con 360',
            'site_logo' => '/public/assets/images/logo.png',
            'email' => 'Chamcon360@gmail.com',
            'hotline' => '0985367464',
            'address' => 'CF Tower, 70 Phạm Ngọc Thạch Q3, TP.HCM',
            'website' => 'https://kids360growth.com',
            'company_name' => 'Công ty TNHH Thân Tâm Trí Việt Nam',
        ], $settings);
    }

    /**
     * Display Landing Page (Home).
     */
    public function home(): View
    {
        $settings = $this->getSettings();

        return view('public.home', compact('settings'));
    }

    /**
     * Display Privacy Policy page.
     */
    public function privacyPolicy(Request $request): View
    {
        $settings = $this->getSettings();
        $activeTab = $request->query('tab', 'privacy');

        return view('public.privacy-policy', compact('settings', 'activeTab'));
    }
}
