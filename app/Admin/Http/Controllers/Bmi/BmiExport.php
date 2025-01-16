<?php

namespace App\Admin\Http\Controllers\Bmi;

use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Models\Bmi;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;


class BmiExport  implements FromQuery, WithHeadings
{
    use Exportable;

    protected $gender;


    public function __construct($gender)
    {
        $this->gender = $gender;

    }


}
