<?php

namespace App\Enums\Vaccination;


use App\Admin\Support\Enum;

enum VaccinationStatus: string
{
    use Enum;

    case Vaccinated = 'vaccinated';
    case NotVaccinated = 'not_vaccinated';

    public function badge(): string
    {
        return match ($this) {
            VaccinationStatus::Vaccinated => 'bg-blue',
            VaccinationStatus::NotVaccinated => 'bg-gray',
        };
    }
}
