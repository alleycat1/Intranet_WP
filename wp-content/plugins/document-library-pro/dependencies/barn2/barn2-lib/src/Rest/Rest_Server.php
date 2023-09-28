<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Rest\Route;
/**
 * A REST server is the main entry point for a REST system. Registers the routes etc.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface Rest_Server
{
    /**
     * Get the namespace for the REST routes that this server registers.
     *
     * @return string The namespace.
     */
    public function get_namespace();
    /**
     * Get the list of routes managed by this REST server.
     *
     * @return Route[] The list of routes.
     */
    public function get_routes();
    /**
     * Get the list of route endpoints.
     *
     * @return string[] The list of endpoints.
     */
    public function get_endpoints();
}
