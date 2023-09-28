<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
/**
 * Provides functions to add the plugin promo to the plugin settings page in the WordPress admin.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.3
 */
class Plugin_Promo extends Abstract_Plugin_Promo implements Registerable
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        \add_action('barn2_before_plugin_settings', [$this, 'render_settings_start'], 10, 1);
        \add_action('barn2_after_plugin_settings', [$this, 'render_settings_end'], 10, 1);
        \add_action('admin_enqueue_scripts', [$this, 'maybe_load_styles']);
    }
    /**
     * Load the plugin promo CSS.
     *
     * @param string $hook
     */
    public function maybe_load_styles($hook)
    {
        $parsed_url = \wp_parse_url($this->plugin->get_settings_page_url());
        if (isset($parsed_url['query'])) {
            \parse_str($parsed_url['query'], $args);
            if (isset($args['page']) && \false !== \strpos($hook, $args['page'])) {
                parent::load_styles();
            }
        }
    }
    public function render_settings_start($plugin_id)
    {
        if ($plugin_id !== $this->plugin->get_id()) {
            return;
        }
        echo '<div class="barn2-promo-wrap">';
        echo '<div class="barn2-promo-inner barn2-settings ' . \esc_attr($this->plugin->get_slug() . '-settings') . '">';
    }
    public function render_settings_end($plugin_id)
    {
        if ($plugin_id !== $this->plugin->get_id()) {
            return;
        }
        echo '</div><!-- barn2-promo-inner -->';
        // Promo content is sanitized via barn2_kses_post.
        // phpcs:ignore WordPress.Security.EscapeOutput
        echo parent::get_promo_sidebar();
        echo '</div><!-- barn2-promo-wrap -->';
    }
}
