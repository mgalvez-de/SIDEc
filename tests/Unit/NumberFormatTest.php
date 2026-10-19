<?php

namespace Tests\Unit;

use App\Support\NumberFormat;
use PHPUnit\Framework\TestCase;

class NumberFormatTest extends TestCase
{
    public function test_uses_comma_for_decimals_and_dot_for_thousands()
    {
        $this->assertSame('1.234,5', NumberFormat::format(1234.5));
        $this->assertSame('3,80', NumberFormat::format('3.80')); // conserva los decimales guardados
        $this->assertSame('1.200', NumberFormat::format(1200));
        $this->assertSame('-0,25', NumberFormat::format('-0.25'));
    }

    public function test_fixed_decimals_and_scientific_notation()
    {
        $this->assertSame('10.000,0', NumberFormat::format(10000, 1));
        $this->assertSame('0,0000001', NumberFormat::format(1.0E-7));
    }

    public function test_non_numeric_values_are_returned_as_they_are()
    {
        $this->assertNull(NumberFormat::format(null));
        $this->assertSame('', NumberFormat::format(''));
        $this->assertSame('No especificado', NumberFormat::format('No especificado'));
    }
}
