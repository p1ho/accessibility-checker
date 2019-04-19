<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\Tests\HeadingStructure;

require_once __DIR__ . "/../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\HeadingStructure\Checker;

final class CheckerTest extends TestCase
{
    public function testBasicHTMLPass(): void
    {
        require "CheckerTestcases/testcases_basic_html_pass.php";
        $checker = new Checker(true);
        foreach ($testcases_basic_html_pass as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLFail(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail.php";
        $checker = new Checker(true);
        foreach ($testcases_basic_html_fail as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLPassNonStrict(): void
    {
        require "CheckerTestcases/testcases_basic_html_pass_nonstrict.php";
        $checker = new Checker(false);
        foreach ($testcases_basic_html_pass_nonstrict as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    public function testBasicHTMLFailStrict(): void
    {
        require "CheckerTestcases/testcases_basic_html_fail_strict.php";
        $checker = new Checker(true);
        foreach ($testcases_basic_html_fail_strict as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $this->assertEquals($testcase->expected_output, $checker->evaluate($dom));
        }
    }

    private function getDOM($s)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($s);
        return $dom;
    }
}
