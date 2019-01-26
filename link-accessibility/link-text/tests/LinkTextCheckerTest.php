<?php

/**
 * Main test execution for Link Text Checker.
 *
 * To run these tests, navigate to the main directory of accessibility-checker,
 * then run:
 * "vendor/bin/phpunit link-accessibility/link-text/tests".
 *
 */

require __DIR__ . "\../LinkTextChecker.php";
require "testcase.php";
require_once __DIR__ . "\..\..\../vendor\autoload.php";

use PHPUnit\Framework\TestCase;

final class LinkTextCheckerTest extends TestCase {

  public function testPass(): void {
    require "testcases_pass.php";
    foreach ($testcases_pass as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $link_node = $dom->getElementsByTagName('a')[0];
      $this->assertEquals(
        $testcase->expected_output,
        LinkTextChecker::evaluate($link_node, "localhost"),
        print_r($testcase->input, TRUE) . 'did not pass all checks.'
      );
    }
  }

  public function testFail(): void {
    require "testcases_fail.php";
    foreach ($testcases_fail as $testcase) {
      $dom = $this->getDOM($testcase->input);
      $link_node = $dom->getElementsByTagName('a')[0];
      $this->assertEquals(
        $testcase->expected_output,
        LinkTextChecker::evaluate($link_node, "localhost"),
        print_r($testcase->input, TRUE) . 'did not fail.'
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
