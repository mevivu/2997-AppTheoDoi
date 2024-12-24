<span @class(['badge', App\Enums\Transaction\TransactionStatus::from($status)->badge()])>
    {{ \App\Enums\Transaction\TransactionStatus::getDescription($status) }}</span>
