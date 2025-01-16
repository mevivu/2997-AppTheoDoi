<?php

namespace App\Admin\Http\Controllers\Bmi;

use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Models\Bmi;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;


class BmiImport implements ToModel
{
    use Importable;

    protected $gender;

    protected BmiRepositoryInterface $bmiRepository;

    public function __construct($gender)
    {
        $this->gender = $gender;
        $this->bmiRepository = App::make(BmiRepositoryInterface::class);
        $this->removeDataByGender($gender);

    }

    public function removeDataByGender($gender): void
    {
        $data = $this->bmiRepository->getBy(
            ['gender' => $gender]
        );
        if ($data) {
            foreach ($data as $item) {
                $item->delete();
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function model(array $row): ?Bmi
    {
        $age = $this->convertToDecimal($row[0]);
        if ($age <= 0) {
            return null;
        }
        return $this->bmiRepository->create([
            'age' => $this->convertToDecimal($row[0]),  // cột A
            'gender' => $this->gender,
            'z_score_minus_3' => $this->convertToDecimal($row[1]),  //  cột B
            'z_score_minus_2' => $this->convertToDecimal($row[2]),  //  cột C
            'z_score_minus_1' => $this->convertToDecimal($row[3]),  //  cột D
            'z_score_0' => $this->convertToDecimal($row[4]),        //  cột E
            'z_score_plus_1' => $this->convertToDecimal($row[5]),   //  cột F
            'z_score_plus_2' => $this->convertToDecimal($row[6]),   // cột G
            'z_score_plus_3' => $this->convertToDecimal($row[7]),   //  cột H
            'status' => ActiveStatus::Active,
        ]);
    }

    protected function convertToDecimal($value): ?float
    {
        if ($value === null) {
            return null;
        }
        return floatval(str_replace(',', '.', $value));
    }
}
