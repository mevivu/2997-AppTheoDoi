<span @class(['badge', App\Enums\Package\PackageType::from($package_type->value)->badge()])>
        {{ \App\Enums\Package\PackageType::getDescription($package_type->value) }}</span>
