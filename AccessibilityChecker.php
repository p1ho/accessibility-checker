<?php

 /**
  * A wrapper class for all the individual accessibility modules
  */

class AccessibilityChecker
{

  private static $dom;
  private static $domain;

  public static function init() {
    require "color-contrast\ColorContrastChecker.php";
    require "heading-structure\HeadingStructureChecker.php";
    require "image-accessibility\ImageAccessibilityChecker.php";
    require "link-accessibility\LinkAccessibilityChecker.php";
  }

  public static function load_html($html) {
    self::$dom = self::_parse_html($html);
  }

  public static function set_domain($domain) {
    self::$domain = $domain;
  }

  public static function evaluate_color_contrast() {
    return ColorContrastChecker::evaluate(self::$dom);
  }

  public static function evaluate_heading_structure() {
    return HeadingStructureChecker::evaluate(self::$dom);
  }

  public static function evaluate_image_accessibility() {
    return ImageAccessibilityChecker::evaluate(self::$dom);
  }

  public static function evaluate_link_accessibility() {
    return LinkAccessibilityChecker::evaluate(self::$dom, self::$domain);
  }

  private static function _parse_html($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    $dom->loadHTML($html);
    return $dom;
  }

}
