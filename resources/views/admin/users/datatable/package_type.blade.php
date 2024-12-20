<span @class(['badge', \App\Enums\Package\PackageType::from($package_type)->badge()])>
    {{ \App\Enums\Package\PackageType::getDescription($package_type) }}
</span>
