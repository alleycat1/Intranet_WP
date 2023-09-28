<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard;

/**
 * Utility methods.
 */
class Util
{
    /**
     * Parse an array and format it to match the pattern supported by the react
     * select control component.
     *
     * @param array $array
     * @param boolean $with_empty whether to display a "select an option" empty option.
     * @return array
     */
    public static function parse_array_for_dropdown(array $array, bool $with_empty = \true)
    {
        $values = [];
        if ($with_empty) {
            $values[] = ['value' => '', 'label' => __('Select an option', 'document-library-pro')];
        }
        foreach ($array as $key => $value) {
            $values[] = ['value' => $key, 'label' => \html_entity_decode($value)];
        }
        return $values;
    }
    /**
     * Parse an array and format it to match the pattern supported by the react
     * select control component.
     *
     * @param array $array
     * @return array
     */
    public static function parse_array_for_radio(array $array)
    {
        $values = [];
        foreach ($array as $key => $value) {
            $values[] = ['value' => \strval($key), 'label' => \html_entity_decode($value)];
        }
        return $values;
    }
    /**
     * Sanitize anything.
     *
     * @param mixed $var the thing to sanitize.
     * @return mixed
     */
    public static function clean($var)
    {
        if (\is_array($var)) {
            return \array_map('self::clean', $var);
        } else {
            return \is_scalar($var) ? \sanitize_text_field($var) : $var;
        }
    }
    /**
     * Retrieve an option from the database
     * handling boolean-like values from checkboxes
     *
     * @param string $option_name The name of the option to be returned.
     * @param string $type The type of control.
     * @return mixed The value of the option.
     */
    public static function get_wc_option(string $option_name, string $type = '')
    {
        $value = \get_option($option_name);
        if ('checkbox' === $type) {
            $value = \in_array($value, ['yes', 'true', \true], \true);
        }
        return $value;
    }
    /**
     * Takes an array of WooCommerce settings and formats it to match the pattern
     * supported by the setup wizard.
     *
     * @param array $settings
     * @param array $pluck list of settings you wish to keep by id.
     * @return array
     */
    public static function pluck_wc_settings(array $settings, array $pluck)
    {
        $fields = [];
        foreach ($settings as $setting) {
            $setting_id = isset($setting['id']) ? $setting['id'] : \false;
            if (!$setting_id) {
                continue;
            }
            if (\in_array($setting_id, $pluck)) {
                $type = $setting['type'];
                if ($setting['type'] === 'sectionend') {
                    continue;
                }
                switch ($setting['type']) {
                    case 'multi_text':
                        $type = 'text_repeater';
                        break;
                }
                $label = isset($setting['title']) ? $setting['title'] : '';
                if ($type === 'title' || $type === 'heading') {
                    $label = $setting['name'];
                }
                if ('checkbox' === $type && isset($setting['checkboxgroup'])) {
                    $label = $setting['desc'];
                }
                $description = \false;
                if (isset($setting['desc_tip'])) {
                    $description = $setting['desc_tip'];
                }
                if (isset($setting['desc'])) {
                    $description = $setting['desc'];
                }
                $fields[$setting_id] = ['type' => $type, 'label' => $label, 'description' => $description, 'value' => self::get_wc_option($setting_id, $type)];
                if (isset($setting['options']) && $type !== 'radio') {
                    $fields[$setting_id]['options'] = self::parse_array_for_dropdown($setting['options']);
                }
                if (isset($setting['options']) && $type === 'radio') {
                    $fields[$setting_id]['options'] = self::parse_array_for_radio($setting['options']);
                }
                if ($type === 'single_select_page') {
                    $fields[$setting_id]['type'] = 'select';
                    $fields[$setting_id]['options'] = self::parse_array_for_dropdown(self::get_pages(\true));
                }
            }
        }
        return $fields;
    }
    /**
     * Query the upsells api and check if the provided license belongs to an access pass.
     *
     * @param object $plugin the plugin instance
     * @param string $license_key
     * @return bool
     */
    public static function license_is_access_pass($plugin, $license_key)
    {
        $is_access_pass = \false;
        $rest_url = 'https://barn2.com/wp-json/upsell/v1/validate/';
        $args = ['license' => $license_key];
        $request = \wp_remote_get(\add_query_arg($args, $rest_url));
        $response = \wp_remote_retrieve_body($request);
        $response = \json_decode($response, \true);
        if (\wp_remote_retrieve_response_code($request) === 200) {
            if (isset($response['is_access_pass'])) {
                $is_access_pass = (bool) $response['is_access_pass'];
                $item_id = $plugin->get_id();
                if ($is_access_pass === \true) {
                    \update_option("barn2_plugin_{$item_id}_license_is_pass", \true);
                } else {
                    \delete_option("barn2_plugin_{$item_id}_license_is_pass");
                }
            }
        }
        return $is_access_pass;
    }
    /**
     * Retrieve UTM id via the barn2.com api.
     *
     * @param object $plugin instance of the plugin.
     * @return string
     */
    public static function get_remote_utm_id($plugin)
    {
        $utm_id = \get_transient($plugin->get_slug() . '_remote_utm_id');
        $rest_url = 'https://api.barn2.com/wp-json/upsell/v1/get/';
        $args = ['plugin' => $plugin->get_slug()];
        if (\false === $utm_id) {
            $request = \wp_remote_get(\add_query_arg($args, $rest_url));
            $response = \wp_remote_retrieve_body($request);
            $response = \json_decode($response, \true);
            if (\wp_remote_retrieve_response_code($request) === 200) {
                if (isset($response['utm_prefix'])) {
                    $utm_id = $response['utm_prefix'];
                    \set_transient($plugin->get_slug() . '_remote_utm_id', $utm_id, \WEEK_IN_SECONDS);
                }
            }
        }
        return $utm_id;
    }
    /**
     * Get an array of pages of the site.
     *
     * @param bool $exclude_empty whether or not an empty option should be displayed within the dropdown.
     * @return array
     */
    public static function get_pages($exclude_empty = \false)
    {
        $pages = \get_pages();
        $options = [];
        if (!$exclude_empty) {
            $options[] = '';
        }
        if (!empty($pages) && \is_array($pages)) {
            foreach ($pages as $page) {
                $options[\absint($page->ID)] = \esc_html($page->post_title);
            }
        }
        return $options;
    }
    /**
     * Generate an url containing the UTM parameters
     * required by the wizard.
     *
     * @param string $url
     * @param string|bool $utm_id UTM ID, example: wro
     * @param object|bool $plugin plugin instance.
     * @return string
     */
    public static function generate_utm_url(string $url, $utm_id = \false, $plugin = \false)
    {
        $utm = $plugin ? \get_transient($plugin->get_slug() . '_remote_utm_id') : \false;
        if (!$utm) {
            $utm = $utm_id;
        }
        return \add_query_arg(['utm_source' => 'wizard', 'utm_medium' => 'wizard', 'utm_campaign' => "{$utm}-wizard", 'utm_content' => "{$utm}-wizard"], $url);
    }
    /**
     * Retrieves an array of internal WP dependencies for bundled JS files.
     *
     * @param Barn2\Lib\Plugin $plugin
     * @param string           $filename The filepath of the JS file relative to the plugin's 'js' directory. Also supports supplying the full path to the file relative to the plugin root.
     * @return array
     */
    public static function get_script_dependencies($plugin, $filename)
    {
        $script_dependencies_file = $plugin->get_dir_path() . 'assets/js/wp-dependencies.json';
        $script_dependencies = \file_exists($script_dependencies_file) ? \file_get_contents($script_dependencies_file) : \false;
        // bail if the wp-dependencies.json file doesn't exist
        if ($script_dependencies === \false) {
            return ['dependencies' => [], 'version' => ''];
        }
        $script_dependencies = \json_decode($script_dependencies, \true);
        // if the asset doesn't exist, and the path is relative to the 'js' directory then try a full path
        if (!isset($script_dependencies[$filename]) && \strpos($filename, './assets/js') === \false && isset($script_dependencies[\sprintf('./assets/js/%s', $filename)])) {
            $filename = \sprintf('./assets/js/%s', $filename);
        }
        if (!isset($script_dependencies[$filename])) {
            return ['dependencies' => [], 'version' => ''];
        }
        return $script_dependencies[$filename];
    }
}
