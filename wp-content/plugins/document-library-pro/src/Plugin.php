<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Widgets\Document_Search,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Translatable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service_Provider,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service_Container,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Premium_Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Notices;

defined( 'ABSPATH' ) || exit;

/**
 * The main plugin class. Responsible for setting up to core plugin services.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin implements Licensed_Plugin, Registerable, Translatable, Service_Provider {
	use Service_Container;

	const NAME    = 'Document Library Pro';
	const ITEM_ID = 194365;

	/**
	 * Constructs and initalizes the main plugin class.
	 *
	 * @param string $file The root plugin __FILE__
	 * @param string $version The current plugin version
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct(
			[
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'version'            => $version,
				'file'               => $file,
				'settings_path'      => 'admin.php?page=document_library_pro',
				'documentation_path' => 'kb-categories/document-library-pro-kb/',
			]
		);
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		parent::register();

		register_activation_hook( $this->get_file(), [ 'Barn2\\Plugin\\Document_Library_Pro\\Install', 'install' ] );

		$plugin_setup = new Admin\Plugin_Setup( $this->get_file(), $this );
		$plugin_setup->register();

		add_action( 'plugins_loaded', [ $this, 'maybe_load_plugin' ] );
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	/**
	 * Maybe bootup plugin
	 */
	public function maybe_load_plugin() {
		if ( ! $this->check_wp_requirements() ) {
			return;
		}

		$this->check_updates();

		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
		add_action( 'init', [ $this, 'register_services' ], 5 );
	}

	/**
	 * Retrieve the plugin services.
	 *
	 * @return array
	 */
	public function get_services() {
		$services = [
			'admin' => new Admin\Admin( $this ),
			'wizard' => new Admin\Wizard\Setup_Wizard( $this ),
		];

		// Initialize plugin if valid
		if ( $this->has_valid_license() ) {
			$services['post_type']                 = new Post_Type();
			$services['taxonomies']                = new Taxonomies();
			$services['shortcode']                 = new Shortcode();
			$services['frontend_scripts']          = new Frontend_Scripts( $this );
			$services['ajax_handler']              = new Ajax_Handler();
			$services['single_content']            = new Single_Content();
			$services['comments']                  = new Comments( $this );
			$services['preview_modal']             = new Preview_Modal();
			$services['search_handler']            = new Search_Handler();
			$services['shortcode/doc_search']      = new Shortcodes\Document_Search();
			$services['rest_api'] = new Submissions\Rest_Api();
			$services['submission_form']           = new Submissions\Frontend_Form();
			$services['shortcode/submission_form'] = new Shortcodes\Frontend_Form();

			// PTP Integration
			$services['ptp_integration']          = new Integration\Posts_Table_Pro();
			$services['ptp/frontend_scripts']     = new Posts_Table_Pro\Frontend_Scripts( $this );
			$services['ptp/ajax_handler']         = new Posts_Table_Pro\Ajax_Handler();
			$services['ptp/theme_integration']    = new Posts_Table_Pro\Integration\Theme_Integration();

			// 3rd Party Integration
			$services['integration/wp_term_order']         = new Integration\WP_Term_Order();
			$services['integration/custom_taxonomy_order'] = new Integration\Custom_Taxonomy_Order();
			$services['integration/facetwp']               = new Integration\FacetWP();
			$services['integration/searchwp']              = new Integration\SearchWP();
		}

		return $services;
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets() {
		if ( ! $this->get_license()->is_valid() ) {
			return;
		}

		register_widget( Document_Search::class );
	}


	/**
	 * Check the WP Requirements
	 *
	 * @return bool
	 */
	private function check_wp_requirements() {
		global $wp_version;

		if ( is_admin() && version_compare( $wp_version, '5.0', '<' ) ) {
			$can_update_core = current_user_can( 'update_core' );

			$admin_notice = new Notices();
			$admin_notice->add(
				'dlp_invalid_wp_version',
				'',
				sprintf(
					/* translators: %1$s: Plugin Name %2$s: Update core link <a href="..">  %3$s: </a> */
					esc_html__( 'The %1$s plugin requires WordPress 4.9 or greater. Please %2$supdate%3$s your WordPress installation.', 'document-library-pro' ),
					'<strong>' . self::NAME . '</strong>',
					( $can_update_core ? sprintf( '<a href="%s">', esc_url( self_admin_url( 'update-core.php' ) ) ) : '' ),
					( $can_update_core ? '</a>' : '' )
				),
				[
					'type'       => 'error',
					'capability' => 'install_plugins',
					'screens'    => [ 'plugins' ]
				]
			);
			$admin_notice->boot();
			return false;
		}

		return true;
	}

	/**
	 * Check if plugin has updates.
	 */
	private function check_updates() {
		$code_version = $this->data['version'];
		$db_version   = get_option( 'dlp_db_version' );
		if ( version_compare( $code_version, $db_version, '>' ) ) {

			$version_updates = Update_Functions::$updates;

			foreach ( (array) $version_updates as $version => $update_functions ) {

				$db_version = get_option( 'dlp_db_version' );

				if ( version_compare( $db_version, $version, '<' ) && version_compare( $code_version, $version, '>=' ) ) {

					foreach ( $update_functions as $function ) {
						if ( is_callable( [ new Update_Functions(), $function ] ) ) {
							Update_Functions::$function();
						}
					}

					update_option( 'dlp_db_version', $version );
					$db_version = $version;

				}

			}

			if ( version_compare( $code_version, $db_version, '>' ) ) {
				update_option( 'dlp_db_version', $code_version );
			}
		}
	}

	/**
	 * Load the text domain directory
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'document-library-pro', false, dirname( $this->get_basename() ) . '/languages' );
	}
}
