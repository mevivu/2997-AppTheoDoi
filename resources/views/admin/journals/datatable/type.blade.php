<span @class(['badge', \App\Enums\Journal\JournalType::from($type)->badge()])>
    {{ \App\Enums\Journal\JournalType::getDescription($type) }}
</span>
