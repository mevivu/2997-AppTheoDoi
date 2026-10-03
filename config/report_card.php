<?php

return [
    /**
     * Bật engine tính toán và lưu calculated_academic_performance, calculation_snapshot
     */
    'engine_enabled' => env('REPORT_CARD_ENGINE', false),

    /**
     * Tự động cập nhật academic_performance theo kết quả tính toán nếu phụ huynh không override
     */
    'auto_classification' => env('REPORT_CARD_AUTO_CLASSIFY', false),

    /**
     * Tự động tính toán và điền/tạo điểm cả năm cho học sinh
     */
    'auto_full_year' => env('REPORT_CARD_AUTO_FULL_YEAR', false),

    /**
     * Cấu hình tệp đính kèm học bạ
     */
    'attachments' => [
        'max_per_evaluation' => 10,
        'max_kb' => 5120, // 5MB
        'disk' => env('REPORT_CARD_DISK', 'local'),
    ],
];
