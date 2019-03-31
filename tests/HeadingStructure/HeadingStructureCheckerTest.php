<?php

/**
 * This is the main test execution file for all tests written for the
 * HeadingStructureChecker.
 *
 * To run these tests, navigate to the main directory of accessibility_checker,
 * then run:
 * "vendor/bin/phpunit heading-structure/tests".
 *
 */

require __DIR__ . "\../HeadingStructureChecker.php";
require "testcase.php";

require_once __DIR__ . "\..\../vendor\autoload.php";

use PHPUnit\Framework\TestCase;

final class HeadingStructureCheckerTest extends TestCase {
  public function testBasicHTMLPass(): void {
    require "testcases_basic_html_pass.php";
    foreach ($testcases_basic_html_pass as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        HeadingStructureChecker::evaluate($dom, TRUE)
      );

    }
  }

  public function testBasicHTMLFail(): void {
    require "testcases_basic_html_fail.php";
    foreach ($testcases_basic_html_fail as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        HeadingStructureChecker::evaluate($dom, TRUE)
      );

    }
  }

  public function testBasicHTMLPassNonStrict(): void {
    require "testcases_basic_html_pass_nonstrict.php";
    foreach ($testcases_basic_html_pass_nonstrict as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        HeadingStructureChecker::evaluate($dom, FALSE)
      );

    }
  }

  public function testBasicHTMLFailStrict(): void {
    require "testcases_basic_html_fail_strict.php";
    foreach ($testcases_basic_html_fail_strict as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $this->assertEquals(
        $testcase->expected_output,
        HeadingStructureChecker::evaluate($dom, TRUE)
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
