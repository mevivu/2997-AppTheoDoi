<?php

namespace Database\Seeders;

use App\Enums\DefaultStatus;
use App\Enums\Expert\ExpertCouncilType;
use App\Enums\Introduction\IntroductionSectionType;
use App\Models\AgeGroup;
use App\Models\Expert;
use App\Models\ExpertCategory;
use App\Models\ExpertPost;
use App\Models\Introduction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntroductionAndExpertCornerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Introduction::truncate();
        ExpertPost::truncate();
        ExpertCategory::truncate();
        Expert::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed Bảng Giới thiệu (Introductions)
        $this->seedIntroductions();

        // 2. Seed Chuyên gia / Cố vấn (Experts theo đúng mockup ảnh)
        $experts = $this->seedExperts();

        // 3. Seed Danh mục Chuyên đề (Expert Categories)
        $categories = $this->seedCategories();

        // 4. Seed Bài viết Chuyên gia (Expert Posts)
        $this->seedPosts($experts, $categories);
    }

    private function seedIntroductions(): void
    {
        $data = [
            [
                'title' => 'Ứng dụng Đồng hành Chăm sóc & Phát triển Toàn diện cho Trẻ',
                'slug' => 've-chung-toi',
                'section_type' => IntroductionSectionType::General->value,
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=1000',
                'icon' => 'ti ti-apps',
                'excerpt' => 'Nền tảng số 1 hỗ trợ cha mẹ Việt theo dõi sức khỏe, dinh dưỡng và các mốc phát triển chuẩn y khoa của bé từ sơ sinh đến vị thành niên.',
                'content' => '<h2>Chào mừng cha mẹ đến với Nền tảng Chăm Con Toàn Diện</h2>'
                    . '<p>Nuôi con là một hành trình kỳ diệu nhưng cũng đầy thách thức. Chúng tôi ra đời với mục tiêu trở thành người trợ lý đắc lực, đồng hành cùng mỗi gia đình Việt Nam ngay từ những ngày đầu thai kỳ cho đến khi trẻ trưởng thành.</p>'
                    . '<h3>Tính năng nổi bật đồng hành cùng bé:</h3>'
                    . '<ul>'
                    . '<li><strong>Theo dõi thể chất chuẩn WHO:</strong> Cập nhật cân nặng, chiều cao, vòng đầu với biểu đồ trực quan, cảnh báo nguy cơ còi xương hoặc thừa cân sớm.</li>'
                    . '<li><strong>Sổ tay tiêm chủng thông minh:</strong> Tự động nhắc lịch tiêm ngừa theo độ tuổi, lưu trữ lịch sử phản ứng sau tiêm.</li>'
                    . '<li><strong>Thực đơn dinh dưỡng đa dạng:</strong> Gợi ý các món ăn dặm, chế độ dinh dưỡng cân bằng theo từng tháng tuổi.</li>'
                    . '<li><strong>Góc Chuyên Gia & Cố Vấn:</strong> Kết nối trực tiếp với Hội đồng Cố vấn Chiến lược và Hội đồng Tư vấn Chuyên môn hàng đầu Việt Nam.</li>'
                    . '</ul>'
                    . '<p>Chúng tôi tin rằng, với sự hỗ trợ của công nghệ và kiến thức y khoa chính xác, hành trình nuôi dạy con của bạn sẽ trở nên nhẹ nhàng, khoa học và ngập tràn niềm vui.</p>',
                'sort_order' => 1,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'title' => 'Tầm nhìn chiến lược 2030: Nâng tầm tầm vóc trẻ em Việt',
                'slug' => 'tam-nhin-chien-luoc',
                'section_type' => IntroductionSectionType::Vision->value,
                'image' => 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?auto=format&fit=crop&q=80&w=1000',
                'icon' => 'ti ti-eye',
                'excerpt' => 'Trở thành hệ sinh thái công nghệ chăm sóc sức khỏe và phát triển trẻ em hàng đầu Đông Nam Á, phục vụ hơn 10 triệu gia đình.',
                'content' => '<h2>Tầm nhìn tương lai</h2>'
                    . '<p>Đến năm 2030, chúng tôi hướng đến mục tiêu trở thành nền tảng chăm sóc trẻ em được tin dùng nhất Việt Nam và vươn tầm khu vực Đông Nam Á.</p>'
                    . '<p>Chúng tôi không ngừng nghiên cứu và ứng dụng trí tuệ nhân tạo (AI) để đưa ra các phân tích cá nhân hóa, giúp phát hiện sớm các dấu hiệu chậm phát triển hoặc bất thường sức khỏe ở trẻ.</p>',
                'sort_order' => 2,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'title' => 'Sứ mệnh phụng sự: Khoa học hóa việc nuôi dạy con',
                'slug' => 'su-menh-phung-su',
                'section_type' => IntroductionSectionType::Mission->value,
                'image' => 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?auto=format&fit=crop&q=80&w=1000',
                'icon' => 'ti ti-target',
                'excerpt' => 'Cung cấp giải pháp số toàn diện kết hợp chuyên môn y khoa chính thống, xóa tan nỗi lo lắng và hoang mang của cha mẹ.',
                'content' => '<h2>Sứ mệnh vì sức khỏe mầm non</h2>'
                    . '<p>Giúp mỗi đứa trẻ được nuôi dưỡng trong môi trường khoa học, yêu thương và an toàn nhất.</p>'
                    . '<p>Thu hẹp khoảng cách tiếp cận thông tin y khoa chính thống giữa các vùng miền, giúp mọi bà mẹ ở bất cứ đâu đều có thể tiếp cận sự tư vấn của các chuyên gia đầu ngành một cách dễ dàng.</p>',
                'sort_order' => 3,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'title' => 'Giá trị cốt lõi: Yêu thương - Khoa học - Tin cậy - Đồng hành',
                'slug' => 'gia-tri-cot-loi',
                'section_type' => IntroductionSectionType::CoreValue->value,
                'image' => 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&q=80&w=1000',
                'icon' => 'ti ti-heart-handshake',
                'excerpt' => 'Mọi sản phẩm, lời khuyên và nội dung xuất bản đều đặt sức khỏe cùng sự phát triển hạnh phúc của trẻ lên trên hết.',
                'content' => '<h2>4 Trụ Cột Giá Trị Cốt Lõi</h2>'
                    . '<ol>'
                    . '<li><strong>Yêu thương vô điều kiện:</strong> Lắng nghe và thấu hiểu những lo lắng, tâm tư nhỏ nhất của các bậc làm cha mẹ.</li>'
                    . '<li><strong>Chuẩn mực khoa học:</strong> 100% dữ liệu tăng trưởng, phác đồ dinh dưỡng tuân thủ tiêu chuẩn WHO và Bộ Y Tế.</li>'
                    . '<li><strong>Chính xác và Tin cậy:</strong> Mọi bài viết chuyên môn đều qua quy trình phản biện nghiêm ngặt của hội đồng bác sĩ chuyên khoa.</li>'
                    . '<li><strong>Đồng hành lâu dài:</strong> Không chỉ là ứng dụng, chúng tôi là người bạn thân thiết trong suốt hành trình khôn lớn của con.</li>'
                    . '</ol>',
                'sort_order' => 4,
                'status' => DefaultStatus::Published->value,
            ],
        ];

        foreach ($data as $item) {
            Introduction::create($item);
        }
    }

    private function seedExperts(): array
    {
        $expertsData = [
            // ==================== KHỐI 1: HỘI ĐỒNG CỐ VẤN CHIẾN LƯỢC CHĂM CON 360 ====================
            [
                'council_type' => ExpertCouncilType::Strategic->value,
                'name' => 'Ths. Ngô Văn Tụ',
                'title' => 'Nguyên TGĐ Điều hành Vinamilk',
                'workplace' => 'Công ty Cổ phần Sữa Việt Nam (Vinamilk)',
                'hospital' => null,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
                'bio' => 'Hơn 30 năm kinh nghiệm điều hành và hoạch định chiến lược cho các tập đoàn dinh dưỡng hàng đầu Việt Nam. Cố vấn định hướng phát triển hệ sinh thái Chăm Con 360.',
                'contact_link' => 'https://chamcon360.vn/co-van-chien-luoc/ngo-van-tu',
                'contact_phone' => '0903888999',
                'is_verified' => true,
                'sort_order' => 1,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'council_type' => ExpertCouncilType::Strategic->value,
                'name' => 'Ts. Hoàng Trung Dũng',
                'title' => 'Chủ tịch Học viện Kingsman',
                'workplace' => 'Học viện Đào tạo Doanh nhân Kingsman',
                'hospital' => null,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=400',
                'bio' => 'Chuyên gia cố vấn quản trị cao cấp, đào tạo lãnh đạo và chiến lược thương hiệu bền vững. Đồng hành xây dựng văn hóa nuôi dạy con nhân bản và hiện đại.',
                'contact_link' => 'https://chamcon360.vn/co-van-chien-luoc/hoang-trung-dung',
                'contact_phone' => '0909666777',
                'is_verified' => true,
                'sort_order' => 2,
                'status' => DefaultStatus::Published->value,
            ],

            // ==================== KHỐI 2: HỘI ĐỒNG TƯ VẤN CHUYÊN MÔN CHĂM CON 360 ====================
            [
                'council_type' => ExpertCouncilType::Professional->value,
                'name' => 'Gv. Nguyễn Văn A',
                'title' => 'Giảng viên - Chuyên gia Dinh dưỡng Thể chất',
                'workplace' => 'Khoa Giáo dục Mầm non - Trường ĐH Sư phạm Hà Nội',
                'hospital' => 'Viện Dinh dưỡng & Phát triển Thể chất Trẻ em',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=400',
                'bio' => 'Tốt nghiệp ĐH Sư phạm Hà Nội, nghiên cứu chuyên sâu về vi chất dinh dưỡng và các bài tập vận động kích thích chiều cao trong 1000 ngày đầu đời.',
                'contact_link' => 'https://zalo.me/chamcon360_gva',
                'contact_phone' => '0988112233',
                'is_verified' => true,
                'sort_order' => 1,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'council_type' => ExpertCouncilType::Professional->value,
                'name' => 'Ts. Ngô Văn B',
                'title' => 'ThS Tâm lý, TS Giáo dục Đặc biệt',
                'workplace' => 'Trường ĐH Sư phạm TP.HCM',
                'hospital' => 'Trung tâm Tư vấn Tâm lý & Can thiệp sớm Trẻ em',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=400',
                'bio' => 'ThS Tâm lý học phát triển, Tiến sĩ Giáo dục đặc biệt ĐH Sư phạm. Chuyên gia tư vấn giải tỏa áp lực nuôi dạy con, khủng hoảng cảm xúc tuổi lên 2, 3 và rèn luyện kỹ năng ngôn ngữ.',
                'contact_link' => 'https://zalo.me/chamcon360_nvb',
                'contact_phone' => '0977223344',
                'is_verified' => true,
                'sort_order' => 2,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'council_type' => ExpertCouncilType::Professional->value,
                'name' => 'NCSTS: Trương Văn C',
                'title' => 'Thạc sĩ Y sinh Hàn Quốc, Nghiên cứu sinh Viện Kỹ thuật Sinh học',
                'workplace' => 'Viện Kỹ thuật Sinh học & Y học Tái tạo',
                'hospital' => 'Khoa Dược Sinh học & Xét nghiệm Miễn dịch',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=400',
                'bio' => 'Thạc sĩ Y sinh học Đại học Quốc gia Seoul (Hàn Quốc), NCS Viện Kỹ thuật Sinh học. Chuyên sâu về miễn dịch học đường tiêu hóa trẻ nhỏ và phòng ngừa dị ứng đạm sữa.',
                'contact_link' => 'https://zalo.me/chamcon360_tvc',
                'contact_phone' => '0966334455',
                'is_verified' => true,
                'sort_order' => 3,
                'status' => DefaultStatus::Published->value,
            ],
        ];

        $experts = [];
        foreach ($expertsData as $data) {
            $experts[] = Expert::create($data);
        }

        return $experts;
    }

    private function seedCategories(): array
    {
        $categoriesData = [
            [
                'name' => 'Dinh dưỡng & Tiêu hóa',
                'slug' => 'dinh-duong-tieu-hoa',
                'icon' => 'fa-solid fa-apple-whole',
                'description' => 'Thực đơn ăn dặm, vi chất dinh dưỡng, xử lý tình trạng biếng ăn và chăm sóc hệ tiêu hóa của trẻ.',
                'sort_order' => 1,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'name' => 'Tăng trưởng & Chiều cao',
                'slug' => 'tang-truong-chieu-cao',
                'icon' => 'fa-solid fa-ruler-vertical',
                'description' => 'Cột mốc tăng trưởng chuẩn WHO, vận động thể chất và giai đoạn vàng bứt phá chiều cao vượt trội.',
                'sort_order' => 2,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'name' => 'Tâm lý & Giấc ngủ',
                'slug' => 'tam-ly-giac-ngu',
                'icon' => 'fa-solid fa-brain',
                'description' => 'Rèn luyện nếp ngủ sinh hoạt khoa học, giải mã khủng hoảng tuổi lên 2, 3 và kết nối cảm xúc cùng con.',
                'sort_order' => 3,
                'status' => DefaultStatus::Published->value,
            ],
            [
                'name' => 'Bệnh thường gặp & Phòng ngừa',
                'slug' => 'benh-thuong-gap-phong-ngua',
                'icon' => 'fa-solid fa-shield-virus',
                'description' => 'Cẩm nang sơ cứu, chăm sóc trẻ khi bị sốt, ho, dị ứng và hướng dẫn tiêm chủng định kỳ an toàn.',
                'sort_order' => 4,
                'status' => DefaultStatus::Published->value,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[] = ExpertCategory::create($data);
        }

        return $categories;
    }

    private function seedPosts(array $experts, array $categories): void
    {
        $age02 = AgeGroup::find(2);
        $age24 = AgeGroup::find(3);

        $postsData = [
            [
                'title' => 'Hướng dẫn xây dựng thực đơn ăn dặm khoa học cho bé từ 6-12 tháng tuổi',
                'slug' => 'huong-dan-xay-dung-thuc-don-an-dam-khoa-hoc-cho-be-tu-6-12-thang-tuoi',
                'expert_id' => $experts[2]->id, // Gv. Nguyễn Văn A (Dinh dưỡng)
                'category_id' => $categories[0]->id, // Dinh dưỡng
                'age_group_id' => $age02?->id ?? 2,
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=800',
                'reading_time' => '5 phút đọc',
                'excerpt' => 'Ăn dặm là bước ngoặt quan trọng đánh dấu sự chuyển đổi từ nguồn sữa mẹ sang thức ăn đặc. Khám phá các nguyên tắc vàng để con hợp tác vui vẻ và hấp thu tối đa dưỡng chất.',
                'expert_quote' => 'Đừng ép trẻ ăn lượng nhiều ngay từ đầu. Hãy để trẻ làm quen với từng kết cấu thức ăn từ mịn đến thô, và luôn tôn trọng tín hiệu no tự nhiên của bé.',
                'content' => '<h2>1. Thời điểm vàng bắt đầu cho bé ăn dặm</h2>'
                    . '<p>Tổ chức Y tế Thế giới (WHO) khuyến cáo nên cho trẻ bú mẹ hoàn toàn trong 6 tháng đầu đời. Tròn 6 tháng (180 ngày), hệ tiêu hóa của trẻ mới tương đối hoàn thiện men tiêu hóa tinh bột và thức ăn đặc.</p>'
                    . '<h2>2. Nguyên tắc "4 nhóm chất" không thể thiếu</h2>'
                    . '<p>Một bữa ăn dặm đầy đủ cần cân đối giữa 4 nhóm dưỡng chất chính:</p>'
                    . '<ul>'
                    . '<li><strong>Chất bột đường:</strong> Gạo tẻ, yến mạch, khoai lang,... cung cấp năng lượng chính cho hoạt động não bộ.</li>'
                    . '<li><strong>Chất đạm:</strong> Thịt nạc heo, ức gà, lòng đỏ trứng, cá hồi,... giúp tái tạo tế bào và phát triển cơ bắp.</li>'
                    . '<li><strong>Chất béo tốt:</strong> Dầu ô-liu, dầu cá hồi nguyên chất, quả bơ,... hòa tan các vitamin A, D, E, K và hỗ trợ phát triển tế bào thần kinh.</li>'
                    . '<li><strong>Rau củ & Trái cây:</strong> Bí đỏ, cà rốt, cải bó xôi, chuối,... bổ sung chất xơ hòa tan chống táo bón và các vi lượng thiết yếu.</li>'
                    . '</ul>'
                    . '<h2>3. Quy tắc tăng thô theo từng giai đoạn</h2>'
                    . '<p>Giai đoạn 6-7 tháng: Thức ăn dạng nghiền mịn rây nhuyễn. Giai đoạn 8-9 tháng: Chuyển sang cháo vỡ hạt và rau củ băm nhỏ. Từ 10-12 tháng: Trẻ có thể tập nhai thức ăn thái hạt lựu mềm hoặc bốc nhón tự chỉ huy.</p>',
                'is_featured' => 1,
                'views' => 1420,
                'sort_order' => 1,
                'status' => DefaultStatus::Published->value,
                'posted_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => '3 giai đoạn vàng thúc đẩy chiều cao tối đa mà cha mẹ không nên bỏ lỡ',
                'slug' => '3-giai-doan-vang-thuc-day-chieu-cao-toi-da-ma-cha-me-khong-nen-bo-lo',
                'expert_id' => $experts[2]->id, // Gv. Nguyễn Văn A
                'category_id' => $categories[1]->id, // Chiều cao
                'age_group_id' => $age24?->id ?? 3,
                'image' => 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?auto=format&fit=crop&q=80&w=800',
                'reading_time' => '4 phút đọc',
                'excerpt' => 'Chiều cao của con người chỉ phụ thuộc khoảng 23% vào gen di truyền. 77% còn lại được quyết định bởi dinh dưỡng, thói quen vận động và chất lượng giấc ngủ sinh học.',
                'expert_quote' => 'Hormone tăng trưởng GH tiết ra nhiều nhất vào ban đêm trong khoảng 22h đến 2h sáng khi trẻ ngủ sâu giấc. Giữ thói quen cho trẻ đi ngủ trước 21h30 là chìa khóa then chốt cho sự phát triển xương.',
                'content' => '<h2>Giai đoạn 1: 1000 ngày đầu đời (Từ mang thai đến 2 tuổi)</h2>'
                    . '<p>Đây là giai đoạn phát triển chiều cao nhanh nhất trong cả cuộc đời. Năm đầu tiên trẻ có thể tăng tới 25cm và năm thứ hai tăng khoảng 10-12cm nếu được chăm sóc dinh dưỡng đúng chuẩn.</p>'
                    . '<h2>Giai đoạn 2: Tuổi tiền tiểu học và tiểu học (3 - 10 tuổi)</h2>'
                    . '<p>Tốc độ tăng trưởng ổn định khoảng 5-7cm/năm. Lúc này cần khuyến khích trẻ vận động ngoài trời ít nhất 60 phút mỗi ngày để tăng tổng hợp Vitamin D tự nhiên.</p>'
                    . '<h2>Giai đoạn 3: Dậy thì và tiền dậy thì</h2>'
                    . '<p>Cơ hội bứt phá cuối cùng trước khi các đầu xương cốt hóa hoàn toàn. Trẻ có thể tăng vọt 8-12cm/năm nếu được bổ sung canxi hữu cơ, kẽm, vitamin K2 và chế độ tập luyện các môn kéo giãn cột sống như bơi lội, bóng rổ.</p>',
                'is_featured' => 1,
                'views' => 985,
                'sort_order' => 2,
                'status' => DefaultStatus::Published->value,
                'posted_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Cách xử lý cơn ăn vạ (Tantrum) ở trẻ: Đồng hành thay vì trừng phạt',
                'slug' => 'cach-xu-ly-con-an-va-tantrum-o-tre-dong-hanh-thay-vi-trung-phat',
                'expert_id' => $experts[3]->id, // Ts. Ngô Văn B (Tâm lý)
                'category_id' => $categories[2]->id, // Tâm lý
                'age_group_id' => $age24?->id ?? 3,
                'image' => 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?auto=format&fit=crop&q=80&w=800',
                'reading_time' => '6 phút đọc',
                'excerpt' => 'Cơn bùng nổ cảm xúc của trẻ thường bắt nguồn từ việc não bộ vùng điều hành chưa hoàn thiện và vốn từ chưa đủ để diễn đạt nỗi thất vọng của mình.',
                'expert_quote' => 'Khi trẻ đang trong cơn giận dữ, hãy ở bên cạnh làm điểm tựa bình yên. Việc quát mắng chỉ kích hoạt thêm phản ứng phòng vệ hoảng sợ và khiến trẻ càng la hét dữ dội hơn.',
                'content' => '<h2>Hiểu đúng về cơn giận của trẻ</h2>'
                    . '<p>Ăn vạ không phải là biểu hiện của "hư đốn" hay "chống đối", mà là phản ứng bình thường trong giai đoạn phát triển tâm lý khi trẻ nhận thức được bản ngã cá nhân nhưng chưa kiểm soát được cảm xúc ức chế.</p>'
                    . '<h2>3 Bước vàng dập tắt cơn giận an toàn:</h2>'
                    . '<ol>'
                    . '<li><strong>Giữ bình tĩnh và đảm bảo an toàn:</strong> Không lớn tiếng tranh luận, đưa trẻ ra khỏi khu vực nguy hiểm hoặc đông người nếu cần.</li>'
                    . '<li><strong>Gọi tên cảm xúc của trẻ:</strong> Hãy nói: "Mẹ biết con đang rất tiếc vì chưa được chơi tiếp", giúp trẻ học cách nhận diện cảm xúc.</li>'
                    . '<li><strong>Đưa ra sự lựa chọn thay thế:</strong> Sau khi trẻ hạ nhiệt, cho trẻ chọn 2 giải pháp có thể chấp nhận được thay vì áp đặt mệnh lệnh tuyệt đối.</li>'
                    . '</ol>',
                'is_featured' => 0,
                'views' => 740,
                'sort_order' => 3,
                'status' => DefaultStatus::Published->value,
                'posted_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'Chăm sóc hệ vi sinh đường ruột và tăng cường miễn dịch tự nhiên cho bé',
                'slug' => 'cham-soc-he-vi-sinh-duong-ruot-va-tang-cuong-mien-dich-tu-nhien-cho-be',
                'expert_id' => $experts[4]->id, // NCSTS. Trương Văn C (Y sinh học)
                'category_id' => $categories[3]->id, // Bệnh thường gặp & Phòng ngừa
                'age_group_id' => $age02?->id ?? 2,
                'image' => 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&q=80&w=800',
                'reading_time' => '5 phút đọc',
                'excerpt' => '70% hệ thống miễn dịch của trẻ nằm ở đường ruột. Việc thiết lập hệ vi sinh vật khỏe mạnh ngay từ những tháng đầu đời quyết định sức đề kháng của con trước các bệnh truyền nhiễm.',
                'expert_quote' => 'Bổ sung men vi sinh đúng chủng (Probiotics kết hợp Prebiotics HMO) giúp củng cố hàng rào niêm mạc ruột, kích thích sản sinh kháng thể tự nhiên IgA chống lại mầm bệnh xâm nhập.',
                'content' => '<h2>1. Ruột non - Tuyến phòng thủ đầu tiên của trẻ</h2>'
                    . '<p>Nghiên cứu y sinh học chỉ ra rằng hệ vi sinh vật đường ruột tham gia trực tiếp vào việc huấn luyện tế bào miễn dịch phân biệt vi khuẩn có lợi và tác nhân gây hại.</p>'
                    . '<h2>2. Các biện pháp bảo vệ hệ tiêu hóa non nớt:</h2>'
                    . '<ul>'
                    . '<li>Ưu tiên sữa mẹ hoàn toàn: Sữa mẹ chứa kháng thể sống và hơn 200 loại oligosaccharides nuôi dưỡng lợi khuẩn.</li>'
                    . '<li>Tránh lạm dụng kháng sinh: Kháng sinh tiêu diệt cả lợi khuẩn, khiến hệ miễn dịch của trẻ suy yếu kéo dài.</li>'
                    . '<li>Bổ sung chất xơ hòa tan FOS/GOS từ thực phẩm tươi khi bắt đầu ăn dặm.</li>'
                    . '</ul>',
                'is_featured' => 0,
                'views' => 1120,
                'sort_order' => 4,
                'status' => DefaultStatus::Published->value,
                'posted_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($postsData as $data) {
            ExpertPost::create($data);
        }
    }
}
