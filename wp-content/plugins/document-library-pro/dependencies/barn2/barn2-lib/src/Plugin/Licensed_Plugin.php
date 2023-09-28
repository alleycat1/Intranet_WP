<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin;

/**
 * Extends the Plugin interface to add additional functions for licensed plugins.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.1
 */
interface Licensed_Plugin extends Plugin
{
    /**
     * Get the plugin license object.
     *
     * @return Barn2\Lib\Plugin\License\License
     */
    public function get_license();
    /**
     * Does this plugin have a valid license?
     *
     * @return boolean true if valid.
     */
    public function has_valid_license();
    /**
     * Get the license setting for the plugin.
     *
     * @return Barn2\Lib\Plugin\License\Admin\License_Setting
     */
    public function get_license_setting();
    /**
     * Get the URL of the page where license settings are managed.
     *
     * @return string The license setting URL
     */
    public function get_license_page_url();
    /**
     * Get the legacy database prefix for the old license system.
     *
     * @return string The prefix or an empty string if not applicable
     */
    public function get_legacy_db_prefix();
}
