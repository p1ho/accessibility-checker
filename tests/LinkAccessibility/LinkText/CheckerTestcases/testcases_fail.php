<?php

/**
 * Test cases (Fail) for Link Text Checker
 *
 * These are just a simple anchor tag.
 *
 * The following are external links that may be gone in the future
 * file download:
 * https://nodejs.org/dist/v10.15.0/node-v10.15.0-x64.msi
 * pdf:
 * https://media.readthedocs.org/pdf/django/2.1.x/django.pdf
 */

$testcases_fail = array(
//------------------------------------------------------------------------------
  // black list word
  new testcase(
      '
    <a href="">Click here</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 2
  new testcase(
      '
    <a href="">click here for more information</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 3
  new testcase(
      '
    <a href="">learn more</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 4
  new testcase(
      '
    <a href="">more details here</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 5
  new testcase(
      '
    <a href="">check this link</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 6
  new testcase(
      '
    <a href="">page with more info</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 7
  new testcase(
      '
    <a href="">find out more on this page</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 8
  new testcase(
      '
    <a href="">download it here</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => true,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 9
  new testcase(
      '
    <a href="">other information</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 10
  new testcase(
      '
    <a href="">view this page</a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 11 and empty text
  new testcase(
      '
    <a href=""> </a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // black list words 12 and empty text
  new testcase(
      '
    <a href=""> </a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text
  new testcase(
      '
    <a href="">https://www.example.net/</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 2
  new testcase(
      '
    <a href="">http://www.example.com/battle.html</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 3
  new testcase(
      '
    <a href="">http://www.example.net/afternoon.html#battle</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 4
  new testcase(
      '
    <a href="">https://www.example.net/bag/board.htm</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 5
  new testcase(
      '
    <a href="">https://www.example.com/books.php</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 6
  new testcase(
      '
    <a href="">http://www.example.com/beginner/approval.php</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 7
  new testcase(
      '
    <a href="">http://example.com/basin.php?boundary=bone</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 8
  new testcase(
      '
    <a href="">https://www.example.com/aftermath.aspx</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 9
  new testcase(
      '
    <a href="">http://example.net/amount/bath?bird=bit&blood=baby</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // url text 10
  new testcase(
      '
    <a href="">https://www.example.com/#back</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => false,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // long url (101 characters)
  new testcase(
      '
    <a href="">aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // long url (101 spaces) will fail both blacklist words and text length (will be trimmed to 0)
  new testcase(
      '
    <a href="">                                                                                                     </a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // long url (lots of spaces) with a black list word will fail just blacklist words
  // and not text length (because of trimming)
  new testcase(
      '
    <a href="">click                                                                                                     </a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // download link w/o 'download'
  new testcase(
      '
    <a href="https://getcomposer.org/Composer-Setup.exe">Composer</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => true,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // pdf link w/o 'pdf'
  new testcase(
      '
    <a href="https://media.readthedocs.org/pdf/django/2.1.x/django.pdf">Django Doc</a>
    ',
      array(
      'passed_blacklist_words'    => true,
      'passed_text_not_url'       => true,
      'passed_text_length'        => true,
      'url_is_download'           => true,
      'text_has_download'         => false,
      'url_is_pdf'                => true,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // image with bad alt text
  new testcase(
      '
    <a href="https://packagist.org"><img src="" alt=""></a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
  // multiple images (first one fail)
  new testcase(
      '
    <a href="https://packagist.org"><img src="" alt=""><img src="" alt="Go to Packagist.org"></a>
    ',
      array(
      'passed_blacklist_words'    => false,
      'passed_text_not_url'       => true,
      'passed_text_length'        => false,
      'url_is_download'           => false,
      'text_has_download'         => false,
      'url_is_pdf'                => false,
      'text_has_pdf'              => false,
    )
  ),
//------------------------------------------------------------------------------
);
