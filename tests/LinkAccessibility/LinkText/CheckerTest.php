<?php

namespace P1ho\AccessibilityChecker\Tests\LinkAccessibility\LinkText;

require_once __DIR__ . "/../../testcase.php";

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\LinkAccessibility\LinkText\Checker;

final class CheckerTest extends TestCase
{
    public function testPass(): void
    {
        require "CheckerTestcases/testcases_pass.php";
        foreach ($testcases_pass as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $link_node = $dom->getElementsByTagName('a')[0];
            $this->assertEquals(
                $testcase->expected_output,
                Checker::evaluate($link_node, "https://www.google.com"),
                print_r($testcase->input, true) . 'did not pass all checks.'
            );
        }
    }

    public function testFail(): void
    {
        require "CheckerTestcases/testcases_fail.php";
        foreach ($testcases_fail as $testcase) {
            $dom = $this->getDOM($testcase->input);
            $link_node = $dom->getElementsByTagName('a')[0];
            $this->assertEquals(
                $testcase->expected_output,
                Checker::evaluate($link_node, "https://www.google.com"),
                print_r($testcase->input, true) . 'did not fail.'
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

    public function testGetSiteUrl(): void
    {
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com')
        );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/')
        );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something')
        );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something')
        );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something/else')
        );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something/else/')
        );
    }

    public function testComputeLinkUrl(): void
    {
        $this->assertEquals('http://example.com', Checker::compute_link_url(
            '//example.com',
            '/some/page/',
            'https://www.google.com'
        ));
        $this->assertEquals('https://www.google.com/this/is/a/path', Checker::compute_link_url(
            '/this/is/a/path',
            '/some/page/',
            'https://www.google.com'
        ));
        $this->assertEquals('http://example.com', Checker::compute_link_url(
            'http://example.com',
            '/some/page/',
            'https://www.google.com'
        ));
        $this->assertEquals('https://example.com', Checker::compute_link_url(
            'https://example.com',
            '/some/page/',
            'https://www.google.com'
        ));
        $this->assertEquals('https://www.google.com/some/page/./this/is/a/path', Checker::compute_link_url(
            './this/is/a/path',
            '/some/page/',
            'https://www.google.com'
        ));
        $this->assertEquals('https://www.google.com/some/page/../this/is/a/path', Checker::compute_link_url(
            '../this/is/a/path',
            '/some/page/',
            'https://www.google.com'
        ));
    }
}
