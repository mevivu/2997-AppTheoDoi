<?php

use App\Enums\Setting\SettingGroup;
use App\Enums\Setting\SettingTypeInput;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $group = defined(SettingGroup::class . '::Affiliate') ? SettingGroup::Affiliate : 11;
        $typeInput = defined(SettingTypeInput::class . '::Textarea') ? SettingTypeInput::Textarea : 6;

        $defaultGuideHtml = <<<'HTML'
<div class="affiliate-guide-content">
    <h2>HƯỚNG DẪN THAM GIA &amp; KIẾM TIỀN CÙNG CHƯƠNG TRÌNH AFFILIATE</h2>
    <p>Chào mừng bạn đến với chương trình Đối tác Tiếp thị liên kết (Affiliate) của <strong>Chăm Con 360</strong>. Chỉ với vài bước đơn giản, bạn có thể lan tỏa ứng dụng hữu ích đến cộng đồng cha mẹ và nhận nguồn thu nhập thụ động hấp dẫn.</p>

    <h3>I. CÁC BƯỚC THAM GIA DÀNH CHO ĐỐI TÁC MỚI</h3>
    <ol>
        <li><strong>Bước 1:</strong> Mở ứng dụng <strong>Chăm Con 360</strong>, vào mục <strong>Tài khoản ➔ Kiếm tiền cùng Chăm con 360</strong>.</li>
        <li><strong>Bước 2:</strong> Xem và nhấn <strong>Đồng ý Quy định &amp; Điều khoản</strong> để mở khóa mã giới thiệu cá nhân của bạn.</li>
        <li><strong>Bước 3:</strong> Nhấn nút <strong>Chia sẻ mã giới thiệu</strong> hoặc sao chép đường liên kết thông minh để gửi cho bạn bè, người thân qua Zalo, Facebook, Messenger hoặc các hội nhóm.</li>
        <li><strong>Bước 4:</strong> Khi bạn bè (F1) tải ứng dụng, đăng ký tài khoản có gắn mã giới thiệu của bạn và hoàn tất tạo hồ sơ bé, hệ thống sẽ tự động cộng tiền thưởng vào <strong>Ví hoa hồng</strong>.</li>
    </ol>

    <h3>II. CƠ CHẾ NHẬN THƯỞNG &amp; THĂNG HẠNG</h3>
    <ul>
        <li><strong>Thưởng giới thiệu thành viên mới (F1):</strong> Nhận từ <strong>1.000đ – 5.000đ/F1</strong> tùy theo cấp bậc đối tác hiện tại ngay khi F1 hoàn tất tạo hồ sơ con.</li>
        <li><strong>Hoa hồng mua gói VIP:</strong> Nhận ngay chiết khấu từ <strong>10% đến 50%</strong> giá trị gói dịch vụ mỗi khi thành viên F1 nâng cấp gói VIP.</li>
        <li><strong>Thăng hạng tự động (Quy tắc HOẶC):</strong> Hệ thống xét duyệt tự động theo 4 cấp bậc (<em>Bạc ➔ Vàng ➔ Bạch Kim ➔ Kim Cương</em>) khi bạn đạt đủ mốc <strong>Số lượng người giới thiệu</strong> HOẶC <strong>Doanh số F1 tích lũy</strong>.</li>
    </ul>

    <h3>III. MẸO CHIA SẺ HIỆU QUẢ GIÚP TĂNG THU NHẬP</h3>
    <ul>
        <li>Chia sẻ trải nghiệm thực tế khi theo dõi chiều cao, cân nặng, đánh giá EQ/AQ/IQ hoặc lộ trình rèn luyện của bé trên các hội nhóm nuôi dạy con.</li>
        <li>Luôn đính kèm <strong>Mã giới thiệu</strong> hoặc <strong>Link tải ứng dụng</strong> ở cuối bài đăng và phần bình luận để phụ huynh dễ dàng nhấn vào đăng ký.</li>
        <li>Nhắc bạn bè hoàn tất bước <strong>Thêm hồ sơ con</strong> sau khi tạo tài khoản để hệ thống kích hoạt tiền thưởng cho bạn.</li>
    </ul>

    <h3>IV. HƯỚNG DẪN RÚT TIỀN HOA HỒNG</h3>
    <ul>
        <li>Khi số dư <strong>Ví hoa hồng</strong> đạt tối thiểu <strong>1.000.000 VNĐ</strong>, bạn có thể gửi yêu cầu rút tiền 24/7 trên ứng dụng (số tiền rút là bội số của <strong>1.000.000 VNĐ</strong>).</li>
        <li>Hoàn tất xác minh danh tính (KYC) và điền chính xác thông tin tài khoản ngân hàng nhận tiền.</li>
        <li>Ban quản trị sẽ tổng hợp, đối soát và chuyển khoản chi trả định kỳ vào <strong>Thứ 5 hàng tuần</strong>.</li>
    </ul>
</div>
HTML;

        DB::table('settings')->updateOrInsert(
            ['setting_key' => 'affiliate_guide'],
            [
                'setting_key' => 'affiliate_guide',
                'setting_name' => 'Bài viết hướng dẫn tham gia & kiếm tiền Affiliate',
                'plain_value' => $defaultGuideHtml,
                'desc' => 'Nội dung bài viết hướng dẫn chi tiết cách tham gia và nhận thưởng tiếp thị liên kết hiển thị trên ứng dụng.',
                'type_input' => $typeInput,
                'type_data' => null,
                'group' => $group,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('setting_key', 'affiliate_guide')->delete();
    }
};
