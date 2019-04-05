<?php

require_once __DIR__ . '/../../vendor/stefangabos/zebra_curl/Zebra_cURL.php';

/**
 * Link Quality Checker class for accessibility_checker
 */

class LinkQualityChecker {

  private static $result;

  /**
   * Check if links (<a>) are optimized
   *
   * Checks include:
   * 1. <a> leads to a redirect
   * 2. <a> leads to a deadlink
   * 3. <a> points to somewhere in the same domain
   *
   * Exceptions (will automatically pass all tests):
   * 1) Links to somewhere else on the same page (href="#something")
   * 2) Email link (href="mailto:somebody@some.url.com")
   * 3) Telephone Link (href="tel:123-456-7890")
   *
   */
  public static function evaluate($url, $domain) {
    // reset curl result
    self::$result = NULL;

    $url = trim($url);
    $domain = trim($domain);

    $is_same_domain = strpos($url, $domain) !== FALSE;

    // if url is empty return dead link
    if (trim($url) === "")
    {
      return array(
        'is_redirect' => FALSE,
        'is_dead' => TRUE,
        'is_same_domain' => FALSE,
      );
    }

    // if url starts with "//", prepend with "http:" for zebraCurl
    else if (substr($url, 0, 2) === "//") {
      $url = "http:$url";
    }

    // if url starts with / or \
    // handle relative link
    else if (preg_match("/^(\/|\\\\){1}/", $url))
    {
      $url = $domain.$url;
    }

    /*
    if url starts with:
    - #
    - tel:
    - mailto:
    - ./ or .\ (because no access to actual path)
    - ../ or .\\ (because no access to actual path)
    automatically pass test
    */
    else if (preg_match("/^(#|tel:|mailto:|\.\/|\.\\\\|\.\.\/|\.\.\\\\){1}/", $url))
    {
      return array(
        'is_redirect' => FALSE,
        'is_dead' => FALSE,
        'is_same_domain' => FALSE,
      );
    }
    // else treat url as original

    // make HEAD request to get Headers
    $curl = new Zebra_cURL();
    $curl->cache(__DIR__. '\..\..\cache/');
    $curl->header($url, function ($result) { self::$result = $result; });
    if (self::$result !== NULL) {
      $curl_info = self::$result->info;
      $http_code = self::$result->info["http_code"];
      $http_code_class = (int)($http_code/100);
    } else {
      return FALSE;
    }

    return array(
      'is_redirect'    => $curl_info['redirect_time'] > 0,
      'is_dead'        => $http_code_class >= 4 || $http_code_class === 0,
      'is_same_domain' => $is_same_domain,
    );

  }

}
