<?php



use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_API_Helper;
// Prevent direct access.
if (!\defined('ABSPATH')) {
    exit;
}
if (!\function_exists('edd_hidden_callback')) {
    function edd_hidden_callback($args)
    {
        if (isset($args['id'], $args['default'])) {
            Settings_API_Helper::settings_field_hidden($args);
        }
    }
}
if (!\function_exists('edd_barn2_license_callback')) {
    function edd_barn2_license_callback($args)
    {
        // Change setting back to a 'text' input. We set the type to 'barn2_license' initially so we can provide our own callback.
        $args['type'] = 'text';
        // Settings_API_Helper uses input_class instead of class.
        if (isset($args['class'])) {
            $args['input_class'] = $args['class'];
        }
        // Ensure a default is set.
        if (!isset($args['default'])) {
            $args['default'] = '';
        }
        Settings_API_Helper::settings_field_text($args);
    }
}
