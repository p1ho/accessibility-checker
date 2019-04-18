<?php

/**
 * testcase class.
 * This is mostly used to increase code readability in testcases.
 */

class testcase
{
    public $input;
    public $expected_output;

    public function __construct($input, $expected_output)
    {
        $this->input = $input;
        $this->expected_output = $expected_output;
    }
}
