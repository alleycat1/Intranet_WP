<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License;

/**
 * Interface to represent an API wrapper for a license system.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface License_API
{
    public function activate_license($license_key, $item_id, $url);
    public function deactivate_license($license_key, $item_id, $url);
    public function check_license($license_key, $item_id, $url);
    public function get_latest_version($license_key, $item_id, $url, $slug, $beta_testing = \false);
}
