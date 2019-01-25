<?php

/**
 * Main test execution for Color Contrast Checker.
 *
 * To run these tests, navigate to the main directory of accessibility-checker,
 * then run:
 * "vendor/bin/phpunit color-contrast/tests".
 *
 * All the tests are going to assume we're running WCAG 2.0 AA
 */

require __DIR__ . "\../ColorContrastChecker.php";
require "testcase.php";
require_once __DIR__ . "\..\../vendor\autoload.php";

use PHPUnit\Framework\TestCase;

final class ColorContrastCheckerTest extends TestCase {

  public function testBasicHTMLPass(): void {
    require "testcases_basic_html_pass.php";
    foreach ($testcases_basic_html_pass as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        ColorContrastChecker::evaluate($dom, $mode = 'AA')
      );
    }
  }

  public function testBasicHTMLFail(): void {
    require "testcases_basic_html_fail.php";
    foreach ($testcases_basic_html_fail as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        ColorContrastChecker::evaluate($dom, $mode = 'AA')
      );
    }
  }

  private function getDOM($s) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($s);
    return $dom;
  }

}
