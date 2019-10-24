<?php declare(strict_types=1);

/**
 * Test cases (Pass) for Image Accessibility Checker
 */

$testcases_basic_html_pass = array(
//------------------------------------------------------------------------------
  new testcase(
      '
    <p>HTML without image</p>
    ',
      array('passed'=>true,'errors'=>array(),'warnings'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <img src="/some/source.jpg" alt="some alt text">
    ',
      array('passed'=>true,'errors'=>array(),'warnings'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <img src="/some/source.jpg" alt="some alt text">
    <img src="/some/source.jpg" alt="some alt text">
    <img src="/some/source.jpg" alt="some alt text">
    ',
      array('passed'=>true,'errors'=>array(),'warnings'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <img src="/some/source.jpg" alt="this sentence has a period. and some words after it">
    <img src="/some/source.jpg" alt="period. comma, period.">
    <img src="/some/source.jpg" alt="some weird character !@#$%^&*()_">
    <img src="/some/source.jpg" alt=".jpg">
    ',
      array('passed'=>true,'errors'=>array(),'warnings'=>array())
  ),
);
