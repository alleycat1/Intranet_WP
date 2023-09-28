<?php
namespace Barn2\Plugin\Document_Library_Pro\Submissions;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Taxonomies;

/**
 * Responsible for registering rest api routes used by the submission form
 * on the frontend.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Rest_Api implements Registerable {

	const API_NAMESPACE = 'dlp/v1';

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'set_object_terms', [ $this, 'set_term' ], 10, 4 );
		add_action( 'edited_term', [ $this, 'edited_term' ], 10, 3 );
		add_action( 'create_term', [ $this, 'edited_term' ], 10, 3 );
		add_action( 'delete_term', [ $this, 'edited_term' ], 10, 3 );
	}

	/**
	 * Verify frontend requests.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return bool
	 */
	public function check_public_permission( $request ) {
		$nonce = $request->get_header( 'x-wp-nonce' );

		if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Register the REST Api routes.
	 *
	 * @return void
	 */
	public function register_routes() {

		register_rest_route(
			self::API_NAMESPACE,
			'/terms/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_terms' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Query the api, retrieve and cache terms for the specified taxonomy.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_terms( $request ) {
		$taxonomy = $request->get_param( 'taxonomy' );

		$allowed_taxonomies = [
			Taxonomies::CATEGORY_SLUG,
			Taxonomies::TAG_SLUG,
			Taxonomies::AUTHOR_SLUG,
		];

		if ( ! in_array( $taxonomy, $allowed_taxonomies, true ) ) {
			return $this->send_error_response(
				[
					'message' => __( 'Invalid taxonomy.', 'document-library-pro' )
				]
			);
		}

		$args = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		];

		$args = apply_filters( 'dlp_forms_get_terms', $args, $taxonomy );

		// Store in a transient to help sites with many terms.
		$terms_hash = 'dlp_terms_' . md5( wp_json_encode( $args ) . $this->get_transient_version( 'dlp_get_' . $args['taxonomy'] ) );
		$terms      = get_transient( $terms_hash );

		if ( false === $terms ) {
			$terms = get_terms( $args );

			set_transient( $terms_hash, $terms, DAY_IN_SECONDS );
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'terms'   => $terms,
			],
			200
		);
	}

	/**
	 * Gets transient version for transients with dynamic names.
	 *
	 * Used to append a unique string (based on time()) to each transient. When transients
	 * are invalidated, the transient version will increment and data will be regenerated.
	 *
	 * @param  string  $group   Name for the group of transients we need to invalidate.
	 * @param  boolean $refresh True to force a new version (Default: false).
	 * @return string Transient version based on time(), 10 digits.
	 */
	public function get_transient_version( $group, $refresh = false ) {
		$transient_name  = $group . '-transient-version';
		$transient_value = get_transient( $transient_name );

		if ( false === $transient_value || true === $refresh ) {
			$this->delete_version_transients( $transient_value );
			set_transient( $transient_name, $transient_value = time() );
		}
		return $transient_value;
	}

	/**
	 * When the transient version increases, this is used to remove all past transients.
	 *
	 * @param string $version
	 * @return void
	 */
	private static function delete_version_transients( $version ) {
		global $wpdb;

		if ( ! wp_using_ext_object_cache() && ! empty( $version ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Only used when object caching is disabled.
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s;", '\_transient\_%' . $version ) );
		}
	}

	/**
	 * Refresh the taxonomy terms cache when terms are created.
	 *
	 * @param string|int $object_id
	 * @param string     $terms
	 * @param string     $tt_ids
	 * @param string     $taxonomy
	 */
	public function set_term( $object_id = '', $terms = '', $tt_ids = '', $taxonomy = '' ) {
		$allowed_taxonomies = [
			Taxonomies::CATEGORY_SLUG,
			Taxonomies::TAG_SLUG,
			Taxonomies::AUTHOR_SLUG
		];

		if ( ! in_array( $taxonomy, $allowed_taxonomies, true ) ) {
			return;
		}

		$this->get_transient_version( 'dlp_get_' . sanitize_text_field( $taxonomy ), true );
	}

	/**
	 * Refresh the taxonomy terms cache when terms are edited.
	 *
	 * @param string|int $term_id
	 * @param string|int $tt_id
	 * @param string     $taxonomy
	 */
	public function edited_term( $term_id = '', $tt_id = '', $taxonomy = '' ) {
		$allowed_taxonomies = [
			Taxonomies::CATEGORY_SLUG,
			Taxonomies::TAG_SLUG
		];

		if ( ! in_array( $taxonomy, $allowed_taxonomies, true ) ) {
			return;
		}

		$this->get_transient_version( 'dlp_get_' . sanitize_text_field( $taxonomy ), true );
	}

	/**
	 * Send an error response via `WP_Rest_Response`.
	 *
	 * @param array $data additional data to send through the response.
	 * @return \WP_REST_Response
	 */
	public function send_error_response( $data = [] ) {
		$response = array_merge( [ 'success' => false ], $data );
		return new \WP_REST_Response( $response, 403 );
	}

}
