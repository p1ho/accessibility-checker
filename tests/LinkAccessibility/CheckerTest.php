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
}
