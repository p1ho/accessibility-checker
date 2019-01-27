<?php

require_once __DIR__ . '/../../vendor/stefangabos/zebra_curl/Zebra_cURL.php';

/**
 * Link Text Checker class for checking link text accessibility
 *
 * Assumes link node passed has 'href' attribute
 *
 * Consulted:
 * https://www.sitepoint.com/15-rules-making-accessible-links/
 * https://webaim.org/techniques/hypertext/
 * https://www.accessibilityoz.com/2014/02/links-and-accessibility/
 *
 * Note:
 * This checker does not address emoji's or emoticons.
 */

class LinkTextChecker {

  /*
  consulted https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types

  'application/pdf' not included because it usually downloads on mobile
  separately instead of show in browser.
   */
  private static $whitelist_mime_set = array(
    'text/plain' => 0,
    'text/html' => 0,
    'text/ecmascript' => 0,
    'text/javascript' => 0,
    'text/css' => 0,
    'image/gif' => 0,
    'image/jpeg' => 0,
    'image/png' => 0,
    'image/svg+xml' => 0,
    'image/x-icon' => 0,
    'image/vnd.microsoft.icon' => 0,
    'audio/wave' => 0,
    'audio/wav' => 0,
    'audio/x-wav',
    'audio/x-pn-wav' => 0,
    'audio/webm' => 0,
    'video/webm' => 0,
    'audio/ogg' => 0,
    'video/ogg' => 0,
    'application/ogg' => 0,
    'application/json' => 0,
    'application/ecmascript' => 0,
    'application/javascript' => 0,
  );

  private static $blacklist_words_set = array(
    "check" => 0,
    "click" => 0,
    "detail" => 0,
    "details" => 0,
    "download" => 0,
    "go" => 0,
    "here" => 0,
    "info" => 0,
    "information" => 0,
    "learn" => 0,
    "link" => 0,
    "more" => 0,
    "now" => 0,
    "other" => 0,
    "page" => 0,
    "read" => 0,
    "see" => 0,
    "this" => 0,
    "view" => 0,
    "visit" => 0,
    "find" => 0,
    "it" => 0,
  );

  // temp storage for curl request result
  private static $result;

  /**
   * Takes in text from the link element and checks if it has anything that
   * might not be the best for accessibility.
   *
   * Those checks include:
   * 1. 2/3 or more of content are blacklisted words (this could be changed)
   * 2. is just a URL but not hidden to screen reader
   * 3. is longer than 100 characters (too long)
   * 4. is a download but not properly indicated
   *    (does not contain the word "download" or fail check #1)
   *
   * Note: external links (target="_blank") annotations can likely be offloaded
   * to Drupal to automate, so we will not flag here.
   *
   * @param  Object $link_node [the node of the <a> tag in a DOMDocument]
   * @return array
   * array(
   *   "no_blacklist_words_passed"  => boolean
   *   "text_not_url_passed"        => boolean
   *   "text_length_passed"         => boolean
   *   "url_is_download"            => boolean
   *   "text_has_download"          => boolean
   *   "url_is_pdf"                 => boolean
   *   "text_has_pdf"               => boolean
   * )
   */
  public static function evaluate($link_node, $domain) {

    $link_text = $link_node->textContent;
    $link_url  = $link_node->getAttribute('href');

    $eval = array(
      'passed_blacklist_words'    => self::_blacklist_words_passed($link_text),
      'passed_text_not_url'       => !self::_is_url($link_text),
      'passed_text_length'        => strlen($link_text) <= 100,
      'url_is_download'           => self::_url_is_download($link_url, $domain),
      'text_has_download'         => strpos(strtolower($link_text), 'download') !== FALSE,
      'url_is_pdf'                => self::_url_is_pdf($link_url, $domain),
      'text_has_pdf'              => strpos(strtolower($link_text), 'pdf') !== FALSE,
    );

    return $eval;
  }

  /**
   * Check if link text passes the blacklist filter
   */
  private static function _blacklist_words_passed($text) {
    // preliminary check (not just blank space)
    $check = preg_match('%[a-z\d]+%iu', $text);
    if ($check === 0 || $check === FALSE) {
      return FALSE;
    }

    $text = strtolower($text);
    $text_array = explode(' ', $text);

    // strip leading/trailing punctuations
    $text_array_strip = array_map(function($x) {
      $strip_front = '%^[^a-z\d]+%iu';
      $strip_end   = '%[^a-z\d]+$%iu';
      return preg_replace(array($strip_front, $strip_end), '', $x);
    }, $text_array);

    // tally blacklisted words
    $num_of_words = count($text_array);
    $num_of_blacklist_words = 0;
    foreach ($text_array_strip as $word) {
      if (isset(self::$blacklist_words_set[$word])) {
        $num_of_blacklist_words++;
      }
    }
    return $num_of_blacklist_words/$num_of_words < 2/3;
  }

  /**
   * Check if link text is url
   */
  private static function _is_url($text) {
    $filter_result = filter_var($text, FILTER_VALIDATE_URL);
    return $filter_result !== FALSE;
  }

  /**
   * Check if hyperlink leads to a download link
   */
  private static function _url_is_download($url, $domain) {
    // reset curl result
    self::$result = NULL;

    /*
    if url is empty, or starts with the following:
    - #
    - tel:
    - mailto:
    - ./ or .\
    - ../ or ..\
    if not, check if it's a relative path, if so, prepend domain to url
     */
    if (trim($url) === "" ||
        preg_match("/^(#|tel:|mailto:|\.\/|\.\\\\|\.\.\/|\.\.\\\\){1}/", $url)) {
      return FALSE;
    } else if ($url[0] === '/') {
      $url = $domain . $url;
    }

    // make HEAD request to get Headers
    $curl = new Zebra_cURL();
    $curl->cache(__DIR__. '\..\..\cache/');
    $curl->header($url, function ($result) { self::$result = $result; });
    if (self::$result !== NULL && self::$result->response[1] === CURLE_OK) {
      $curl_resp = self::$result->headers['responses'];
      $curl_info = self::$result->info;
    } else {
      return FALSE;
    }

    /*
    Check Content Disposition:
    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition

    first check if Content Disposition is set, if it is, check if it's set
    to 'attachment'. If it is, return True

    If Content Disposition is not set or it is not 'attachment', we check
    MIME Type, if it is not one of the types that could be rendered by
    browsers, return True

    Else, return False
    */
    $headers_destination = $curl_resp[count($curl_resp)-1];
    if (isset($headers_destination['Content-Disposition']) &&
        strpos($headers_destination['Content-Disposition'], 'attachment') === 0) {
      return TRUE;
    }

    // check MIME Type
    if (isset($curl_info['content_type'])) {
      $mime_type = explode(';', $curl_info['content_type'])[0];
      /*
      if content-type can be rendered in browser, we return false because it
      will not initiate a download.
       */
      return !isset(self::$whitelist_mime_set[$mime_type]);
    } else {
      /*
      if content type is not specified, check if it actually made a request.
      if request code is 0, means it didn't make a request, return FALSE
      else, it means it made a request, but content type is unknown, return TRUE
       */
      return $curl_info['http_code'] !== 0;
    }
  }

  /**
   * Check if hyperlink leads to a pdf link
   */
  private static function _url_is_pdf($url, $domain) {
    // reset curl result
    self::$result = NULL;

    /*
    if url is empty, or starts with the following:
    - #
    - tel:
    - mailto:
    - ./ or .\
    - ../ or ..\
    if not, check if it's a relative path, if so, prepend domain to url
     */
    if (trim($url) === "" ||
        preg_match("/^(#|tel:|mailto:|\.\/|\.\\\\|\.\.\/|\.\.\\\\){1}/", $url)) {
      return FALSE;
    } else if ($url[0] === '/') {
      $url = $domain . $url;
    }

    // if $url ends with .pdf
    if (substr(trim($url), -strlen('.pdf')) === '.pdf') {
      return TRUE;
    }

    // make HEAD request to get Headers
    $curl = new Zebra_cURL();
    $curl->cache(__DIR__. '\..\..\cache/');
    $curl->header($url, function ($result) { self::$result = $result; });
    if (self::$result !== NULL) {
      $curl_resp = self::$result->headers['responses'];
      $curl_info = self::$result->info;
    } else {
      return FALSE;
    }

    // check MIME Type
    return isset($curl_info['content_type']) &&
           explode(';', $curl_info['content_type'])[0] === 'application/pdf';
  }

}
