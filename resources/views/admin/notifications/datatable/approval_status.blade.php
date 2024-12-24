<span @class(['badge', App\Enums\ApprovalStatus::from($approval_status)->badge()])>
        {{ \App\Enums\ApprovalStatus::getDescription($approval_status) }}</span>
