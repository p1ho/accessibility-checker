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

 require_once __DIR__ . "/../testcase.php";

 use PHPUnit\Framework\TestCase;
 use P1ho\AccessibilityChecker\ColorContrast\Checker;

 final class CheckerTest extends TestCase
 {
     public function testBasicHTMLPass(): void
     {
         require "CheckerTestcases/testcases_basic_html_pass.php";
         $checker = new Checker();
         foreach ($testcases_basic_html_pass as $testcase) {
             $dom = $this->getDOM($testcase->input);
             $this->assertEquals(
                 $testcase->expected_output,
                 $checker->evaluate($dom)
            );
         }
     }

     public function testBasicHTMLFail(): void
     {
         require "CheckerTestcases/testcases_basic_html_fail.php";
         $checker = new Checker();
         foreach ($testcases_basic_html_fail as $testcase) {
             $dom = $this->getDOM($testcase->input);
             $this->assertEquals(
                 $testcase->expected_output,
                 $checker->evaluate($dom)
            );
         }
     }

     private function getDOM($s)
     {
         $dom = new \DOMDocument();
         libxml_use_internal_errors(true);
         $dom->loadHTML($s);
         return $dom;
     }
 }
