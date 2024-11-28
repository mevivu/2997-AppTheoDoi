@switch($order_type)
    @case(\App\Enums\Order\OrderType::C_Intercity->value)
        <x-link :href="route('admin.cIntercity.edit', $id)" :title="$code" />
    @break

    @case(\App\Enums\Order\OrderType::C_Delivery->value)
        <x-link :href="route('admin.cDelivery.edit', $id)" :title="$code" />
    @break

    @case(\App\Enums\Order\OrderType::C_RIDE->value)
        <x-link :href="route('admin.cRide.edit', $id)" :title="$code" />
    @break

    @case(\App\Enums\Order\OrderType::C_CAR->value)
        <x-link :href="route('admin.cCar.edit', $id)" :title="$code" />
    @break

    @case(\App\Enums\Order\OrderType::C_Multi->value)
        <x-link :href="route('admin.cMulti.edit', $id)" :title="$code" />
    @break

    @default
        N/A
    @break
@endswitch
