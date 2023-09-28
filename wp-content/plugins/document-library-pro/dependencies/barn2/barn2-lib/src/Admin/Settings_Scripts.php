<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
/**
 * Registers the tooltip assets
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 */
class Settings_Scripts implements Registerable
{
    /**
     * The plugin object.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * Constructor.
     *
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        \add_action('admin_enqueue_scripts', [$this, 'load_scripts'], 5);
    }
    /**
     * Load any scripts required by settings fields.
     */
    public function load_scripts()
    {
        if (!\wp_script_is('barn2-tiptip', 'registered')) {
            \wp_register_script('barn2-tiptip', \plugins_url('dependencies/barn2/barn2-lib/build/js/jquery-tiptip/jquery.tipTip.js', $this->plugin->get_file()), ['jquery'], $this->plugin->get_version(), \true);
            \wp_register_style('barn2-tooltip', \plugins_url('dependencies/barn2/barn2-lib/build/css/tooltip-styles.css', $this->plugin->get_file()), [], $this->plugin->get_version());
        }
    }
}
