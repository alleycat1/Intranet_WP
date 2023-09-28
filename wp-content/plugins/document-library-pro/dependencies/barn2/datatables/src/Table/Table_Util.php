<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table;

/**
 * Table utilities.
 *
 * @package   Barn2\datatables
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Util
{
    /**
     * Formats an array of attributes into a string to be used inside a HTML tag.
     * The first attribute will contain a single space before it.
     *
     * E.g.
     * <code>Table_Util::format_attributes( array( 'data-thing' => 'foo', 'class' = 'test' ) )</code>
     *
     * would give this string:
     *
     * <code>' data-thing="foo" class="test"'</code>
     *
     * @param array $atts The attributes to format
     * @return string The attribute string
     */
    public static function format_attributes($atts)
    {
        if (empty($atts)) {
            return '';
        }
        $result = '';
        foreach ($atts as $name => $value) {
            // Ignore null attributes and empty strings
            if ('' === $value || null === $value) {
                continue;
            }
            if (!\is_string($value)) {
                $value = \var_export($value, \true);
            }
            // If attribute contains a double-quote, wrap it in single-quotes to avoid parsing errors
            if (\false === \strpos($value, '"')) {
                $result .= \sprintf(' %s="%s"', $name, \esc_attr($value));
            } else {
                // Escape the attribute, then convert double-quotes back
                $result .= \sprintf(" %s='%s'", $name, \str_replace('&quot;', '"', \esc_attr($value)));
            }
        }
        return $result;
    }
}
