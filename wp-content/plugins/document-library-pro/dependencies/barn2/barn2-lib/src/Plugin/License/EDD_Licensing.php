<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License;

/**
 * This class provides an interface to the EDD Software Licensing API. API requests are handled on the Barn2 website by the EDD Software Licensing plugin.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.1
 */
final class EDD_Licensing implements License_API
{
    /**
     * @var string The URL of the EDD Software Licensing API.
     */
    const EDD_LICENSING_ENDPOINT = 'https://barn2.com/edd-sl';
    /**
     * @var int API timeout in seconds.
     */
    const API_TIMEOUT = 20;
    /**
     * @var EDD_Licensing The single instance of this class.
     */
    private static $_instance = null;
    public static function instance()
    {
        if (\is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Activate the specified license key.
     *
     * Returns a <code>stdClass</code> object containing two properties:
     * - success: true or false. Whether the request returned successfully.
     * - response: If success is true, it will contain the JSON-decoded response (an object) from
     * the server containing the result. If success if false, it will contain an error message (string)
     * indicating why the request failed.
     *
     * @param string $license_key The license key to activate.
     * @param int $item_id The download ID for the item to check.
     * @param string (URL) $url The URL to activate.
     * @return stdClass The result object (see above).
     */
    public function activate_license($license_key, $item_id, $url)
    {
        $api_params = ['edd_action' => 'activate_license', 'license' => $license_key, 'item_id' => $item_id, 'url' => $url];
        return $this->api_request($api_params);
    }
    /**
     * Deactivate the specified license key.
     *
     * Returns a <code>stdClass</code> object containing two properties:
     * - success: true or false. Whether the request returned successfully.
     * - response: If success is true, it will contain the JSON-decoded response (an object) from
     * the server containing the result. If success if false, it will contain an error message (string)
     * indicating why the request failed.
     *
     * @param string $license_key The license key to deactivate.
     * @param int $item_id The download ID for the item to check.
     * @param string (URL) $url The URL to deactivate.
     * @return stdClass The result object (see above).
     */
    public function deactivate_license($license_key, $item_id, $url)
    {
        $api_params = ['edd_action' => 'deactivate_license', 'license' => $license_key, 'item_id' => $item_id, 'url' => $url];
        return $this->api_request($api_params);
    }
    /**
     * Checks the specified license key.
     *
     * Returns a <code>stdClass</code> object containing two properties:
     * - success: true or false. Whether the request returned successfully.
     * - response: If success is true, it will contain the JSON-decoded response (an object) from
     * the server containing the license information. If success if false, it will contain an error
     * message (string) indicating why the request failed.
     *
     * @param string $license_key The license key to check.
     * @param int $item_id The download ID for the item to check.
     * @param string (URL) $url The URL to check.
     * @return stdClass The result object (see above).
     */
    public function check_license($license_key, $item_id, $url)
    {
        $api_params = ['edd_action' => 'check_license', 'license' => $license_key, 'item_id' => $item_id, 'url' => $url];
        return $this->api_request($api_params);
    }
    /**
     * Gets the latest version information for the specified plugin.
     *
     * Returns a <code>stdClass</code> object containing two properties:
     * - success: true or false. Whether the request returned successfully.
     * - response: If success is true, it will contain the JSON-decoded response (an object) from
     * the server containing the latest version information. If success if false, it will contain
     * an error message (string) indicating why the request failed.
     *
     * @param string $license_key The license key.
     * @param int $item_id The download ID for the item to check.
     * @param string (URL) $url The URL of the site we're checking updates for.
     * @param string $slug The plugin slug.
     * @param boolean $beta_testing Whether to check for beta versions.
     * @return stdClass The result object (see above).
     */
    public function get_latest_version($license_key, $item_id, $url, $slug, $beta_testing = \false)
    {
        $api_params = ['edd_action' => 'get_version', 'license' => $license_key, 'item_id' => $item_id, 'url' => $url, 'slug' => $slug, 'beta' => $beta_testing];
        $result = $this->api_request($api_params);
        if ($result->success && \is_object($result->response)) {
            foreach ($result->response as $prop => $data) {
                // We're forced to use the (potentially usafe) maybe_unserialize here as the
                // EDD Software Licensing API serializes some of the returned plugin data.
                $result->response->{$prop} = \maybe_unserialize($data);
            }
        }
        return $result;
    }
    private function api_request($params)
    {
        // Call the Software Licensing API.
        $response = \wp_remote_post(self::EDD_LICENSING_ENDPOINT, \apply_filters('barn2_edd_licensing_api_request_args', ['timeout' => self::API_TIMEOUT, 'body' => $params]));
        // Build the result.
        $result = new \stdClass();
        if (self::is_api_error($response)) {
            $result->success = \false;
            $result->response = self::get_api_error_message($response);
        } else {
            $result->success = \true;
            $result->response = \json_decode(\wp_remote_retrieve_body($response));
        }
        return $result;
    }
    private static function is_api_error($response)
    {
        return \is_wp_error($response) || 200 !== \wp_remote_retrieve_response_code($response);
    }
    private static function get_api_error_message($response)
    {
        if (\is_wp_error($response)) {
            return $response->get_error_message();
        } elseif (\wp_remote_retrieve_response_message($response)) {
            return \wp_remote_retrieve_response_message($response);
        } else {
            return __('An error has occurred, please try again.', 'document-library-pro');
        }
    }
}
