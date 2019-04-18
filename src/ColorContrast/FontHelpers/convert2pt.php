<?php declare(strict_types=1);

/**
 * convert2pt function
 *
 * see:
 * https://stackoverflow.com/questions/5912528/font-size-translating-to-actual-point-size
 * https://www.w3schools.com/cssref/css_units.asp
 *
 * @param  string  $tag_name
 * @param  string  $size_value
 * @param  float  $parent_size
 * @param  int $root_size [Optional, default is 12pt, can be overriden]
 * @return float [converted to pt]
 */

function convert2pt(string $tag_name, string $size_value, float $parent_size, int $root_size = 12): float
{
    // if it's negative number, throw error
    if ($size_value[0] === '-') {
        throw new Exception("Invalid Font-size: font-size may not be negative.");
    }

    require "default_font_sizes.php";

    $css_value_lookup = array(
    'medium'   => 12,
    'xx-small' => 7,
    'x-small'  => 7.5,
    'small'    => 10,
    'large'    => 13.5,
    'x-large'  => 18,
    'xx-large' => 24,
    'smaller'  => 10,
    'larger'   => 14,
  );
    if (isset($css_value_lookup[$size_value])) {
        return $css_value_lookup[$size_value];
    } elseif ($size_value === "initial") {
        if (isset($default_font_sizes[$tag_name])) {
            return $default_font_sizes[$tag_name];
        } else {
            return $default_font_sizes['html'];
        }
    } elseif ($size_value === "inherit") {
        return $parent_size;
    } else {
        if (strpos($size_value, 'rem') !== false) {
            /*
            It represents the percentage of root_size (12pt by default)
             */
            return (float)substr($size_value, 0, -3) * $root_size;
        }
        if (strpos($size_value, 'em') !== false) {
            /*
            It represents the percentage of parent_size (given in the arguments)
             */
            return (float)substr($size_value, 0, -2) * $parent_size;
        }
        if (strpos($size_value, '%') !== false) {
            /*
            Divide it by 100, then same procedure as 'em'.
             */
            return ((float)substr($size_value, 0, -1) / 100) * $parent_size;
        }
        if (strpos($size_value, 'ex') !== false) {
            /*
            It's the height of the parent's lowercase letter, but there's no straight
            forward conversion. There was a hacky solution for legacy browsers where
            1ex it's .5em, so I'll use that here for a general case.
            https://stackoverflow.com/questions/918612/what-is-the-value-of-the-css-ex-unit
            https://stackoverflow.com/questions/12470954/javascript-convert-ex-to-px
             */
            return (float)substr($size_value, 0, -2) * ($parent_size / 2);
        }
        if (strpos($size_value, 'ch') !==false) {
            /*
            1ch is roughly 6pt, so I'm just going to use that as rule of thumb
            https://jwilsson.com/unit-converter/
             */
            return (float)substr($size_value, 0, -2) * 6;
        }
        if (strpos($size_value, 'v') !== false) {
            /*
            as mentioned in ColorContrastChecker, we'll default these dynamic units
            to the browser default font size as our solution.
            */
            return $default_font_sizes['html'];
        }

        /*
        All previous if statements, if entered, will return, so if execution gets
        to this line, it means the unit must either be absolute or invalid.
        The absolute conversion table was taken from:
        https://github.com/andyjansson/css-unit-converter/blob/master/index.js
         */
        $unit2pt = array(
      'px' => .75,
      'cm' => 72.0/2.54,
      'mm' => 72.0/25.4,
      'in' => 72,
      'pc' => 12,
      'pt' => 1,
    );

        if (isset($unit2pt[substr($size_value, -2)])) {
            $unit = substr($size_value, -2);
            return (float)substr($size_value, 0, -2) * $unit2pt[$unit];
        } else {
            throw new Exception("Conversion Error: Unable to parse given size value.");
        }
    }
}
