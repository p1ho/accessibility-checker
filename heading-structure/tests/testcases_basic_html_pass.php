<?php

/**
 * Test cases (Pass) for Heading Structure Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 * Because we assume the data we pass into Heading Structure Checker is coming
 * from Drupal, the way the testcases are written here tries to adhere to the
 * same format (assumes everything's already wrapped in <body>)
 *
 * These are general pass cases that should pass for both strict/non-strict
 * modes.
 */

$testcases_basic_html_pass = array(
//------------------------------------------------------------------------------
  new testcase(
    '
    <p>This is HTML without heading</p>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Title</h3>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <div>
      <h3>Title</h3>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Title (h3)</h3>
    <div>
      <h4>Title (h4)</h4>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Title (h3)</h3>
    <div>
      <h4>Title (h4)</h4>
      <div>
        <h5>Title (h5) (Same container with h4)</h5>
      </div>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Title (h3)</h3>
    <div>
      <h4>Title (h4)</h4>
    </div>
    <div>
      <section>
        <h5>Title (h5) (Different container with h4)</h5>
      </section>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Title (h3)</h3>
    <h3>Title2 (h3)</h3>
    <h3>Title3 (h3)</h3>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // test h3 through h6
  new testcase(
    '
    <div>
      <h3>Title (h3)</h3>
      <div>
        <h4>Title (h4)</h4>
        <div>
          <h5>Title (h5)</h5>
          <div>
            <h6>Title (h6)</h6>
          </div>
        </div>
      </div>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // malformed html should still pass
  new testcase(
    '
    <h3>Title</h4>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // other valid tags that may resemble heading tags are not parsed
  new testcase(
    '
    <h3>Title</h3>
    <hr/>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // malformed html h3 through h6 (no closing tags)
  // Note: their containing element is still properly closed
  new testcase(
    '
    <div>
      <h3>Title (h3)
    </div>
    <div>
      <div>
        <h4>Title (h4)
      </div>
    </div>
    <div>
      <div>
        <div>
          <h5>Title (h5)
        </div>
      </div>
    </div>
    <div>
      <div>
        <div>
          <div>
            <h6>Title (h6)
          </div>
        </div>
      <div>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  // malformed html test h3 through h6 (wrong closing tags)
  // Note: their containing element is still properly closed
  new testcase(
    '
    <div>
      <h3>Title (h3)</h1>
    </div>
    <div>
      <div>
        <h4>Title (h4)</h2>
      </div>
    </div>
    <div>
      <div>
        <div>
          <h5>Title (h5)</h3>
        </div>
      </div>
    </div>
    <div>
      <div>
        <div>
          <div>
            <h6>Title (h6)</h4
          </div>
        </div>
      <div>
    </div>
    ',
    array('passed'=>TRUE,'errors'=>array())
  ),
//------------------------------------------------------------------------------
);
