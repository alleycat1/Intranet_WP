<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
/**
 * Abstract class to handle the plugin promo sidebar used in most Barn2 plugins.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 */
abstract class Abstract_Plugin_Promo
{
    /**
     * The plugin object.
     *
     * @var Plugin
     */
    protected $plugin;
    /**
     * The content of the plugin promo.
     *
     * @var string
     */
    private $promo_content = null;
    /**
     * Constructor.
     *
     * @param Plugin $plugin The plugin object.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * Retrieve the complete promo sidebar to insert into the settings page.
     *
     * @return string The promo sidebar.
     */
    protected function get_promo_sidebar()
    {
        $promo_content = $this->get_promo_content();
        if (!empty($promo_content)) {
            // Promo content is sanitized via barn2_kses_post.
            return '<div id="barn2_plugins_promo" class="barn2-plugins-promo-wrapper">' . Util::barn2_kses_post($promo_content) . '</div>';
        }
        return '';
    }
    /**
     * Retrieve the plugin promo content from the API.
     *
     * @return string The promo sidebar content.
     */
    protected function get_promo_content()
    {
        if (null !== $this->promo_content) {
            return $this->promo_content;
        }
        $plugin_id = $this->plugin->get_id();
        $review_content = \get_transient('barn2_plugin_review_banner_' . $plugin_id);
        $promo_response_data = \get_transient('barn2_plugin_promo_' . $plugin_id);
        if (\false === $review_content) {
            $review_content_url = Util::barn2_api_url('/wp-json/promos/v1/get/' . $plugin_id . '?_=' . \gmdate('mdY'));
            $review_content_url = \add_query_arg(['source' => \rawurlencode(\get_bloginfo('url')), 'template' => 'review_request'], $review_content_url);
            $review_response = \wp_remote_get($review_content_url, ['sslverify' => \defined('WP_DEBUG') && \WP_DEBUG ? \false : \true]);
            if (200 !== \wp_remote_retrieve_response_code($review_response)) {
                $review_content = '';
            } else {
                $review_content = \json_decode(\wp_remote_retrieve_body($review_response), \true);
                \set_transient('barn2_plugin_review_banner_' . $plugin_id, $review_content, 7 * \DAY_IN_SECONDS);
            }
        }
        $plugins_installed = Util::get_installed_barn2_plugins() ?: [];
        if (\false === $promo_response_data || !\is_array($promo_response_data)) {
            $promo_content_url = Util::barn2_api_url('/wp-json/promos/v1/get/' . $plugin_id . '?_=' . \gmdate('mdY'));
            $promo_content_url = \add_query_arg('source', \rawurlencode(\get_bloginfo('url')), $promo_content_url);
            if ($plugins_installed) {
                $plugins_installed = \array_column($plugins_installed, 'ITEM_ID');
                $promo_content_url = \add_query_arg('plugins_installed', \implode(',', $plugins_installed), $promo_content_url);
            }
            $promo_response = \wp_remote_get($promo_content_url, ['sslverify' => \defined('WP_DEBUG') && \WP_DEBUG ? \false : \true]);
            if (200 === \wp_remote_retrieve_response_code($promo_response)) {
                $promo_response_data = \json_decode(\wp_remote_retrieve_body($promo_response), \true);
                \set_transient('barn2_plugin_promo_' . $plugin_id, $promo_response_data, 7 * \DAY_IN_SECONDS);
            }
        }
        $promo_content = '';
        $count = 0;
        if (\is_array($promo_response_data) && isset($promo_response_data['promos'])) {
            foreach ($promo_response_data['promos'] as $promo) {
                if (!\in_array(\absint($promo['product_id']), $plugins_installed, \true)) {
                    $promo_content .= $promo['html'];
                    $count++;
                }
                if ($count >= 2) {
                    break;
                }
            }
            $promo_content = \sprintf($promo_response_data['template'], $promo_content);
        }
        $this->promo_content = $review_content . $promo_content;
        return $this->promo_content;
    }
    /**
     * Load the plugin promo stylesheet.
     *
     * @return void
     */
    public function load_styles()
    {
        \wp_enqueue_style('barn2-plugins-promo', \plugins_url('dependencies/barn2/barn2-lib/build/css/plugin-promo-styles.css', $this->plugin->get_file()), [], $this->plugin->get_version(), 'all');
    }
}
