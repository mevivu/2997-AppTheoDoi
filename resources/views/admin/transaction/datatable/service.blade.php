<span @class(['badge', App\Enums\Transaction\TransactionEnumService::from($service)->badge()])>
    {{ \App\Enums\Transaction\TransactionEnumService::getDescription($service) }}</span>
