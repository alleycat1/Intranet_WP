<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_API_Helper,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args as PTP_Table_Args;

defined( 'ABSPATH' ) || exit;

/**
 * Document Table Setting Tab
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Grid implements Registerable {
	const TAB_ID       = 'document_grid';
	const OPTION_GROUP = 'document_library_pro_grid';
	const MENU_SLUG    = 'dlp-settings-grid';

	private $plugin;
	private $id;
	private $title;
	private $default_settings;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin           = $plugin;
		$this->id               = 'document_grid';
		$this->title            = __( 'Document Grid', 'document-library-pro' );
		$this->default_settings = Options::get_user_shortcode_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register the Settings with WP Settings API.
	 */
	public function register_settings() {

		Settings_API_Helper::add_settings_section(
			'dlp_grid_title',
			self::MENU_SLUG,
			'',
			[ $this, 'grid_settings_title' ],
			[]
		);

		Settings_API_Helper::add_settings_section(
			'dlp_grid_content',
			self::MENU_SLUG,
			__( 'Library content', 'document-library-pro' ),
			[ $this, 'grid_settings_description' ],
			$this->get_grid_settings()
		);

		Settings_API_Helper::add_settings_section(
			'dlp_grid_design',
			self::MENU_SLUG,
			__( 'Design', 'document-library-pro' ),
			'__return_false',
			$this->get_design_settings()
		);

	}

	/**
	 * Get the Settings Tab description.
	 */
	public function grid_settings_title() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'The following options are used when documents are listed in a grid layout. You can override them in the [doc_library] shortcode. See the %1$sknowledge base%2$s for details of how to configure your document grids even further.', 'document-library-pro' ) .
			'</p>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#document-grid-tab' ), true ),
			'</a>'
		);
	}

	/**
	 * Get the Grid Settings description.
	 */
	public function grid_settings_description() {
		esc_html_e( 'Choose which information to display in the grid of documents.', 'document-library-pro' );
	}

	/**
	 * Get the Grid settings.
	 *
	 * @return array
	 */
	private function get_grid_settings() {

		return [
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[grid_content]',
				'title'   => __( 'Display', 'document-library-pro' ),
				'type'    => 'multicheckbox',
				'options' => [
					'image'          => __( 'Image', 'document-library-pro' ),
					'title'          => __( 'Title', 'document-library-pro' ),
					'file_type'      => __( 'File type', 'document-library-pro' ),
					'file_size'      => __( 'File size', 'document-library-pro' ),
					'download_count' => __( 'Download count', 'document-library-pro' ),
					'doc_categories' => __( 'Categories', 'document-library-pro' ),
					'excerpt'        => __( 'Excerpt/content', 'document-library-pro' ),
					'custom_fields'  => __( 'Custom fields', 'document-library-pro' ),
					'link'           => __( 'Document link', 'document-library-pro' ),
				],
				'default' => $this->default_settings['grid_content'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[grid_columns]',
				'title'   => __( 'Number of columns', 'document-library-pro' ),
				'type'    => 'select',
				'options' => [
					'autosize' => __( 'Auto-size', 'document-library-pro' ),
					'1'        => __( '1 column', 'document-library-pro' ),
					'2'        => __( '2 columns', 'document-library-pro' ),
					'3'        => __( '3 columns', 'document-library-pro' ),
					'4'        => __( '4 columns', 'document-library-pro' ),
				],
				'default' => $this->default_settings['grid_columns'],
			],
		];
	}

	/**
	 * Get the Design settings.
	 *
	 * @return array
	 */
	private function get_design_settings() {
		return [
			[
				'title'       => __( 'Background color', 'document-library-pro' ),
				'type'        => 'color',
				'desc'        => __( 'Change the color that appears behind the file type icons.', 'document-library-pro' ),
				'id'          => Options::MISC_OPTION_KEY . '[grid_image_bg]',
				'field_class' => 'custom-design',
				'default'     => '#f7f7ed'
			],
			[
				'title'       => __( 'Category background color', 'document-library-pro' ),
				'type'        => 'color',
				'desc'        => __( 'Change the color that appears behind the category badges.', 'document-library-pro' ),
				'id'          => Options::MISC_OPTION_KEY . '[grid_category_bg]',
				'field_class' => 'custom-design',
				'default'     => '#f7fafc'
			],
		];
	}

	/**
	 * Get the Tab title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the Tab ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}
}
