<?php

namespace App\Admin\Exel\Clinic;

use App\Admin\Repositories\Clinic\ClinicRepositoryInterface;
use App\Admin\Repositories\ClinicType\ClinicTypeRepositoryInterface;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Repositories\District\DistrictRepositoryInterface;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Traits\ImageSystem;
use App\Traits\ImportValidationTrait;
use Exception;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ClinicImport implements ToModel, WithStartRow
{
    use Importable, ImportValidationTrait;

    protected ClinicRepositoryInterface $clinicRepository;
    protected ClinicTypeRepositoryInterface $clinicTypeRepository;
    protected ProvinceRepositoryInterface $provinceRepository;
    protected DistrictRepositoryInterface $districtRepository;
    protected WardRepositoryInterface $wardRepository;

    // Static error variable
    protected static array $errors = [];
    protected static int $currentRow = 2;

    public function __construct()
    {
        $this->clinicRepository = App::make(ClinicRepositoryInterface::class);
        $this->clinicTypeRepository = App::make(ClinicTypeRepositoryInterface::class);
        $this->provinceRepository = App::make(ProvinceRepositoryInterface::class);
        $this->districtRepository = App::make(DistrictRepositoryInterface::class);
        $this->wardRepository = App::make(WardRepositoryInterface::class);
    }

    /**
     * @throws Exception
     */
    public function model(array $row)
    {
        if ($this->isRowEmpty($row, [0])) {
            self::$currentRow++;
            return null;
        }

        $validationErrors = $this->validateRow($row);

        if (!empty($validationErrors)) {
            $this->addRowErrors($validationErrors, self::$currentRow, self::$errors);
        }

        self::$currentRow++;

        if (empty($validationErrors)) {
            return $this->createClinicRecord($row);
        }

        return null;
    }

    /**
     * Validate dữ liệu của một dòng theo đúng cấu trúc Excel từ ảnh
     */
    private function validateRow(array $row): array
    {
        $errors = [];

        // Sanitize data theo cấu trúc Excel từ ảnh (9 cột)
        $name = $this->sanitizeString($row[0] ?? '');              // Tên
        $hotline = $this->sanitizeString($row[1] ?? '');           // Hotline
        $clinicTypeName = $this->sanitizeString($row[2] ?? '');    // Loại phòng khám
        $openingTime = $this->sanitizeString($row[3] ?? '');       // Giờ mở cửa
        $closingTime = $this->sanitizeString($row[4] ?? '');       // Giờ đóng cửa
        $address = $this->sanitizeString($row[5] ?? '');           // Địa chỉ
        $wardId = $this->sanitizeString($row[6] ?? '');            // Phường
        $provinceId = $this->sanitizeString($row[7] ?? '');        // Thành Phố
        $schedule = $this->sanitizeString($row[8] ?? '');          // Lịch

        // Validate required fields
        $errors = array_merge($errors, $this->validateRequired($name, 'Tên phòng khám'));

        // Validate lengths
        $errors = array_merge($errors, $this->validateLength($name, 'Tên phòng khám', 255, 2));
        $errors = array_merge($errors, $this->validateLength($address, 'Địa chỉ', 500));

        // Validate hotline format
        if (!empty($hotline)) {
            if (!preg_match('/^[0-9+\-\s()]{8,15}$/', $hotline)) {
                $errors[] = "Hotline không đúng định dạng";
            }
        }

        // Parse và validate time format
        $parsedOpeningTime = null;
        $parsedClosingTime = null;

        if (!empty($openingTime)) {
            $parsedOpeningTime = $this->parseTimeFromExcel($openingTime);
            if ($parsedOpeningTime === null) {
                $errors[] = "Giờ mở cửa không đúng định dạng (hỗ trợ: HH:MM, H:MM AM/PM, HH:MM:SS AM/PM)";
            }
        }

        if (!empty($closingTime)) {
            $parsedClosingTime = $this->parseTimeFromExcel($closingTime);
            if ($parsedClosingTime === null) {
                $errors[] = "Giờ đóng cửa không đúng định dạng (hỗ trợ: HH:MM, H:MM AM/PM, HH:MM:SS AM/PM)";
            }
        }

        // Validate time logic
        if ($parsedOpeningTime && $parsedClosingTime) {
            if (strtotime($parsedOpeningTime) >= strtotime($parsedClosingTime)) {
                $errors[] = "Giờ mở cửa phải nhỏ hơn giờ đóng cửa";
            }
        }

        return $errors;
    }

    /**
     * Tìm hoặc tạo ClinicType bằng tên
     * @throws Exception
     */
    private function findOrCreateClinicType(string $name): ?object
    {
        if (empty($name)) {
            return null;
        }

        $clinicType = $this->clinicTypeRepository->findByField('name', $name);

        if (!$clinicType) {
            $clinicType = $this->clinicTypeRepository->create([
                'name' => $name,
                'status' => ActiveStatus::Active,
                'description' => $name
            ]);
        }

        return $clinicType;
    }

    /**
     * Tạo record clinic theo đúng cấu trúc
     * @throws Exception
     */
    private function createClinicRecord(array $row)
    {
        // Parse data từ Excel theo đúng 9 cột trong ảnh
        $name = $this->sanitizeString($row[0] ?? '');              // Tên
        $hotline = $this->sanitizeString($row[1] ?? '');           // Hotline
        $clinicTypeName = $this->sanitizeString($row[2] ?? '');    // Loại phòng khám
        $openingTimeRaw = $row[3] ?? '';                           // Giờ mở cửa
        $closingTimeRaw = $row[4] ?? '';                           // Giờ đóng cửa
        $address = $this->sanitizeString($row[5] ?? '');           // Địa chỉ
        $wardId = $this->sanitizeString($row[6] ?? '');          // Phường
        $provinceId = $this->sanitizeString($row[7] ?? '');      // Thành Phố
        $schedule = $this->sanitizeString($row[8] ?? '');          // Lịch

        // Tìm hoặc tạo ClinicType bằng tên
        $clinicType = $this->findOrCreateClinicType($clinicTypeName);

        // Tìm địa chỉ (chỉ tìm, không tạo mới)
        $ward = $this->wardRepository->findOrFail($wardId);
        // Parse time để lưu vào database với format chuẩn (HH:MM)
        $openingTime = $this->parseTimeFromExcel($openingTimeRaw);
        $closingTime = $this->parseTimeFromExcel($closingTimeRaw);
        return $this->clinicRepository->create([
            'name' => $name,
            'hotline' => $hotline ?: null,
            'opening_time' => $openingTime ?: null,
            'closing_time' => $closingTime ?: null,
            'address' => $address ?: null,
            'clinic_type_id' => $clinicType ? $clinicType->id : null,
            'ward_id' => $ward ? $ward->id : null,
            'province_id' => $provinceId,
            'status' => ActiveStatus::Active,
            'avatar' => ImageSystem::DEFAULT_IMAGE,
            'schedule' => $schedule ?: null,
        ]);
    }

    /**
     * Xử lý sau khi import xong
     */
    public static function afterImport(): void
    {
        if (count(self::$errors) > 0) {
            $errors = self::$errors;
            self::resetState();

            throw new Exception(json_encode([
                'type' => 'validation_errors',
                'errors' => $errors,
                'count' => count($errors)
            ]));
        }

        self::resetState();
    }

    /**
     * Reset trạng thái static
     */
    private static function resetState(): void
    {
        self::$errors = [];
        self::$currentRow = 2;
    }

    /**
     * Dòng bắt đầu đọc dữ liệu
     */
    public function startRow(): int
    {
        return 2;
    }
}
