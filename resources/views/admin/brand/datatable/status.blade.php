<span @class(['badge', App\Enums\Brand\BrandStatus::from($status)->badge()])>
    {{ App\Enums\Brand\BrandStatus::from($status)->label() }}
</span>
