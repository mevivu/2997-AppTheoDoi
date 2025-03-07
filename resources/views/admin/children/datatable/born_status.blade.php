<span @class(['badge', \App\Enums\Child\BornStatus::from($is_born)->badge()])>
    {{ \App\Enums\Child\BornStatus::getDescription($is_born) }}
</span>
