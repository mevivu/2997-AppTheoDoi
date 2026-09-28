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

class LessonAge02Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tìm hoặc tạo nhóm tuổi 0-2 tuổi (id = 2)
        $targetAgeGroup = AgeGroup::where('name', 'like', '%0-2%')->first();

        if (!$targetAgeGroup) {
            $targetAgeGroup = AgeGroup::firstOrCreate(
                ['name' => '0-2 tuổi'],
                [
                    'min_months' => 0,
                    'max_months' => 24,
                    'status' => ActiveStatus::Active,
                    'sort_order' => 1,
                ]
            );
        }

        $ageGroupId = $targetAgeGroup->id;

        // 2. Hệ thống 18 danh mục chuẩn khoa học & bài học mẫu cho trẻ 0 - 24 tháng
        $sampleData = [
            // ==========================================
            // 1. THỂ CHẤT (PQ)
            // ==========================================
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Height,
                'category_name' => 'Chiều cao & Kéo giãn',
                'description' => 'Các bài tập massage, vận động chân tay co duỗi và bơi thủy liệu hỗ trợ phát triển chiều cao tối ưu.',
                'lessons' => [
                    [
                        'name' => 'Bài tập đạp xe trên không và kéo giãn chân cho bé (0-12 tháng)',
                        'description' => 'Động tác co duỗi nhẹ nhàng 2 chân theo nhịp điệu giúp kích thích lưu thông máu và kéo dài xương chi dưới.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi sáng 10-15 phút',
                        'benefit' => 'Kích thích sụn tiếp hợp ở khớp gối, giúp bé ngủ sâu và giải phóng hormone tăng trưởng GH',
                        'tools' => 'Thảm êm, dầu massage thiên nhiên',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn massage chân và bài tập đạp xe co duỗi cho bé',
                                'url' => 'https://www.youtube.com/watch?v=oc4QS2USKmk',
                                'duration' => 210,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Bmi,
                'category_name' => 'Cân nặng & Dinh dưỡng',
                'description' => 'Theo dõi tăng trưởng thể trọng, dinh dưỡng bú mẹ, ăn dặm khoa học và massage kích thích tiêu hóa.',
                'lessons' => [
                    [
                        'name' => 'Massage I LOVE YOU kích thích tiêu hóa và tăng hấp thu dưỡng chất',
                        'description' => 'Kỹ thuật xoa bụng theo chiều kim đồng hồ giúp bé tống hơi thừa, giảm đầy bụng táo bón và ăn ngon miệng.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Sau khi tắm hoặc trước cữ bú 30 phút',
                        'benefit' => 'Kích thích nhu động ruột, tăng khả năng hấp thu dinh dưỡng và hỗ trợ tăng cân khỏe mạnh',
                        'tools' => 'Dầu massage hữu cơ, khăn ủ ấm',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Kỹ thuật massage bụng I Love You chuẩn chuyên gia',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 260,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Strength,
                'category_name' => 'Vận động thô (Lẫy/Bò/Đi)',
                'description' => 'Bài tập tummy time nằm sấp, tập lẫy, ngồi vững, bò vượt chướng ngại vật và chập chững những bước đi đầu tiên.',
                'lessons' => [
                    [
                        'name' => 'Tummy Time - Nằm sấp phát triển cơ cổ và cột sống (0-6 tháng)',
                        'description' => 'Đặt bé nằm sấp mỗi ngày trong tầm kiểm soát giúp phát triển nhóm cơ nâng đỡ đầu và cột sống lưng chắc khỏe.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => '3 - 5 phút mỗi cữ thức, 3 lần/ngày',
                        'benefit' => 'Làm khỏe cơ cổ, vai, ngực; chống hội chứng đầu bẹp và là tiền đề để bé biết lẫy sớm',
                        'tools' => 'Thảm chơi xốp, gối ôm hình trăng khuyết',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Tummy time đúng cách - Bí quyết bé cứng cáp đầu đời',
                                'url' => 'https://www.youtube.com/watch?v=L_LUpnjgPso',
                                'duration' => 240,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Kích thích bé tập bò và vượt chướng ngại vật mềm (7-12 tháng)',
                        'description' => 'Bố trí chướng ngại vật bằng gối mềm kích thích bé dùng lực chân và tay phối hợp bò chéo nhịp nhàng.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Hàng ngày 15 phút',
                        'benefit' => 'Kích hoạt sự liên kết 2 bán cầu não thông qua chuyển động chéo, phát triển thể lực toàn diện',
                        'tools' => 'Gối xốp, thảm chơi, đồ chơi phát nhạc',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Trò chơi kích thích phản xạ tập bò cho bé',
                                'url' => 'https://www.youtube.com/watch?v=M6LoQ8eW2L4',
                                'duration' => 300,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::PQ,
                'key' => LessonCategoryKey::Endurance,
                'category_name' => 'Vận động tinh (Cầm/Nắm)',
                'description' => 'Luyện phản xạ ngón tay, cầm nắm đồ chơi, bốc nhặt hạt và phối hợp tay mắt khéo léo.',
                'lessons' => [
                    [
                        'name' => 'Bài tập bốc nhặt ngón tay càng cua (Pincer Grasp) cho bé 9-18 tháng',
                        'description' => 'Dạy bé dùng ngón trỏ và ngón cái nhặt những mẩu thức ăn nhỏ, rèn luyện sự chính xác của cơ ngón tay.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Trong giờ ăn dặm hoặc giờ chơi',
                        'benefit' => 'Hoàn thiện vận động tinh ngón tay, phối hợp tay - mắt - não bộ, tiền đề cho việc cầm thìa và bút',
                        'tools' => 'Bánh ăn dặm tan trong miệng, khay chia ngăn',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Cách rèn luyện kỹ năng nhặt nhón càng cua cho bé',
                                'url' => 'https://www.youtube.com/watch?v=kY3B11w_aHw',
                                'duration' => 280,
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
                'key' => LessonCategoryKey::Visual,
                'category_name' => 'Thị giác & Nhận biết',
                'description' => 'Kích thích thị giác bằng thẻ đen trắng (0-3 tháng), thẻ màu tương phản cao và nhận diện đồ vật quen thuộc.',
                'lessons' => [
                    [
                        'name' => 'Phương pháp tráo thẻ đen trắng kích hoạt tế bào thị giác (0-3 tháng)',
                        'description' => 'Sử dụng hình vẽ hình học tương phản trắng đen cách mắt bé 20-30cm để thu hút tiêu cự và kích thích vỏ não thị giác.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => '1 - 2 phút mỗi lần, 3 lần mỗi ngày',
                        'benefit' => 'Phát triển dây thần kinh thị giác, tăng khả năng tập trung chú ý và nhận biết hình khối',
                        'tools' => 'Bộ Flashcard đen trắng Glenn Doman',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn tráo thẻ đen trắng chuẩn giáo dục sớm',
                                'url' => 'https://www.youtube.com/watch?v=pWepfJ-8XU0',
                                'duration' => 190,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::Language,
                'category_name' => 'Ngôn ngữ đầu đời',
                'description' => 'Trò chuyện cùng bé, dạy bập bẹ âm thanh đầu đời, nhận diện tiếng động và vốn từ đơn giản.',
                'lessons' => [
                    [
                        'name' => 'Trò chuyện đối đáp Parentese giúp bé phát triển vốn từ vượt trội',
                        'description' => 'Giao tiếp với bé bằng ngữ điệu cao bổng, biểu cảm phong phú và dừng lại chờ bé ê a đáp lại.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mọi lúc khi mẹ thay bỉm, tắm hoặc cho ăn',
                        'benefit' => 'Kích hoạt vùng xử lý ngôn ngữ Broca, giúp bé sớm biết nói và phát âm rõ ràng',
                        'tools' => 'Gương soi mặt an toàn',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Nghệ thuật trò chuyện Parentese kích hoạt ngôn ngữ bé',
                                'url' => 'https://www.youtube.com/watch?v=e_04ZrNroTo',
                                'duration' => 250,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::Memory,
                'category_name' => 'Trí nhớ & Chú ý',
                'description' => 'Trò chơi ú òa (Peekaboo), tìm đồ vật bị giấu, rèn luyện sự chú ý thị giác và khả năng ghi nhớ tạm thời.',
                'lessons' => [
                    [
                        'name' => 'Trò chơi Ú Òa và khái niệm Đối tượng tồn tại (Object Permanence)',
                        'description' => 'Dùng khăn che mặt mẹ hoặc giấu món đồ chơi dưới khăn rồi mở ra, dạy bé bài học về sự tồn tại liên tục của vật chất.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi ngày 10 phút',
                        'benefit' => 'Phát triển tư duy trừu tượng, tăng cường trí nhớ ngắn hạn và giảm lo âu chia ly',
                        'tools' => 'Khăn lụa mềm nhiều màu sắc, đồ chơi quen thuộc',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Trò chơi Ú Òa kinh điển và lợi ích phát triển não bộ',
                                'url' => 'https://www.youtube.com/watch?v=wbXy5K_NnK0',
                                'duration' => 210,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::IQ,
                'key' => LessonCategoryKey::MathLogic,
                'category_name' => 'Logic & Khám phá',
                'description' => 'Nhận biết nguyên nhân - kết quả (bấm nút phát nhạc, thả đồ chơi rơi), phân biệt to - nhỏ, xếp khối gỗ.',
                'lessons' => [
                    [
                        'name' => 'Trò chơi thả bóng vào hộp - Khám phá nguyên nhân và kết quả',
                        'description' => 'Bé thả quả bóng qua lỗ tròn, bóng lăn ra ngoài khay. Bài học rèn luyện tư duy không gian và mối quan hệ nhân quả.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => '2 - 3 lần mỗi tuần',
                        'benefit' => 'Hiểu quy luật nguyên nhân - kết quả, rèn luyện tính kiên trì và phối hợp tay mắt',
                        'tools' => 'Hộp thả bóng Montessori bằng gỗ',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Hướng dẫn chơi hộp thả bóng Montessori cho trẻ',
                                'url' => 'https://www.youtube.com/watch?v=y6Sxv-sUYtM',
                                'duration' => 230,
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
                'description' => 'Nhận diện nụ cười, giọng điệu vui vẻ/yêu thương của bố mẹ và phản hồi bằng ánh mắt, tiếng cười.',
                'lessons' => [
                    [
                        'name' => 'Bắt chước biểu cảm gương mặt - Cầu nối cảm xúc đầu đời',
                        'description' => 'Mẹ làm các biểu cảm vui mừng, ngạc nhiên, mỉm cười gần mặt bé để bé quan sát và bắt chước theo.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Khi bé tỉnh táo, sảng khoái',
                        'benefit' => 'Kích hoạt tế bào thần kinh phản chiếu (Mirror Neurons), đặt nền móng cho trí thông minh cảm xúc EQ',
                        'tools' => 'Gương soi không vỡ',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Bắt chước biểu cảm gương mặt nuôi dưỡng EQ cho bé',
                                'url' => 'https://www.youtube.com/watch?v=HQttS23_j_Y',
                                'duration' => 220,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::SocialCommunication,
                'category_name' => 'Tương tác mẹ con',
                'description' => 'Da tiếp da, giao tiếp ánh mắt, tạo cảm giác an toàn gắn kết bền vững (secure attachment).',
                'lessons' => [
                    [
                        'name' => 'Kỹ thuật ôm ấp da tiếp da và tương tác giao tiếp ánh mắt',
                        'description' => 'Tiếp xúc da kề da giúp điều hòa nhịp tim, thân nhiệt và sản sinh hormone tình yêu oxytocin cho cả mẹ và con.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Hàng ngày',
                        'benefit' => 'Tạo dựng cảm giác an toàn tuyệt đối, giúp bé tự tin khám phá thế giới sau này',
                        'tools' => 'Không gian yên tĩnh, ấm cúng',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Ý nghĩa kỳ diệu của cái ôm da tiếp da',
                                'url' => 'https://www.youtube.com/watch?v=oc4QS2USKmk',
                                'duration' => 200,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::Empathy,
                'category_name' => 'Vỗ về & Thấu cảm',
                'description' => 'Dạy bé cử chỉ âu yếm, ôm hôn, biết chia sẻ đồ chơi và phản ứng dịu dàng với người thân.',
                'lessons' => [
                    [
                        'name' => 'Cùng bé ôm ấp thú bông và học cách vỗ về yêu thương',
                        'description' => 'Mẹ làm mẫu cử chỉ ôm thú bông, xoa đầu, thơm má và hướng dẫn bé thực hiện cùng.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Trước giờ ngủ hoặc sau giờ chơi',
                        'benefit' => 'Nuôi dưỡng lòng trắc ẩn, tính dịu dàng và khả năng đồng cảm với vạn vật xung quanh',
                        'tools' => 'Thú bông vải mềm',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Dạy bé cử chỉ âu yếm và thể hiện tình cảm',
                                'url' => 'https://www.youtube.com/watch?v=a1INNYZ_wG8',
                                'duration' => 240,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::EQ,
                'key' => LessonCategoryKey::EmotionControl,
                'category_name' => 'Tự xoa dịu & Bình tĩnh',
                'description' => 'Hỗ trợ bé tự trấn an khi khóc đêm, đối phó cơn khủng hoảng xa mẹ và chuyển đổi cảm xúc êm dịu.',
                'lessons' => [
                    [
                        'name' => 'Phương pháp 5S (Swaddle, Side, Shush, Swing, Suck) xoa dịu cơn khóc',
                        'description' => 'Áp dụng 5 bước của bác sĩ Harvey Karp: Quấn kén, nằm nghiêng, tiếng suỵt trắng, đung đưa và mút mát.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Khi bé quấy khóc gắt ngủ',
                        'benefit' => 'Tái lập phản xạ dịu êm như trong bụng mẹ, giúp bé tự điều hòa thần kinh và ngủ ngoan',
                        'tools' => 'Khăn quấn cotton thoáng khí, máy tạo tiếng ồn trắng',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Phương pháp 5S dỗ bé khóc ngoan trong 1 phút',
                                'url' => 'https://www.youtube.com/watch?v=L_LUpnjgPso',
                                'duration' => 320,
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
                'category_name' => 'Kiên trì tập vận động',
                'description' => 'Khuyến khích bé tự đứng dậy khi té ngã nhẹ, kiên trì với tay lấy món đồ chơi ngoài tầm với.',
                'lessons' => [
                    [
                        'name' => 'Khích lệ bé tự đứng dậy khi ngã mông nhẹ nhàng (10-18 tháng)',
                        'description' => 'Khi bé ngã nhẹ trên sàn xốp, mẹ bình tĩnh mỉm cười và cổ vũ: "Không sao đâu, con tự đứng lên nhé!" thay vì hoảng hốt bế xốc.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Trong quá trình tập đi',
                        'benefit' => 'Xây dựng tinh thần kiên cường, không bỏ cuộc trước khó khăn và tăng chỉ số vượt khó AQ',
                        'tools' => 'Thảm xốp chống trơn trượt',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Cách ứng xử giúp con không sợ ngã và dũng cảm đứng lên',
                                'url' => 'https://www.youtube.com/watch?v=M6LoQ8eW2L4',
                                'duration' => 260,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Flexibility,
                'category_name' => 'Thích nghi môi trường',
                'description' => 'Làm quen với người lạ, thích nghi không gian mới, tiếng ồn nhẹ và điều kiện sinh hoạt linh hoạt.',
                'lessons' => [
                    [
                        'name' => 'Đưa bé ra ngoài thiên nhiên làm quen môi trường ánh sáng và âm thanh',
                        'description' => 'Dạo chơi công viên, cho bé cảm nhận ánh nắng dịu nhẹ, tiếng chim hót và làn gió thoảng.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => '2 - 3 lần mỗi tuần',
                        'benefit' => 'Tăng cường sức đề kháng, tính linh hoạt và khả năng hòa nhập với môi trường sống tự nhiên',
                        'tools' => 'Xe đẩy em bé, nón mềm che nắng',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Cùng bé dạo chơi và khám phá thiên nhiên xung quanh',
                                'url' => 'https://www.youtube.com/watch?v=kY3B11w_aHw',
                                'duration' => 210,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Patience,
                'category_name' => 'Kiên nhẫn chờ đợi',
                'description' => 'Tập thói quen chờ đến lượt khi ăn uống, không cáu gắt tức thì khi chưa được đáp ứng ngay.',
                'lessons' => [
                    [
                        'name' => 'Tập cho bé thói quen ngồi ghế ăn dặm kiên nhẫn đợi bữa ăn',
                        'description' => 'Tập cho bé ngồi ngoan trên ghế ăn, mẹ đếm từ 1 đến 5 trước khi đưa thìa thức ăn, khen ngợi khi bé ngồi yên chờ đợi.',
                        'difficulty' => LessonDifficulty::Medium,
                        'frequency' => 'Mỗi bữa ăn chính',
                        'benefit' => 'Hình thành tính kiên nhẫn, khả năng tự kiểm soát ham muốn tức thì và tôn trọng nề nếp gia đình',
                        'tools' => 'Ghế ăn dặm High Chair, yếm máng silicon',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Rèn tính kiên nhẫn cho bé từ bàn ăn dặm',
                                'url' => 'https://www.youtube.com/watch?v=pWepfJ-8XU0',
                                'duration' => 270,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::AQ,
                'key' => LessonCategoryKey::Positivity,
                'category_name' => 'Tự tin khám phá',
                'description' => 'Vượt qua sợ hãi khi tiếp xúc chất liệu mới (cát, cỏ, nước) với thái độ tò mò và vui vẻ.',
                'lessons' => [
                    [
                        'name' => 'Trò chơi cảm giác Sensory Bin - Chạm vào các bề mặt tự nhiên',
                        'description' => 'Mẹ chuẩn bị khay chứa yến mạch, đậu hoặc thạch dẻo. Khuyến khích bé thò tay chạm vào và khám phá kết cấu.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Cuối tuần',
                        'benefit' => 'Giải tỏa sợ hãi chất liệu lạ, phát triển xúc giác và nuôi dưỡng tinh thần thám hiểm tích cực',
                        'tools' => 'Khay nhựa, ngũ cốc an toàn ăn được',
                        'access_type' => LessonAccessType::Vip,
                        'videos' => [
                            [
                                'title' => 'Tự làm Sensory Bin kích thích xúc giác tại nhà cho bé',
                                'url' => 'https://www.youtube.com/watch?v=e_04ZrNroTo',
                                'duration' => 230,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 5. THAI GIÁO & HỌC TẬP SỚM (THAI_GIAO)
            // ==========================================
            [
                'pillar' => EducationPillar::THAI_GIAO,
                'key' => LessonCategoryKey::StudyMethod,
                'category_name' => 'Đa giác quan sớm',
                'description' => 'Kích thích thính giác qua âm thanh thiên nhiên, xúc giác qua sách vải sờ chạm và thị giác qua tranh ảnh.',
                'lessons' => [
                    [
                        'name' => 'Phương pháp kích hoạt đa giác quan Montessori cho trẻ dưới 2 tuổi',
                        'description' => 'Kết hợp âm thanh xúc xắc gỗ, chất liệu vải nhung - xơ - mượt và màu sắc kích thích não bộ tiếp nhận đa luồng tín hiệu.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Hàng ngày 20 phút',
                        'benefit' => 'Gia tăng mật độ kết nối sinap nơ-ron thần kinh trong giai đoạn phát triển vàng 0-2 tuổi',
                        'tools' => 'Sách vải sột soạt, vòng gỗ chuông reo',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Giáo dục đa giác quan Montessori cho bé 0-2 tuổi',
                                'url' => 'https://www.youtube.com/watch?v=wbXy5K_NnK0',
                                'duration' => 290,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'pillar' => EducationPillar::THAI_GIAO,
                'key' => LessonCategoryKey::Focus,
                'category_name' => 'Đọc sách tranh & Tập trung',
                'description' => 'Thói quen đọc sách Ehon trước giờ ngủ, tăng khả năng ngồi yên lắng nghe giọng đọc truyền cảm.',
                'lessons' => [
                    [
                        'name' => 'Đọc sách tranh Ehon - Xây dựng thói quen đọc sách từ 6 tháng tuổi',
                        'description' => 'Mẹ cùng bé lật từng trang sách Ehon có hình vẽ to, màu sắc sống động và đọc lời thơ ngắn gọn, giàu nhạc điệu.',
                        'difficulty' => LessonDifficulty::Easy,
                        'frequency' => 'Mỗi tối trước khi đi ngủ',
                        'benefit' => 'Kéo dài thời gian chú ý tập trung, nuôi dưỡng niềm đam mê đọc sách và tư duy hình tượng',
                        'tools' => 'Bộ sách Ehon Nhật Bản bìa cứng bo tròn góc',
                        'access_type' => LessonAccessType::Free,
                        'videos' => [
                            [
                                'title' => 'Cách đọc sách Ehon cuốn hút giúp bé ngồi yên say sưa',
                                'url' => 'https://www.youtube.com/watch?v=y6Sxv-sUYtM',
                                'duration' => 310,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 3. Thực thi chèn hoặc cập nhật idempotent vào database
        $sortOrderCat = 1;
        foreach ($sampleData as $item) {
            $categorySlug = Str::slug($item['category_name']) . '-' . $ageGroupId . '-' . $item['pillar']->value;

            $category = LessonCategory::updateOrCreate(
                [
                    'age_group_id' => $ageGroupId,
                    'pillar' => $item['pillar']->value,
                    'key' => $item['key']->value,
                ],
                [
                    'name' => $item['category_name'],
                    'slug' => $categorySlug,
                    'description' => $item['description'],
                    'sort_order' => $sortOrderCat++,
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
                        'content' => "<p>{$lessonData['description']}</p><p><strong>Mục tiêu bài học:</strong> Giúp trẻ phát triển toàn diện theo chuẩn chuyên gia giáo dục sớm lứa tuổi 0 - 2 tuổi.</p>",
                        'difficulty' => $lessonData['difficulty'],
                        'frequency' => $lessonData['frequency'],
                        'benefit' => $lessonData['benefit'],
                        'tools' => $lessonData['tools'],
                        'access_type' => $lessonData['access_type'],
                        'view_count' => rand(20, 180),
                        'sort_order' => $sortOrderLesson++,
                        'status' => ActiveStatus::Active,
                    ]
                );

                // Xóa video cũ nếu có để tạo lại đồng bộ
                $lesson->videos()->delete();

                // Tạo 1 video bài học chính (mặc định YouTube https://youtu.be/4hMDBAVVD_0)
                $firstVideo = $lessonData['videos'][0] ?? null;
                $defaultYtUrl = 'https://youtu.be/4hMDBAVVD_0';
                $video = new LessonVideo([
                    'title' => $firstVideo['title'] ?? $lessonData['name'],
                    'video_type' => VideoType::YouTube,
                    'video_url' => $defaultYtUrl,
                    'thumbnail' => 'https://img.youtube.com/vi/4hMDBAVVD_0/hqdefault.jpg',
                    'duration_seconds' => 298,
                    'sort_order' => 1,
                ]);
                $lesson->videos()->save($video);
            }
        }
    }
}
