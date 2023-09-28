<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
/**
 * Represents a REST route accessible via the REST API.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface Route extends Registerable
{
    /**
     * Get the REST route base which is appended to the namespace (e.g. mybase).
     *
     * @return string The REST base.
     */
    public function get_base();
    /**
     * Get the full endpoint including namespace (e.g. myplugin/v1/mybase).
     *
     * @return string The full REST route.
     */
    public function get_endpoint();
}
