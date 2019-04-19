<?php declare(strict_types=1);

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
