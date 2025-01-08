<span @class(['badge', \App\Enums\Product\ProductStatus::tryFrom($status)?->badge() ?? 'bg-secondary'])>
    {{ \App\Enums\Product\ProductStatus::asSelectArray()[$status] ?? __('Unknown') }}
</span>
