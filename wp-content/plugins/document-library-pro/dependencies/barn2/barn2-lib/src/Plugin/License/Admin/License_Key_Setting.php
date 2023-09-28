<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_API_Helper;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\License;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use WC_Admin_Settings;
/**
 * Handles the display and saving of the license key on the plugin settings page.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2
 */
class License_Key_Setting implements Registerable, License_Setting
{
    const OVERRIDE_HASH = 'caf9da518b5d4b46c2ef1f9d7cba50ad';
    const ACTIVATE_KEY = 'activate_key';
    const DEACTIVATE_KEY = 'deactivate_key';
    const CHECK_KEY = 'check_key';
    private $license;
    private $is_woocommerce;
    private $is_edd;
    private $saving_key = \false;
    private $deferred_message;
    public function __construct(License $license, $is_woocommerce = \false, $is_edd = \false)
    {
        $this->license = $license;
        $this->is_woocommerce = (bool) $is_woocommerce;
        $this->is_edd = (bool) $is_edd;
    }
    public function register()
    {
        \add_action('admin_init', [$this, 'process_license_action'], 5);
        if ($this->is_edd) {
            // Include EDD settings callbacks.
            include_once __DIR__ . '/edd-settings-functions.php';
            // Handle the license settings message for EDD.
            \add_filter('sanitize_option_edd_settings', [$this, 'handle_edd_license_message'], 20);
        } elseif ($this->is_woocommerce && !\has_action('woocommerce_admin_field_hiden')) {
            // Add hidden field to WooCommerce settings.
            \add_action('woocommerce_admin_field_hiden', [Settings_API_Helper::class, 'settings_field_hidden']);
        }
    }
    /**
     * Process a license action from the plugin license settings page (i.e. activate, deactivate or check license)
     */
    public function process_license_action()
    {
        if ($this->is_license_action(self::ACTIVATE_KEY)) {
            $license_setting = \filter_input(\INPUT_POST, $this->get_license_setting_name(), \FILTER_DEFAULT, \FILTER_REQUIRE_ARRAY);
            if (isset($license_setting['license'])) {
                $license = \sanitize_text_field($license_setting['license']);
                $activated = $this->activate_license($license);
                $this->add_settings_message(__('License key successfully activated.', 'document-library-pro'), __('There was an error activating your license key.', 'document-library-pro'), $activated);
            }
        } elseif ($this->is_license_action(self::DEACTIVATE_KEY)) {
            $deactivated = $this->license->deactivate();
            $this->add_settings_message(__('License key successfully deactivated.', 'document-library-pro'), __('There was an error deactivating your license key, please try again.', 'document-library-pro'), $deactivated);
        } elseif ($this->is_license_action(self::CHECK_KEY)) {
            $this->license->refresh();
            $this->add_settings_message(__('The license key looks good!', 'document-library-pro'), __('There\'s a problem with your license key.', 'document-library-pro'), $this->license->is_active());
        }
    }
    private function is_license_action($action)
    {
        return isset($_SERVER['REQUEST_METHOD']) && 'POST' === $_SERVER['REQUEST_METHOD'] && $this->get_license_setting_name() === \filter_input(\INPUT_POST, $action, \FILTER_DEFAULT);
    }
    public function get_license_setting_name()
    {
        return $this->license->get_setting_name();
    }
    private function activate_license($license_key)
    {
        // Check if we're overriding the license activation.
        $override = \filter_input(\INPUT_POST, 'license_override', \FILTER_SANITIZE_SPECIAL_CHARS);
        if ($override && $license_key && self::OVERRIDE_HASH === \md5($override)) {
            $this->license->override($license_key, 'active');
            return \true;
        }
        return $this->license->activate($license_key);
    }
    private function add_settings_message($sucess_message, $error_message, $success = \true)
    {
        if ($this->is_woocommerce) {
            if ($success) {
                WC_Admin_Settings::add_message($sucess_message);
            } else {
                WC_Admin_Settings::add_error($error_message);
            }
        } else {
            $slug = 'license_message';
            $message = $success ? $sucess_message : $error_message;
            $type = $success ? 'updated' : 'error';
            if ($this->is_edd) {
                $this->deferred_message = ['slug' => $slug, 'message' => $message, 'type' => $type];
            } else {
                \add_settings_error($this->get_license_setting_name(), $slug, $message, $type);
            }
        }
    }
    public function get_license_key_setting()
    {
        $setting = ['title' => __('License key', 'document-library-pro'), 'type' => 'text', 'id' => $this->get_license_setting_name() . '[license]', 'desc' => $this->get_license_description(), 'class' => 'regular-text'];
        if ($this->is_woocommerce) {
            $setting['desc_tip'] = __('The licence key is contained in your order confirmation email.', 'document-library-pro');
        } elseif ($this->is_edd) {
            // EDD uses title case for setting names, so let's keep things consistent.
            $setting['title'] = __('License Key', 'document-library-pro');
            // Set type to 'barn2_license' so the callback to render setting will be 'edd_barn2_license_callback'.
            $setting['type'] = 'barn2_license';
            // EDD uses 'name' instead of 'title'.
            $setting['name'] = $setting['title'];
            unset($setting['title']);
        }
        if ($this->is_license_setting_readonly()) {
            $setting['custom_attributes'] = ['readonly' => 'readonly'];
        }
        return $setting;
    }
    /**
     * Retrieve the description for the license key input, to display on the settings page.
     *
     * @return string The license key status message
     */
    private function get_license_description()
    {
        $buttons = ['check' => $this->license_action_button(self::CHECK_KEY, __('Check', 'document-library-pro')), 'activate' => $this->license_action_button(self::ACTIVATE_KEY, __('Activate', 'document-library-pro')), 'deactivate' => $this->license_action_button(self::DEACTIVATE_KEY, __('Deactivate', 'document-library-pro'))];
        $message = $this->license->get_status_help_text();
        if ($this->license->is_active()) {
            $message = \sprintf('<span style="color:green;">✓&nbsp;%s</span>', $message);
        } elseif ($this->license->get_license_key()) {
            // If we have a license key and it's not active, mark it red for user to take action.
            if ($this->license->is_inactive() && $this->is_license_action('deactivate_key')) {
                // ...except if the user has just deactivated, in which case just show a plain confirmation message.
                $message = __('License key deactivated.', 'document-library-pro');
            } else {
                $message = \sprintf('<span style="color:red;">%s</span>', $message);
            }
        }
        if ($this->is_license_setting_readonly()) {
            unset($buttons['activate']);
        } else {
            unset($buttons['check'], $buttons['deactivate']);
        }
        return '<span class="submit">' . \implode('', $buttons) . '</span> ' . $message;
    }
    private function license_action_button($input_name, $button_text)
    {
        return \sprintf('<button type="submit" class="button" name="%1$s" value="%2$s" style="margin-right:4px;">%3$s</button>', \esc_attr($input_name), \esc_attr($this->get_license_setting_name()), $button_text);
    }
    private function is_license_setting_readonly()
    {
        return $this->license->is_active();
    }
    public function get_license_override_setting()
    {
        $override_code = \filter_input(\INPUT_GET, 'license_override', \FILTER_SANITIZE_SPECIAL_CHARS);
        return $override_code ? ['type' => 'hidden', 'id' => 'license_override', 'default' => \sanitize_text_field($override_code)] : [];
    }
    public function save_posted_license_key()
    {
        if ($this->saving_key) {
            return;
        }
        $license_setting = \filter_input(\INPUT_POST, $this->get_license_setting_name(), \FILTER_DEFAULT, \FILTER_REQUIRE_ARRAY);
        if (!isset($license_setting['license'])) {
            return;
        }
        $this->save_license_key($license_setting['license']);
    }
    /**
     * Save the specified license key.
     *
     * If there is a valid key currently active, the current key will be deactivated first
     * before activating the new one.
     *
     * @param string $license_key The license key to save.
     * @return string The license key.
     */
    public function save_license_key($license_key)
    {
        if ($this->saving_key) {
            return $license_key;
        }
        // phpcs:ignore WordPress.Security.NonceVerification
        if (\array_intersect([self::DEACTIVATE_KEY, self::ACTIVATE_KEY, self::CHECK_KEY], \array_keys($_POST))) {
            return $license_key;
        }
        $this->saving_key = \true;
        $license_key = \sanitize_text_field($license_key);
        // Deactivate old license key first if it was valid.
        if ($this->license->is_active() && $license_key !== $this->license->get_license_key()) {
            $this->license->deactivate();
        }
        // If new license key is different to current key, or current key isn't active, attempt to activate.
        if ($license_key !== $this->license->get_license_key() || !$this->license->is_active()) {
            $this->activate_license($license_key);
        }
        $this->saving_key = \false;
        return $license_key;
    }
    public function handle_edd_license_message($options)
    {
        global $wp_settings_errors;
        if (!empty($this->deferred_message)) {
            // Clear any other messages (e.g. 'Settings Updated') so we only show our license message.
            $wp_settings_errors = [];
            // We need to use 'edd-notices' setting to get message to show in EDD settings pages.
            \add_settings_error('edd-notices', $this->deferred_message['slug'], $this->deferred_message['message'], $this->deferred_message['type']);
            $this->deferred_message = [];
        }
        return $options;
    }
}
