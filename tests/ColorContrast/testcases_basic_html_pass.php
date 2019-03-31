<?php

/**
 * Test cases (Pass) for Color Contrast Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 *
 * These are general cases that should pass the Color Contrast test.
 */

$testcases_basic_html_pass = array(
//------------------------------------------------------------------------------
  // normal text
  new testcase(
    '
    <h1>title</h1>
    <p>text</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // text with good background color
  new testcase(
    '
    <p style="background-color: #757575;">text</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // large text with ok background color
  new testcase(
    '
    <h1 style="color: #858585">large text 1 (h1)</h1>
    <p style="font-size: 18pt; color: #858585">large text 2 (p)</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // <mark> should pass
  new testcase(
    '
    <mark>marked text</mark>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // <a> should pass
  new testcase(
    '
    <a href="#">link</a>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  /*
  rgb background:
  #008000
  rgb(0, 128, 0)
  hsl(120, 100%, 25.1%))
  */
  new testcase(
    '
    <p style="color: rgb(0, 128, 0)">green text</p>
    <p style="color: #008000">green text</p>
    <p style="color: hsl(120, 100%, 25.1%)">green text</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // rgba background
  new testcase(
    '
    <p style="background-color: rgba(0, 255, 0, .5)">green background</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // rgb background + rgb font
  new testcase(
    '
    <p style="background-color: #008000; color: white;  ">should pass</p>
    <p style="background-color: #000000; color: #949494;">should pass</p>
    <p style="background-color: #FF00FF; color: #1C1C1C;">should pass</p>
    <p style="background-color: #800000; color: #D3BAD9;">should pass</p>
    <p style="background-color: #800080; color: #E9ECDF;">should pass</p>
    <p style="background-color: #237807; color: #E1F7CF;">should pass</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // rgba background + rgba font
  new testcase(
    '
    <p style="background-color: rgba(0, 0, 0, .7); color: rgba(255, 255, 255, .7)">should pass</p>
    <p style="background-color: rgba(25, 35, 50, .8); color: rgba(255, 255, 200, .8)">should pass</p>
    <p style="background-color: rgba(255, 200, 128, .8); color: rgba(37, 47, 33, .7)">should pass</p>
    <h1 style="background-color: rgba(63, 128, 15, 0.8); color: rgba(10, 30, 17, 0.7)">large text should pass</h1>
    <h1 style="background-color: rgba(53, 70, 41, .35); color: rgba(58, 48, 18, 0.62)">should pass</h1>
    <h1 style="background-color: rgba(112, 245, 18, 0.48); color: rgba(68, 45, 77, 0.62)">should pass</h1>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------

);
