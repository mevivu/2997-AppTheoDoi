<span @class(['badge', \App\Enums\User\UserStatus::from($status)->badge()])>
    {{ \App\Enums\User\UserStatus::getDescription($status) }}
</span>
