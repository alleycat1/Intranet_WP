<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License;

/**
 * Interface which represents a summary of the plugin license.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface License_Summary
{
    public function get_license_key();
    public function exists();
    public function is_active();
    public function is_valid();
}
