<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard;

/**
 * Determine if the setup wizard should be displayed after plugin's activation.
 */
class Starter
{
    /**
     * Holds the plugin's object.
     *
     * @var object
     */
    public $plugin;
    /**
     * Setup wizard slug.
     *
     * @var string
     */
    private $slug;
    /**
     * Initialize the starter.
     *
     * @param object $plugin
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->slug = $this->plugin->get_slug() . '-setup-wizard';
    }
    /**
     * Determine if the conditions to start the setup wizard are met.
     *
     * @return boolean
     */
    public function should_start()
    {
        return !$this->plugin->has_valid_license();
    }
    /**
     * Determine if the transient was created.
     *
     * @return bool
     */
    public function detected()
    {
        return \get_transient("_{$this->slug}_activation_redirect");
    }
    /**
     * Creates a short timed transient which is used to detect if the wizard should start.
     *
     * @return void
     */
    public function create_transient()
    {
        \set_transient("_{$this->slug}_activation_redirect", \true, 30);
    }
    /**
     * Delete the short timed transient.
     *
     * @return void
     */
    public function delete_transient()
    {
        \delete_transient("_{$this->slug}_activation_redirect");
    }
    /**
     * Redirect the user to the setup wizard.
     *
     * @return void
     */
    public function redirect()
    {
        $url = \add_query_arg(['page' => $this->slug], \admin_url());
        \wp_safe_redirect($url);
        exit;
    }
}
