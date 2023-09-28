<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
use DateTime;
/**
 * License magic.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2.1
 */
class Plugin_License implements Registerable, License
{
    const RENEWAL_DISCOUNT_CODE = 'RENEWAL20';
    private $item_id;
    private $license_api;
    private $legacy_db_prefix;
    private $license_option;
    private $license_data = null;
    private $home_url = null;
    /**
     * Creates a new plugin license instance.
     *
     * @param int $item_id             The item ID for this plugin.
     * @param License_API $license_api The API to perform the various license actions (activate, deactivate, etc).
     * @param string $legacy_db_prefix Legacy plugins only - the database prefix for the license settings.
     */
    public function __construct($item_id, License_API $license_api, $legacy_db_prefix = '')
    {
        $this->item_id = (int) $item_id;
        $this->license_api = $license_api;
        $this->legacy_db_prefix = \rtrim($legacy_db_prefix, '_');
        $this->license_option = 'barn2_plugin_license_' . $this->item_id;
    }
    public function register()
    {
        \add_action('plugins_loaded', [$this, 'migrate_legacy_license'], 0);
        // early, before any is_valid() checks.
    }
    public function get_item_id()
    {
        return $this->item_id;
    }
    public function exists()
    {
        return \get_option($this->license_option) ? \true : \false;
    }
    public function is_valid()
    {
        // A 'valid' license is one which is active or expired.
        // For expired licenses the plugin will still work (i.e. is valid) but plugin updates will be disabled.
        return $this->get_license_key() && \in_array($this->get_status(), ['active', 'expired']);
    }
    public function is_active()
    {
        return $this->get_license_key() && 'active' === $this->get_status();
    }
    public function is_expired()
    {
        return $this->get_license_key() && 'expired' === $this->get_status();
    }
    public function is_inactive()
    {
        return $this->get_license_key() && 'inactive' === $this->get_status();
    }
    public function is_disabled()
    {
        return $this->get_license_key() && 'disabled' === $this->get_status();
    }
    public function get_license_key()
    {
        $data = $this->get_license_data();
        return isset($data['license']) ? $data['license'] : '';
    }
    public function get_status()
    {
        $data = $this->get_license_data();
        return isset($data['status']) ? $data['status'] : '';
    }
    public function get_status_help_text()
    {
        $message = $this->get_error_message();
        switch ($this->get_status()) {
            case 'active':
                $message = __('Your license key is active.', 'document-library-pro');
                break;
            case 'inactive':
                if (!$message) {
                    $message = $this->get_license_inactive_message();
                }
                break;
            case 'expired':
                if (!$message) {
                    $message = $this->get_license_expired_message();
                }
                break;
            case 'disabled':
                if (!$message) {
                    $message = $this->get_license_disabled_message();
                }
                break;
            case 'invalid':
            default:
                if (!$this->get_license_key()) {
                    $message = __('Please enter your license key.', 'document-library-pro');
                } elseif (!$message) {
                    $message = __('Your license key is invalid.', 'document-library-pro');
                }
                break;
        }
        return $message;
    }
    /**
     * Attempt to activate the specified license key.
     *
     * @param string $license_key The license key to activate.
     * @return boolean true if successfully activated, false otherwise.
     */
    public function activate($license_key)
    {
        // Check a license was supplied.
        if (!$license_key) {
            $this->set_missing_license();
            return \false;
        }
        $result = \false;
        $url_to_activate = $this->get_home_url();
        $license_data = ['license' => $license_key, 'url' => $url_to_activate];
        $api_result = $this->license_api->activate_license($license_key, $this->item_id, $url_to_activate);
        if ($api_result->success) {
            // Successful response - now check whether license is valid.
            $response = $api_result->response;
            // $response->license will be 'valid' or 'invalid'.
            if ('valid' === $response->license) {
                $license_data['status'] = 'active';
                $result = \true;
                \do_action('barn2_license_activated_' . $this->item_id, $license_key, $url_to_activate);
            } else {
                // Invalid license.
                $license_data['error_code'] = isset($response->error) ? $response->error : 'error';
                $license_data['status'] = $this->to_license_status($license_data['error_code']);
            }
            // Store the returned license info.
            $license_data['license_info'] = $this->format_license_info($response);
        } else {
            // API error - set license to invalid as we can't activate.
            $license_data['status'] = 'invalid';
            $license_data['error_code'] = 'error';
            $license_data['error_message'] = $api_result->response;
        }
        $this->set_license_data($license_data);
        return $result;
    }
    /**
     * Attempt to deactivate the current license key.
     *
     * @return boolean true if successfully deactivated, false otherwise.
     */
    public function deactivate()
    {
        // Bail early if already inactive.
        if ($this->is_inactive()) {
            return \true;
        }
        // If license is overridden bypass API and set status manually.
        if ($this->is_license_overridden()) {
            $this->set_status('inactive');
            return \true;
        }
        // We can't deactivate a license if it's not currently active.
        if (!$this->is_active()) {
            return \false;
        }
        $result = \false;
        $license_data = [];
        $url_to_deactivate = $this->get_active_url();
        $api_result = $this->license_api->deactivate_license($this->get_license_key(), $this->item_id, $url_to_deactivate);
        if ($api_result->success) {
            // Successful response - now check whether license is valid.
            $response = $api_result->response;
            $result = \true;
            // $response->license will be 'deactivated' or 'failed'.
            if ('deactivated' === $response->license) {
                // License deactivated, so update status.
                $license_data['status'] = 'inactive';
                // Store returned license info.
                $license_data['license_info'] = $this->format_license_info($response);
                $this->update_license_data($license_data);
            } else {
                // Deactivation failed - reasons: already deactivated via Account page, license has expired, bad data, etc.
                // In this case we refresh license data to ensure we have correct state stored in database.
                $this->refresh();
            }
            \do_action('barn2_license_deactivated_' . $this->item_id, $this->get_license_key(), $url_to_deactivate);
        } else {
            // API error
            $license_data['error_code'] = 'error';
            $license_data['error_message'] = $api_result->response;
            $this->update_license_data($license_data);
        }
        return $result;
    }
    /**
     * Refresh the current license key information from the EDD Licensing server. Ensures the correct
     * license state for this plugin is stored in the database.
     *
     * @return void
     */
    public function refresh()
    {
        // No point refreshing if license doesn't exist.
        if (!$this->get_license_key()) {
            return;
        }
        // If license is overridden, we shouldn't refresh as it will lose override state.
        if ($this->is_license_overridden()) {
            return;
        }
        $license_data = ['license' => $this->get_license_key()];
        // We use the home url when checking the license, as the license result should reflect the current site, not any previous site.
        $api_result = $this->license_api->check_license($this->get_license_key(), $this->item_id, $this->get_home_url());
        if ($api_result->success) {
            // Successful response returned.
            $response = $api_result->response;
            if ('valid' === $response->license) {
                // Valid (and active) license.
                $license_data['status'] = 'active';
            } else {
                // Invalid license - $response->license will contain the reason for the invalid license - e.g. expired, inactive, site_inactive, etc.
                $license_data['error_code'] = $response->license;
                $license_data['status'] = $this->to_license_status($response->license);
            }
            // Store returned license info.
            $license_data['license_info'] = $this->format_license_info($response);
            \do_action('barn2_license_refreshed_' . $this->item_id, $this->get_license_key(), $this->get_home_url());
        } else {
            // API error - store the error but don't change license status (e.g. temporary communication error).
            $license_data['error_code'] = 'error';
            $license_data['error_message'] = $api_result->response;
        }
        $this->update_license_data($license_data);
    }
    public function override($license_key, $status)
    {
        if (!$license_key || !$this->is_valid_status($status)) {
            return;
        }
        $this->set_license_data(['license' => $license_key, 'url' => $this->get_home_url(), 'status' => $status, 'override' => \true]);
        \do_action('barn2_license_activated_' . $this->item_id, $license_key, $this->get_home_url());
    }
    public function get_setting_name()
    {
        return $this->license_option;
    }
    public function get_error_code()
    {
        $license_data = $this->get_license_data();
        return isset($license_data['error_code']) ? $license_data['error_code'] : '';
    }
    public function get_error_message()
    {
        if (!$this->get_error_code()) {
            return '';
        }
        $message = '';
        $license_info = $this->get_license_info();
        switch ($this->get_error_code()) {
            case 'missing':
                $message = \sprintf(
                    /* translators: 1: account page link start, 2: account page link end */
                    __('Invalid license key - please check your order confirmation email or %1$sAccount%2$s.', 'document-library-pro'),
                    Util::format_store_link_open('account'),
                    '</a>'
                );
                break;
            case 'missing_url':
                $message = __('No URL was supplied for activation, please contact support.', 'document-library-pro');
                break;
            case 'key_mismatch':
                $message = __('License key mismatch, please contact support.', 'document-library-pro');
                break;
            case 'license_not_activable':
                $message = __('This license is for a bundled product and cannot be activated.', 'document-library-pro');
                break;
            case 'item_name_mismatch':
            case 'invalid_item_id':
                $message = __('Your license key is not valid for this plugin.', 'document-library-pro');
                break;
            case 'no_activations_left':
                $limit = '';
                if (isset($license_info['max_sites'])) {
                    /* translators: %s: The number of sites the license is activated on */
                    $limit = \sprintf(_n('%s site active', '%s sites active', \absint($license_info['max_sites']), 'document-library-pro'), $license_info['max_sites']);
                }
                /* translators: %s: The sites active description, e.g. '2 sites active' */
                $message = \sprintf(__('Your license key has reached its activation limit (%s).', 'document-library-pro'), $limit);
                $read_more_link = Util::format_store_link('kb/license-key-problems', __('Read more', 'document-library-pro'));
                /* translators: support for RTL, 1: the license error, 2: a link */
                $message = \sprintf(__('%1$s %2$s', 'document-library-pro'), $message, $read_more_link);
                break;
            case 'inactive':
            case 'site_inactive':
                $message = $this->get_license_inactive_message();
                break;
            case 'expired':
                $message = $this->get_license_expired_message();
                // See if we have a valid expiry date by checking first 4 chars are numbers (the expiry year).
                // This is only a rough check - createFromFormat() will validate fully and return a DateTime object if valid.
                if (!empty($license_info['expires']) && \is_numeric(\substr($license_info['expires'], 0, 4))) {
                    if ($expiry_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $license_info['expires'])) {
                        /* translators: %s: The license expiry date */
                        $message = \sprintf(__('Your license key expired on %s.', 'document-library-pro'), $expiry_datetime->format(\get_option('date_format')));
                    }
                }
                $renewal_link = Util::format_link($this->get_renewal_url(), __('Renew now for 20% discount.', 'document-library-pro'), \true);
                /* translators: support for RTL, 1: the license error, 2: a link */
                $message = \sprintf(__('%1$s %2$s', 'document-library-pro'), $message, $renewal_link);
                break;
            case 'disabled':
                $message = $this->get_license_disabled_message();
                break;
            default:
                $license_data = $this->get_license_data();
                if (!empty($license_data['error_message'])) {
                    $message = $license_data['error_message'];
                } else {
                    $message = __('Your license key is invalid.', 'document-library-pro');
                }
                break;
        }
        return $message;
    }
    public function get_active_url()
    {
        $data = $this->get_license_data();
        return $this->clean_license_url($data['url']);
    }
    public function has_site_moved()
    {
        $active_url = $this->get_active_url();
        if (!$active_url) {
            return \false;
        }
        // Exclude overridden licenses - we don't want to automatically deactivate these or show admin notice to user.
        if ($this->is_license_overridden()) {
            return \false;
        }
        $has_moved = $active_url !== $this->get_home_url();
        if ($has_moved && $this->is_active()) {
            $this->set_site_inactive();
        }
        return $has_moved;
    }
    public function get_renewal_url($apply_discount = \true)
    {
        $discount_code = $apply_discount ? self::RENEWAL_DISCOUNT_CODE : '';
        $license_info = $this->get_license_info();
        if (!empty($license_info['item_id'])) {
            $price_id = !empty($license_info['price_id']) ? $license_info['price_id'] : 0;
            return Util::get_add_to_cart_url($license_info['item_id'], $price_id, $discount_code);
        } else {
            return Util::barn2_url('our-wordpress-plugins');
        }
    }
    public function migrate_legacy_license()
    {
        if (empty($this->legacy_db_prefix)) {
            return;
        }
        if ($this->exists()) {
            return;
        }
        $license_key = \get_option($this->legacy_db_prefix . '_license_key');
        if ($license_key && \is_string($license_key)) {
            // Migrate from legacy license data.
            $data = ['license' => $license_key, 'url' => $this->get_home_url()];
            $status = \get_option($this->legacy_db_prefix . '_license_status');
            if ('valid' === $status) {
                $data['status'] = 'active';
            } elseif ('deactivated' === $status) {
                $data['status'] = 'inactive';
            } else {
                $data['status'] = 'invalid';
            }
            // Remove legacy license data.
            \delete_option($this->legacy_db_prefix . '_license_key');
            \delete_option($this->legacy_db_prefix . '_license_status');
            \delete_option($this->legacy_db_prefix . '_license_error');
            \delete_option($this->legacy_db_prefix . '_license_debug');
            $this->set_license_data($data);
        }
    }
    private function get_home_url()
    {
        if (null === $this->home_url) {
            // We don't use home_url() here as this runs the 'home_url' filter which other plugins hook into (e.g. multi-lingual plugins).
            $this->home_url = $this->clean_license_url(\get_option('home'));
        }
        return $this->home_url;
    }
    /**
     * Cleans the URL to use for the license.
     *
     * As in EDD Software Licensing, we ignore www. and http/https in URL to prevent similar URLs causing separate license activations.
     *
     * @param string $url The URL to clean.
     * @return string Cleaned URL.
     */
    private function clean_license_url($url)
    {
        if (empty($url)) {
            return $url;
        }
        // To lowercase.
        $url = \strtolower($url);
        // Strip www.
        $url = \str_replace(['://www.', ':/www.'], '://', $url);
        // Strip scheme.
        $url = \str_replace(['http://', 'https://', 'http:/', 'https:/'], '', $url);
        // Remove trailing slash.
        $url = \untrailingslashit($url);
        return $url;
    }
    public function is_license_overridden()
    {
        $license_data = $this->get_license_data();
        return !empty($license_data['override']);
    }
    private function get_default_data()
    {
        return ['license' => '', 'status' => 'invalid', 'url' => '', 'error_code' => '', 'error_message' => '', 'license_info' => []];
    }
    private function get_license_data()
    {
        if (null === $this->license_data) {
            $license = \get_option($this->license_option, $this->get_default_data());
            if (\is_scalar($license)) {
                $license = ['license' => $license];
            }
            $this->license_data = \array_merge($this->get_default_data(), (array) $license);
        }
        return $this->license_data;
    }
    private function set_license_data(array $data)
    {
        $this->license_data = $this->sanitize_license_data($data);
        \update_option($this->license_option, $this->license_data, \false);
    }
    private function update_license_data(array $data)
    {
        $license_data = $this->get_license_data();
        // Clear any previous error before updating.
        $license_data['error_code'] = '';
        $license_data['error_message'] = '';
        // Merge current data with new $data before setting.
        $this->set_license_data(\array_merge($license_data, $data));
    }
    private function sanitize_license_data(array $data)
    {
        $default_data = $this->get_default_data();
        $data = \array_merge($default_data, $data);
        if (!$this->is_valid_status($data['status'])) {
            $data['status'] = $default_data['status'];
        }
        // License is invalid if there's no license key.
        if (empty($data['license'])) {
            $data['status'] = 'invalid';
        }
        return $data;
    }
    private function set_missing_license()
    {
        $this->set_license_data(['license' => '', 'status' => 'invalid', 'error_code' => 'missing']);
    }
    private function set_site_inactive()
    {
        $this->update_license_data(['status' => 'inactive', 'error_code' => 'site_inactive']);
    }
    private function format_license_info($api_response)
    {
        $info = [];
        // License info should always return the expiry date, so it's considered valid if this is present.
        if (isset($api_response->expires)) {
            // Cast response to array.
            $info = (array) $api_response;
            // Remove the stuff we don't need.
            unset($info['success'], $info['license'], $info['checksum'], $info['error']);
        }
        return $info;
    }
    private function get_license_info()
    {
        $data = $this->get_license_data();
        return isset($data['license_info']) ? $data['license_info'] : [];
    }
    private function set_status($status)
    {
        // Status is sanitized during update_license_data().
        $this->update_license_data(['status' => $status]);
    }
    private function is_valid_status($status)
    {
        return $status && \in_array($status, ['active', 'inactive', 'expired', 'disabled', 'invalid']);
    }
    private function to_license_status($api_license_status)
    {
        if ('valid' === $api_license_status) {
            return 'active';
        } elseif (\in_array($api_license_status, ['inactive', 'site_inactive'])) {
            return 'inactive';
        } elseif (\in_array($api_license_status, ['expired', 'disabled'])) {
            return $api_license_status;
        }
        return 'invalid';
    }
    private function get_license_inactive_message()
    {
        return __('Your license key is not active. Please reactivate or save the settings.', 'document-library-pro');
    }
    private function get_license_expired_message()
    {
        return __('Your license key has expired.', 'document-library-pro');
    }
    private function get_license_disabled_message()
    {
        return \sprintf(
            /* translators: 1: purchase link start, 2: purchase link end. */
            __('Your license key has been disabled. Please %1$spurchase a new license key%2$s to continue using the plugin.', 'document-library-pro'),
            Util::format_link_open($this->get_renewal_url(\false), \true),
            '</a>'
        );
    }
}
