<span @class([
    'badge',
    \App\Enums\User\Gender::from($gender)->badge(),

])>
      {{ \App\Enums\User\Gender::getDescription($gender) }}
</span>
