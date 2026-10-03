<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\CalculatorResolver;
use App\Services\ReportCard\PrimaryCalculator;
use App\Services\ReportCard\SecondaryCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculatorResolverTest extends TestCase
{
    private CalculatorResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new CalculatorResolver();
    }

    public function test_resolves_primary_from_level_string(): void
    {
        $calc = $this->resolver->resolve('primary');
        $this->assertInstanceOf(PrimaryCalculator::class, $calc);
    }

    public function test_resolves_secondary_from_level_strings(): void
    {
        $this->assertInstanceOf(SecondaryCalculator::class, $this->resolver->resolve('lower_secondary'));
        $this->assertInstanceOf(SecondaryCalculator::class, $this->resolver->resolve('upper_secondary'));
    }

    public function test_fallback_to_class_id_when_level_is_null(): void
    {
        $this->assertInstanceOf(PrimaryCalculator::class, $this->resolver->resolve(null, 3));
        $this->assertInstanceOf(SecondaryCalculator::class, $this->resolver->resolve(null, 7));
        $this->assertInstanceOf(SecondaryCalculator::class, $this->resolver->resolve(null, 11));
    }

    public function test_invalid_level_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->resolver->resolve('unknown_level');
    }
}
