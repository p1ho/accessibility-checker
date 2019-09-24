<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\LinkAccessibility\LinkQuality;

use P1ho\AccessibilityChecker\LinkAccessibility\Base;

/**
 * Link Quality Checker class for accessibility_checker
 */

class Checker extends Base
{
    private static $result;
    private static $cache_time = 86400;
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
     * @param string $link_path [path to be evaluated]
     * @param string $page_url [a page url, MUST include protocol]
     * @return array
     */
    public static function evaluate(string $link_path, string $page_url): array
    {
        // reset curl result
        self::$result = null;

        $link_path = trim($link_path);
        $page_url = trim($page_url);
        $link_domain = static::get_site_url($link_path);
        $site_domain = static::get_site_url($page_url);
        $page_path = str_replace($site_domain, "", $page_url);

        $link_path_strip_protocol = preg_replace('/(http)s?\:\/\//i', '', $link_domain);
        $is_same_domain = strpos($site_domain, $link_path_strip_protocol) !== false;

        /*
        if url is empty, or starts with the following:
        - #
        - tel:
        - mailto:
        if not, check if it's a relative path, if so, prepend domain to url
         */
        if (trim($link_path) === "" ||
            preg_match("/^(#|tel:|mailto:){1}/", $link_path)) {
            return [
                'is_redirect'    => false,
                'is_dead'        => false,
                'is_same_domain' => false,
                'timed_out'      => false];
        } else {
            $link_path = static::compute_link_url($link_path, $page_path, $site_domain);
        }

        // make HEAD request to get Headers
        $curl = new \Zebra_cURL();
        $curl->cache(__DIR__. '/../../../cache/', self::$cache_time, true, 0777);
        // HEAD requests should NOT take long to return results, will terminate
        // after 5 seconds.
        $curl->option([
          CURLOPT_TIMEOUT => 5,
          CURLOPT_CONNECTTIMEOUT => 5
        ]);
        $curl->get($link_path, function ($result) {
            if ($result->response[1] == CURLE_OK) {
                self::$result = $result;
            } else {
                self::$result = null;
            }
        });
        if (self::$result !== null) {
            $curl_info = self::$result->info;
            $http_code = self::$result->info["http_code"];
            $http_code_class = (int)($http_code/100);
            return [
                'is_redirect'    => $curl_info['redirect_time'] > 0,
                'is_dead'        => $http_code_class >= 4 || $http_code_class === 0,
                'is_same_domain' => $is_same_domain,
                'timed_out'      => false];
        } else {
            return [
                'is_redirect'    => false,
                'is_dead'        => false,
                'is_same_domain' => $is_same_domain,
                'timed_out'      => true];
        }
    }
}
