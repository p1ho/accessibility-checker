<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\Tests\HeadingStructure;

require_once __DIR__ . "/../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\HeadingStructure\Checker;

/*
Note: There was an original assumption where headings should start at <h3>,
so all the test cases written so far conforms to that.
This is why the checker instantiation all use "new Checker(2, true);"
 */

final class CheckerTest extends TestCase
{
    public function testBasicHTMLPass(): void
    {
        require "CheckerTestcases/testcases_basic_html_pass.php";
        $checker = new Checker(2, true);
        foreach ($testcases_basic_html_pass as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLFail(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail.php";
        $checker = new Checker(2, true);
        foreach ($testcases_basic_html_fail as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLPassNonStrict(): void
    {
        require "CheckerTestcases/testcases_basic_html_pass_nonstrict.php";
        $checker = new Checker(2, false);
        foreach ($testcases_basic_html_pass_nonstrict as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLFailStrict(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail_strict.php";
        $checker = new Checker(2, true);
        foreach ($testcases_basic_html_fail_strict as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testHeadingShift(): void
    {
        // no heading shift
        $checker = new Checker(0, true);
        $html = "<h1>Normal h1</h1>";
        $testcase = new \testcase($html, [
          'passed' => true,
          'errors' => []
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        // heading shift should override 0 and default to 1
        $checker = new Checker(-1, true);
        $html = "<h2>Normal h2</h2>";
        $testcase = new \testcase($html, [
          'passed' => true,
          'errors' => []
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        $checker = new Checker(1, false);
        $html = "<h3>Normal h3</h3>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading skipped',
              'tag' => 'h3',
              'text' => 'Normal h3',
              'html' => $html,
              'recommendation' => "<h2> is expected before the placement of this heading.",
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        $checker = new Checker(2, false);
        $html = "<h4>Normal h4</h4>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading skipped',
              'tag' => 'h4',
              'text' => 'Normal h4',
              'html' => $html,
              'recommendation' => "<h3> is expected before the placement of this heading.",
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        $checker = new Checker(3, false);
        $html = "<h5>Normal h5</h5>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading skipped',
              'tag' => 'h5',
              'text' => 'Normal h5',
              'html' => $html,
              'recommendation' => "<h4> is expected before the placement of this heading.",
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        $checker = new Checker(4, false);
        $html = "<h6>Normal h6</h6>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading skipped',
              'tag' => 'h6',
              'text' => 'Normal h6',
              'html' => $html,
              'recommendation' => "<h5> is expected before the placement of this heading.",
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        $checker = new Checker(5, false);
        $html = "<h6>Normal h6</h6>";
        $testcase = new \testcase($html, [
          'passed' => true,
          'errors' => []
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        // no headings are allowed
        $checker = new Checker(6, false);
        $html = "<h6>Normal h6</h6>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading unallowed',
              'tag' => 'h6',
              'text' => 'Normal h6',
              'html' => $html,
              'recommendation' => 'Check and use only allowed headings (no headings are allowed).'
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
        
        // should work even if heading shift is out of bound
        $checker = new Checker(7, false);
        $html = "<h6>Normal h6</h6>";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading unallowed',
              'tag' => 'h6',
              'text' => 'Normal h6',
              'html' => $html,
              'recommendation' => 'Check and use only allowed headings (no headings are allowed).'
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
    }

    public function testingMultipleH1(): void
    {
        // by default multiple h1 should fail
        $checker = new Checker(0, true);
        $html = "
          <h1>First h1</h1>
          <h1>Second h1</h1>
        ";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading unallowed',
              'tag' => 'h1',
              'text' => 'Second h1',
              'html' => '<h1>Second h1</h1>',
              'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );

        // unallowed heading takes precedence than nesting problem
        $checker = new Checker(0, true);
        $html = "
          <div><h1>First h1</h1></div>
          <h1>Second h1</h1>
        ";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading unallowed',
              'tag' => 'h1',
              'text' => 'Second h1',
              'html' => '<h1>Second h1</h1>',
              'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );

        // throw some other headings into the mix
        $checker = new Checker(0, false);
        $html = "
          <h1>First h1</h1>
          <h2>Some h2</h2>
          <h3>Some h3</h3>
          <h1>Second h1</h1>
        ";
        $testcase = new \testcase($html, [
          'passed' => false,
          'errors' => [
            (object) [
              'type' => 'heading unallowed',
              'tag' => 'h1',
              'text' => 'Second h1',
              'html' => '<h1>Second h1</h1>',
              'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
            ]
          ]
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );

        // if allow_multiple_h1 is set to true, multiple h1 should pass
        $checker = new Checker(0, true, true);
        $html = "
          <h1>First h1</h1>
          <h1>Second h1</h1>
        ";
        $testcase = new \testcase($html, [
          'passed' => true,
          'errors' => []
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom),
            print_r($testcase->input, true) . 'did not pass all checks.'
        );
    }

    private function getDOM($s)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($s);
        return $dom;
    }
}
