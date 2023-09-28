<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib;

/**
 * A trait for a service container.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2
 */
trait Service_Container
{
    private $services = [];
    public function register_services()
    {
        Util::register_services($this->_get_services());
    }
    private function _get_services()
    {
        //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
        if (empty($this->services)) {
            $this->services = $this->get_services();
        }
        return $this->services;
    }
    public function get_services()
    {
        // Overridden by classes using this trait.
        return [];
    }
    public function get_service($id)
    {
        $services = $this->_get_services();
        return isset($services[$id]) ? $services[$id] : null;
    }
}
