<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Traits;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Notices;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
/**
 * Helper methods to check for WP and/or WC requirements.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
trait Check_WP_Requirements
{
    /**
     * Check the WP Requirements are met.
     *
     * @param string $version The version to check for.
     * @param Plugin $plugin The instance of the plugin.
     * @return boolean
     */
    private function check_wp_requirements(string $version, Plugin $plugin) : bool
    {
        global $wp_version;
        $slug = $plugin->get_slug();
        if (\is_admin() && \version_compare($wp_version, $version, '<')) {
            $can_update_core = \current_user_can('update_core');
            $admin_notice = new Notices();
            $admin_notice->add($slug . '_invalid_wp_version', '', \sprintf(
                /* translators: %1$s: Plugin name. %2$s: Update Core <a> tag open. %3$s: <a> tag close. %4$s: WP Version required. */
                __('The %1$s plugin requires WordPress %4$s or greater. Please %2$supdate%3$s your WordPress installation.','document-library-pro' ),
                '<strong>' . $plugin->get_name() . '</strong>',
                $can_update_core ? \sprintf('<a href="%s">', \esc_url(\self_admin_url('update-core.php'))) : '',
                $can_update_core ? '</a>' : '',
                $version
            ), ['type' => 'error', 'capability' => 'install_plugins', 'screens' => ['plugins']]);
            $admin_notice->boot();
            return \false;
        }
        return \true;
    }
    /**
     * Check the WooCommerce requirements are met.
     *
     * @param string $version The version to check for.
     * @param Plugin $plugin The instance of the plugin.
     * @return bool
     */
    private function check_wc_requirements(string $version, Plugin $plugin) : bool
    {
        $slug = $plugin->get_slug();
        if (!\class_exists('WooCommerce')) {
            if (\is_admin()) {
                $admin_notice = new Notices();
                $admin_notice->add(
                    $slug . '_woocommerce_missing',
                    '',
                    /* translators: %1$s: Install WooCommerce <a> tag open. %2$s: <a> tag close. %3$s: Plugin name  */
                    \sprintf(__('Please %1$sinstall WooCommerce%2$s in order to use %3$s.','document-library-pro' ), Util::format_link_open('https://woocommerce.com/', \true), '</a>', $plugin->get_name()),
                    ['type' => 'error', 'capability' => 'install_plugins', 'screens' => ['plugins']]
                );
                $admin_notice->boot();
            }
            return \false;
        }
        global $woocommerce;
        if (\version_compare($woocommerce->version, $version, '<')) {
            if (\is_admin()) {
                $admin_notice = new Notices();
                $admin_notice->add(
                    $slug . '_invalid_wc_version',
                    '',
                    /* translators: %1$s: Plugin name. %2$s: Version required. */
                    \sprintf(__('The %1$s plugin requires WooCommerce %2$s or greater. Please update your WooCommerce setup first.','document-library-pro' ), $plugin->get_name(), $version),
                    ['type' => 'error', 'capability' => 'install_plugins', 'screens' => ['plugins', 'woocommerce_page_wc-settings']]
                );
                $admin_notice->boot();
            }
            return \false;
        }
        return \true;
    }
}
