<?php

/**
 * Test cases (Pass) for Link Text Checker
 *
 * These are just a simple anchor tag.
 *
 * The following are external links that may be gone in the future
 * file download:
 * https://nodejs.org/dist/v10.15.0/node-v10.15.0-x64.msi
 * pdf:
 * https://media.readthedocs.org/pdf/django/2.1.x/django.pdf
 */

$testcases_pass = array(
//------------------------------------------------------------------------------
  // link
  new testcase(
    '
    <a href="">Apply here</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => FALSE,
      'text_has_download'         => FALSE,
      'url_is_pdf'                => FALSE,
      'text_has_pdf'              => FALSE,
    )
  ),
//------------------------------------------------------------------------------
  // download
  new testcase(
    '
    <a href="https://nodejs.org/dist/v10.15.0/node-v10.15.0-x64.msi">Download Nodejs</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => TRUE,
      'url_is_pdf'                => FALSE,
      'text_has_pdf'              => FALSE,
    )
  ),
//------------------------------------------------------------------------------
  // download 2
  new testcase(
    '
    <a href="https://nodejs.org/dist/v10.15.0/node-v10.15.0-x64.msi">Nodejs Download</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => TRUE,
      'url_is_pdf'                => FALSE,
      'text_has_pdf'              => FALSE,
    )
  ),
//------------------------------------------------------------------------------
  // download 3
  new testcase(
    '
    <a href="https://nodejs.org/dist/v10.15.0/node-v10.15.0-x64.msi">Nodejs (download)</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => TRUE,
      'url_is_pdf'                => FALSE,
      'text_has_pdf'              => FALSE,
    )
  ),
//------------------------------------------------------------------------------
  // pdf
  new testcase(
    '
    <a href="https://media.readthedocs.org/pdf/django/2.1.x/django.pdf">Django PDF</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => FALSE,
      'url_is_pdf'                => TRUE,
      'text_has_pdf'              => TRUE,
    )
  ),
//------------------------------------------------------------------------------
  // pdf 2
  new testcase(
    '
    <a href="https://media.readthedocs.org/pdf/django/2.1.x/django.pdf">Django pdf</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => FALSE,
      'url_is_pdf'                => TRUE,
      'text_has_pdf'              => TRUE,
    )
  ),
//------------------------------------------------------------------------------
  // pdf 3
  new testcase(
    '
    <a href="https://media.readthedocs.org/pdf/django/2.1.x/django.pdf"><span>(pdf)</span> Django</a>
    ',
    array(
      'passed_blacklist_words'    => TRUE,
      'passed_text_not_url'       => TRUE,
      'passed_text_length'        => TRUE,
      'url_is_download'           => TRUE,
      'text_has_download'         => FALSE,
      'url_is_pdf'                => TRUE,
      'text_has_pdf'              => TRUE,
    )
  ),
//------------------------------------------------------------------------------
);
