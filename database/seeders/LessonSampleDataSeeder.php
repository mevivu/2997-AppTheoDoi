<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonCategoryKey;
use App\Enums\Lesson\LessonDifficulty;
use App\Enums\Video\VideoType;
use App\Models\AgeGroup;
use App\Models\Lesson;
use App\Models\LessonCategory;
use App\Models\LessonVideo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ưu tiên nhóm tuổi 4-6 tuổi (hoặc 2-4 tuổi nếu chưa có 4-6)
        $targetAgeGroup = AgeGroup::where('name', 'like', '%4-6%')->first()
            ?? AgeGroup::where('name', 'like', '%2-4%')->first()
            ?? AgeGroup::first();

        if (!$targetAgeGroup) {
            $targetAgeGroup = AgeGroup::create([
                'name' => '4-6 tuổi',
                'min_months' => 48,
                'max_months' => 72,
                'status' => ActiveStatus::Active,
                'sort_order' => 4,
            ]);
        }

        $ageGroupId = $targetAgeGroup->id;

        // Định nghĩa 18 danh mục & bài học mẫu theo đúng bảng của người dùng
        $sampleData = [
            // ==========================================
            // 1. THỂ CHẤT (PQ)
            // ==========================================
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Height,
                'category_name' => 'Chiều cao',
                'description' => 'Các bài tập kéo giãn cơ xương khớp, kích thích sụn tiếp hợp phát triển chiều cao tối ưu.',
                'lessons' => [
                    [
                        'name' => 'Bài tập vươn người hái sao buổi sáng',
                        'description' => 'Động tác kéo giãn toàn thân nhẹ nhàng ngay khi thức dậy giúp kéo dài đốt sống và kích hoạt hormone tăng trưởng.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi sáng 10-15 phút',
                        'benefit' => 'Tăng độ dẻo dai cột sống, kích thích đĩa sụn xương dài phát triển',
                        'tools' => 'Thảm tập xốp mềm',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn động tác vươn cao hái sao từng bước',
                                'url' => 'https://www.youtube.com/watch?v=oc4QS2USKmk',
                                'duration' => 210,
                            ],
                            [
                                'title' => 'Cùng bé thực hành bài tập kéo giãn cơ thể buổi sáng',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 320,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Bmi,
                'category_name' => 'BMI',
                'description' => 'Vận động thể lực đốt cháy năng lượng thừa, duy trì chỉ số khối cơ thể (BMI) cân đối và khỏe mạnh.',
                'lessons' => [
                    [
                        'name' => 'Vận động nhịp điệu kiểm soát thể trạng BMI',
                        'description' => 'Chuỗi động tác nhảy aerobic thiếu nhi vui nhộn giúp điều hòa cân nặng và săn chắc cơ thể.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '3 - 4 lần mỗi tuần',
                        'benefit' => 'Cân bằng chuyển hóa năng lượng, ngăn ngừa thừa cân béo phì ở trẻ',
                        'tools' => 'Giày thể thao, trang phục co giãn',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Khởi động vui vẻ và nhảy aerobic thiếu nhi',
                                'url' => 'https://www.youtube.com/watch?v=L_LUpnjgPso',
                                'duration' => 240,
                            ],
                            [
                                'title' => 'Bài tập bật nhảy Cardio nhẹ nhàng cho bé',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 310,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Strength,
                'category_name' => 'Sức mạnh',
                'description' => 'Tăng cường sức mạnh cơ bắp vùng thân trên, cơ đùi và khả năng chịu lực của các khớp.',
                'lessons' => [
                    [
                        'name' => 'Trò chơi gấu bò vượt chướng ngại vật',
                        'description' => 'Mô phỏng tư thế bò của chú gấu giúp phát triển đồng đều nhóm cơ vai, cánh tay và cơ đùi.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '15 phút mỗi ngày',
                        'benefit' => 'Tăng sức mạnh bắp tay, bắp chân và độ ổn định của khớp vai',
                        'tools' => 'Gối ôm mềm làm chướng ngại vật',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Kỹ thuật bò đúng tư thế bảo vệ khớp cổ tay',
                                'url' => 'https://www.youtube.com/watch?v=Y5b9s6J6Yk8',
                                'duration' => 195,
                            ],
                            [
                                'title' => 'Thử thách vượt chướng ngại vật tăng cường sức mạnh',
                                'url' => 'https://www.youtube.com/watch?v=oc4QS2USKmk',
                                'duration' => 280,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Endurance,
                'category_name' => 'Sức bền',
                'description' => 'Rèn luyện dung tích phổi, sức khỏe tim mạch và duy trì vận động trong thời gian dài.',
                'lessons' => [
                    [
                        'name' => 'Chuỗi vận động liên hoàn rèn luyện sức bền tim mạch',
                        'description' => 'Kết hợp chạy chậm tại chỗ, bật nhảy và vượt chướng ngại vật giúp rèn luyện sự bền bỉ của tim và phổi.',
                        'difficulty' => LessonDifficulty::Hard,
                        'frequency' => '3 lần mỗi tuần',
                        'benefit' => 'Nâng cao dung tích sống của phổi, tăng sức dẻo dai toàn diện',
                        'tools' => 'Bình nước, không gian rộng rãi',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn điều hòa nhịp thở khi chạy nhảy liên tục',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 230,
                            ],
                            [
                                'title' => 'Thực hành chuỗi vận động bền bỉ cùng âm nhạc sôi động',
                                'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                                'duration' => 360,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 2. TRÍ TUỆ (IQ)
            // ==========================================
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::Language,
                'category_name' => 'Ngôn ngữ',
                'description' => 'Mở rộng vốn từ vựng phong phú, phát âm tròn vành rõ chữ và rèn luyện kỹ năng diễn đạt gãy gọn.',
                'lessons' => [
                    [
                        'name' => 'Học phát âm chuẩn và mở rộng vốn từ thế giới tự nhiên',
                        'description' => 'Khám phá tên gọi các loài động thực vật, luyện phát âm các phụ âm khó qua các câu chuyện ngắn.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Hằng ngày 15-20 phút',
                        'benefit' => 'Làm giàu vốn từ ngữ, tăng khả năng phản xạ giao tiếp tự nhiên',
                        'tools' => 'Bộ thẻ Flashcard thế giới tự nhiên',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Luyện khẩu hình phát âm các từ vựng thân quen',
                                'url' => 'https://www.youtube.com/watch?v=M6LoFBC3RVU',
                                'duration' => 270,
                            ],
                            [
                                'title' => 'Kể chuyện sinh động giúp bé ghi nhớ câu từ',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 350,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::MathLogic,
                'category_name' => 'Toán học – Logic',
                'description' => 'Làm quen với các con số, tập đếm số lượng, so sánh lớn bé và phân loại logic sự vật.',
                'lessons' => [
                    [
                        'name' => 'Đếm số lượng thông minh và so sánh hơn kém',
                        'description' => 'Học đếm từ 1 đến 20 qua các món đồ chơi thân thuộc, hiểu bản chất về số lượng và trật tự dãy số.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '3 buổi mỗi tuần',
                        'benefit' => 'Hình thành tư duy số học sớm, nhận biết quy luật logic toán học',
                        'tools' => 'Khối rubik, que tính, que gỗ màu sắc',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Bài hát số đếm vui nhộn từ 1 đến 10',
                                'url' => 'https://www.youtube.com/watch?v=_UR-l3QI2nE',
                                'duration' => 190,
                            ],
                            [
                                'title' => 'Trò chơi so sánh đĩa quả nào nhiều hơn, ít hơn',
                                'url' => 'https://www.youtube.com/watch?v=m2z9yKk2xM4',
                                'duration' => 260,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::Visual,
                'category_name' => 'Hình ảnh',
                'description' => 'Phát triển thị giác, nhận diện màu sắc, hình khối học và tư duy không gian đa chiều.',
                'lessons' => [
                    [
                        'name' => 'Nhận biết hình khối không gian và phối màu sáng tạo',
                        'description' => 'Bé làm quen với hình vuông, tròn, tam giác, khối lập phương và học cách phối hợp các dải màu.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => '2-3 lần mỗi tuần',
                        'benefit' => 'Tăng độ nhạy bén thị giác, phát triển óc thẩm mỹ và quan sát',
                        'tools' => 'Bộ đồ chơi xếp hình khối, màu vẽ',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Khám phá thế giới sắc màu rực rỡ quanh em',
                                'url' => 'https://www.youtube.com/watch?v=020g-0hhCAU',
                                'duration' => 220,
                            ],
                            [
                                'title' => 'Thực hành ghép hình ngôi nhà từ các hình học cơ bản',
                                'url' => 'https://www.youtube.com/watch?v=m2z9yKk2xM4',
                                'duration' => 290,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::Memory,
                'category_name' => 'Trí nhớ',
                'description' => 'Rèn luyện trí nhớ ngắn hạn và dài hạn qua các trò chơi ghi nhớ vị trí, chuỗi đồ vật và hình ảnh.',
                'lessons' => [
                    [
                        'name' => 'Trò chơi lật thẻ bài tìm cặp hình giống nhau',
                        'description' => 'Kích thích não bộ ghi nhớ vị trí các cặp tranh tương đồng, tăng tốc độ liên tưởng hình ảnh.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '15 phút mỗi ngày',
                        'benefit' => 'Tăng khả năng tập trung chú ý và khả năng lưu giữ thông tin',
                        'tools' => 'Bộ bài Memo Card hoặc tranh ghép đôi',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Mẹo ghi nhớ vị trí các quân bài nhanh nhất',
                                'url' => 'https://www.youtube.com/watch?v=m2z9yKk2xM4',
                                'duration' => 240,
                            ],
                            [
                                'title' => 'Thử thách thi tài ghi nhớ chuỗi 5 đồ vật biến mất',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 310,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 3. CẢM XÚC (EQ)
            // ==========================================
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::EmotionRecognition,
                'category_name' => 'Nhận biết cảm xúc',
                'description' => 'Giúp trẻ gọi tên chính xác các trạng thái cảm xúc: Vui vẻ, Buồn bã, Tức giận, Ngạc nhiên, Sợ hãi.',
                'lessons' => [
                    [
                        'name' => 'Gương soi cảm xúc: Nhận diện biểu cảm nét mặt',
                        'description' => 'Bé đứng trước gương quan sát khuôn mặt khi vui cười, cau mày hay ngạc nhiên và học cách gọi đúng tên cảm xúc.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => '2 lần mỗi tuần',
                        'benefit' => 'Nhận thức bản thân sâu sắc, không còn bối rối trước những xúc cảm nảy sinh',
                        'tools' => 'Chiếc gương soi nhỏ, tranh ảnh biểu cảm',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Bài hát vui nhộn về các khuôn mặt cảm xúc',
                                'url' => 'https://www.youtube.com/watch?v=yCjJyiqpAuU',
                                'duration' => 195,
                            ],
                            [
                                'title' => 'Cùng phân tích nét mặt của bạn thỏ trong truyện tranh',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 280,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::Empathy,
                'category_name' => 'Đồng cảm',
                'description' => 'Nuôi dưỡng lòng trắc ẩn, biết đặt mình vào vị trí của người khác và sẻ chia khi người xung quanh cần giúp đỡ.',
                'lessons' => [
                    [
                        'name' => 'Học cách quan tâm và sẻ chia đồ chơi cùng bạn',
                        'description' => 'Tình huống thực tế khi bạn khóc hoặc bị ngã, bé học cách hỏi han ân cần và trao đi sự ấm áp.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Hằng ngày trong sinh hoạt',
                        'benefit' => 'Hình thành nhân cách nhân hậu, xây dựng mối quan hệ bạn bè bền chặt',
                        'tools' => 'Gấu bông, búp bê đóng vai tình huống',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Câu chuyện chú chim sẻ biết chia sẻ hạt kê ngọt ngào',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 310,
                            ],
                            [
                                'title' => 'Bài tập đóng vai an ủi khi bạn gặp chuyện buồn',
                                'url' => 'https://www.youtube.com/watch?v=yCjJyiqpAuU',
                                'duration' => 245,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::SocialCommunication,
                'category_name' => 'Giao tiếp xã hội',
                'description' => 'Kỹ năng chào hỏi lịch sự, lắng nghe người đối diện, xếp hàng chờ đợi và hòa nhập với tập thể.',
                'lessons' => [
                    [
                        'name' => 'Kỹ năng chào hỏi tự tin và nói lời cảm ơn, xin lỗi',
                        'description' => 'Dạy bé ánh mắt thân thiện, nụ cười rạng rỡ và cách nói lời cảm ơn, xin lỗi chân thành trong cuộc sống.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi ngày',
                        'benefit' => 'Tự tin khi ra nơi công cộng, tạo thiện cảm với mọi người',
                        'tools' => 'Tình huống mô phỏng gia đình',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Phép lịch sự nhí: Lời chào đi trước, nụ cười theo sau',
                                'url' => 'https://www.youtube.com/watch?v=jGflCSq4PCU',
                                'duration' => 210,
                            ],
                            [
                                'title' => 'Kỹ năng lắng nghe người khác mà không ngắt lời',
                                'url' => 'https://www.youtube.com/watch?v=7wtfhZwyrcc',
                                'duration' => 275,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::Motivation,
                'category_name' => 'Động lực',
                'description' => 'Kích thích động lực nội tại, tinh thần chủ động tìm tòi khám phá và niềm tự hào khi hoàn thành mục tiêu.',
                'lessons' => [
                    [
                        'name' => 'Bảng khen thưởng nhiệm vụ nhí: Tự dọn đồ chơi',
                        'description' => 'Thiết lập bảng theo dõi thành tích nhỏ xinh để bé hào hứng hoàn thành các việc tự lập hằng ngày.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Hằng ngày',
                        'benefit' => 'Tạo dựng thói quen tự giác, không cần bố mẹ thúc giục',
                        'tools' => 'Bảng dán sticker ngôi sao khen thưởng',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Khám phá niềm vui khi tự tay xếp gọn góc đồ chơi',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 250,
                            ],
                            [
                                'title' => 'Bài học ăn mừng thành công nhỏ của chính mình',
                                'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                                'duration' => 290,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::EmotionControl,
                'category_name' => 'Kiểm soát cảm xúc',
                'description' => 'Kỹ thuật thở sâu, làm dịu cơn giận dữ, giải tỏa bức bối mà không la hét hay đập phá đồ đạc.',
                'lessons' => [
                    [
                        'name' => 'Chiếc hộp bình tĩnh: Bí quyết làm dịu cơn tức giận',
                        'description' => 'Dạy bé đếm từ 1 đến 5 kết hợp hít thở chậm để cơn nóng giận tan biến, bình tĩnh nói ra mong muốn.',
                        'difficulty' => LessonDifficulty::Hard,
                        'frequency' => 'Khi trẻ có dấu hiệu cáu giận',
                        'benefit' => 'Kiểm soát tốt hành vi bộc phát, nâng cao chỉ số thông minh cảm xúc',
                        'tools' => 'Quả bóng xốp mềm xả stress, góc thư giãn',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Hít thở bong bóng xà phòng để giải tỏa tức giận',
                                'url' => 'https://www.youtube.com/watch?v=yCjJyiqpAuU',
                                'duration' => 260,
                            ],
                            [
                                'title' => 'Hướng dẫn bé cách nói ra điều mình muốn thay vì gào khóc',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 330,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 4. VƯỢT KHÓ (AQ)
            // ==========================================
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Resilience,
                'category_name' => 'Khả năng chịu đựng',
                'description' => 'Rèn luyện bản lĩnh kiên cường, dũng cảm đứng lên khi vấp ngã và không dễ dàng gục ngã trước khó khăn.',
                'lessons' => [
                    [
                        'name' => 'Vấp ngã tự đứng lên: Không sợ trầy xước nhỏ',
                        'description' => 'Tạo môi trường thử thách an toàn để bé học cách phủi bụi, tự đứng dậy mỉm cười sau khi vấp chân.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Mỗi khi vận động ngoài trời',
                        'benefit' => 'Tăng sức chịu đựng tinh thần, giảm thói quen mè nheo khóc lóc',
                        'tools' => 'Sân cỏ hoặc thảm đệm an toàn',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Bài học từ chú sư tử con dũng cảm',
                                'url' => 'https://www.youtube.com/watch?v=Y5b9s6J6Yk8',
                                'duration' => 235,
                            ],
                            [
                                'title' => 'Kỹ năng tự kiểm tra vết thương nhẹ và bình tĩnh xử lý',
                                'url' => 'https://www.youtube.com/watch?v=oc4QS2USKmk',
                                'duration' => 295,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Patience,
                'category_name' => 'Kiên nhẫn',
                'description' => 'Học cách chờ đợi đến lượt, kiên trì hoàn thành việc dở dang và không bỏ cuộc giữa chừng.',
                'lessons' => [
                    [
                        'name' => 'Xây tháp gỗ cao tầng: Bài học về lòng kiên trì',
                        'description' => 'Tháp gỗ bị đổ nhiều lần, bé kiên nhẫn phân tích nguyên nhân và xếp lại từng tầng vững chắc hơn.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '3 lần mỗi tuần',
                        'benefit' => 'Hình thành đức tính kiên nhẫn, không nản lòng khi gặp thất bại',
                        'tools' => 'Bộ khối gỗ domino hoặc khối lắp ghép',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Bí quyết giữ bình tĩnh khi tòa tháp gỗ bị đổ',
                                'url' => 'https://www.youtube.com/watch?v=m2z9yKk2xM4',
                                'duration' => 240,
                            ],
                            [
                                'title' => 'Thực hành xếp lại kiên trì cho đến khi hoàn tất',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 320,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Positivity,
                'category_name' => 'Tích cực',
                'description' => 'Nhìn nhận vấn đề với lăng kính lạc quan, tìm thấy điều may mắn và niềm vui trong nghịch cảnh.',
                'lessons' => [
                    [
                        'name' => 'Trời mưa không ra sân được: Cùng tìm niềm vui trong nhà',
                        'description' => 'Khi kế hoạch đi công viên bị hủy vì trời mưa, bé học cách sáng tạo trò chơi cắm trại thú vị ngay trong phòng khách.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Khi gặp tình huống bất khả kháng',
                        'benefit' => 'Tư duy tích cực, linh hoạt biến điều bất lợi thành cơ hội trải nghiệm',
                        'tools' => 'Chăn mền, đèn pin tạo lều cắm trại',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Câu chuyện chú ếch thích tắm mưa và nụ cười rạng rỡ',
                                'url' => 'https://www.youtube.com/watch?v=7wtfhZwyrcc',
                                'duration' => 215,
                            ],
                            [
                                'title' => 'Trò chơi sáng tạo biến ngày mưa thành ngày phiêu lưu',
                                'url' => 'https://www.youtube.com/watch?v=jGflCSq4PCU',
                                'duration' => 300,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::SelfReflection,
                'category_name' => 'Tự phản hồi',
                'description' => 'Khả năng nhìn lại hành vi của bản thân, nhận ra điều mình làm tốt và điều cần cải thiện sau mỗi trải nghiệm.',
                'lessons' => [
                    [
                        'name' => 'Nhật ký 3 điều tuyệt vời trước giờ đi ngủ',
                        'description' => 'Trước khi ngủ, mẹ và bé cùng trò chuyện về 3 việc bé đã làm tốt hôm nay và 1 điều bé muốn làm tốt hơn vào ngày mai.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi tối trước khi ngủ',
                        'benefit' => 'Kích thích khả năng tự đánh giá, hình thành tinh thần cầu tiến liên tục',
                        'tools' => 'Sổ tay vẽ hình nhật ký tí hon',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn thủ thỉ cùng con nhìn lại một ngày ý nghĩa',
                                'url' => 'https://www.youtube.com/watch?v=D1ZYhVpdXbQ',
                                'duration' => 250,
                            ],
                            [
                                'title' => 'Học cách mỉm cười nhận lỗi và quyết tâm sửa đổi',
                                'url' => 'https://www.youtube.com/watch?v=yCjJyiqpAuU',
                                'duration' => 280,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Flexibility,
                'category_name' => 'Linh hoạt',
                'description' => 'Thích nghi nhanh với sự thay đổi, sẵn sàng thử nghiệm cách làm mới khi phương án cũ không hiệu quả.',
                'lessons' => [
                    [
                        'name' => 'Tìm nhiều con đường về đích: Tư duy giải pháp linh hoạt',
                        'description' => 'Khi một cách giải mê cung hay đường đi bị chặn, bé không cáu gắt mà hào hứng tìm lối rẽ thứ hai, thứ ba.',
                        'difficulty' => LessonDifficulty::Hard,
                        'frequency' => '2 lần mỗi tuần',
                        'benefit' => 'Tư duy đa chiều, khả năng xoay sở giải quyết vấn đề vượt trội',
                        'tools' => 'Bản đồ mê cung giấy, các hình khối định hướng',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Mẹo tư duy đường vòng thông minh khi gặp ngõ cụt',
                                'url' => 'https://www.youtube.com/watch?v=m2z9yKk2xM4',
                                'duration' => 260,
                            ],
                            [
                                'title' => 'Thử nghiệm nhiều phương án sáng tạo giải cứu bạn thú bông',
                                'url' => 'https://www.youtube.com/watch?v=Y5b9s6J6Yk8',
                                'duration' => 335,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $sortOrderCategory = 1;

        foreach ($sampleData as $item) {
            $catSlug = Str::slug($item['category_name']) . '-' . $item['key']->value;

            // Tìm hoặc tạo Danh mục bài học
            $category = LessonCategory::updateOrCreate(
                [
                    'age_group_id' => $ageGroupId,
                    'key' => $item['key'],
                ],
                [
                    'pillar' => $item['pillar'],
                    'name' => $item['category_name'],
                    'slug' => $catSlug,
                    'description' => $item['description'],
                    'sort_order' => $sortOrderCategory++,
                    'status' => ActiveStatus::Active,
                ]
            );

            // Tạo các bài học mẫu cho danh mục này
            $sortOrderLesson = 1;
            foreach ($item['lessons'] as $lessonData) {
                $lessonSlug = Str::slug($lessonData['name']) . '-' . $category->id;

                $lesson = Lesson::updateOrCreate(
                    [
                        'lesson_category_id' => $category->id,
                        'name' => $lessonData['name'],
                    ],
                    [
                        'slug' => $lessonSlug,
                        'description' => $lessonData['description'],
                        'content' => "<p>{$lessonData['description']}</p><p><strong>Mục tiêu bài học:</strong> Giúp trẻ phát triển toàn diện theo chuẩn chuyên gia giáo dục sớm.</p>",
                        'difficulty' => $lessonData['difficulty'],
                        'frequency' => $lessonData['frequency'],
                        'benefit' => $lessonData['benefit'],
                        'tools' => $lessonData['tools'],
                        'access_type' => $lessonData['access_type'],
                        'view_count' => rand(15, 250),
                        'sort_order' => $sortOrderLesson++,
                        'status' => ActiveStatus::Active,
                    ]
                );

                // Xóa video cũ nếu có để tạo lại đồng bộ
                $lesson->videos()->delete();

                // Tạo 1 video bài học duy nhất cho mỗi bài học
                $firstVideo = $lessonData['videos'][0] ?? null;
                if ($firstVideo) {
                    $video = new LessonVideo([
                        'title' => $firstVideo['title'],
                        'video_type' => VideoType::YouTube,
                        'video_url' => $firstVideo['url'],
                        'duration_seconds' => $firstVideo['duration'],
                        'sort_order' => 1,
                    ]);
                    $lesson->videos()->save($video);
                }
            }
        }
    }
}
