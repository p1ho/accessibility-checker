<?php

require_once __DIR__ . "/../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\ColorContrast\Calculator;

final class CalculatorTest extends TestCase
{

  /**
   * Tests whether same color in different format are converted equally.
   * If this test passes, we can assume color conversion works correctly, so
   * we can just stick to testing rgb combinations in subsequent tests.
   */
    public function testColorConversion(): void
    {
        require "CalculatorTestcases/testcases_ColorConversion.php";
        foreach ($testcases_ColorConversion as $testcase) {
            $result = Calculator::evaluate($testcase[0], $testcase[1])['contrast_ratio'];
            // has to be accurate up to 1 decimal place
            $result_rounded = round($result*10)/10;
            $this->assertEquals(1, $result_rounded, // ratio of 1 means colors compared are the same
            print_r($testcase[0], true) . 'does not equal ' . print_r($testcase[1], true));
        }
    }

    public function testContrastRatio(): void
    {
        require "CalculatorTestcases/testcases_ContrastRatio.php";
        foreach ($testcases_ContrastRatio as $testcase) {
            $result = Calculator::evaluate($testcase->input[0], $testcase->input[1])['contrast_ratio'];
            $this->assertEquals($testcase->expected_output, $result);
        }
    }
}
