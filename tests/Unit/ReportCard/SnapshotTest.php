<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\SecondaryCalculator;
use PHPUnit\Framework\TestCase;

class SnapshotTest extends TestCase
{
    public function test_snapshot_schema_and_privacy(): void
    {
        $subjects = [
            new SubjectInput(2, 'Toán', 'score', true, 1, 8.0, 7.0),
            new SubjectInput(13, 'Ngữ văn', 'score', true, 2, 8.0, 8.0),
            new SubjectInput(3, 'Ngoại ngữ 1', 'score', true, 3, 8.0, 8.0),
            new SubjectInput(17, 'KHTN', 'score', true, 4, 8.0, 8.0),
            new SubjectInput(6, 'Lịch sử và Địa lí', 'score', true, 5, 8.0, 8.0),
            new SubjectInput(14, 'Tin học', 'score', true, 6, 8.0, 8.0),
            new SubjectInput(15, 'Công nghệ', 'score', true, 7, 8.0, 8.0),
            new SubjectInput(9, 'GDTC', 'comment', true, 8, null, null, null, 'achieved', 'achieved'),
        ];

        $input = new ReportCardInput('lower_secondary', 6, 'full_year', $subjects);
        $calculator = new SecondaryCalculator();
        $result = $calculator->calculate($input);

        $snapshot = $result->snapshot();

        // 1. Kiểm tra cấu trúc schema
        $this->assertArrayHasKey('version', $snapshot);
        $this->assertArrayHasKey('regulation', $snapshot);
        $this->assertArrayHasKey('education_level', $snapshot);
        $this->assertArrayHasKey('semester', $snapshot);
        $this->assertArrayHasKey('status', $snapshot);
        $this->assertArrayHasKey('rating', $snapshot);
        $this->assertArrayHasKey('subjects', $snapshot);
        $this->assertArrayHasKey('rules', $snapshot);
        $this->assertArrayHasKey('adjustment', $snapshot);
        $this->assertArrayHasKey('missing', $snapshot);
        $this->assertArrayHasKey('warnings', $snapshot);

        // 2. Bảo mật & riêng tư: không chứa dữ liệu nhạy cảm của người dùng
        $forbiddenKeys = ['child_name', 'fullname', 'user_id', 'child_id', 'token', 'password', 'phone'];
        foreach ($forbiddenKeys as $key) {
            $this->assertArrayNotHasKey($key, $snapshot);
        }

        // 3. Mỗi môn học trong snapshot không chứa token/user info
        foreach ($snapshot['subjects'] as $sub) {
            $this->assertArrayHasKey('id', $sub);
            $this->assertArrayHasKey('name', $sub);
            $this->assertArrayHasKey('value', $sub);
            $this->assertArrayNotHasKey('child_id', $sub);
        }
    }
}
