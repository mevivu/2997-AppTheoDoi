<?php

namespace App\Admin\Http\Controllers\Pwa;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PwaController extends Controller
{
    public function manifest(): Response
    {
        return response([
            'name' => 'Chăm Con 360 - Quản trị hệ thống',
            'short_name' => 'Chăm Con Admin',
            'description' => 'Hệ thống CMS Quản trị Chăm Con 360.',
            'start_url' => route('admin.dashboard'),
            'scope' => url('/admin').'/',
            'display' => 'standalone',
            'background_color' => '#1e293b',
            'theme_color' => '#2563eb',
            'lang' => 'vi',
            'icons' => [
                ['src' => asset('/public/admin/assets/images/admin-icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset('/public/admin/assets/images/admin-icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset('/public/admin/assets/images/admin-icon-maskable-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function serviceWorker(): Response
    {
        return response()
            ->view('admin.pwa.service-worker', [], 200, ['Content-Type' => 'application/javascript'])
            ->header('Service-Worker-Allowed', parse_url(url('/admin'), PHP_URL_PATH).'/');
    }

    public function offline(): View
    {
        return view('admin.pwa.offline');
    }
}
