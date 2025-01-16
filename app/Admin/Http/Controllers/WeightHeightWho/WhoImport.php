<?php

namespace App\Admin\Http\Controllers\WeightHeightWho;

use App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;


class WhoImport implements ToModel
{
    use Importable;

    protected $gender;

    protected WeightHeightWhoRepositoryInterface $whoRepository;

    public function __construct($gender)
    {
        $this->gender = $gender;
        $this->whoRepository = App::make(WeightHeightWhoRepositoryInterface::class);
        $this->removeDataByGender($gender);

    }

    public function removeDataByGender($gender): void
    {
        $data = $this->whoRepository->getBy(
            ['gender' => $gender]
        );
        if ($data) {
            foreach ($data as $item) {
                $item->delete();
            }
        }
    }

    /**
     * @throws Exception
     */
    public function model(array $row)
    {
        if ($this->convertToDecimal($row[0]) == 0 && $this->convertToDecimal($row[1]) == 0 &&
            $this->convertToDecimal($row[2]) == 0 && $this->convertToDecimal($row[3]) == 0) {
            return null;
        }
        return $this->whoRepository->create([
            'age' => $this->convertToDecimal($row[0]),
            'month' => $this->convertToDecimal($row[1]), // Column B
            'height' => $this->convertToDecimal($row[2]), // Column C
            'weight' => $this->convertToDecimal($row[3]), // Column D
            'height_change' => $this->convertToDecimal($row[4]), // Column E
            'weight_change' => $this->convertToDecimal($row[5]), // Column F
            'gender' => $this->gender,
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
