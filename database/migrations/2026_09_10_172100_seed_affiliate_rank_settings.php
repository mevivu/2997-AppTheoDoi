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
            // Cấu hình Doanh số tối thiểu đạt cấp bậc (VNĐ)
            [
                'setting_key' => 'affiliate_sales_bronze',
                'setting_name' => 'Doanh số tối thiểu: Mẹ Đồng (VNĐ)',
                'plain_value' => '0',
                'desc' => 'Mốc doanh số F1 tối thiểu để đạt cấp Mẹ Đồng (cấp mặc định).',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_sales_silver',
                'setting_name' => 'Doanh số tối thiểu: Mẹ Bạc (VNĐ)',
                'plain_value' => '2000000',
                'desc' => 'Mốc doanh số F1 tích lũy tối thiểu để nâng cấp lên Mẹ Bạc.',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_sales_gold',
                'setting_name' => 'Doanh số tối thiểu: Mẹ Vàng (VNĐ)',
                'plain_value' => '10000000',
                'desc' => 'Mốc doanh số F1 tích lũy tối thiểu để nâng cấp lên Mẹ Vàng.',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_sales_diamond',
                'setting_name' => 'Doanh số tối thiểu: Mẹ Kim Cương (VNĐ)',
                'plain_value' => '30000000',
                'desc' => 'Mốc doanh số F1 tích lũy tối thiểu để nâng cấp lên Mẹ Kim Cương.',
                'type_input' => 11, // Price
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Cấu hình % Hoa hồng theo cấp bậc
            [
                'setting_key' => 'affiliate_commission_bronze',
                'setting_name' => 'Tỷ lệ hoa hồng: Mẹ Đồng (%)',
                'plain_value' => '5',
                'desc' => 'Tỷ lệ phần trăm (%) hoa hồng nhận được khi F1 mua gói dịch vụ dành cho Mẹ Đồng.',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_commission_silver',
                'setting_name' => 'Tỷ lệ hoa hồng: Mẹ Bạc (%)',
                'plain_value' => '10',
                'desc' => 'Tỷ lệ phần trăm (%) hoa hồng nhận được khi F1 mua gói dịch vụ dành cho Mẹ Bạc.',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_commission_gold',
                'setting_name' => 'Tỷ lệ hoa hồng: Mẹ Vàng (%)',
                'plain_value' => '15',
                'desc' => 'Tỷ lệ phần trăm (%) hoa hồng nhận được khi F1 mua gói dịch vụ dành cho Mẹ Vàng.',
                'type_input' => 2, // Number
                'type_data' => null,
                'group' => 11, // Affiliate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'affiliate_commission_diamond',
                'setting_name' => 'Tỷ lệ hoa hồng: Mẹ Kim Cương (%)',
                'plain_value' => '20',
                'desc' => 'Tỷ lệ phần trăm (%) hoa hồng nhận được khi F1 mua gói dịch vụ dành cho Mẹ Kim Cương.',
                'type_input' => 2, // Number
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
            'affiliate_sales_bronze',
            'affiliate_sales_silver',
            'affiliate_sales_gold',
            'affiliate_sales_diamond',
            'affiliate_commission_bronze',
            'affiliate_commission_silver',
            'affiliate_commission_gold',
            'affiliate_commission_diamond',
        ])->delete();
    }
};
