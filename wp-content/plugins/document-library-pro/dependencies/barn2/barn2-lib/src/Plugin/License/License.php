<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License;

/**
 * Interface to represent a plugin license.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.1
 */
interface License extends License_Summary
{
    public function get_item_id();
    public function activate($license_key);
    public function deactivate();
    public function refresh();
    public function override($license_key, $status);
    public function is_expired();
    public function is_disabled();
    public function is_inactive();
    public function get_status();
    public function get_status_help_text();
    public function get_active_url();
    public function has_site_moved();
    public function get_renewal_url($apply_discount = \true);
    public function get_setting_name();
}
