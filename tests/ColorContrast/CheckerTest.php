<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\Tests\ColorContrast;

require_once __DIR__ . "/../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\ColorContrast\Checker;

final class CheckerTest extends TestCase
{
    public function testBasicHTMLPass(): void
    {
        require "CheckerTestcases/testcases_basic_html_pass.php";
        $checker = new Checker("AA", "white", "black");
        foreach ($testcases_basic_html_pass as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals(
                $testcase->expected_output,
                $checker->evaluate($dom),
                trim($testcase->input)
          );
        }
    }

    public function testBasicHTMLFailFastFail(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail_fast_fail.php";
        $checker = new Checker("AA", "white", "black", true);
        foreach ($testcases_basic_html_fail_fast_fail as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals(
                $testcase->expected_output,
                $checker->evaluate($dom),
                trim($testcase->input)
          );
        }
    }

    public function testBasicHTMLFailNoFastFail(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail_no_fast_fail.php";
        $checker = new Checker("AA", "white", "black", false);
        foreach ($testcases_basic_html_fail_no_fast_fail as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals(
                $testcase->expected_output,
                $checker->evaluate($dom),
                trim($testcase->input)
          );
        }
    }

    public function testColorOverride(): void
    {
        // check override on checker instantiation
        $checker = new Checker("AA", "blue", "white");
        $input = '<p style="color: blue">blue text on blue</p>';
        $expected_output = array(
            'passed'=>false,
            'errors'=>array(
            (object) [
                'type' => 'low contrast',
                'mode' => 'AA',
                'tag' => 'p',
                'text' => 'blue text on blue',
                'html' => '<p style="color: blue">blue text on blue</p>',
                'text_is_large' => false,
                'contrast_ratio' => '1.00',
                'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
            ])
        );
        $dom = $this->getDOM($input);
        $this->assertEquals(
            $expected_output,
            $checker->evaluate($dom)
        );

        $checker = new Checker("AA", "white", "blue");
        $input = '<p style="background-color: blue">blue text on blue</p>';
        $expected_output = array(
            'passed'=>false,
            'errors'=>array(
            (object) [
                'type' => 'low contrast',
                'mode' => 'AA',
                'tag' => 'p',
                'text' => 'blue text on blue',
                'html' => '<p style="background-color: blue">blue text on blue</p>',
                'text_is_large' => false,
                'contrast_ratio' => '1.00',
                'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
            ])
        );
        $dom = $this->getDOM($input);
        $this->assertEquals(
            $expected_output,
            $checker->evaluate($dom)
        );

        // check override on evaluate method call with normal instantiation
        $checker = new Checker();
        $input = '<p style="color: blue">blue text on blue</p>';
        $expected_output = array(
            'passed'=>false,
            'errors'=>array(
            (object) [
                'type' => 'low contrast',
                'mode' => 'AA',
                'tag' => 'p',
                'text' => 'blue text on blue',
                'html' => '<p style="color: blue">blue text on blue</p>',
                'text_is_large' => false,
                'contrast_ratio' => '1.00',
                'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
            ])
        );
        $dom = $this->getDOM($input);
        $this->assertEquals(
            $expected_output,
            $checker->evaluate($dom, "blue", "white")
        );

        $checker = new Checker();
        $input = '<p style="background-color: blue">blue text on blue</p>';
        $expected_output = array(
            'passed'=>false,
            'errors'=>array(
            (object) [
                'type' => 'low contrast',
                'mode' => 'AA',
                'tag' => 'p',
                'text' => 'blue text on blue',
                'html' => '<p style="background-color: blue">blue text on blue</p>',
                'text_is_large' => false,
                'contrast_ratio' => '1.00',
                'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
            ])
        );
        $dom = $this->getDOM($input);
        $this->assertEquals(
            $expected_output,
            $checker->evaluate($dom, "white", "blue")
        );

        // check override on evaluate method dominates override on instantiation
        $checker = new Checker("AA", "yellow", "black");
        $input = '<p style="color: white">white text on white</p>';
        $expected_output = array(
            'passed'=>false,
            'errors'=>array(
            (object) [
                'type' => 'low contrast',
                'mode' => 'AA',
                'tag' => 'p',
                'text' => 'white text on white',
                'html' => '<p style="color: white">white text on white</p>',
                'text_is_large' => false,
                'contrast_ratio' => '1.00',
                'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
            ])
        );
        $dom = $this->getDOM($input);
        $this->assertEquals(
            $expected_output,
            $checker->evaluate($dom, "white", "blue")
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
