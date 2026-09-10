<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'setting_key' => 'affiliate_active',
                'setting_name' => 'Kích hoạt trả thưởng hoa hồng khi đăng ký',
                'plain_value' => '1',
                'desc' => 'Bật hoặc tắt toàn bộ chương trình cộng tiền thưởng khi có người dùng đăng ký qua mã giới thiệu.',
                'type_input' => 9, // Checkbox
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_reward_referrer',
                'setting_name' => 'Tiền thưởng cho người giới thiệu (VNĐ)',
                'plain_value' => '10000',
                'desc' => 'Số tiền cộng vào ví người giới thiệu khi có người mới đăng ký thành công bằng mã của họ.',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_reward_referee',
                'setting_name' => 'Tiền thưởng cho người mới đăng ký (VNĐ)',
                'plain_value' => '0',
                'desc' => 'Số tiền chào mừng cộng vào ví người mới khi đăng ký thành công có nhập mã giới thiệu.',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('setting_key', [
            'affiliate_active',
            'affiliate_reward_referrer',
            'affiliate_reward_referee',
        ])->delete();
    }
};
