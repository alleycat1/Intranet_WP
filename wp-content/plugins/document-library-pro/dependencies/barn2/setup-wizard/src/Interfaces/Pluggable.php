<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Setup_Wizard;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Step;
interface Pluggable
{
    /**
     * Inject the plugin instance to a Step instance.
     *
     * @param object $plugin Barn2 Plugin Instance
     * @return Step
     */
    public function with_plugin($plugin);
    /**
     * Get the plugin assigned to the step.
     *
     * @return object
     */
    public function get_plugin();
    /**
     * Send the setup wizard to the step class.
     *
     * @param Setup_Wizard $wizard
     * @return Step
     */
    public function with_wizard(Setup_Wizard $wizard);
    /**
     * Get the setup wizard assigned to the step class.
     *
     * @return Setup_Wizard
     */
    public function get_wizard();
}
