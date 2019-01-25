<?php

/**
 * Main test execution for Color Contrast Getter.
 *
 * To run these tests, navigate to the main directory of accessibility-checker,
 * then run:
 * "vendor/bin/phpunit color-contrast/color-contrast-helpers/tests".
 *
 * Note: The test does not cover AA/AAA Pass fail cases because the numbers are
 * already hardcoded in the logic and is unlikely to change.
 */

require __DIR__ . "\../ColorContrastGetter.php";
require "testcase.php";
require_once __DIR__ . "\..\..\../vendor\autoload.php";

use PHPUnit\Framework\TestCase;

final class ColorContrastGetterTest extends TestCase {

  /**
   * Tests whether same color in different format are converted equally.
   * If this test passes, we can assume color conversion works correctly, so
   * we can just stick to testing rgb combinations in subsequent tests.
   */
  public function testColorConversion(): void {
    require "testcases_ColorConversion.php";
    foreach ($testcases_ColorConversion as $testcase) {
      $result = ColorContrastGetter::evaluate(
        $testcase[0],
        $testcase[1]
      )['contrast_ratio'];
      // has to be accurate up to 1 decimal place
      $result_rounded = round($result*10)/10;
      $this->assertEquals(
        1, // ratio of 1 means colors compared are the same
        $result_rounded,
        print_r($testcase[0], TRUE) . 'does not equal ' . print_r($testcase[1], TRUE)
      );
    }
  }

  public function testContrastRatio(): void {
    require "testcases_ContrastRatio.php";
    foreach ($testcases_ContrastRatio as $testcase) {
      $result = ColorContrastGetter::evaluate(
        $testcase->input[0],
        $testcase->input[1]
      )['contrast_ratio'];
      $this->assertEquals(
        $testcase->expected_output,
        $result
      );
    }
  }

}
