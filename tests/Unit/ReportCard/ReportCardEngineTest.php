<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\ReportCardEngine;
use PHPUnit\Framework\TestCase;

class ReportCardEngineTest extends TestCase
{
    public function test_engine_calculates_successfully_via_facade(): void
    {
        $engine = new ReportCardEngine();

        $subjects = [
            new SubjectInput(2, 'Toán', 'score', true, 1, 8.0, 8.0),
            new SubjectInput(13, 'Ngữ văn', 'score', true, 2, 8.0, 8.0),
            new SubjectInput(3, 'Ngoại ngữ 1', 'score', true, 3, 8.0, 8.0),
            new SubjectInput(17, 'KHTN', 'score', true, 4, 8.0, 8.0),
            new SubjectInput(6, 'Lịch sử và Địa lí', 'score', true, 5, 8.0, 8.0),
            new SubjectInput(14, 'Tin học', 'score', true, 6, 8.0, 8.0),
            new SubjectInput(15, 'Công nghệ', 'score', true, 7, 8.0, 8.0),
            new SubjectInput(9, 'GDTC', 'comment', true, 8, null, null, null, 'achieved', 'achieved'),
            new SubjectInput(26, 'Âm nhạc', 'comment', true, 9, null, null, null, 'achieved', 'achieved'),
            new SubjectInput(27, 'Mĩ thuật', 'comment', true, 10, null, null, null, 'achieved', 'achieved'),
            new SubjectInput(16, 'HĐTN', 'comment', true, 11, null, null, null, 'achieved', 'achieved'),
        ];

        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);
        $result = $engine->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('good', $result->rating);
    }

    public function test_engine_handles_unknown_level_gracefully_without_crashing(): void
    {
        $engine = new ReportCardEngine();
        $input = new ReportCardInput('invalid_level', 99, 'semester_1', []);
        $result = $engine->calculate($input);

        $this->assertSame('invalid_input', $result->status);
        $this->assertNotEmpty($result->warnings);
    }
}
