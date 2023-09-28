<?php
namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Grid\Grid_Factory,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Factory,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the AJAX requests for document tables that have folders enabled
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Ajax_Handler implements Service, Registerable, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_front_end();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'wp_ajax_nopriv_dlp_fetch_grid', [ self::class, 'fetch_grid' ] );
		add_action( 'wp_ajax_dlp_fetch_grid', [ self::class, 'fetch_grid' ] );

		add_action( 'wp_ajax_nopriv_dlp_folder_search', [ self::class, 'folder_search' ] );
		add_action( 'wp_ajax_dlp_folder_search', [ self::class, 'folder_search' ] );

		add_action( 'wp_ajax_nopriv_dlp_fetch_table', [ self::class, 'fetch_folder_library' ] );
		add_action( 'wp_ajax_dlp_fetch_table', [ self::class, 'fetch_folder_library' ] );

		add_action( 'wp_ajax_nopriv_dlp_download_count', [ self::class, 'count_download' ] );
		add_action( 'wp_ajax_dlp_download_count', [ self::class, 'count_download' ] );
	}

	/**
	 * Fetches a Grid or Table via AJAX.
	 */
	public static function fetch_folder_library() {
		$category_id    = filter_input( INPUT_POST, 'category_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$shortcode_atts = filter_input( INPUT_POST, 'shortcode_atts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : [];

		$default_args = Table_Args::get_site_defaults();

		$category_object = get_term( $category_id, Taxonomies::CATEGORY_SLUG );

		$folder_args = [
			'post_type'     => 'dlp_document',
			'search_box'    => false,
			'doc_category'  => $category_object->slug,
			'numeric_terms' => true,
		];

		$sub_categories = get_term_children( $category_id, Taxonomies::CATEGORY_SLUG );
		
		$sub_categories = array_map(
			function( $term_id ) {
				$term = get_term( $term_id, Taxonomies::CATEGORY_SLUG );

				return $term->slug;
			},
			$sub_categories
		);

		if ( ! empty( $sub_categories ) && ! is_wp_error( $sub_categories ) ) {
			$folder_args['exclude_doc_category'] = implode( ',', $sub_categories );
		}

		$args = array_merge( $default_args, $shortcode_atts, $folder_args );

		$args['reset_button'] = $args['filters'] !== 'false' && $args['filters'] !== '' ? 'true' : 'false';

		$output['layout'] = $args['layout'];
		$document_layout  = $args['layout'] === 'grid' ? Grid_Factory::create( $args ) : Table_Factory::create( $args );

		if ( ! $document_layout ) {
			wp_die( 'Error: Document library could not be loaded.', 'document-library-pro' );
		}

		if ( empty( $document_layout->query->get_posts() ) ) {
			$output['html'] = '';

			wp_send_json( $output );
		}

		$output['html'] = $args['layout'] === 'grid' ? $document_layout->get_grid( 'html' ) : $document_layout->get_table( 'html' );

		wp_send_json( $output );
	}

	/**
	 * Fetches a Grid Page via AJAX
	 */
	public static function fetch_grid() {
		$grid_id = filter_input( INPUT_POST, 'grid_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$grid    = Grid_Factory::fetch( $grid_id );

		if ( ! $grid ) {
			wp_die( 'Error: Document grid could not be loaded.' );
		}

		// Build the args to update
		$page_number  = filter_input( INPUT_POST, 'page_number', FILTER_VALIDATE_INT ) ?? 1;
		$search_query = filter_input( INPUT_POST, 'search_query', FILTER_DEFAULT ) ?? '';

		// Don't search unless they've typed at least 3 characters.
		if ( ! empty( $search_query ) && ! PTP_Util::is_valid_search_term( $search_query ) ) {
			$search_query = '';
		}

		// Retrieve the new grid
		$args['page_number']      = $page_number;
		$args['user_search_term'] = $search_query;

		$grid->update( $args );

		$output = $grid->get_grid( 'html_parts' );

		wp_send_json( $output );
	}

	/**
	 * Fetch a folder search via AJAX
	 */
	public static function folder_search() {
		$search_query   = filter_input( INPUT_POST, 'search_query', FILTER_DEFAULT ) ?? '';
		$shortcode_atts = filter_input( INPUT_POST, 'shortcode_atts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : [];

		// Don't search unless they've typed at least 3 characters.
		if ( ! empty( $search_query ) && ! PTP_Util::is_valid_search_term( $search_query ) ) {
			wp_send_json( [] );
		}

		$default_args = Table_Args::get_site_defaults();

		$folder_search_args = [
			'post_type'        => 'dlp_document',
			'user_search_term' => $search_query,
			'lazy_load'        => false,
			'search_box'       => false,
			'cache'            => false,
		];

		$args = array_merge( $default_args, $shortcode_atts, $folder_search_args );

		$args['reset_button'] = $args['filters'] !== 'false' && $args['filters'] !== '' ? 'true' : 'false';

		$document_library = $args['layout'] === 'grid' ? Grid_Factory::create( $args ) : Table_Factory::create( $args );

		if ( ! $document_library ) {
			wp_die( 'Error: Document library could not be loaded.', 'document-library-pro' );
		}

		$output['layout'] = $args['layout'];

		if ( empty( $document_library->query->get_posts() ) ) {
			$output['html'] = ! empty( $args['no_posts_filtered_message'] ) ? $args['no_posts_filtered_message'] : __( 'No matching documents', 'document-library-pro' );
			wp_send_json( $output );
		}

		$output['html'] = $args['layout'] === 'grid' ? $document_library->get_grid( 'html' ) : $document_library->get_table( 'html' );

		wp_send_json( $output );
	}

	/**
	 * Count a download via AJAX
	 */
	public static function count_download() {
		$download_id  = filter_input( INPUT_POST, 'download_id', FILTER_SANITIZE_NUMBER_INT );
		$download_ids = filter_input( INPUT_POST, 'download_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY ) ?? (array) $download_id;

		foreach ( $download_ids as $download_id ) {
			$count = (int) get_post_meta( $download_id, '_dlp_download_count', true ) ?? 0;
			$count++;
			update_post_meta( $download_id, '_dlp_download_count', $count );
		}

		wp_send_json( [
			$download_id,
			$download_ids
		] );
	}

	/**
	 * Fetches a table via AJAX
	 *
	 * @deprecated
	 */
	public static function fetch_table() {
		_deprecated_function( __METHOD__, '1.3', esc_html( self::class . '::fetch_folder_library' ) );

		self::fetch_folder_library();
	}
}
