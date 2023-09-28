<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_API_Helper,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args as PTP_Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Document Table Setting Tab
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Table implements Registerable {
	const TAB_ID       = 'document_libraries';
	const OPTION_GROUP = 'document_library_pro_table';
	const MENU_SLUG    = 'dlp-settings-libraries';

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
		$this->id               = 'document_libraries';
		$this->title            = __( 'Document Tables', 'document-library-pro' );
		$this->default_settings = array_merge( PTP_Table_Args::get_table_defaults(), Options::get_dlp_specific_default_args() );
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

		Settings_API_Helper::add_settings_section( 'dlp_grid_title', self::MENU_SLUG, '', [ $this, 'table_content_description' ], [] );

		// Table Content section.
		Settings_API_Helper::add_settings_section( 'dlp_shortcode_defaults', self::MENU_SLUG, __( 'Library content', 'document-library-pro' ), '__return_false', $this->get_library_content_settings() );

		// Document links
		Settings_API_Helper::add_settings_section( 'dlp_links', self::MENU_SLUG, __( 'Document links', 'document-library-pro' ), '__return_false', $this->get_document_link_settings() );

		// Loading Posts section.
		Settings_API_Helper::add_settings_section( 'dlp_post_loading', self::MENU_SLUG, __( 'Loading & performance', 'document-library-pro' ), '__return_false', $this->get_performance_settings() );

		// Table Controls section.
		Settings_API_Helper::add_settings_section( 'dlp_table_controls', self::MENU_SLUG, __( 'Document library controls', 'document-library-pro' ), '__return_false', $this->get_table_controls_settings() );

		// Table design.
		Settings_API_Helper::add_settings_section( 'dlp_design', self::MENU_SLUG, __( 'Design', 'document-library-pro' ), [ $this, 'display_table_design_description' ], $this->get_design_settings() );
	}

	/**
	 * Get the Library Content settings.
	 *
	 * @return array
	 */
	private function get_library_content_settings() {
		return [
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[columns]',
				'title'   => __( 'Columns', 'document-library-pro' ),
				'type'    => 'text',
				'desc'    => __( 'Enter the fields to include in your document tables.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-columns/' ),
				'default' => $this->default_settings['columns'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[image_size]',
				'title'   => __( 'Image size', 'document-library-pro' ),
				'type'    => 'text',
				'desc'    => __( 'Enter WxH in pixels (e.g. 80x80).', 'document-library-pro' ) . $this->read_more( 'kb/document-library-image-options/#image-size' ),
				'default' => $this->default_settings['image_size'],
			],
		];
	}

	/**
	 * Get the Doocument Link settings.
	 *
	 * @return array
	 */
	private function get_document_link_settings() {
		return [
			[
				'title'   => __( 'Accessing documents', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[accessing_documents]',
				'desc'    => __( 'How a user accesses documents from the ‘link’ column.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-settings/#accessing-documents' ),
				'options' => [
					'link'     => __( 'Link to document', 'document-library-pro' ),
					'checkbox' => __( 'Multi-select checkboxes', 'document-library-pro' ),
					'both'     => __( 'Both', 'document-library-pro' ),
				],
				'default' => $this->default_settings['accessing_documents']
			],
			[
				'title'   => __( 'Multi-download button', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[multi_download_button]',
				'desc'    => __( 'The position of the button to download all selected documents.', 'document-library-pro' ),
				'options' => [
					'below' => __( 'Below document library', 'document-library-pro' ),
					'above' => __( 'Above document library', 'document-library-pro' ),
					'both'  => __( 'Both', 'document-library-pro' ),
				],
				'default' => $this->default_settings['multi_download_button']
			],
			[
				'title'   => __( 'Multi-download button text', 'document-library-pro' ),
				'type'    => 'text',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[multi_download_text]',
				'desc'    => __( 'The text for the button to download all selected documents.', 'document-library-pro' ),
				'default' => $this->default_settings['multi_download_text']
			],
		];
	}

	/**
	 * Get the Performance settings.
	 *
	 * @return array
	 */
	private function get_performance_settings() {
		return [
			[
				'title'             => __( 'Lazy load', 'document-library-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[lazy_load]',
				'label'             => __( 'Load the document table one page at a time', 'document-library-pro' ),
				'desc'              => __( 'Enable this if you have many documents or experience slow page load times.', 'document-library-pro' ) . '<br/>' .
				__( 'Warning: Lazy load limits the searching and sorting features in the document library. Only use it if you definitely need it.', 'document-library-pro' ) .
				$this->read_more( 'kb/document-library-lazy-load/' ),
				'default'           => $this->default_settings['lazy_load'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'post-limit',
					'data-toggle-val'  => 0
				]
			],
			[
				'title'             => __( 'Document limit', 'document-library-pro' ),
				'type'              => 'number',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[post_limit]',
				'desc'              => __( 'The maximum number of documents to display in each table. Enter -1 to show all documents.', 'document-library-pro' ),
				'default'           => $this->default_settings['post_limit'],
				'class'             => 'small-text post-limit',
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'title'             => __( 'Caching', 'document-library-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[cache]',
				'label'             => __( 'Cache document libraries to improve load time', 'document-library-pro' ),
				'default'           => $this->default_settings['cache'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'expires-after'
				]
			],
			[
				'title'             => __( 'Cache expires after', 'document-library-pro' ),
				'type'              => 'number',
				'id'                => Options::MISC_OPTION_KEY . '[cache_expiry]',
				'suffix'            => __( 'hours', 'document-library-pro' ),
				'desc'              => __( 'Your table data will be refreshed after this length of time.', 'document-library-pro' ),
				'default'           => 6,
				'class'             => 'expires-after',
				'custom_attributes' => [
					'min' => 1,
					'max' => 9999
				]
			],
		];
	}

	/**
	 * Get the Table Controls settings.
	 *
	 * @return array
	 */
	private function get_table_controls_settings() {
		return [
			[
				'title'             => __( 'Search filters', 'document-library-pro' ),
				'type'              => 'select',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[filters]',
				'options'           => [
					'false'  => __( 'Disabled', 'document-library-pro' ),
					'true'   => __( 'Show based on columns in table', 'document-library-pro' ),
					'custom' => __( 'Custom', 'document-library-pro' )
				],
				'desc'              => __( 'Show dropdown menus to filter by doc_categories, doc_tags, or custom taxonomy.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-filters/' ),
				'default'           => $this->default_settings['filters'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'custom-search-filter',
					'data-toggle-val'  => 'custom'
				]
			],
			[
				'title' => __( 'Custom filters', 'document-library-pro' ),
				'type'  => 'text',
				'id'    => Options::SHORTCODE_OPTION_KEY . '[filters_custom]',
				'desc'  => __( 'Enter the filters as a comma-separated list.', 'document-library-pro' ),
				'class' => 'regular-text custom-search-filter'
			],
			[
				'title'   => __( 'Page length', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[page_length]',
				'options' => [
					'top'    => __( 'Above library', 'document-library-pro' ),
					'bottom' => __( 'Below library', 'document-library-pro' ),
					'both'   => __( 'Above and below library', 'document-library-pro' ),
					'false'  => __( 'Hidden', 'document-library-pro' )
				],
				'desc'    => __( "The position of the 'Show [x] entries' dropdown list.", 'document-library-pro' ),
				'default' => $this->default_settings['page_length']
			],
		];
	}

	/**
	 * Get the Table Design settings.
	 *
	 * @return string
	 */
	private function get_design_settings() {
		return [
			[
				'id'                => Options::MISC_OPTION_KEY . '[design]',
				'title'             => __( 'Design', 'document-library-pro' ),
				'type'              => 'radio',
				'options'           => [
					'default' => __( 'Default', 'document-library-pro' ),
					'custom'  => __( 'Custom', 'document-library-pro' ),
				],
				'default'           => 'default',
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'custom-design',
					'data-toggle-val'  => 'custom'
				]
			],
			[
				'title'             => __( 'Borders', 'document-library-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[external_border]',
				'desc'              => $this->get_icon( 'external-border.svg', __( 'External border', 'document-library-pro' ) ) . __( 'External', 'document-library-pro' ),
				'placeholder'       => __( 'Width', 'document-library-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[header_border]',
				/* translators: 'Header' in this context refers to the headings in the table. */
				'desc'              => $this->get_icon( 'header-border.svg', __( 'Header border', 'document-library-pro' ) ) . __( 'Header', 'document-library-pro' ),
				'placeholder'       => __( 'Width', 'document-library-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[body_border]',
				/* translators: 'Body' in this context refers to the main table content. */
				'desc'              => $this->get_icon( 'cell-border.svg', __( 'Body border', 'document-library-pro' ) ) . __( 'Body', 'document-library-pro' ),
				'placeholder'       => __( 'Width', 'document-library-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Header background color', 'document-library-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[header_bg]',
				'field_class' => 'custom-design'
			],
			[
				'title'             => __( 'Header text', 'document-library-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[header_text]',
				'custom_attributes' => [
					'min' => 8,
					'max' => 50
				],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Main background color', 'document-library-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[body_bg]',
				'field_class' => 'custom-design'
			],
			[
				'title'       => __( 'Alternating background color (optional)', 'document-library-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[body_bg_alt]',
				'field_class' => 'custom-design'
			],
			[
				'title'             => __( 'Body text', 'document-library-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[body_text]',
				'custom_attributes' => [
					'min' => 8,
					'max' => 50
				],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Spacing', 'document-library-pro' ),
				'type'        => 'select',
				'id'          => Options::MISC_OPTION_KEY . '[table_spacing]',
				'options'     => [
					'default'  => __( 'Theme default', 'document-library-pro' ),
					'compact'  => __( 'Compact', 'document-library-pro' ),
					'normal'   => __( 'Normal', 'document-library-pro' ),
					'spacious' => __( 'Spacious', 'document-library-pro' ),
				],
				'default'     => 'default',
				'field_class' => 'custom-design'
			]
		];
	}

	/**
	 * Output the Table Design description.
	 */
	public function display_table_design_description() {
		?>
		<p><?php esc_html_e( 'Customize the design of the document tables.', 'document-library-pro' ); ?></p>
		<?php
	}

	/**
	 * Output the Table Content description.
	 */
	public function table_content_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'The following options are used when documents are listed in a table layout. You can override them in the [doc_library] shortcode. See the %1$sknowledge base%2$s for details of how to configure your document tables even further.', 'document-library-pro' ) .
			'</p>',
             // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#document-tables-tab' ), true ),
			'</a>'
		);
	}

	/**
	 * Get a Read more KB link.
	 *
	 * @param string $path
	 * @return string
	 */
	private function read_more( $path ) {
		return ' ' . Lib_Util::barn2_link( $path );
	}

	/**
	 * Get icon for color size field.
	 *
	 * @param string $icon
	 * @param string $alt_text
	 *
	 * @return string
	 */
	private function get_icon( $icon, $alt_text = '' ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" width="20" height="20" class="icon" />',
			Util::get_asset_url( 'images/' . ltrim( $icon, '/' ) ),
			$alt_text
		);
	}

	/**
	 * Get the tab title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the tab ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}
}
