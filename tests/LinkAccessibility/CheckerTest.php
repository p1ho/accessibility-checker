<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\Tests\LinkAccessibility;

require_once __DIR__ . "/../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\LinkAccessibility\Checker;

final class CheckerTest extends TestCase
{
    public function testInstantiation(): void
    {
        $hasError = false;
        try {
            $checker = new Checker();
        } catch (\Exception $e) {
            $hasError = true;
        }
        $this->assertFalse($hasError);
    }

    public function testIgnorePlaceholderLink(): void
    {
        $checker = new Checker();
        $testcase = new \testcase('<a>Link without href</a>', [
          'passed' => 1,
          'errors' => []
        ]);
        $dom = $this->getDOM($testcase->input);
        $this->assertEquals(
            $testcase->expected_output,
            $checker->evaluate($dom, "https://www.google.com"),
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
