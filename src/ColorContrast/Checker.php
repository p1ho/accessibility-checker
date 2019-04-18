<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\ColorContrast;

/**
 * Color Contrast Checker class for Accessibility Checker
 *
 * Assumptions:
 * 1) Default background is white
 * 2) Default font color is black
 * 3) No external CSS file is imposed on the page
 * 4) No <style> / <script> (Too hard to validate and parse)
 * 5) No 'font' CSS property used (Too hard to validate and parse)
 * 6) Property 'opacity' or equivalent is not used
 * 6) All child elements are on top of their parents
 *
 * Process:
 * As it goes through the DOM, if it detects existence of texts, it will compare
 * its color against the background color, and see if the contrast ratio is good
 * with the WCAG 2.0 standard (AA/AAA) and font-size/weight.
 *
 * As we go deeper into the DOM, the color/background-color will be updated if
 * new tags with styles are discovered. For example, if a colored container is
 * found, it is assumed it will be the new background for all texts underneath
 * it (unless another colored container is found inside this container). RGBA()
 * and HSLA() are also supported.
 *
 * Because only absolute css units (such as pt/px) and some relative units
 * (such as em/rem) can give a definite answer on whether a text is 'large',
 * dynamic css units(vw/vh) will just be defaulted to 12pt(medium).[5][6][7]
 *
 * Background-image is ignored because there is no reliable metric to tell how
 * 'accessible' fonts look on an image
 * (The test will fall back on checking the nearest color container, it is up
 * to the image user to determine if the font is readable)
 *
 * Several special cases:
 * - If <mark> is encountered without style attribute, background will be set to
 *   yellow, and font color will be set to black (per HTML standard). However,
 *   if <mark> is wrapped around block elements or inline elements with inherent
 *   style (e.g., <a>), its effect will not register.[2]
 *
 * - If <b> or <strong> is encountered, font-size will be set to bold.
 *
 * - If <a> is encountered, font-color will be set to blue (standard default).
 *
 * - If <h1> through <h6> is encountered, font-size and font-weight will be
 * adjusted automatically according to their default size [4] unless otherwise
 * specified in the style attribute.
 *
 * Consulted:
 * [1] http://webaccess.hr.umich.edu/best/color.html
 * [2] https://developer.mozilla.org/en-US/docs/Web/HTML/Element/mark
 * [3] https://github.com/andyjansson/css-unit-converter
 * [4] http://zuga.net/articles/html-heading-elements/
 * [5] https://www.w3schools.com/cssref/css_units.asp
 * [6] https://i.stack.imgur.com/E7bTc.jpg
 * [7] https://www.w3schools.com/cssref/pr_font_font-size.asp
 * [8] https://www.w3schools.com/cssref/pr_font_weight.asp
 * [9] https://www.w3.org/TR/WCAG/#contrast-minimum
 */

class Checker
{

  // Keeps track of errors
    private static $errors;

    // Keeps track of text
    private static $content;

    // Keeps mode
    private static $mode;

    // Default font-color
    private static $default_font_color = "black";

    // Default font-sizes (in pt)
    private static $default_font_sizes;

    // Block elements
    private static $block_elements;

    // Temp holder for transparent colors
    private static $parent_true_bg_color;
    private static $parent_true_font_color;

    /**
     * evaluate function.
     * If mode is not supplied, it will assume we are evaluating using WCAG 2.0 AA
     * If it is supplied but invalid, we will also assume AA.
     *
     * @param  DOMObject $dom  [The whole parsed HTML DOM Tree]
     * @param  string    $mode [either 'AA' or 'AAA']
     * @return array
     */
    public static function evaluate($dom, $mode = 'AA')
    {
        self::_init();
        self::$errors = array();

        if (count($dom->getElementsByTagName('body')) > 0) {
            $body = $dom->getElementsByTagName('body')[0];
            self::$content = $body->textContent;
        } else {
            $body = null;
        }

        $eval_array = array();

        if ($body === null) {
            $eval_array['passed'] = true;
            $eval_array['errors'] = array();
        } else {
            if (!in_array($mode, array('AA', 'AAA'))) {
                $mode = 'AA';
            }
            self::$mode = $mode;

            self::_eval_DOM(
          $body,
          self::$parent_true_bg_color,
          self::$parent_true_font_color,
          12,
          false,
          0,
          false
      );

            $eval_array['passed'] = (count(self::$errors) === 0);
            $eval_array['errors'] = self::$errors;
        }

        return $eval_array;
    }

    /**
     * require needed resources
     */
    private static function _init()
    {
        require_once "color-contrast-helpers/ColorContrastGetter.php";
        require_once "color-contrast-helpers/rgba2rgb.php";
        require_once "color-contrast-helpers/hsla2rgb.php";
        require_once "font-helper/convert2pt.php";
        require "font-helper/default_font_sizes.php";
        require "font-helper/block_elements.php";
        self::$default_font_sizes     = $default_font_sizes;
        self::$block_elements         = $block_elements;
        self::$parent_true_bg_color   = "white";
        self::$parent_true_font_color = "black";
    }

    /**
     * Recursive DOM Element parsing helper
     * @param  DOMElement   $dom_el
     * @param  String|Array $bg_color     [array form if rgb or hsl]
     * @param  String|Array $font_color   [array form if rgb or hsl]
     * @param  Integer      $font_size    [in pt]
     * @param  Boolean      $font_is_bold
     * @param  Integer      $semantic_nesting_level
     * [will increment if the current block is nested inside one of:
     *  <article>, <aside>, <nav>, <section>, because this changes <h1> size ]
     * @param Boolean       $in_mark      [in mark tag]
     * @return void
     */
    private static function _eval_DOM(
      $dom_el,
      $bg_color,
      $font_color,
      $font_size,
      $font_is_bold,
      $semantic_nesting_level,
      $in_mark
  ) {
        // skip comments
        if (get_class($dom_el) === 'DOMComment') {
            return;
        }

        // set variables
        $tag_name = $dom_el->tagName;

        // skip <style>/<script>/<br>
        if (in_array($tag_name, array('style', 'script', 'br'))) {
            return;
        }

        $text = self::_get_text_content($dom_el);

        $root_size         = 12; // assumes root font size is 12 pt to begin with
        $parent_bg_color   = $bg_color;
        $parent_font_color = $font_color;
        $parent_font_size  = $font_size;
        $parent_is_bold    = $font_is_bold;

        /* -------------------------------------------------------------------------
        The following checks applies default stylings of special HTML elements, if
        later it is found they have style attributes, these will be overwritten.
         */

        /*
        Special check 1:
        if inside <mark> and inside a block element, then <mark> loses its highlight
         - color remains 'black'
         - background resets to 'white'
         */
        if ($in_mark && in_array($tag_name, self::$block_elements)) {
            $in_mark = false;
            $bg_color = "white";
            self::$parent_true_bg_color = $bg_color;
        }

        /*
        Special check 2:
        if it encounters <mark>
         */
        if ($tag_name === "mark") {
            $font_color = "black";
            $bg_color = "yellow";
            $in_mark = true;
            self::$parent_true_bg_color = $bg_color;
        }

        /*
        Special check 3:
        if it encounters <b> or <strong>
         */
        if (in_array($tag_name, array('b', 'strong'))) {
            $font_is_bold = true;
        }

        /*
        Special check 4:
        if it encounters <a>
         */
        if ($tag_name === "a") {
            $font_color = "blue";
        }

        /*
        Special check 5:
        if it encounters semantic containers that will change <h1> size
         */
        if (in_array($tag_name, array('article', 'aside', 'nav', 'section'))) {
            $semantic_nesting_level += 1;
        }

        /*
        Special check 6:
        if it encounters heading tag, set its default size
         */
        if (self::_is_heading_tag($tag_name)) {
            // specialcase <h1>
            if ($tag_name === "h1") {
                $h_num = (int)$tag_name[1];
                $h_num += $semantic_nesting_level;
                $font_size = self::$default_font_sizes["h".$h_num];
            } else {
                $font_size = self::$default_font_sizes[$tag_name];
            }
            $font_is_bold = true;
        }

        /*--------------------------------------------------------------------------
        Parsing Style String if it exists
         */

        $style_str = $dom_el->getAttribute('style');
        if ($style_str !== "") {
            $style_properties = self::_get_style_properties(
          $style_str,
          $tag_name,
          $parent_bg_color,
          $parent_font_color,
          $parent_font_size,
          $parent_is_bold
      ); // need parent style in case of relative styles

            if ($style_properties['background-color'] !== null) {
                if ($style_properties['background-color'] !== "invalid") {
                    $bg_color = $style_properties['background-color'];
                } else {
                    self::$errors[] = (object) [
            'type' => 'invalid color',
            'property' => 'background-color',
            'tag' => $tag_name,
            'text' => $text,
          ];
                }
            }

            if ($style_properties['color'] !== null) {
                if ($style_properties['color'] !== "invalid") {
                    $font_color = $style_properties['color'];
                } else {
                    self::$errors[] = (object) [
            'type' => 'invalid color',
            'property' => 'color',
            'tag' => $tag_name,
            'text' => $text,
          ];
                }
            }

            if ($style_properties['font-size'] !== null) {
                if ($style_properties['font-size'] !== "invalid") {
                    $font_size = $style_properties['font-size'];
                } else {
                    self::$errors[] = (object) [
            'type' => 'invalid size',
            'property' => 'font-size',
            'tag' => $tag_name,
            'text' => $text,
          ];
                }
            }

            if ($style_properties['font_is_bold'] !== null) {
                if ($style_properties['font_is_bold'] !== "invalid") {
                    $font_is_bold = $style_properties['font_is_bold'];
                } else {
                    self::$errors[] = (object) [
            'type' => 'invalid weight',
            'property' => 'font-weight',
            'tag' => $tag_name,
            'text' => $text,
          ];
                }
            }
        } else {
            // if no style string and font color has alpha value
            $parent_true = self::$parent_true_font_color;
            if (isset($parent_true['a'])) {
                // rgba
                if (isset($parent_true['r']) &&
            isset($parent_true['g']) &&
            isset($parent_true['b'])) {
                    $font_color = rgba2rgb($parent_true, $bg_color);
                }
                // hsla
                if (isset($parent_true['h']) &&
            isset($parent_true['s']) &&
            isset($parent_true['l'])) {
                    $font_color = hsla2rgb($parent_true, $bg_color);
                }
            }
        }

        $font_is_large = self::_is_large_font($font_size, $font_is_bold);

        /*--------------------------------------------------------------------------
        Conduct Color Contrast Check
         */
        $evaluation = ColorContrastGetter::evaluate($font_color, $bg_color);
        $contrast_ratio = number_format($evaluation['contrast_ratio'], 2);
        if (self::$mode === 'AA') {
            if ($font_is_large) {
                if (!$evaluation['passed_wcag_2_aa_lg']) {
                    self::$errors[] = (object) [
            'type' => 'low contrast',
            'mode' => 'AA',
            'tag' => $tag_name,
            'text' => $text,
            'text_is_large' => true,
            'contrast_ratio' => $contrast_ratio,
          ];
                }
            } else {
                if (!$evaluation['passed_wcag_2_aa']) {
                    self::$errors[] = (object) [
            'type' => 'low contrast',
            'mode' => 'AA',
            'tag' => $tag_name,
            'text' => $text,
            'text_is_large' => false,
            'contrast_ratio' => $contrast_ratio,
          ];
                }
            }
        } elseif ($mode == 'AAA') {
            if ($font_is_large) {
                if (!$evaluation['passed_wcag_2_aaa_lg']) {
                    self::$errors[] = (object) [
            'type' => 'low contrast',
            'mode' => 'AAA',
            'tag' => $tag_name,
            'text' => $text,
            'text_is_large' => true,
            'contrast_ratio' => $contrast_ratio,
          ];
                }
            } else {
                if (!$evaluation['passed_wcag_2_aaa']) {
                    self::$errors[] = (object) [
            'type' => 'low contrast',
            'mode' => 'AA',
            'tag' => $tag_name,
            'text' => $text,
            'text_is_large' => false,
            'contrast_ratio' => $contrast_ratio,
          ];
                }
            }
        }

        /*--------------------------------------------------------------------------
        Go through child elements
         */
        $child_elements = self::_get_childElements($dom_el);
        foreach ($child_elements as $child_element) {
            self::_eval_DOM(
          $child_element,
          $bg_color,
          $font_color,
          $font_size,
          $font_is_bold,
          $semantic_nesting_level,
          $in_mark
      );
        }
    }

    /**
     * _get_childElements helper. Because DOMElement->childNodes also returns
     * DOMText which is not what we want, this helps with filtering those out.
     * @param  DOMElement $dom_el
     * @return array      [Array containing only DOMElement objects]
     */
    private static function _get_childElements($dom_el)
    {
        $child_nodes    = $dom_el->childNodes;
        $child_elements = array();
        foreach ($child_nodes as $node) {
            if (property_exists($node, 'tagName')) {
                $child_elements[] = $node;
            }
        }
        return $child_elements;
    }

    /**
     * _get_text_content helper.
     * Gets text content that are not wrapped in any other tags.
     * If it encounters another child tag, it will replace its content with '<tag>...</tag>'.
     * If it encounters <br>, it will replace it with ' '.
     *
     * Example:
     * <div>
     *   This is
     *   <div>I'm wrapped</div>
     *   some text
     * </div>
     *
     * Running this function over the node would return 'This is <div>...</div> some text'.
     *
     * @param  DOMElement $dom_el
     * @return string
     */
    private static function _get_text_content($dom_el)
    {
        $text = '';
        foreach ($dom_el->childNodes as $childNode) {
            if (get_class($childNode) === 'DOMText') {
                $text .= htmlspecialchars_decode(trim($childNode->wholeText));
            } elseif (get_class($childNode) === 'DOMComment') {
                continue;
            } else {
                if ($childNode->tagName == 'br') {
                    $text .= ' ';
                } else {
                    $tag = $childNode->tagName;
                    $text .= "<$tag>...</$tag>";
                }
            }
        }
        return str_replace(array("\r", "\n"), '', $text);
    }

    /**
     * _get_style_properties helper.
     * This parses the whole style attribute string and returns array categorized
     * by style properties we need.
     * font-weight is replaced by font-is-bold for easier parsing
     *
     * @param  String  $style_str      [the string in the 'style' attribute]
     * @param  String  $tag_name       [the tag_name (needed for font_size)]
     * @param  Array|String $parent_bg_color
     * [parent's rendered background color. Could be different from parent's true
     * value if parent had transparent color]
     * @param  Array|String $parent_font_color
     * [parent's rendered font color. Could be different from parent's true value
     * if parent had transparent color]
     * @param  Integer $font_size      [parent's font size]
     * @param  Boolean $parent_is_bold [whether the parent font was bold]
     * @return array  [array containing color and font style info]
     */
    private static function _get_style_properties(
      $style_str,
      $tag_name,
      $parent_bg_color,
      $parent_font_color,
      $parent_font_size,
      $parent_is_bold
  ) {

    // parse style string
        $style_items = explode(';', strtolower($style_str));
        $styles_raw = array();
        foreach ($style_items as $item) {
            $item_split = array_map('trim', explode(':', $item));
            if (count($item_split) <= 1) {
                break;
            } else {
                $styles_raw[$item_split[0]] = $item_split[1];
            }
        }

        // initialize the array to be returned
        $styles = array(
      'background-color' => null,
      'color' => null,
      'font-size' => null,
      'font_is_bold' => null,
    );

        // parse background (note: this must be done before parsing font)
        $property = 'background-color';
        $parent_true = self::$parent_true_bg_color;
        if (isset($styles_raw[$property])) {
            $value = $styles_raw[$property];

            if (self::_is_color_function($value)) {
                $child_color_array = self::_color_function_to_array($value);
                self::$parent_true_bg_color = $child_color_array;

                // if it's transparent
                if (isset($child_color_array['a'])) {
                    // rgba
                    if (isset($child_color_array['r']) &&
              isset($child_color_array['g']) &&
              isset($child_color_array['b'])) {
                        $styles[$property] = rgba2rgb($child_color_array, $parent_bg_color);
                    }
                    // hsla
                    if (isset($child_color_array['h']) &&
              isset($child_color_array['s']) &&
              isset($child_color_array['l'])) {
                        $styles[$property] = hsla2rgb($child_color_array, $parent_bg_color);
                    }
                } else {
                    $styles[$property] = $child_color_array;
                }
            } elseif (self::_is_hex($value) || self::_is_color_name($value)) {
                $styles[$property] = $value;
                self::$parent_true_bg_color = $value;
            } elseif ($value === "transparent" || $value === "initial") {

        // can set these to rendered parent bg color because connection to the
                // potential transparency is lost.
                $styles[$property] = $parent_bg_color;
                self::$parent_true_bg_color = $parent_bg_color;
            } elseif ($value === "inherit") {

        // if it's transparent
                if (gettype($parent_true) === 'array' &&
            isset($parent_true['a'])) {

          // rgba
                    if (isset($parent_true['r']) &&
              isset($parent_true['g']) &&
              isset($parent_true['b'])) {
                        $styles[$property] = rgba2rgb($parent_true, $parent_bg_color);
                    }
                    // hsla
                    if (isset($parent_true['h']) &&
              isset($parent_true['s']) &&
              isset($parent_true['l'])) {
                        $styles[$property] = hsla2rgb($parent_true, $parent_bg_color);
                    }
                } else {
                    $styles[$property] = $parent_bg_color;
                }
            } else {
                $styles[$property] = "invalid";
            }
        }

        // parse font color
        $property = 'color';
        $parent_true = self::$parent_true_font_color;
        $bg_color = $styles['background-color'] ?? $parent_bg_color;

        if (isset($styles_raw[$property])) {
            $value = $styles_raw[$property];

            if (self::_is_color_function($value)) {
                $child_color_array = self::_color_function_to_array($value);
                self::$parent_true_font_color = $child_color_array;

                // if it's transparent
                if (isset($child_color_array['a'])) {
                    // rgba
                    if (isset($child_color_array['r']) &&
              isset($child_color_array['g']) &&
              isset($child_color_array['b'])) {
                        $styles[$property] = rgba2rgb($child_color_array, $bg_color);
                    }
                    // hsla
                    if (isset($child_color_array['h']) &&
              isset($child_color_array['s']) &&
              isset($child_color_array['l'])) {
                        $styles[$property] = hsla2rgb($child_color_array, $bg_color);
                    }
                } else {
                    $styles[$property] = $child_color_array;
                }
            } elseif (self::_is_hex($value) || self::_is_color_name($value)) {
                $styles[$property] = $value;
                self::$parent_true_font_color = $value;
            } elseif ($value === "transparent") {
                $styles[$property] = $bg_color;
                self::$parent_true_font_color = array(
          'r' => 0,
          'g' => 0,
          'b' => 0,
          'a' => 0
        );
            } elseif ($value === "initial") {

        // by default texts are black
                $styles[$property] = self::$default_font_color;
                self::$parent_true_font_color = self::$default_font_color;
            } elseif ($value === "inherit") {

        // if it's transparent
                if (gettype($parent_true) === 'array' &&
            isset($parent_true['a'])) {

          // rgba
                    if (isset($parent_true['r']) &&
              isset($parent_true['g']) &&
              isset($parent_true['b'])) {
                        $styles[$property] = rgba2rgb($parent_true, $bg_color);
                    }
                    // hsla
                    if (isset($parent_true['h']) &&
              isset($parent_true['s']) &&
              isset($parent_true['l'])) {
                        $styles[$property] = hsla2rgb($parent_true, $bg_color);
                    }
                } else {
                    $styles[$property] = $parent_true;
                }
            } else {
                $styles[$property] = "invalid";
            }
        } else {
            // if its parent is transparent (which means it will inherit)
            if (gettype($parent_true) === 'array' &&
          isset($parent_true['a'])) {

        // rgba
                if (isset($parent_true['r']) &&
            isset($parent_true['g']) &&
            isset($parent_true['b'])) {
                    $styles[$property] = rgba2rgb($parent_true, $bg_color);
                }
                // hsla
                if (isset($parent_true['h']) &&
            isset($parent_true['s']) &&
            isset($parent_true['l'])) {
                    $styles[$property] = hsla2rgb($parent_true, $bg_color);
                }
            }
            // if parent non-transparent, child inherits parent. (No action needed)
        }

        /*
        As mentioned in the introduction, because 'font' introduces too many
        complications, it is skipped and we will only parse 'font-weight' and
        'font-size' if they exist.
         */

        // parse font-size
        if (isset($styles_raw['font-size'])) {
            $property = 'font-size';
            $value = $styles_raw[$property];
            try {
                $styles[$property] = convert2pt(
            $tag_name,
            $value,
            $parent_font_size,
            $root_size = self::$default_font_sizes['html']
        );
            } catch (Exception $e) {
                $styles[$property] = "invalid";
            }
        }

        // parse font-weight
        if (isset($styles_raw['font-weight'])) {
            $property = 'font-weight';
            $value = $styles_raw[$property];
            try {
                $styles['font_is_bold'] = self::_font_is_bold(
            $tag_name,
            $value,
            $parent_is_bold
        );
            } catch (Exception $e) {
                $styles['font_is_bold'] = "invalid";
            }
        }

        return $styles;
    }

    /**
     * _is_heading_tag function. check if passed in tag name ($s) is one of the
     * valid heading tags
     * @param  String  $s [tag name]
     * @return boolean
     */
    private static function _is_heading_tag($s)
    {
        return preg_match('%^h[1-6]{1}$%iu', $s);
    }

    /**
     * check if color value is rgb() / rgba() / hsl() / hsla()
     */
    private static function _is_color_function($s)
    {
        return preg_match('%^((rgb)|(hsl))a?(\({1}.*\){1})$%iu', $s);
    }

    /**
     * converts rgb() / rgba() / hsl() / hsla() to array form
     * Note: hsl() / hsla() has % in the values, it will be trimmed
     */
    private static function _color_function_to_array($s)
    {
        if (strpos($s, 'rgba(') === 0) {
            $num_str = substr($s, 5, -1);
            $num_array = array_map('trim', explode(',', $num_str));
            if (count($num_array) != 4) {
                $num_array[3] = 1;
            }
            return array(
        'r' => (float)$num_array[0],
        'g' => (float)$num_array[1],
        'b' => (float)$num_array[2],
        'a' => (float)$num_array[3],
      );
        } elseif (strpos($s, 'rgb(') === 0) {
            $num_str = substr($s, 4, -1);
            $num_array = array_map('trim', explode(',', $num_str));
            return array(
        'r' => (float)$num_array[0],
        'g' => (float)$num_array[1],
        'b' => (float)$num_array[2],
      );
        } elseif (strpos($s, 'hsla(') === 0) {
            $num_str = substr($s, 5, -1);
            $num_array = array_map(function ($x) {
                return trim(str_replace('%', '', $x));
            }, explode(',', $num_str));
            if (count($num_array) != 4) {
                $num_array[3] = 1;
            }
            return array(
        'h' => (float)$num_array[0],
        's' => (float)$num_array[1],
        'l' => (float)$num_array[2],
        'a' => (float)$num_array[3],
      );
        } elseif (strpos($s, 'hsl(') === 0) {
            $num_str = substr($s, 4, -1);
            $num_array = array_map(function ($x) {
                return trim(str_replace('%', '', $x));
            }, explode(',', $num_str));
            return array(
        'h' => (float)$num_array[0],
        's' => (float)$num_array[1],
        'l' => (float)$num_array[2],
      );
        }
    }

    /**
     * check if string is '#' followed by 3 or 6 characters
     */
    private static function _is_hex($s)
    {
        return preg_match('%^#{1}([\d\w]{3}|[\d\w]{6})$%iu', $s);
    }

    /**
     * check if string is a valid color_name
     */
    private static function _is_color_name($s)
    {
        require "color-contrast-helpers/color_name_mapping.php";
        return isset($color_name_mapping[$s]);
    }

    /**
     * This is for css attribute 'font-weight', see if it's bold
     * https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight
     * http://htmldog.com/references/css/properties/font-weight/
     *
     * @param String  $tag_name       [tag this is style found in]
     * @param String  $value          [actual style property value]
     * @param Boolean $parent_is_bold [whether parent was bold]
     * @return boolean
     */
    private static function _font_is_bold($tag_name, $value, $parent_is_bold)
    {
        if (in_array($value, array("bold","bolder"))) {
            return true;
        }
        if (in_array($value, array("normal","lighter","unset"))) {
            return false;
        }
        if ($value === "inherit") {
            return $parent_is_bold;
        }
        if ($value === "initial") {
            // I tested <b> and <strong>, when set to initial, they are not bolded.
            return self::_is_heading_tag($tag_name);
        }
        if (is_numeric($value)) {
            $num = (float)$value;
            if ($num > 1 && $num < 1000) {
                return $num > 700;
            } else {
                throw new Exception("invalid Argument: numeric font-weight out of range. ($num was given)");
            }
        } else {
            throw new Exception("Invalid Argument: invalid font-weight. ('$value' was given)");
        }
    }

    /**
     * _is_large_font function.
     * Takes in a size value (in pt) and a boolean on whether it is bold,
     * then checks against WCAG 2.0 guideline to see if it qualifies as large.
     * See: https://developer.paciellogroup.com/blog/2012/05/whats-large-text-in-wcag-2-0-parlance/
     * @param  Number   $font_size   [in pt]
     * @param  Boolean  $is_bold     [see if the text is bold]
     * @return boolean
     */
    private static function _is_large_font($font_size, $is_bold)
    {
        return $font_size >= 18 || ($font_size >= 14 && $is_bold);
    }
}
