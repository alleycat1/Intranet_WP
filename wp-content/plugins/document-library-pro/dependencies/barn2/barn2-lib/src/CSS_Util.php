<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib;

/**
 * Utility functions for building CSS.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.1
 */
class CSS_Util
{
    public static function build_background_style($bg_color, $important = \false)
    {
        if (!$bg_color) {
            return '';
        }
        $important_dec = $important ? ' !important' : '';
        return \sprintf('background-color: %1$s%2$s;', $bg_color, $important_dec);
    }
    public static function build_border_style($option, $borders = 'all', $important = \false)
    {
        $option = \wp_parse_args($option, ['size' => '', 'color' => '']);
        if (!\is_numeric($option['size']) && empty($option['color'])) {
            return '';
        }
        if (!\is_array($borders)) {
            $borders = \array_filter((array) $borders);
        }
        $result = '';
        $border_size = \is_numeric($option['size']) ? $option['size'] . 'px' : '';
        $border_color = $option['color'];
        $important_dec = $important ? ' !important' : '';
        foreach ($borders as $border) {
            $border_edge = '';
            if (\in_array($border, ['top', 'left', 'bottom', 'right'], \true)) {
                $border_edge = $border . '-';
            }
            if ($border_size || $border_color) {
                $result .= \sprintf('border-%1$sstyle: solid%2$s;', $border_edge, $important_dec);
            }
            if ($border_size) {
                $result .= \sprintf('border-%1$swidth: %2$s%3$s;', $border_edge, $border_size, $important_dec);
            }
            if ($border_color) {
                $result .= \sprintf('border-%1$scolor: %2$s%3$s;', $border_edge, $border_color, $important_dec);
            }
        }
        return $result;
    }
    public static function build_font_style($option, $important = \false)
    {
        $option = \wp_parse_args($option, ['size' => '', 'color' => '']);
        if (!\is_numeric($option['size']) && empty($option['color'])) {
            return '';
        }
        $style = '';
        $important_dec = $important ? ' !important' : '';
        if (\is_numeric($option['size'])) {
            $style .= \sprintf('font-size: %1$upx%2$s;', $option['size'], $important_dec);
        }
        if (!empty($option['color'])) {
            $style .= \sprintf('color: %1$s%2$s;', $option['color'], $important_dec);
        }
        return $style;
    }
}
