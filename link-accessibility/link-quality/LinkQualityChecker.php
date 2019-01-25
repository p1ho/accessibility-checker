<?php

/**
 * Link Quality Checker class for accessibility_checker
 */

class LinkQualityChecker {

  private static $curl;

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
    // init curl
    if (!isset(self::$curl)) {
      self::$curl = curl_init();
      curl_setopt_array(self::$curl, array(
        CURLOPT_HEADER => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => 'HEAD',
        CURLOPT_NOBODY => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
      ));
    }

    $url = trim($url);
    $domain = trim($domain);
    $is_same_domain = strpos($url, $domain) !== FALSE;

    if ($url[0] === '#' || substr($url, 0, 4) === 'tel:' || substr($url, 0, 7) === 'mailto:') {
      return array(
        'is_redirect'    => FALSE,
        'is_dead'        => FALSE,
        'is_same_domain' => FALSE,
      );
    } else if ($url[0] === '/') {
      $url = $domain . $url;
      $is_same_domain = FALSE;
    }

    curl_setopt(self::$curl, CURLOPT_URL, $url);
    $curl_resp = curl_exec(self::$curl);
    $curl_info = curl_getinfo(self::$curl);

    $http_code = $curl_info["http_code"];
    $http_code_class = (int)($http_code/100);

    return array(
      'is_redirect'    => $http_code_class == 3,
      'is_dead'        => $http_code_class >= 4 || $http_code_class === 0,
      'is_same_domain' => $is_same_domain,
    );

  }

}
