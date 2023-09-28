<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces\Deferrable;
use WP_REST_Response;
use JsonSerializable;
/**
 * Class that handles registration of the rest api route
 * for the given plugin.
 */
class Api implements JsonSerializable
{
    const API_NAMESPACE = 'barn2-setup-wizard/v1';
    /**
     * Plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * List of steps (configured) for which we're loading api routes.
     *
     * @var array
     */
    private $steps;
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     * @param array $steps
     */
    public function __construct($plugin = \false, array $steps = [])
    {
        if ($plugin) {
            $this->plugin = $plugin;
        }
        $this->steps = $steps;
    }
    /**
     * Get the plugin's instance.
     *
     * @return object
     */
    public function get_plugin()
    {
        return $this->plugin;
    }
    /**
     * Attach the plugin's instance to the step.
     *
     * @param object $plugin
     * @return self
     */
    public function set_plugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }
    /**
     * Hook API Routes into WP.
     *
     * @return void
     */
    public function register_api_routes()
    {
        \add_action('rest_api_init', [$this, 'register_routes']);
    }
    /**
     * Check if a given request has admin access.
     *
     * @param  \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     */
    public function check_permissions($request)
    {
        return \true;
        //return wp_verify_nonce( $request->get_header( 'x-wp-nonce' ), 'wp_rest' ) && current_user_can( 'manage_options' );
    }
    /**
     * Get the api namespace for the steps.
     *
     * @return string
     */
    public function get_api_namespace()
    {
        return self::API_NAMESPACE . '/' . $this->get_plugin()->get_slug();
    }
    /**
     * Register api routes required by the wizard steps.
     *
     * @return void
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'steps', [['methods' => 'GET', 'callback' => [$this, 'get_steps'], 'permission_callback' => [$this, 'check_permissions']], ['methods' => 'POST', 'callback' => [$this, 'save_fields'], 'permission_callback' => [$this, 'check_permissions']]]);
        \register_rest_route($this->get_api_namespace(), 'license', [['methods' => 'GET', 'callback' => [$this, 'get_license'], 'permission_callback' => [$this, 'check_permissions']], ['methods' => 'POST', 'callback' => [$this, 'handle_license'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Find a step given it's key.
     *
     * @param string $key
     * @return Step
     */
    private function get_step_by_key(string $key)
    {
        foreach ($this->steps as $step) {
            if ($step->get_id() === $key) {
                return $step;
            }
        }
        return \false;
    }
    /**
     * Retrieve fields and their values (of a step).
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_steps($request)
    {
        $config = [];
        /** @var Step $step */
        foreach ($this->steps as $step) {
            if ($step instanceof Deferrable) {
                $details = $step->get_step_details();
                $config[] = \array_merge(['key' => $step->get_id(), 'fields' => $step->get_fields(), 'hidden' => $step->is_hidden()], $details);
            } else {
                $config[] = ['key' => $step->get_id(), 'label' => $step->get_name(), 'description' => $step->get_description(), 'heading' => $step->get_title(), 'tooltip' => $step->get_tooltip(), 'fields' => $step->get_fields(), 'hidden' => $step->is_hidden()];
            }
        }
        $utm_prefix = Util::get_remote_utm_id($this->get_plugin());
        return self::send_success_response(['steps' => $config, 'utm_prefix' => $utm_prefix]);
    }
    /**
     * Save fields of a step into the database.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function save_fields($request)
    {
        $step = $request->get_param('step');
        $step = $this->get_step_by_key($step);
        if (empty($step) || !$step instanceof Step) {
            return self::send_error_response(['message' => __('Could not find the appropriate step.', 'document-library-pro')]);
        }
        $values = Util::clean($request->get_param('values'));
        return $step->submit($values);
    }
    /**
     * Returns details about the license.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_license($request)
    {
        return self::send_success_response($this->get_license_details());
    }
    /**
     * Get license details from the database.
     *
     * @return array
     */
    private function get_license_details()
    {
        if (!\method_exists($this->get_plugin(), 'get_license')) {
            return ['status' => '', 'exists' => \false, 'key' => '', 'status_help_text' => '', 'error_message' => '', 'free_plugin' => \true];
        }
        $license_handler = $this->get_plugin()->get_license();
        return ['status' => $license_handler->get_status(), 'exists' => $license_handler->exists(), 'key' => $license_handler->get_license_key(), 'status_help_text' => $license_handler->get_status_help_text(), 'error_message' => $license_handler->get_error_message()];
    }
    /**
     * Handle licensing actions via the api.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function handle_license($request)
    {
        $license_key = $request->get_param('license');
        $action = $request->get_param('action');
        $allowed_actions = ['activate', 'check', 'deactivate'];
        if (empty($license_key)) {
            return self::send_error_response(['message' => __('Please enter a license key.', 'document-library-pro')]);
        }
        if (!\in_array($action, $allowed_actions, \true)) {
            return self::send_error_response(['message' => __('Invalid action requested.', 'document-library-pro')]);
        }
        $license_handler = $this->get_plugin()->get_license();
        switch ($action) {
            case 'activate':
                $license_handler->activate(\sanitize_text_field($license_key));
                break;
            case 'check':
                $license_handler->refresh();
                break;
            case 'deactivate':
                $license_handler->deactivate();
                break;
        }
        return self::send_success_response($this->get_license_details());
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public static function send_success_response($data = [])
    {
        $response = \array_merge(['success' => \true], $data);
        return new WP_REST_Response($response, 200);
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public static function send_error_response($data = [])
    {
        $response = \array_merge(['success' => \false], $data);
        return new WP_REST_Response($response, 403);
    }
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [];
    }
}
