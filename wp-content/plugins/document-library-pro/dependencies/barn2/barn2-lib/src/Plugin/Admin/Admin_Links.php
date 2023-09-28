<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
/**
 * Core admin functions for our plugins (e.g. adding the settings link).
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Links implements Registerable, Service
{
    /**
     * @var Plugin The core plugin data (ID, version, etc).
     */
    protected $plugin;
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    public function register()
    {
        // Add settings link from Plugins page.
        \add_filter('plugin_action_links_' . $this->plugin->get_basename(), [$this, 'add_settings_link']);
        // Add documentation link to meta info on Plugins page.
        \add_filter('plugin_row_meta', [$this, 'add_documentation_link'], 10, 2);
    }
    public function add_settings_link($links)
    {
        if (!($settings_url = $this->plugin->get_settings_page_url())) {
            return $links;
        }
        // Don't add link if it's a WooCommerce plugin but WooCommerce is not active.
        if ($this->plugin->is_woocommerce() && !Util::is_woocommerce_active()) {
            return $links;
        }
        // Don't add link if it's an EDD plugin but EDD is not active.
        if ($this->plugin->is_edd() && !Util::is_edd_active()) {
            return $links;
        }
        \array_unshift($links, \sprintf('<a href="%1$s">%2$s</a>', \esc_url($settings_url), __('Settings', 'document-library-pro')));
        return $links;
    }
    public function add_documentation_link($links, $file)
    {
        if ($file !== $this->plugin->get_basename()) {
            return $links;
        }
        // Bail if there's no documentation URL.
        if (!($documentation_url = $this->plugin->get_documentation_url())) {
            return $links;
        }
        $row_meta = ['docs' => \sprintf(
            '<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
            \esc_url($documentation_url),
            /* translators: %s: The plugin name */
            \esc_attr(\sprintf(__('View %s documentation', 'document-library-pro'), $this->plugin->get_name())),
            esc_html__('Docs', 'document-library-pro')
        )];
        return \array_merge($links, $row_meta);
    }
}
