<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\ColorContrast;

require_once "CalculatorHelpers/convert2rgb.php";

/**
 * Color Contrast Calculator
 *
 * Transcribed and modified from
 * https://snook.ca/technical/colour_contrast/colour.html
 * (Code to check taken from function updateColourResults())
 */

class Calculator
{

  /**
   * Takes in 2 colors (one of: hex string, or rgb/hsl/hsv associative arrays,
   * the keys in this array should be in lower case)
   * and check the contrast between the 2, will return evaluation in an
   * associative array.
   *
   * Note: if hex is passed, it must include the '#' and has a length of either
   * 4 or 7 including the '#'.
   *
   * only WCAG 2.0 is used because WCAG 1.0 is outdated.
   *
   * @param  string|array $color_1
   * @param  string|array $color_2
   * @return array
   * array(
   *   "contrast_ratio"         => float
   *   "passed_wcag_2_aa"       => boolean
   *   "passed_wcag_2_aa_lg"    => boolean
   *   "passed_wcag_2_aaa"      => boolean
   *   "passed_wcag_2_aaa_lg"   => boolean
   * )
   *
   */
    public static function evaluate($color_1, $color_2): array
    {
        $eval_array = array();
        $color_1_rgb = array_map(function ($x) {
            return $x/255;
        }, convert2rgb($color_1));
        $color_2_rgb = array_map(function ($x) {
            return $x/255;
        }, convert2rgb($color_2));
        $l1 = self::_get_luminance($color_1_rgb);
        $l2 = self::_get_luminance($color_2_rgb);

        if ($l1 >= $l2) {
            $ratio = ($l1 + .05) / ($l2 + .05);
        } else {
            $ratio = ($l2 + .05) / ($l1 + .05);
        }
        $ratio = round($ratio * 100) / 100; // round to 2 decimal places
        $eval_array['contrast_ratio'      ] = $ratio;
        $eval_array['passed_wcag_2_aa'    ] = $ratio >= 4.5;
        $eval_array['passed_wcag_2_aa_lg' ] = $ratio >= 3;
        $eval_array['passed_wcag_2_aaa'   ] = $ratio >= 7;
        $eval_array['passed_wcag_2_aaa_lg'] = $ratio >= 4.5;

        return $eval_array;
    }

    /**
     * _get_luminance function. Converts RGB to luminance to test contrast
     * RGB values has to be between 0 and 1!!
     *
     * @param  array  $rgb [array('r' => number, 'g' => number, 'b' => number)]
     * @return float $luminance
     */
    private static function _get_luminance(array $rgb): float
    {
        // gamma correct rgb
        foreach ($rgb as $color => $value) {
            if ($value < 0.03928) {
                $rgb[$color] = $rgb[$color] / 12.92;
            } else {
                $rgb[$color] = pow((($rgb[$color] + 0.055) / 1.055), 2.4);
            }
        }
        $l = (0.2126 * $rgb['r']) + (0.7152 * $rgb['g']) + (0.0722 * $rgb['b']);
        return $l;
    }
}
