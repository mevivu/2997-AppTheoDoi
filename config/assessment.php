<?php

use App\Enums\Group\GroupType;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonCategoryKey;
use App\Enums\Question\QuestionType;

return [
    /*
    |--------------------------------------------------------------------------
    | Assessment & Recommendation Configuration
    |--------------------------------------------------------------------------
    |
    | Ngưỡng xác định danh mục yếu, số lượng danh mục tối đa được đề xuất,
    | cùng toàn bộ bảng ánh xạ chuẩn giữa question_groups, cột ratings,
    | lesson_categories và các thang điểm phân bậc (bands) kèm hành động cho bố mẹ.
    |
    */

    'weak_threshold' => 6.0,
    'max_weak_categories' => 3,
    'max_recommendation_lessons' => 6,

    'categories' => [
        // ======================== IQ ========================
        QuestionType::IQ->value => [
            'linguistic' => [
                'key' => 'linguistic',
                'rating_column' => 'linguistic',
                'group_type' => GroupType::Linguistic->value,
                'label' => 'Ngôn ngữ',
                'lesson_category_key' => LessonCategoryKey::Language->value,
                'pillar' => EducationPillar::IQ->value,
            ],
            'logic_math' => [
                'key' => 'logic_math',
                'rating_column' => 'logic_math',
                'group_type' => GroupType::LogicMath->value,
                'label' => 'Toán học & Logic',
                'lesson_category_key' => LessonCategoryKey::MathLogic->value,
                'pillar' => EducationPillar::IQ->value,
            ],
            'visual' => [
                'key' => 'visual',
                'rating_column' => 'visual',
                'group_type' => GroupType::Visual->value,
                'label' => 'Hình ảnh & Không gian',
                'lesson_category_key' => LessonCategoryKey::Visual->value,
                'pillar' => EducationPillar::IQ->value,
            ],
            'memory' => [
                'key' => 'memory',
                'rating_column' => 'memory',
                'group_type' => GroupType::Memory->value,
                'label' => 'Trí nhớ',
                'lesson_category_key' => LessonCategoryKey::Memory->value,
                'pillar' => EducationPillar::IQ->value,
            ],
        ],

        // ======================== EQ ========================
        // Ánh xạ chuẩn theo đúng câu hỏi question_groups và cách hiển thị của App:
        // - emotional_awareness -> social_awareness (Nhận thức cảm xúc)
        // - emotional_regulation -> self_regulation (Kiểm soát cảm xúc)
        // - motivation -> decision_making (Động lực)
        // - empathy -> relationship_management (Đồng cảm)
        // - social_skills -> optimism (Kĩ năng xã hội)
        QuestionType::EQ->value => [
            'social_awareness' => [
                'key' => 'social_awareness',
                'rating_column' => 'social_awareness',
                'group_type' => GroupType::EmotionalAwareness->value,
                'label' => 'Nhận thức cảm xúc',
                'lesson_category_key' => LessonCategoryKey::EmotionRecognition->value,
                'pillar' => EducationPillar::EQ->value,
            ],
            'self_regulation' => [
                'key' => 'self_regulation',
                'rating_column' => 'self_regulation',
                'group_type' => GroupType::EmotionalRegulation->value,
                'label' => 'Kiểm soát cảm xúc',
                'lesson_category_key' => LessonCategoryKey::EmotionControl->value,
                'pillar' => EducationPillar::EQ->value,
            ],
            'decision_making' => [
                'key' => 'decision_making',
                'rating_column' => 'decision_making',
                'group_type' => GroupType::Motivation->value,
                'label' => 'Động lực',
                'lesson_category_key' => LessonCategoryKey::Motivation->value,
                'pillar' => EducationPillar::EQ->value,
            ],
            'relationship_management' => [
                'key' => 'relationship_management',
                'rating_column' => 'relationship_management',
                'group_type' => GroupType::Empathy->value,
                'label' => 'Đồng cảm',
                'lesson_category_key' => LessonCategoryKey::Empathy->value,
                'pillar' => EducationPillar::EQ->value,
            ],
            'optimism' => [
                'key' => 'optimism',
                'rating_column' => 'optimism',
                'group_type' => GroupType::SocialSkills->value,
                'label' => 'Kĩ năng xã hội',
                'lesson_category_key' => LessonCategoryKey::SocialCommunication->value,
                'pillar' => EducationPillar::EQ->value,
            ],
        ],

        // ======================== AQ ========================
        QuestionType::AQ->value => [
            'endurance' => [
                'key' => 'endurance',
                'rating_column' => 'endurance',
                'group_type' => GroupType::Tolerance->value,
                'label' => 'Khả năng chịu đựng',
                'lesson_category_key' => LessonCategoryKey::Resilience->value,
                'pillar' => EducationPillar::AQ->value,
            ],
            'flexibility' => [
                'key' => 'flexibility',
                'rating_column' => 'flexibility',
                'group_type' => GroupType::Flexibility->value,
                'label' => 'Tính linh hoạt',
                'lesson_category_key' => LessonCategoryKey::Flexibility->value,
                'pillar' => EducationPillar::AQ->value,
            ],
            'perseverance' => [
                'key' => 'perseverance',
                'rating_column' => 'perseverance',
                'group_type' => GroupType::Perseverance->value,
                'label' => 'Tính kiên trì',
                'lesson_category_key' => LessonCategoryKey::Patience->value,
                'pillar' => EducationPillar::AQ->value,
            ],
            'positivity' => [
                'key' => 'positivity',
                'rating_column' => 'positivity',
                'group_type' => GroupType::Positivity->value,
                'label' => 'Tính tích cực',
                'lesson_category_key' => LessonCategoryKey::Positivity->value,
                'pillar' => EducationPillar::AQ->value,
            ],
            'self_reflection' => [
                'key' => 'self_reflection',
                'rating_column' => 'self_reflection',
                'group_type' => GroupType::SelfReflection->value,
                'label' => 'Khả năng tự phản hồi',
                'lesson_category_key' => LessonCategoryKey::SelfReflection->value,
                'pillar' => EducationPillar::AQ->value,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Thang điểm đánh giá, Nhãn, Đặc điểm và Hành động cho Bố Mẹ
    |--------------------------------------------------------------------------
    */
    'bands' => [
        // ======================== AQ ========================
        QuestionType::AQ->value => [
            [
                'band' => 'B1',
                'max_score' => 3.0,
                'label' => 'Quitter',
                'description' => 'Tiêu cực, dễ bỏ cuộc (Quitter)',
                'parent_action' => 'Hãy bắt đầu bằng những nhiệm vụ thật nhỏ, vừa sức để con dễ đạt được thành công. Khi con gặp khó khăn, bố mẹ hãy động viên con thử thêm một lần, hướng dẫn từng bước và ghi nhận nỗ lực thay vì chỉ chú trọng kết quả.',
            ],
            [
                'band' => 'B2',
                'max_score' => 5.0,
                'label' => 'Quitter',
                'description' => 'Miễn cưỡng hoặc chưa sẵn lòng đối mặt với khó khăn (Quitter)',
                'parent_action' => 'Khuyến khích con chủ động thử những việc hơi khó hơn khả năng hiện tại. Hãy cho con lựa chọn cách thực hiện, chia nhiệm vụ thành từng bước nhỏ và đồng hành khi cần thiết, nhưng đừng làm thay con ngay khi con gặp khó khăn.',
            ],
            [
                'band' => 'B3',
                'max_score' => 7.0,
                'label' => 'Camper',
                'description' => 'Tích cực nhưng đôi khi cần hỗ trợ (Camper)',
                'parent_action' => 'Hãy giúp con hình thành thói quen kiên trì đến cùng với những mục tiêu đã đặt ra. Khi con muốn bỏ cuộc, bố mẹ hãy hỏi: “Con đang gặp khó khăn ở đâu? Chúng ta có thể thử cách nào khác?” để con học cách tự tìm giải pháp.',
            ],
            [
                'band' => 'B4',
                'max_score' => 9.0,
                'label' => 'Climber',
                'description' => 'Tích cực, tự lực và có sự cố gắng (Climber)',
                'parent_action' => 'Giao cho con những thử thách mới phù hợp với độ tuổi, khuyến khích con tự lập kế hoạch, thực hiện và đánh giá kết quả. Hãy giúp con hiểu rằng thất bại là cơ hội học hỏi và biết linh hoạt thay đổi phương pháp khi cần.',
            ],
            [
                'band' => 'B5',
                'max_score' => 10.0,
                'label' => 'Climber',
                'description' => 'Rất tích cực, kiên trì, vượt khó tốt (Climber)',
                'parent_action' => 'Tiếp tục tạo cơ hội để con chinh phục những mục tiêu có ý nghĩa và giúp đỡ người khác vượt qua khó khăn. Khuyến khích con cân bằng giữa nỗ lực, nghỉ ngơi và điều chỉnh mục tiêu; kiên trì không có nghĩa là phải cố chấp theo đuổi một cách làm không còn phù hợp.',
            ],
        ],

        // ======================== EQ ========================
        QuestionType::EQ->value => [
            [
                'band' => 'B1',
                'max_score' => 3.0,
                'label' => 'Tiêu cực',
                'description' => 'Tiêu cực, khó kiểm soát cảm xúc (tiêu cực).',
                'parent_action' => 'Hãy giúp con gọi tên cảm xúc như vui, buồn, tức giận hoặc lo lắng. Khi con mất bình tĩnh, bố mẹ nên giữ giọng nói nhẹ nhàng, giúp con bình tĩnh rồi mới cùng con tìm cách giải quyết. Hướng dẫn con diễn đạt cảm xúc bằng lời nói thay vì la hét hoặc hành động bộc phát.',
            ],
            [
                'band' => 'B2',
                'max_score' => 5.0,
                'label' => 'Tiêu cực ẩn',
                'description' => 'Tiêu cực, nhưng không thể hiện ra ngoài (tiêu cực ẩn)',
                'parent_action' => 'Hãy tạo không gian an toàn để con chia sẻ mà không sợ bị trách mắng. Bố mẹ có thể hỏi: “Hôm nay có chuyện gì khiến con buồn không?” và kiên nhẫn lắng nghe, không ép con phải trả lời ngay. Khuyến khích con thể hiện cảm xúc bằng lời nói, tranh vẽ hoặc viết nhật ký.',
            ],
            [
                'band' => 'B3',
                'max_score' => 7.0,
                'label' => 'Trung tính',
                'description' => 'Trung tính, có cố gắng kiểm soát nhưng chưa hoàn toàn tự tin (Trung tính)',
                'parent_action' => 'Hãy khuyến khích con chủ động nói ra cảm xúc, nhu cầu và suy nghĩ của mình. Cùng con luyện cách xử lý những tình huống thường gặp như bất đồng với bạn, bị từ chối hoặc mắc lỗi. Ghi nhận mỗi lần con biết bình tĩnh, lắng nghe và tìm cách giải quyết phù hợp.',
            ],
            [
                'band' => 'B4',
                'max_score' => 9.0,
                'label' => 'Tích cực',
                'description' => 'Tích cực, biết cách kiểm soát và xử lý tình huống (tích cực)',
                'parent_action' => 'Tiếp tục tạo cơ hội để con thực hành giải quyết mâu thuẫn, đặt mình vào vị trí của người khác và diễn đạt ý kiến một cách tôn trọng. Khuyến khích con tự tìm giải pháp trước khi nhờ bố mẹ hỗ trợ.',
            ],
            [
                'band' => 'B5',
                'max_score' => 10.0,
                'label' => 'Rất tích cực',
                'description' => 'Rất tích cực, dễ dàng kiểm soát cảm xúc và giúp người khác (Rất Tích cực)',
                'parent_action' => 'Khuyến khích con phát huy khả năng đồng cảm, hòa giải mâu thuẫn và hỗ trợ bạn bè khi phù hợp. Đồng thời, giúp con hiểu rằng cảm xúc của bản thân cũng quan trọng; con có quyền nói “không”, đặt giới hạn và tìm người lớn đáng tin cậy khi cần.',
            ],
        ],

        // ======================== IQ ========================
        QuestionType::IQ->value => [
            [
                'band' => 'B1',
                'max_score' => 3.0,
                'label' => 'Kém',
                'description' => 'Trẻ gặp khó khăn trong một số hoạt động như ghi nhớ, ngôn ngữ, tư duy logic hoặc nhận biết hình ảnh.',
                'parent_action' => 'Hãy bắt đầu bằng các trò chơi đơn giản, ngắn và phù hợp với độ tuổi. Hướng dẫn con từng bước, lặp lại nhẹ nhàng và khuyến khích con diễn đạt suy nghĩ của mình.',
            ],
            [
                'band' => 'B2',
                'max_score' => 5.0,
                'label' => 'Trung bình',
                'description' => 'Trẻ đã có một số kỹ năng nền tảng nhưng còn cần luyện tập để thực hiện ổn định hơn.',
                'parent_action' => 'Dành 10–15 phút mỗi ngày cùng con đọc sách, chơi trò ghi nhớ, giải toán Logic hoặc ghép hình. Tăng dần độ khó khi con đã quen và tránh gây áp lực về kết quả.',
            ],
            [
                'band' => 'B3',
                'max_score' => 7.0,
                'label' => 'Khá cao',
                'description' => 'Trẻ thực hiện được nhiều nhiệm vụ phù hợp với độ tuổi nhưng vẫn có lĩnh vực cần củng cố.',
                'parent_action' => 'Khuyến khích con giải câu đố, kể lại câu chuyện, tìm quy luật và tự giải thích cách làm. Quan sát lĩnh vực con còn khó khăn để lựa chọn bài tập phù hợp.',
            ],
            [
                'band' => 'B4',
                'max_score' => 9.0,
                'label' => 'Rất cao',
                'description' => 'Trẻ thể hiện khả năng tốt trong các nhiệm vụ được đánh giá, biết vận dụng kiến thức để giải quyết vấn đề.',
                'parent_action' => 'Tạo cơ hội cho con tiếp cận câu đố mới, bài toán tư duy, trò chơi ngôn ngữ và hoạt động sáng tạo. Khuyến khích con thử nhiều cách giải quyết khác nhau.',
            ],
            [
                'band' => 'B5',
                'max_score' => 10.0,
                'label' => 'Xuất sắc',
                'description' => 'Trẻ đạt kết quả cao trong các nhiệm vụ đánh giá hiện tại.',
                'parent_action' => 'Mở rộng thử thách phù hợp với sở thích và độ tuổi, khuyến khích con tự đặt câu hỏi, khám phá và giải thích cách suy nghĩ. Không nên chỉ tập trung vào điểm số hoặc liên tục giao bài khó hơn.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Thang phân loại trí tuệ Wechsler chuẩn quốc tế (Deviation IQ)
    |--------------------------------------------------------------------------
    | Áp dụng cho các bài kiểm tra IQ chuẩn hóa theo phân phối chuẩn Gaussian:
    | IQ = 100 + 15 * (Raw - Mean) / StdDev.
    | Mean = 100, StdDev = 15.
    */
    'wechsler_bands' => [
        [
            'band' => 'W1',
            'max_score' => 69,
            'label' => 'Rất thấp (Extremely Low)',
            'description' => 'Trẻ gặp nhiều khó khăn trong các nhiệm vụ tư duy, nhận thức và xử lý thông tin so với các bạn cùng độ tuổi (thuộc nhóm dưới 2.2% dân số).',
            'parent_action' => 'Bố mẹ nên kiên nhẫn đồng hành cùng con từ những bước cơ bản nhất, chia nhỏ từng nhiệm vụ và lặp lại thường xuyên. Nên tham khảo ý kiến chuyên gia tâm lý hoặc bác sĩ nhi để có lộ trình can thiệp và hỗ trợ phát triển phù hợp.',
        ],
        [
            'band' => 'W2',
            'max_score' => 79,
            'label' => 'Ranh giới (Borderline)',
            'description' => 'Trẻ có biểu hiện phát triển chậm hơn mức trung bình của lứa tuổi ở một số lĩnh vực nhận thức (khoảng 6.7% dân số).',
            'parent_action' => 'Dành nhiều thời gian hướng dẫn con trực quan, sử dụng đồ chơi giáo dục và hình ảnh sinh động. Khích lệ mỗi tiến bộ nhỏ của con và tạo môi trường học tập không áp lực.',
        ],
        [
            'band' => 'W3',
            'max_score' => 89,
            'label' => 'Dưới trung bình (Low Average)',
            'description' => 'Trẻ có khả năng nhận thức ở mức dưới trung bình một chút so với nhóm tuổi, cần thêm thời gian để củng cố kỹ năng nền tảng (khoảng 16.1% dân số).',
            'parent_action' => 'Thường xuyên cùng con ôn tập kiến thức, đọc sách và chơi các trò chơi rèn luyện trí nhớ, tư duy logic hàng ngày. Kiên nhẫn giải thích và động viên con tự tin hơn.',
        ],
        [
            'band' => 'W4',
            'max_score' => 109,
            'label' => 'Trung bình (Average)',
            'description' => 'Trẻ có mức độ phát triển trí tuệ và tư duy nhận thức hoàn toàn bình thường, tương đương với đa số các bạn cùng trang lứa (khoảng 50.0% dân số).',
            'parent_action' => 'Duy trì các hoạt động học tập, đọc sách và vui chơi lành mạnh. Khuyến khích con tò mò, khám phá các chủ đề mới và phát triển đều các nhóm kỹ năng ngôn ngữ, logic và hình ảnh.',
        ],
        [
            'band' => 'W5',
            'max_score' => 119,
            'label' => 'Trên trung bình (High Average)',
            'description' => 'Trẻ thể hiện năng lực nhận thức và tư duy tốt hơn mức trung bình của lứa tuổi, tiếp thu nhanh và nhạy bén (khoảng 16.1% dân số).',
            'parent_action' => 'Cung cấp cho con những tài liệu, câu đố và bài tập mang tính thử thách cao hơn. Khuyến khích con tự suy nghĩ nhiều cách giải quyết vấn đề và phát triển tư duy phản biện.',
        ],
        [
            'band' => 'W6',
            'max_score' => 129,
            'label' => 'Ưu tú (Superior)',
            'description' => 'Trẻ có trí tuệ phát triển vượt trội, khả năng logic, ngôn ngữ và ghi nhớ xuất sắc so với độ tuổi (thuộc nhóm Top 6.7% dân số).',
            'parent_action' => 'Tạo điều kiện để con thỏa sức khám phá những lĩnh vực chuyên sâu con yêu thích như khoa học, toán học, nghệ thuật. Đồng thời chú trọng rèn luyện kỹ năng cảm xúc (EQ) và vượt khó (AQ) để con phát triển toàn diện.',
        ],
        [
            'band' => 'W7',
            'max_score' => 999,
            'label' => 'Rất ưu tú (Very Superior)',
            'description' => 'Trẻ sở hữu tiềm năng trí tuệ đặc biệt xuất sắc, tiếp thu và xử lý thông tin vượt xa các bạn đồng trang lứa (thuộc nhóm Top 2.2% cao nhất của dân số).',
            'parent_action' => 'Khuyến khích con theo đuổi đam mê, tạo cơ hội cho con tiếp xúc với các bài toán mở và dự án sáng tạo. Đồng hành cùng con giữ vững sự khiêm tốn, biết lắng nghe và cân bằng giữa trí tuệ và đời sống tinh thần.',
        ],
    ],
];
