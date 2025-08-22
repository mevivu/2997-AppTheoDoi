<?php

namespace App\Admin\Http\Controllers\WeightHeightWho;

use App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;


class WhoImport implements ToModel
{
    use Importable;

    protected $gender;

    protected WeightHeightWhoRepositoryInterface $whoRepository;

    public function __construct($gender)
    {
        try {
            Log::info("WhoImport constructor started", ['gender' => $gender]);

            $this->gender = $gender;
            $this->whoRepository = App::make(WeightHeightWhoRepositoryInterface::class);

            Log::info("Repository initialized successfully");

            $this->removeDataByGender($gender);

            Log::info("WhoImport constructor completed successfully");

        } catch (Exception $e) {
            Log::error("Error in WhoImport constructor", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'gender' => $gender
            ]);
            throw $e;
        }
    }

    public function removeDataByGender($gender): void
    {
        try {
            Log::info("Starting removeDataByGender", ['gender' => $gender]);

            $data = $this->whoRepository->getBy(
                ['gender' => $gender]
            );

            Log::info("Retrieved data for deletion", [
                'gender' => $gender,
                'count' => $data ? count($data) : 0
            ]);

            if ($data) {
                $deletedCount = 0;
                foreach ($data as $item) {
                    try {
                        $item->delete();
                        $deletedCount++;
                    } catch (Exception $e) {
                        Log::error("Error deleting individual item", [
                            'item_id' => $item->id ?? 'unknown',
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]);
                        throw $e;
                    }
                }
                Log::info("Successfully deleted items", [
                    'gender' => $gender,
                    'deleted_count' => $deletedCount
                ]);
            }

        } catch (Exception $e) {
            Log::error("Error in removeDataByGender", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'gender' => $gender
            ]);
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function model(array $row)
    {
        try {
            Log::debug("Processing row", ['row' => $row]);

            // Validate row data
            if (!is_array($row) || count($row) < 6) {
                Log::warning("Invalid row data", [
                    'row' => $row,
                    'row_count' => is_array($row) ? count($row) : 'not_array'
                ]);
                return null;
            }

            $convertedValues = [];
            for ($i = 0; $i < 6; $i++) {
                try {
                    $convertedValues[$i] = $this->convertToDecimal($row[$i] ?? null);
                } catch (Exception $e) {
                    Log::error("Error converting value to decimal", [
                        'column_index' => $i,
                        'value' => $row[$i] ?? 'null',
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    throw $e;
                }
            }

            Log::debug("Converted values", ['converted_values' => $convertedValues]);

            // Check if all main values are zero
            if ($convertedValues[0] == 0 && $convertedValues[1] == 0 &&
                $convertedValues[2] == 0 && $convertedValues[3] == 0) {
                Log::debug("Skipping row - all main values are zero");
                return null;
            }

            $modelData = [
                'age' => $convertedValues[0],
                'month' => $convertedValues[1],
                'height' => $convertedValues[2],
                'weight' => $convertedValues[3],
                'height_change' => $convertedValues[4],
                'weight_change' => $convertedValues[5],
                'gender' => $this->gender,
                'status' => ActiveStatus::Active,
            ];

            Log::debug("Creating model with data", ['model_data' => $modelData]);

            $result = $this->whoRepository->create($modelData);

            Log::debug("Model created successfully", [
                'created_id' => $result->id ?? 'unknown'
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("Error in model method", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'row' => $row ?? 'null',
                'gender' => $this->gender
            ]);
            throw $e;
        }
    }

    protected function convertToDecimal($value): ?float
    {
        try {
            Log::debug("Converting value to decimal", ['original_value' => $value]);

            if ($value === null || $value === '') {
                Log::debug("Value is null or empty, returning null");
                return null;
            }

            // Validate that value can be converted
            if (!is_numeric(str_replace(',', '.', $value))) {
                Log::warning("Value is not numeric", [
                    'value' => $value,
                    'after_replace' => str_replace(',', '.', $value)
                ]);
                return null;
            }

            $result = floatval(str_replace(',', '.', $value));

            Log::debug("Conversion successful", [
                'original' => $value,
                'result' => $result
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("Error in convertToDecimal", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'value' => $value
            ]);
            throw $e;
        }
    }
}
