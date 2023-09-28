<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WooCommerce\Admin;

/**
 * Utility functions for WooCommerce settings.
 *
 * @package    Barn2\barn2-lib
 * @author     Barn2 Plugins <support@barn2.com>
 * @license    GPL-3.0
 * @copyright  Barn2 Media Ltd
 * @version    1.1
 * @deprecated Replaced by Barn2\Lib\Admin\Settings_Util
 */
class Settings_Util
{
    public static function bool_to_checkbox_setting($bool)
    {
        return (bool) $bool ? 'yes' : 'no';
    }
    public static function checkbox_setting_to_bool($value)
    {
        return \in_array($value, ['yes', \true], \true) ? \true : \false;
    }
    public static function get_checkbox_option($option, $default = \false)
    {
        return self::checkbox_setting_to_bool(\get_option($option, $default));
    }
    public static function get_custom_attributes($field)
    {
        $custom_attributes = [];
        if (!empty($field['custom_attributes']) && \is_array($field['custom_attributes'])) {
            foreach ($field['custom_attributes'] as $attribute => $attribute_value) {
                $custom_attributes[] = \esc_attr($attribute) . '="' . \esc_attr($attribute_value) . '"';
            }
        }
        return \implode(' ', $custom_attributes);
    }
}
