<?php

use App\Enums\Class\EducationLevel;

return [
    'qualities' => [
        [
            'id' => 1,
            'key' => 'yeu_nuoc',
            'label' => 'Yêu nước',
            'match_names' => ['Yêu nước'],
        ],
        [
            'id' => 2,
            'key' => 'nhan_ai',
            'label' => 'Nhân ái',
            'match_names' => ['Nhân ái'],
        ],
        [
            'id' => 3,
            'key' => 'cham_chi',
            'label' => 'Chăm chỉ',
            'match_names' => ['Chăm chỉ'],
        ],
        [
            'id' => 4,
            'key' => 'trung_thuc',
            'label' => 'Trung thực',
            'match_names' => ['Trung thực', 'Trung thực, kỉ luật'],
        ],
        [
            'id' => 5,
            'key' => 'trach_nhiem',
            'label' => 'Trách nhiệm',
            'match_names' => ['Trách nhiệm', 'Tự tin, trách nhiệm'],
        ],
    ],

    'capabilities' => [
        [
            'id' => 2,
            'key' => 'tu_chu_tu_hoc',
            'label' => 'Năng lực tự chủ và tự học',
            'match_names' => ['Năng lực tự chủ và tự học', 'Tự phục vụ, tự quản'],
        ],
        [
            'id' => 1,
            'key' => 'giao_tiep_hop_tac',
            'label' => 'Năng lực giao tiếp và hợp tác',
            'match_names' => ['Năng lực giao tiếp và hợp tác', 'Giao tiếp và hợp tác'],
        ],
        [
            'id' => 3,
            'key' => 'giai_quyet_van_de',
            'label' => 'Năng lực giải quyết vấn đề và sáng tạo',
            'match_names' => ['Năng lực giải quyết vấn đề và sáng tạo', 'Giải quyết vấn đề và sáng tạo'],
        ],
    ],

    'stages' => [
        EducationLevel::Primary->value => [
            'label' => 'Tiểu học',
            'class_ids' => [1, 2, 3, 4, 5],
            'legend' => [
                'T' => 'Hoàn thành tốt',
                'H' => 'Hoàn thành',
                'C' => 'Chưa hoàn thành',
                'Đ' => 'Đạt',
            ],
            'subjects' => [
                ['key' => 'tieng_viet', 'label' => 'Tiếng Việt', 'match_names' => ['Tiếng Việt']],
                ['key' => 'toan', 'label' => 'Toán', 'match_names' => ['Toán']],
                ['key' => 'ngoai_ngu_1', 'label' => 'Ngoại ngữ 1', 'match_names' => ['Ngoại ngữ 1', 'Tiếng Anh']],
                ['key' => 'dao_duc', 'label' => 'Đạo đức', 'match_names' => ['Đạo đức']],
                ['key' => 'tu_nhien_xa_hoi', 'label' => 'Tự nhiên và Xã hội', 'match_names' => ['Tự nhiên và Xã hội']],
                ['key' => 'lich_su_dia_li', 'label' => 'Lịch sử và Địa lí', 'match_names' => ['Lịch sử và Địa lí', 'Lịch sử và Địa lý']],
                ['key' => 'khoa_hoc', 'label' => 'Khoa học', 'match_names' => ['Khoa học']],
                [
                    'key' => 'tin_hoc_cong_nghe',
                    'label' => 'Tin học và Công nghệ',
                    'match_names' => ['Tin học', 'Công nghệ', 'Tin học và Công nghệ'],
                    'is_composite' => true,
                ],
                ['key' => 'giao_duc_the_chat', 'label' => 'Giáo dục thể chất', 'match_names' => ['Giáo dục thể chất']],
                [
                    'key' => 'nghe_thuat',
                    'label' => 'Nghệ thuật (Âm nhạc, Mĩ thuật)',
                    'match_names' => ['Âm nhạc', 'Mĩ thuật', 'Nghệ thuật (Âm nhạc, Mĩ thuật)'],
                    'is_composite' => true,
                ],
                ['key' => 'hoat_dong_trai_nghiem', 'label' => 'Hoạt động trải nghiệm', 'match_names' => ['Hoạt động trải nghiệm']],
            ],
        ],

        EducationLevel::LowerSecondary->value => [
            'label' => 'THCS',
            'class_ids' => [6, 7, 8, 9],
            'legend' => [
                'Đ' => 'Đạt',
                'CĐ' => 'Chưa đạt',
                'T' => 'Tốt',
                'C' => 'Cần cố gắng',
            ],
            'subjects' => [
                ['key' => 'ngu_van', 'label' => 'Ngữ văn', 'match_names' => ['Ngữ văn']],
                ['key' => 'toan', 'label' => 'Toán', 'match_names' => ['Toán']],
                ['key' => 'ngoai_ngu_1', 'label' => 'Ngoại ngữ 1', 'match_names' => ['Ngoại ngữ 1', 'Tiếng Anh']],
                ['key' => 'giao_duc_cong_dan', 'label' => 'Giáo dục công dân', 'match_names' => ['Giáo dục công dân']],
                ['key' => 'lich_su_dia_li', 'label' => 'Lịch sử và Địa lí', 'match_names' => ['Lịch sử và Địa lí', 'Lịch sử và Địa lý']],
                ['key' => 'khoa_hoc_tu_nhien', 'label' => 'Khoa học tự nhiên', 'match_names' => ['Khoa học tự nhiên']],
                ['key' => 'cong_nghe', 'label' => 'Công nghệ', 'match_names' => ['Công nghệ']],
                ['key' => 'tin_hoc', 'label' => 'Tin học', 'match_names' => ['Tin học']],
                ['key' => 'giao_duc_the_chat', 'label' => 'Giáo dục thể chất', 'match_names' => ['Giáo dục thể chất']],
                [
                    'key' => 'nghe_thuat',
                    'label' => 'Nghệ thuật (Âm nhạc, Mĩ thuật)',
                    'match_names' => ['Âm nhạc', 'Mĩ thuật', 'Nghệ thuật (Âm nhạc, Mĩ thuật)'],
                    'is_composite' => true,
                ],
                [
                    'key' => 'hoat_dong_trai_nghiem_huong_nghiep',
                    'label' => 'Hoạt động trải nghiệm, hướng nghiệp',
                    'match_names' => ['Hoạt động trải nghiệm, hướng nghiệp'],
                ],
                [
                    'key' => 'noi_dung_giao_duc_dia_phuong',
                    'label' => 'Nội dung giáo dục của địa phương',
                    'match_names' => ['Nội dung giáo dục của địa phương'],
                ],
            ],
        ],

        EducationLevel::UpperSecondary->value => [
            'label' => 'THPT',
            'class_ids' => [10, 11, 12],
            'legend' => [
                'Đ' => 'Đạt',
                'CĐ' => 'Chưa đạt',
                'T' => 'Tốt',
                'C' => 'Cần cố gắng',
            ],
            'subjects' => [
                ['key' => 'ngu_van', 'label' => 'Ngữ văn', 'match_names' => ['Ngữ văn']],
                ['key' => 'toan', 'label' => 'Toán', 'match_names' => ['Toán']],
                ['key' => 'ngoai_ngu_1', 'label' => 'Ngoại ngữ 1', 'match_names' => ['Ngoại ngữ 1', 'Tiếng Anh']],
                ['key' => 'lich_su', 'label' => 'Lịch sử', 'match_names' => ['Lịch sử']],
                ['key' => 'giao_duc_the_chat', 'label' => 'Giáo dục thể chất', 'match_names' => ['Giáo dục thể chất']],
                [
                    'key' => 'giao_duc_quoc_phong_an_ninh',
                    'label' => 'Giáo dục quốc phòng và an ninh',
                    'match_names' => ['Giáo dục quốc phòng và an ninh'],
                ],
                ['key' => 'dia_li', 'label' => 'Địa lí', 'match_names' => ['Địa lí', 'Địa lý']],
                [
                    'key' => 'giao_duc_kinh_te_phap_luat',
                    'label' => 'Giáo dục kinh tế và pháp luật',
                    'match_names' => ['Giáo dục kinh tế và pháp luật'],
                ],
                ['key' => 'vat_li', 'label' => 'Vật lí', 'match_names' => ['Vật lí']],
                ['key' => 'hoa_hoc', 'label' => 'Hóa học', 'match_names' => ['Hóa học']],
                ['key' => 'sinh_hoc', 'label' => 'Sinh học', 'match_names' => ['Sinh học']],
                ['key' => 'cong_nghe', 'label' => 'Công nghệ', 'match_names' => ['Công nghệ']],
                ['key' => 'tin_hoc', 'label' => 'Tin học', 'match_names' => ['Tin học']],
                ['key' => 'am_nhac', 'label' => 'Âm nhạc', 'match_names' => ['Âm nhạc']],
                ['key' => 'mi_thuat', 'label' => 'Mĩ thuật', 'match_names' => ['Mĩ thuật']],
                [
                    'key' => 'hoat_dong_trai_nghiem_huong_nghiep',
                    'label' => 'Hoạt động trải nghiệm, hướng nghiệp',
                    'match_names' => ['Hoạt động trải nghiệm, hướng nghiệp'],
                ],
                [
                    'key' => 'noi_dung_giao_duc_dia_phuong',
                    'label' => 'Nội dung giáo dục của địa phương',
                    'match_names' => ['Nội dung giáo dục của địa phương'],
                ],
            ],
        ],
    ],
];
