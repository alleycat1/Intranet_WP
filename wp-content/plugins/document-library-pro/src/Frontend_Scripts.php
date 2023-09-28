<?php
namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Frontend_Scripts as PTP_Frontend_Scripts,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Util\Util,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;
/**
 * Responsible for registering the front-end styles and scripts in Document Library Pro.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Service, Registerable, Conditional {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

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
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		add_action( 'document_library_pro_before_get_table', [ $this, 'enqueue_table_scripts' ], 10, 1 );
		add_action( 'document_library_pro_before_get_grid', [ $this, 'enqueue_grid_scripts' ], 10, 1 );
	}

	/**
	 * Register the CSS assets.
	 */
	public function register_styles() {
		wp_register_style( 'dlp-folders', $this->asset_url( 'css/dlp-folders.css' ), [], $this->plugin->get_version() );
		wp_register_style( 'dlp-table', $this->asset_url( 'css/dlp-table.css' ), [], $this->plugin->get_version() );
		wp_register_style( 'dlp-grid', $this->asset_url( 'css/dlp-grid.css' ), [], $this->plugin->get_version() );
		wp_register_style( 'dlp-search-box', $this->asset_url( 'css/dlp-search-box.css' ), [], $this->plugin->get_version() );

		if ( is_singular( Post_Type::POST_TYPE_SLUG ) ) {
			wp_enqueue_style( 'dlp-single-post', $this->asset_url( 'css/dlp-single-post.css' ), [], $this->plugin->get_version() );
		}

		$shortcode_options = Options::get_user_shortcode_options();
		$misc_options      = array_merge( Options::get_additional_options(), [ 'grid_columns' => $shortcode_options['grid_columns'] ] );


		wp_add_inline_style( 'dlp-grid', self::build_custom_grid_styles( $misc_options ) );
	}

	/**
	 * Register JS assets.
	 */
	public function register_scripts() {

		// Folders
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-folders.js' )['dependencies'], [ 'jquery', 'jquery-blockui' ] );
		wp_register_script( 'dlp-folders', $this->asset_url( 'js/dlp-folders.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-folders',
			'dlp_folders_params',
			apply_filters(
				'document_library_pro_folders_script_params',
				[
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'                => wp_create_nonce( 'dlp-folders' ),
					'ajax_action'               => 'dlp_fetch_table',
					'ajax_folder_search'        => 'dlp_folder_search',
					'ajax_folder_library'       => 'dlp_folder_library',
					'ajax_min_search_term_len'  => max( 1, absint( apply_filters( 'document_library_pro_minimum_search_term_length', 3 ) ) )
				]
			)
		);

		// Grid
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-grid.js' )['dependencies'], [ 'jquery', 'jquery-blockui' ] );
		wp_register_script( 'dlp-grid', $this->asset_url( 'js/dlp-grid.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-grid',
			'dlp_grid_params',
			[
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'               => wp_create_nonce( 'dlp-grid' ),
				'ajax_action'              => 'dlp_fetch_grid',
				'ajax_min_search_term_len' => max( 1, absint( apply_filters( 'document_library_pro_minimum_search_term_length', 3 ) ) )

			]
		);

		// Download Zip
		wp_register_script( 'dlp-download-zip', $this->asset_url( 'js/dlp-download-zip.js' ), [], $this->plugin->get_version(), true );
		wp_register_script( 'dlp-multi-download', $this->asset_url( 'js/dlp-multi-download.js' ), [ 'jquery', 'dlp-download-zip' ], $this->plugin->get_version(), true );
		wp_localize_script(
			'dlp-multi-download',
			'dlp_multi_download_params',
			[
				'zip_failed_error' => __( 'Failed to create the zip file. Please reselect your documents and try again.', 'document-library-pro' )
			]
		);

		// Preview
		wp_register_script( 'micromodal', $this->asset_url( 'js/micromodal/micromodal.min.js' ), [], '0.4.6', true );
		wp_register_script( 'dlp-preview', $this->asset_url( 'js/dlp-preview.js' ), [ 'jquery', 'micromodal' ], $this->plugin->get_version(), true );
		wp_localize_script(
			'dlp-preview',
			'dlp_preview_params',
			[
				'pdf_error'   => __( 'Sorry, your browser doesn\'t support embedded PDFs.', 'document-library-pro' ),
				'audio_error' => __( 'Sorry, your browser doesn\'t support embedded audio.', 'document-library-pro' ),
				'video_error' => __( 'Sorry, your browser doesn\'t support embedded video.', 'document-library-pro' )
			]
		);

		// Download Count
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( $this->plugin, 'dlp-count.js' )['dependencies'], [ 'jquery' ] );
		wp_register_script( 'dlp-count', $this->asset_url( 'js/dlp-count.js' ), $script_dependencies, $this->plugin->get_version(), true );
		Util::add_inline_script_params(
			'dlp-count',
			'dlp_count_params',
			[
				'ajax_action' => 'dlp_download_count',
				'ajax_nonce'  => wp_create_nonce( 'dlp-count' ),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	/**
	 * Enqueue the table assets.
	 *
	 * @param Posts_Table $posts_table
	 */
	public function enqueue_table_scripts( $posts_table ) {
		self::load_document_table_scripts( $posts_table->args );
	}

	/**
	 * Enqueue the grid assets.
	 *
	 * @param Document_Grid $document_grid
	 */
	public function enqueue_grid_scripts( $document_grid ) {
		self::load_document_grid_scripts( $document_grid->args );
	}

	/**
	 * Load the table assets.
	 *
	 * @param mixed|null $args
	 */
	public static function load_document_table_scripts( $args = null ) {
		wp_enqueue_style( 'dlp-table' );

		if ( ! $args ) {
			return;
		}

		if ( in_array( $args->accessing_documents, [ 'checkbox', 'both' ], true ) ) {
			wp_enqueue_script( 'dlp-multi-download' );
		}

		if ( $args->preview ) {
			self::load_preview_scripts();
		}

		self::load_download_count_scripts();
	}

	/**
	 * Load the document grid assets.
	 *
	 * @param Table_Args|null $args
	 */
	public static function load_document_grid_scripts( $args = null ) {
		wp_enqueue_style( 'dlp-grid' );
		wp_enqueue_script( 'dlp-grid' );

		if ( ! $args ) {
			return;
		}

		if ( $args->preview ) {
			self::load_preview_scripts();
		}

		if ( $args->lightbox ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			wp_enqueue_script( 'photoswipe-ui-default' );

			add_action( 'wp_footer', [ self::class, 'load_photoswipe_template' ] );
		}

		if ( $args->shortcodes ) {
			// Add fitVids.js for responsive video if we're displaying shortcodes.
			if ( apply_filters( 'document_library_pro_use_fitvids', true ) ) {
				wp_enqueue_script( 'fitvids' );
			}

			// Queue media element and playlist scripts/styles.
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-playlist' );

			add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
		}

		self::load_download_count_scripts();
	}

	/**
	 * Load Photoswipe Template.
	 */
	public static function load_photoswipe_template() {
		PTP_Util::include_template( 'photoswipe.php' );
	}

	/**
	 * Load Preview Scripts.
	 */
	public static function load_preview_scripts() {
		wp_enqueue_script( 'dlp-preview' );
	}

	/**
	 * Load Download Count Scripts.
	 */
	public static function load_download_count_scripts() {
		wp_enqueue_script( 'dlp-count' );
	}

	/**
	 * Load Folder Scripts.
	 *
	 * @param Table_Args $args
	 */
	public static function load_folder_scripts( $args ) {
		if ( ! apply_filters( 'document_library_pro_load_frontend_scripts', true ) ) {
			return;
		}

		if ( $args->layout === 'table' ) {
			PTP_Frontend_Scripts::load_table_scripts( $args );
			self::load_document_table_scripts( $args );
		}

		if ( $args->layout === 'grid' ) {
			self::load_document_grid_scripts( $args );
		}

		wp_enqueue_script( 'dlp-folders' );
		wp_enqueue_style( 'dlp-folders' );
	}

	/**
	 * Get the assets url.
	 *
	 * @param string $path
	 * @return string
	 */
	private function asset_url( $path ) {
		return $this->plugin->get_dir_url() . 'assets/' . ltrim( $path, '/' );
	}

	/**
	 * Build Custom Grid Styles.
	 *
	 * @param array $options
	 * @return string
	 */
	private static function build_custom_grid_styles( $options ) {
		$styles = [];

		// Ensure all keys for grid design options are set.
		$options = array_merge(
			array_fill_keys( [ 'grid_image_bg' ], '' ),
			$options
		);

		if ( ! empty( $options['grid_image_bg'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-featured-icon',
				'css'      => sprintf( 'background-color: %1$s !important;', $options['grid_image_bg'] )
			];
		}

		if ( ! empty( $options['grid_category_bg'] ) ) {
			$styles[] = [
				'selector' => '.dlp-grid-card-categories span',
				'css'      => sprintf( 'background-color: %1$s !important;', $options['grid_category_bg'] )
			];
		}

		$result = array_reduce(
			$styles,
			function( $carry, $style ) {
				if ( ! empty( $style['css'] ) ) {
					$carry .= sprintf( '%1$s { %2$s } ', $style['selector'], $style['css'] );
				}
				return $carry;
			},
			''
		);

		return $result;
	}
}
