<span @class([
    'badge',
    \App\Enums\Order\OrderType::from($order_type)->badge(),
])>
    {{ \App\Enums\Order\OrderType::getDescription($order_type) }}
</span>
