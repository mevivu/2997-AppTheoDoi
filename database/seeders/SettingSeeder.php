<?php

namespace Database\Seeders;

use App\Enums\Setting\SettingGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Setting\SettingTypeInput;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            [
                'setting_key' => 'site_name',
                'setting_name' => 'Tên site',
                'plain_value' => 'Site name',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Tên của website, shop, app',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'site_logo',
                'setting_name' => 'Logo',
                'plain_value' => '/public/assets/images/logo.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'desc' => 'Logo thương hiệu',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'email',
                'setting_name' => 'Email',
                'plain_value' => 'mevivu@gmail.com',
                'type_input' => SettingTypeInput::Email,
                'group' => SettingGroup::General,
                'desc' => 'Email liên hệ',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'hotline',
                'setting_name' => 'Số điện thoại',
                'plain_value' => '0999999999',
                'type_input' => SettingTypeInput::Phone,
                'group' => SettingGroup::General,
                'desc' => 'Số điện thoại liên lạc.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'address',
                'setting_name' => 'Địa chỉ',
                'plain_value' => '998/42/15 Quang Trung, GV',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Địa chỉ liên lạc.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'bank_name',
                'setting_name' => 'Tên ngân hàng',
                'plain_value' => 'BIDV',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Tên ngân hàng',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'bank_account',
                'setting_name' => 'Số tài khoản ngân hàng',
                'plain_value' => '0999999999',
                'type_input' => SettingTypeInput::Number,
                'group' => SettingGroup::General,
                'desc' => 'Số tài khoản ngân hàng.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'account_holder',
                'setting_name' => 'Chủ tài khoản',
                'plain_value' => 'MeVivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Chủ tài khoản.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'qr_code',
                'setting_name' => 'Mã QR',
                'plain_value' => '/public/assets/images/qr_code.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'desc' => 'Mã QR.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'payment_syntax',
                'setting_name' => 'Cú pháp nạp tiền',
                'plain_value' => 'TX00001_NAPTEN',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Cú pháp nạp tiênd.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'introduce',
                'setting_name' => 'Giới thiệu',
                'plain_value' => 'Chào các bạn, chúng tôi là Mevivu',
                'type_input' => SettingTypeInput::Textarea,
                'group' => SettingGroup::General,
                'desc' => 'Chào các bạn, chúng tôi là Mevivu.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'policy',
                'setting_name' => 'Điều khoản',
                'plain_value' => 'Nhập điều khoản ở đây',
                'type_input' => SettingTypeInput::Textarea,
                'group' => SettingGroup::General,
                'desc' => 'Chào các bạn, chúng tôi là Mevivu.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'clause',
                'setting_name' => 'Chính sách',
                'plain_value' => 'Nhập Chính sách ở đây',
                'type_input' => SettingTypeInput::Textarea(),
                'group' => SettingGroup::General,
                'desc' => 'Chào các bạn, chúng tôi là Mevivu.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'aes_secret_key',
                'setting_name' => 'Khóa AES',
                'plain_value' => 'CUrnm5Ba0siAos3fm8cQOsWfA1tx8Ct78lssJQhGNWnd3X6bPZryEFQt',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::System,
                'desc' => 'Khóa mã hóa AES cho ứng dụng.',
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);
    }
}
