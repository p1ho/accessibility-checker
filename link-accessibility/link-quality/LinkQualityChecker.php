<?php

require_once __DIR__ . '\..\..\vendor/stefangabos/zebra_curl/Zebra_cURL.php';

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
      return array(
        'is_redirect'    => FALSE,
        'is_dead'        => FALSE,
        'is_same_domain' => FALSE,
      );
    } else if ($url[0] === '/') {
      $url = $domain . $url;
      $is_same_domain = FALSE;
    }

    // make HEAD request to get Headers
    $curl = new Zebra_cURL();
    $curl->cache(__DIR__. '\..\..\cache/');
    $curl->header($url, function ($result) { self::$result = $result; });
    if (self::$result !== NULL) {
      $http_code = self::$result->info["http_code"];
      $http_code_class = (int)($http_code/100);
    } else {
      return FALSE;
    }

    return array(
      'is_redirect'    => $http_code_class == 3,
      'is_dead'        => $http_code_class >= 4 || $http_code_class === 0,
      'is_same_domain' => $is_same_domain,
    );

  }

}
