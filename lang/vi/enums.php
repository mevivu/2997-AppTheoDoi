<?php

use App\Enums\ApprovalStatus;
use App\Enums\Brand\BrandStatus;
use App\Enums\Child\BornStatus;
use App\Enums\Class\LevelGroup;
use App\Enums\DefaultStatus;
use App\Enums\Group\GroupType;
use App\Enums\Permission\PermissionType;
use App\Enums\Question\AgeGroup;
use App\Enums\Random;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\DeleteStatus;
use App\Enums\FeaturedStatus;
use App\Enums\OpenStatus;
use App\Enums\Module\ModuleStatus;
use App\Enums\Vaccination\VaccinationStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationOption;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Enums\Post\PostStatus;
use App\Enums\PostCategory\PostCategoryStatus;
use App\Enums\PriorityStatus;
use App\Enums\Journal\JournalType;
use App\Enums\Slider\SliderStatus;
use App\Enums\VerifiedStatus;
use App\Enums\Setting\SettingGroup;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use App\Enums\Child\ChildStatus;
use App\Enums\Support\SupportType;
use App\Enums\Answser\AnswerType;
use App\Enums\Guide\GuideType;
use App\Enums\User\{
    Gender,
    UserStatus,
    UserRoles,
    UserActive,
};

return [
    AnswerType::class => [
        AnswerType::Image->value => 'Hình ảnh',
        AnswerType::Normal->value => 'Văn bản',
    ],
    Random::class => [
        Random::YES->value => 'Có',
    ],
    LevelGroup::class => [
        LevelGroup::Junior->value => 'Nhỏ hơn 6',
        LevelGroup::Senior->value => 'Lớn hơn hoặc bằng 6',
    ],
    AgeGroup::class => [
        AgeGroup::Under_10->value => 'Dưới 10 tuổi',
        AgeGroup::Above_10->value => 'Trên 10 tuổi',
    ],
    GroupType::class => [
        GroupType::Empathy->value => 'Đồng cảm',
        GroupType::Motivation->value => 'Động lực',
        GroupType::SocialSkills->value => 'Kỹ năng xã hội',
        GroupType::EmotionalRegulation->value => 'Kiểm soát cảm xúc',
        GroupType::EmotionalAwareness->value => 'Nhận thức cảm xúc',
        GroupType::Tolerance->value => 'Khả năng chịu đựng',
        GroupType::Flexibility->value => 'Tính linh hoạt',
        GroupType::Perseverance->value => 'Tính kiên trì',
        GroupType::Positivity->value => 'Tính tích cực',
        GroupType::SelfReflection->value => 'Khả năng tự phản hồi',

    ],
    PermissionType::class => [
        PermissionType::USER->value => 'Người dùng',
        PermissionType::ADMIN->value => 'ADMIN',
    ],
    SupportType::class => [
        SupportType::HelpCenter->value => 'Trung tâm trợ giúp',
        SupportType::Guide->value => 'Hướng dẫn sử dụng',
    ],
    ExerciseType::class => [
        ExerciseType::PHYSICAL->value => 'Bài tập thể chất',
        ExerciseType::POWER->value => 'Bài tập sức mạnh',
    ],
    DeleteStatus::class => [
        DeleteStatus::Deleted->value => 'Đã xóa',
        DeleteStatus::NotDeleted->value => 'Chưa xóa',
    ],
    TransactionStatus::class => [
        TransactionStatus::Pending->value => 'Chưa giải quyết',
        TransactionStatus::Confirmed->value => 'Đã xác nhận',
    ],
    BornStatus::class => [
        BornStatus::Born->value => 'Đã sinh',
        BornStatus::Unborn->value => 'Chưa sinh',
    ],
    ActiveStatus::class => [
        ActiveStatus::Active->value => 'Hoạt động',
        ActiveStatus::Deleted->value => 'Đã xóa',
        ActiveStatus::Draft->value => 'Bản nháp',
    ],

    PackageStatus::class => [
        PackageStatus::Active->value => 'Hoạt động',
        PackageStatus::Deleted->value => 'Đã xóa',
        PackageStatus::Draft->value => 'Bản nháp',
    ],
    ChildStatus::class => [
        ChildStatus::Active->value => 'Hoạt động',
        ChildStatus::Draft->value => 'Bản nháp',
        ChildStatus::Deleted->value => 'Đã xóa',
    ],
    JournalType::class => [
        JournalType::Prescription->value => "Đơn thuốc",
        JournalType::Moment->value => "khoảnh khắc"
    ],
    PackageType::class => [
        PackageType::Trial->value => 'Dùng thử',
        PackageType::OneMonth->value => '1 tháng',
        PackageType::ThreeMonths->value => '3 tháng',
        PackageType::SixMonths->value => '6 tháng',
        PackageType::OneYear->value => '1 năm',
        PackageType::TwoYear->value => '2 năm',
        PackageType::Normal->value => 'Thường',
    ],
    Gender::class => [
        Gender::Male->value => 'Nam',
        Gender::Female->value => 'Nữ',
        Gender::Unknown->value => 'Chưa biết',
    ],
    VaccinationStatus::class => [
        VaccinationStatus::Vaccinated->value => 'Đã tiêm vắc-xin',
        VaccinationStatus::NotVaccinated->value => 'Chưa tiêm vắc-xin',
    ],
    PostCategoryStatus::class => [
        PostCategoryStatus::Published => 'Đã xuất bản',
        PostCategoryStatus::Draft => 'Bản nháp'
    ],
    PostStatus::class => [
        PostStatus::Published->value => 'Đã xuất bản',
        PostStatus::Draft->value => 'Bản nháp'
    ],
    PriorityStatus::class => [
        PriorityStatus::Priority->value => 'Ưu tiên',
        PriorityStatus::NotPriority->value => 'Không ưu tiên'
    ],
    FeaturedStatus::class => [
        FeaturedStatus::Featured->value => 'Nổi bật',
        FeaturedStatus::Featureless->value => 'Không nổi bật'
    ],
    SliderStatus::class => [
        SliderStatus::Active => 'Hoạt động',
        SliderStatus::Inactive => 'Ngưng hoạt động'
    ],
    NotificationStatus::class => [
        NotificationStatus::READ->value => 'Đã đọc',
        NotificationStatus::NOT_READ->value => 'Chưa đọc',
    ],


    NotificationOption::class => [
        NotificationOption::All->value => 'Cho tất cả',
        NotificationOption::One->value => 'Cho một người',
    ],
    MessageType::class => [
        MessageType::UNCLASSIFIED->value => 'Không phân loại',
        MessageType::PAYMENT->value => 'Thanh toán',
    ],
    NotificationType::class => [
        NotificationType::All->value => 'Thông báo tất cả',
        NotificationType::Customer->value => 'Thông báo người dùng',
    ],

    ApprovalStatus::class => [
        ApprovalStatus::PENDING->value => 'Chưa duyệt',
        ApprovalStatus::ACTIVE->value => 'Đã duyệt',
        ApprovalStatus::REJECTED->value => 'Từ chối',
    ],

    UserRoles::class => [
        UserRoles::Customer->value => 'Khách hàng',
        UserRoles::Driver->value => 'Tài xế',
    ],
    UserActive::class => [
        UserActive::Active->value => 'Xác nhận',
    ],
    UserStatus::class => [
        UserStatus::Active->value => 'Hoạt động',
        UserStatus::PendingConfirmation->value => 'Chờ xác nhận',
        UserStatus::Lock->value => 'Đã khoá',
        UserStatus::Inactive->value => 'Không hoạt động',
    ],

    DefaultStatus::class => array(
        DefaultStatus::Published->value => 'Đã xuất bản',
        DefaultStatus::Draft->value => 'Bản nháp',
        DefaultStatus::Deleted->value => 'Đã xoá',
    ),

    SettingGroup::class => [
        SettingGroup::General => 'Chung',
        SettingGroup::UserDiscount => 'Chiết khấu mua hàng theo cấp TV',
        SettingGroup::UserUpgrade => 'SL SP nâng cấp TV',
    ],
    ModuleStatus::class => [
        ModuleStatus::ChuaXong => 'Chưa xong',
        ModuleStatus::DaXong => 'Đã xong',
        ModuleStatus::DaDuyet => 'Đã duyệt'
    ],

    VerifiedStatus::class => [
        VerifiedStatus::Active->value => 'Đã xác nhận',
        VerifiedStatus::Pending->value => 'Chờ xác nhận',

    ],
    OpenStatus::class => [
        OpenStatus::ON->value => 'Mở',
        OpenStatus::OFF->value => 'Tắt',

    ],
    BrandStatus::class => [
        BrandStatus::Active->value => 'Hoạt động',
        BrandStatus::Deleted->value => 'Đã xóa',
        BrandStatus::Draft->value => 'Bản nháp',
    ],
    GuideType::class => [
        GuideType::Strength->value => 'Sức mạnh',
        GuideType::Endurance->value => 'Sức bền',
        GuideType::Develop->value=> 'Phát triển',
    ]
];
