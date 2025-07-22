<?php
namespace App\Traits;

trait ImportValidationTrait {

    /**
     * Validate email format
     */
    protected function validateEmail(string $email): array
    {
        $errors = [];

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không đúng định dạng";
        }

        return $errors;
    }

    /**
     * Validate phone number (Vietnam format)
     */
    protected function validatePhoneNumber(string $phone): array
    {
        $errors = [];

        if (!empty($phone)) {
            // Remove spaces and special characters
            $cleanPhone = preg_replace('/[\s\-\(\)\.]+/', '', $phone);

            // Check Vietnam phone format
            if (!preg_match('/^(0|\+84)[0-9]{8,9}$/', $cleanPhone)) {
                $errors[] = "Số điện thoại không đúng định dạng Việt Nam";
            }
        }

        return $errors;
    }

    /**
     * Validate required field
     */
    protected function validateRequired(mixed $value, string $fieldName): array
    {
        $errors = [];

        if (empty($value) || (is_string($value) && trim($value) === '')) {
            $errors[] = "{$fieldName} không được để trống";
        }

        return $errors;
    }

    /**
     * Validate string length
     */
    protected function validateLength(string $value, string $fieldName, int $maxLength, int $minLength = 0): array
    {
        $errors = [];

        if (!empty($value)) {
            $length = strlen($value);

            if ($length > $maxLength) {
                $errors[] = "{$fieldName} không được vượt quá {$maxLength} ký tự";
            }

            if ($length < $minLength) {
                $errors[] = "{$fieldName} phải có ít nhất {$minLength} ký tự";
            }
        }

        return $errors;
    }

    /**
     * Validate numeric value
     */
    protected function validateNumeric(mixed $value, string $fieldName, float $min = null, float $max = null): array
    {
        $errors = [];

        if (!empty($value)) {
            if (!is_numeric($value)) {
                $errors[] = "{$fieldName} phải là số";
            } else {
                $numValue = (float) $value;

                if ($min !== null && $numValue < $min) {
                    $errors[] = "{$fieldName} phải lớn hơn hoặc bằng {$min}";
                }

                if ($max !== null && $numValue > $max) {
                    $errors[] = "{$fieldName} phải nhỏ hơn hoặc bằng {$max}";
                }
            }
        }

        return $errors;
    }

    /**
     * Validate date format
     */
    protected function validateDate(string $date, string $fieldName, string $format = 'Y-m-d'): array
    {
        $errors = [];

        if (!empty($date)) {
            $dateTime = \DateTime::createFromFormat($format, $date);

            if (!$dateTime || $dateTime->format($format) !== $date) {
                $errors[] = "{$fieldName} không đúng định dạng {$format}";
            }
        }

        return $errors;
    }

    /**
     * Validate array contains value
     */
    protected function validateInArray(mixed $value, array $allowedValues, string $fieldName): array
    {
        $errors = [];

        if (!empty($value) && !in_array($value, $allowedValues)) {
            $allowedString = implode(', ', $allowedValues);
            $errors[] = "{$fieldName} phải là một trong các giá trị: {$allowedString}";
        }

        return $errors;
    }

    /**
     * Validate unique value in database
     */
    protected function validateUnique(string $value, string $fieldName, string $model, string $column, $excludeId = null): array
    {
        $errors = [];

        if (!empty($value)) {
            $query = app($model)->where($column, $value);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if ($query->exists()) {
                $errors[] = "{$fieldName} '{$value}' đã tồn tại";
            }
        }

        return $errors;
    }

    /**
     * Sanitize string input
     */
    protected function sanitizeString(string $value): string
    {
        // Remove HTML tags and trim
        return trim(strip_tags($value));
    }

    /**
     * Check if row is empty
     */
    protected function isRowEmpty(array $row, array $requiredColumns = []): bool
    {
        if (empty($requiredColumns)) {
            // Check all columns
            return empty(array_filter($row, function($value) {
                return !empty(trim($value));
            }));
        }

        // Check only required columns
        foreach ($requiredColumns as $column) {
            if (!empty(trim($row[$column] ?? ''))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Format error message with row number
     */
    protected function formatErrorMessage(int $rowNumber, string $message): string
    {
        return "Dòng {$rowNumber}: {$message}";
    }

    /**
     * Add multiple errors to error list
     */
    protected function addRowErrors(array $errors, int $rowNumber, array &$errorList): void
    {
        foreach ($errors as $error) {
            $errorList[] = $this->formatErrorMessage($rowNumber, $error);
        }
    }

    private function parseTimeFromExcel($timeValue): ?string
    {
        if (empty($timeValue) && $timeValue !== 0 && $timeValue !== '0') {
            return null;
        }

        // Nếu là số (Excel serial time)
        if (is_numeric($timeValue)) {
            $timeDecimal = floatval($timeValue);

            // Validate range (0-1 cho thời gian trong ngày)
            if ($timeDecimal < 0 || $timeDecimal >= 1) {
                return null;
            }

            // Chuyển đổi decimal sang giờ:phút
            $totalMinutes = round($timeDecimal * 24 * 60);
            $hour = floor($totalMinutes / 60);
            $minute = $totalMinutes % 60;

            // Validate giá trị
            if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                return sprintf('%02d:%02d', $hour, $minute);
            }

            return null;
        }

        // Nếu là string, xử lý như trước
        $timeString = trim(strval($timeValue));

        // Regex để match các format time phổ biến từ Excel
        $patterns = [
            // Format: 8:00:00 AM/PM
            '/^(\d{1,2}):(\d{2}):(\d{2})\s*(AM|PM)$/i',
            // Format: 8:00 AM/PM
            '/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i',
            // Format: 08:00:00 (24h)
            '/^(\d{1,2}):(\d{2}):(\d{2})$/',
            // Format: 08:00 (24h)
            '/^(\d{1,2}):(\d{2})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $timeString, $matches)) {
                $hour = intval($matches[1]);
                $minute = intval($matches[2]);

                // Xử lý AM/PM nếu có
                if (isset($matches[4])) {
                    $period = strtoupper($matches[4]);
                    if ($period === 'PM' && $hour !== 12) {
                        $hour += 12;
                    } elseif ($period === 'AM' && $hour === 12) {
                        $hour = 0;
                    }
                }

                // Validate giá trị hour và minute
                if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                    return sprintf('%02d:%02d', $hour, $minute);
                }
            }
        }

        return null;
    }
}
