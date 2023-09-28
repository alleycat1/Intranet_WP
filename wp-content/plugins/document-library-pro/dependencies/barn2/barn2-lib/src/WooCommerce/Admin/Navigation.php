<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WooCommerce\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable, Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin, Automattic\WooCommerce\Admin\Features\Navigation\Menu;
/**
 * Adds support for new WooCommerce navigation
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 */
class Navigation implements Registerable
{
    private $plugin;
    private $navigation_id;
    private $navigation_title;
    public function __construct(Plugin $plugin, $navigation_id = null, $navigation_title = null)
    {
        $this->plugin = $plugin;
        $this->navigation_id = $navigation_id;
        $this->navigation_title = $navigation_title;
    }
    public function register()
    {
        \add_action('admin_menu', [$this, 'register_navigation_items']);
    }
    public function register_navigation_items()
    {
        if (!\class_exists('\\Automattic\\WooCommerce\\Admin\\Features\\Navigation\\Menu')) {
            return;
        }
        Menu::add_plugin_item(['id' => \is_null($this->navigation_id) ? \sanitize_title($this->plugin->get_name()) : $this->navigation_id, 'title' => \is_null($this->navigation_title) ? $this->plugin->get_name() : $this->navigation_title, 'capability' => 'manage_woocommerce', 'url' => $this->plugin->get_settings_page_url()]);
    }
}
