<?php

use App\Enums\DefaultStatus;
use App\Enums\DeleteStatus;
use App\Enums\OpenStatus;
use App\Enums\Module\ModuleStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationOption;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use App\Enums\VerifiedStatus;
use App\Enums\Setting\SettingGroup;
use App\Enums\ActiveStatus;
use App\Enums\User\{
    Gender,
    UserStatus,
    UserRoles,
    UserActive,
};

return [

    DeleteStatus::class => [
        DeleteStatus::Deleted->value => 'Đã xóa',
        DeleteStatus::NotDeleted->value => 'Chưa xóa',
    ],


    ActiveStatus::class => [
        ActiveStatus::Active->value => 'Hoạt động',
        ActiveStatus::Deleted->value => 'Đã xóa',
        ActiveStatus::Draft->value => 'Bản nháp',
    ],
    Gender::class => [
        Gender::Male->value => 'Nam',
        Gender::Female->value => 'Nữ',
        Gender::Other->value => 'Khác',
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
        MessageType::DEPOSIT->value => 'Thông báo nạp tiền',
        MessageType::WITHDRAW->value => 'Thông báo rút tiền',
        MessageType::PAYMENT->value => 'Thanh toán',
        MessageType::PAYMENT->value => 'Thanh toán',
        MessageType::TEMPORARY_HOLD->value => 'Tạm giữ',
        MessageType::REPORT->value => 'Báo cáo',
    ],
    NotificationType::class => [
        NotificationType::All->value => 'Thông báo tất cả',
        NotificationType::Customer->value => 'Thông báo nhân viên',
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
];
