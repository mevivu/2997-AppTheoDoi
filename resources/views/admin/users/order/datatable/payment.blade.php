<span @class([
    'badge',
    App\Enums\Payment\PaymentMethod::from($payment_method)->badge(),
])>{{ \App\Enums\Payment\PaymentMethod::getDescription($payment_method) }}</span>
