<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
/**
 * Handles the display of admin notices for the plugin license (e.g. license expired).
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2
 */
class License_Notices implements Registerable
{
    const FIRST_ACTIVATION = 'first_activation';
    const EXPIRED = 'expired';
    const DISABLED = 'disabled';
    const SITE_MOVED = 'site_moved';
    /**
     * @var Licensable_Plugin The plugin to handle notices for.
     */
    private $plugin;
    public function __construct(Licensed_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    public function register()
    {
        \add_action('admin_init', [$this, 'add_notices'], 50);
        \add_action('barn2_license_activated_' . $this->plugin->get_id(), [$this, 'cleanup_transients']);
        \add_action('admin_enqueue_scripts', [$this, 'load_scripts']);
        \add_action('wp_ajax_barn2_dismiss_notice', [$this, 'ajax_dismiss_notice']);
    }
    public function add_notices()
    {
        // Don't add notices if we're doing a post (e.g. saving the settings).
        if (isset($_SERVER['REQUEST_METHOD']) && 'POST' === $_SERVER['REQUEST_METHOD']) {
            return;
        }
        $license = $this->plugin->get_license();
        if (!$license->exists()) {
            // Add first activation notice.
            $this->maybe_add_notice(self::FIRST_ACTIVATION, [$this, 'first_activation_notice']);
        } elseif ($license->is_expired()) {
            // Add expired license notice.
            $this->maybe_add_notice(self::EXPIRED, [$this, 'expired_license_notice']);
        } elseif ($license->is_disabled()) {
            // Add disabled license notice
            $this->maybe_add_notice(self::DISABLED, [$this, 'disabled_license_notice']);
        } elseif ($license->has_site_moved()) {
            // Add 'site moved to new URL' notice.
            $this->maybe_add_notice(self::SITE_MOVED, [$this, 'site_moved_notice']);
        }
    }
    private function maybe_add_notice($notice_type, $notice_callback)
    {
        // Don't add the notice if it's a WooCommerce plugin and WooCommerce is not installed, as license page won't be available.
        if ($this->plugin->is_woocommerce() && !Util::is_woocommerce_active()) {
            return;
        }
        // Ditto for EDD plugins.
        if ($this->plugin->is_edd() && !Util::is_edd_active()) {
            return;
        }
        if (!$this->is_notice_dismissed($notice_type)) {
            \add_action('admin_notices', $notice_callback, 50);
        }
    }
    public function first_activation_notice()
    {
        if (!$this->plugin->get_license_page_url()) {
            return;
        }
        $plugin_name = '<strong>' . $this->plugin->get_name() . '</strong>';
        ?>
		<div class="notice notice-warning is-dismissible barn2-notice" data-id="<?php 
        echo \esc_attr($this->plugin->get_id());
        ?>" data-type="<?php 
        echo \esc_attr(self::FIRST_ACTIVATION);
        ?>">
			<p>
			<?php 
        // phpcs:disable WordPress.Security.EscapeOutput
        \printf(
            /* translators: 1: the plugin name, 2: settings link start, 3: settings link end. */
            __('Thank you for installing %1$s. To get started, please %2$s enter your license key%3$s.', 'document-library-pro'),
            $plugin_name,
            Util::format_link_open($this->plugin->get_license_page_url()),
            '</a>'
        );
        // phpcs:enable WordPress.Security.EscapeOutput
        ?>
				</p>
		</div>
		<?php 
    }
    public function expired_license_notice()
    {
        $plugin_name = '<strong>' . $this->plugin->get_name() . '</strong>';
        ?>
		<div class="notice notice-warning is-dismissible barn2-notice" data-id="<?php 
        echo \esc_attr($this->plugin->get_id());
        ?>" data-type="<?php 
        echo \esc_attr(self::EXPIRED);
        ?>">
			<p>
			<?php 
        // phpcs:disable WordPress.Security.EscapeOutput
        \printf(
            /* translators: 1: the plugin name, 2: renewal link start, 3: renewal link end. */
            __('Your license key for %1$s has expired. %2$sClick here to renew for 20%% discount%3$s.', 'document-library-pro'),
            $plugin_name,
            Util::format_link_open($this->plugin->get_license()->get_renewal_url(), \true),
            '</a>'
        );
        // phpcs:enable WordPress.Security.EscapeOutput
        ?>
				</p>
		</div>
		<?php 
    }
    public function disabled_license_notice()
    {
        $plugin_name = '<strong>' . \esc_html($this->plugin->get_name()) . '</strong>';
        ?>
		<div class="notice notice-error is-dismissible barn2-notice" data-id="<?php 
        echo \esc_attr($this->plugin->get_id());
        ?>" data-type="<?php 
        echo \esc_attr(self::DISABLED);
        ?>">
			<p>
			<?php 
        // phpcs:disable WordPress.Security.EscapeOutput
        \printf(
            /* translators: 1: the plugin name, 2: renewal link start, 3: renewal link end. */
            __('You no longer have a valid license for %1$s. Please %2$spurchase a new license key%3$s to continue using the plugin.', 'document-library-pro'),
            $plugin_name,
            Util::format_link_open($this->plugin->get_license()->get_renewal_url(\false), \true),
            '</a>'
        );
        // phpcs:enable WordPress.Security.EscapeOutput
        ?>
				</p>
		</div>
		<?php 
    }
    public function site_moved_notice()
    {
        if (!$this->plugin->get_license_page_url()) {
            return;
        }
        $plugin_name = '<strong>' . $this->plugin->get_name() . '</strong>';
        ?>
		<div class="notice notice-error is-dismissible barn2-notice" data-id="<?php 
        echo \esc_attr($this->plugin->get_id());
        ?>" data-type="<?php 
        echo \esc_attr(self::SITE_MOVED);
        ?>">
			<p>
			<?php 
        // phpcs:disable WordPress.Security.EscapeOutput
        \printf(
            /* translators: 1: the plugin name, 2: settings link start, 3: settings link end. */
            __('%1$s - your site has moved to a new domain. Please %2$sreactivate your license key%3$s.', 'document-library-pro'),
            $plugin_name,
            Util::format_link_open($this->plugin->get_license_page_url()),
            '</a>'
        );
        // phpcs:enable WordPress.Security.EscapeOutput
        ?>
				</p>
		</div>
		<?php 
    }
    public function cleanup_transients()
    {
        // Clear notice dismissal transients when license is activated.
        \delete_transient($this->get_notice_dismissed_transient_name(self::EXPIRED));
        \delete_transient($this->get_notice_dismissed_transient_name(self::DISABLED));
        \delete_transient($this->get_notice_dismissed_transient_name(self::SITE_MOVED));
    }
    public function load_scripts()
    {
        if (!\wp_script_is('barn2-notices', 'registered')) {
            \wp_register_script('barn2-notices', \plugins_url('dependencies/barn2/barn2-lib/build/js/admin/barn2-notices.js', $this->plugin->get_file()), ['jquery'], $this->plugin->get_version(), \true);
        }
        \wp_enqueue_script('barn2-notices');
    }
    public function ajax_dismiss_notice()
    {
        $item_id = \filter_input(\INPUT_POST, 'id', \FILTER_VALIDATE_INT);
        $notice_type = \filter_input(\INPUT_POST, 'type', \FILTER_SANITIZE_SPECIAL_CHARS);
        // Check data is valid.
        if (!$item_id || !\in_array($notice_type, [self::FIRST_ACTIVATION, self::EXPIRED, self::DISABLED, self::SITE_MOVED], \true)) {
            \wp_die();
        }
        if ($item_id === $this->plugin->get_id()) {
            $this->dismiss_notice($notice_type);
        }
        \wp_die();
    }
    private function dismiss_notice($notice_type)
    {
        \set_transient($this->get_notice_dismissed_transient_name($notice_type), \true);
    }
    private function is_notice_dismissed($notice_type)
    {
        return (bool) \get_transient($this->get_notice_dismissed_transient_name($notice_type));
    }
    private function get_notice_dismissed_transient_name($notice_type)
    {
        return "barn2_notice_dismissed_{$notice_type}_" . $this->plugin->get_id();
    }
}
