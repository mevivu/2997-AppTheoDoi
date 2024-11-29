<span @class(['badge', \App\Enums\Child\ChildStatus::from($status)->badge()])>
    {{ \App\Enums\Child\ChildStatus::getDescription($status) }}
</span>
