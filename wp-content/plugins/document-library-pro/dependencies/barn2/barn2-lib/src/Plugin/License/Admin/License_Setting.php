<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\Admin;

/**
 * Interface which represents a license key setting.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface License_Setting
{
    public function get_license_setting_name();
    public function get_license_key_setting();
    public function get_license_override_setting();
    public function save_license_key($license_key);
    public function save_posted_license_key();
}
