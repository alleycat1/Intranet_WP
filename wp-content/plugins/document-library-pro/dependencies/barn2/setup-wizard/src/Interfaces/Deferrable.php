<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces;

/**
 * Implement this interface with steps to indicate
 * that the details of a step should be loaded via the rest api
 * instead of being loaded when the class is initialized.
 */
interface Deferrable
{
    /**
     * Returns steps details.
     *
     * @return array
     */
    public function get_step_details();
}
