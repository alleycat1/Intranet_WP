<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin;

/**
 * Basic interface implemented by all Barn2 plugins.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2
 */
interface Plugin
{
    /**
     * Get the plugin ID.
     *
     * $return int The plugin ID.
     */
    public function get_id();
    /**
     * Gets the name of this plugin.
     *
     * @return string The plugin name.
     */
    public function get_name();
    /**
     * Gets the plugin version number (e.g. 1.3.2).
     *
     * @return string The version number.
     */
    public function get_version();
    /**
     * Gets the full path to the main plugin file.
     *
     * @return string The main plugin file.
     */
    public function get_file();
    /**
     * Get the 'basename' for the plugin (e.g. my-plugin/my-plugin.php).
     *
     * @return string The plugin basename.
     */
    public function get_basename();
    /**
     * Get the slug for this plugin (e.g. my-plugin).
     *
     * @return string The plugin slug.
     */
    public function get_slug();
    /**
     * Get the full path to the plugin folder, with trailing slash (e.g. /wp-content/plugins/my-plugin/).
     *
     * @return string The plugin directory path.
     */
    public function get_dir_path();
    /**
     * Get the URL to the plugin folder, with trailing slash.
     *
     * @return string (URL)
     */
    public function get_dir_url();
    /**
     * Is this plugin a WooCommerce extension?
     *
     * @return boolean true if it's a WooCommerce extension.
     */
    public function is_woocommerce();
    /**
     * Is this plugin an Easy Digital Downloads extension?
     *
     * @return boolean true if it's an EDD extension.
     */
    public function is_edd();
    /**
     * Get the settings page URL in the WordPress admin.
     *
     * @return string (URL)
     */
    public function get_settings_page_url();
    /**
     * Get the documentation URL for this plugin.
     *
     * @return string (URL)
     */
    public function get_documentation_url();
    /**
     * Get the support URL for this plugin.
     *
     * @return string (URL)
     */
    public function get_support_url();
}
