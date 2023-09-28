<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_API_Helper,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args as PTP_Table_Args;

defined( 'ABSPATH' ) || exit;

/**
 * General Setting Tab
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class General implements Registerable {
	const TAB_ID       = 'general';
	const OPTION_GROUP = 'document_library_pro_general';
	const MENU_SLUG    = 'dlp-settings-general';

	private $plugin;
	private $license_setting;
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
		$this->license_setting  = $plugin->get_license_setting();
		$this->id               = 'general';
		$this->title            = __( 'General', 'document-library-pro' );
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

		// Licence Key section.
		Settings_API_Helper::add_settings_section(
			'ptp_license_key',
			self::MENU_SLUG,
			'',
			[ $this, 'support_links' ],
			[
				$this->license_setting->get_license_key_setting(),
				$this->license_setting->get_license_override_setting()
			]
		);

		// Document Data
		Settings_API_Helper::add_settings_section( 'dlp_general_fields', self::MENU_SLUG, __( 'Document data', 'document-library-pro' ), [ $this, 'display_document_data_description' ], $this->get_document_data_settings() );

		// Document Lists
		Settings_API_Helper::add_settings_section( 'dlp_shared_fields', self::MENU_SLUG, __( 'Document lists', 'document-library-pro' ), [ $this, 'display_document_lists_description' ], $this->get_document_lists_settings() );

		// Folders
		Settings_API_Helper::add_settings_section( 'dlp_folder_fields', self::MENU_SLUG, __( 'Folders', 'document-library-pro' ), [ $this, 'display_folders_description' ], $this->get_folders_settings() );

		// Document Links
		Settings_API_Helper::add_settings_section( 'dlp_links', self::MENU_SLUG, __( 'Document links', 'document-library-pro' ), '__return_false', $this->get_document_links_settings() );

		// Document Preview
		Settings_API_Helper::add_settings_section( 'dlp_preview', self::MENU_SLUG, __( 'Document preview', 'document-library-pro' ), '__return_false', $this->get_document_preview_settings() );

		// Library Content
		Settings_API_Helper::add_settings_section( 'dlp_library_content', self::MENU_SLUG, __( 'Library content', 'document-library-pro' ), '__return_false', $this->get_library_content_settings() );

		// Library Controls
		Settings_API_Helper::add_settings_section( 'dlp_table_controls', self::MENU_SLUG, __( 'Search', 'document-library-pro' ), '__return_false', $this->get_library_controls_settings() );

		// Document Limits
		Settings_API_Helper::add_settings_section( 'dlp_document_limits', self::MENU_SLUG, __( 'Number of documents', 'document-library-pro' ), '__return_false', $this->get_document_limit_settings() );

		// Document Sorting
		Settings_API_Helper::add_settings_section( 'dlp_document_sorting', self::MENU_SLUG, __( 'Sorting', 'document-library-pro' ), '__return_false', $this->get_document_sorting_settings() );

		// Frontend Uploader
		Settings_API_Helper::add_settings_section( 'dlp_frontend_submission', self::MENU_SLUG, __( 'Front end document submission', 'document-library-pro' ), [ $this, 'display_frontend_submission_description' ], $this->get_frontend_submission_settings() );

		// Version control
		Settings_API_Helper::add_settings_section( 'dlp_version_control', self::MENU_SLUG, __( 'Version control', 'document-library-pro' ), [ $this, 'display_version_control_description' ], $this->get_version_control_settings() );
	}

	/**
	 * Output the Document Data description.
	 */
	public function display_document_data_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'Use the following options to manage the fields that are used to store information about your documents. You can add additional fields using a custom fields plugin and display them in the table layout. %1$sRead more%2$s.', 'document-library-pro' ) .
			'</p>',
			  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-settings/#document-fields' ), true ),
			'</a>'
		);
	}

	/**
	 * Output the frontend submission description.
	 */
	public function display_frontend_submission_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'Use the [dlp_submission_form] shortcode to allow people to add documents from the front end. %1$sRead more%2$s.', 'document-library-pro' ) .
			'</p>',
			  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/add-import-documents/#front-end-document-uploader' ), true ),
			'</a>'
		);
	}

	/**
	 * Output the Version Control description.
	 */
	public function display_version_control_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'The version control options allow you to decide how to keep track of the uploaded files. %1$sRead more%2$s.', 'document-library-pro' ) .
			'</p>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-version-control/' ), true ),
			'</a>'
		);
	}

	/**
	 * Output the Document Folders description.
	 */
	public function display_folders_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'The folders option allows you to nest your document libraries in a hierarchical folder tree of your document categories. %1$sRead more%2$s.', 'document-library-pro' ) .
			'</p>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-folders/' ), true ),
			'</a>'
		);
	}


	/**
	 * Output the Document Lists description.
	 */
	public function display_document_lists_description() {
		printf(
			'<p>' .
			/* translators: %1: knowledge base link start, %2: knowledge base link end */
			esc_html__( 'These options set defaults for all your document libraries and are used for the table and grid layout. You can override them in the shortcode for individual libraries. %1$sRead more%2$s.', 'document-library-pro' ) .
			'</p>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/document-library-options/' ), true ),
			'</a>'
		);
	}

	/**
	 * Output the Barn2 Support Links.
	 */
	public function support_links() {
		printf(
			'<p>%s | %s | %s</p>',
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			Lib_Util::format_link( $this->plugin->get_documentation_url(), __( 'Documentation', 'document-library-pro' ), true ),
			Lib_Util::format_link( $this->plugin->get_support_url(), __( 'Support', 'document-library-pro' ), true ),
			sprintf(
				'<a class="barn2-wiz-restart-btn" href="%s">%s</a>',
				add_query_arg( [ 'page' => $this->plugin->get_slug() . '-setup-wizard' ], admin_url( 'admin.php' ) ),
				__( 'Setup wizard', 'document-library-pro' )
			)
			// phpcs:enable
		);
	}

	/**
	 * Get the Document Data settings.
	 *
	 * @return array
	 */
	private function get_document_data_settings() {
		return [
			[
				'id'      => Options::DOCUMENT_FIELDS_OPTION_KEY,
				'title'   => __( 'Document fields', 'document-library-pro' ),
				'type'    => 'multicheckbox',
				'options' => [
					'editor'        => __( 'Content', 'document-library-pro' ),
					'excerpt'       => __( 'Excerpt', 'document-library-pro' ),
					'thumbnail'     => __( 'Featured image', 'document-library-pro' ),
					'comments'      => __( 'Comments', 'document-library-pro' ),
					'custom-fields' => __( 'Custom fields', 'document-library-pro' ),
					'author'        => __( 'Authors', 'document-library-pro' ),
				],
				'default' => [
					'editor'        => '1',
					'excerpt'       => '1',
					'thumbnail'     => '1',
					'comments'      => '0',
					'author'        => '1',
					'custom-fields' => '0',
				],
			],
			[
				'id'      => Options::DOCUMENT_SLUG_OPTION_KEY,
				'title'   => __( 'Document slug', 'document-library-pro' ),
				'type'    => 'text',
				'desc'    => __( 'Change the permalink for your documents.', 'document-library-pro' ) . $this->read_more( '/kb/document-library-settings/#document-slug' ),
				'default' => 'document',
			],
		];
	}

	/**
	 * Get the version control settings.
	 *
	 * @return array
	 */
	private function get_version_control_settings() {
		return [
			[
				'title'             => __( 'Enable', 'document-library-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::DOCUMENT_FIELDS_OPTION_KEY . '[version_control]',
				'label'             => __( 'Enable version control', 'document-library-pro' ),
				'default'           => $this->default_settings['version_control'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'version-control-mode',
				]
			],
			[
				'title'       => __( 'Replacing files', 'document-library-pro' ),
				'type'        => 'radio',
				'id'          => Options::DOCUMENT_FIELDS_OPTION_KEY . '[version_control_mode]',
				'options'     => [
					'keep'   => __( 'When replacing a file, keep the original in the Media Library', 'document-library-pro' ),
					'delete' => __( 'When replacing a file, delete the old version from the Media Library', 'document-library-pro' ),
				],
				'default'     => $this->default_settings['version_control_mode'],
				'field_class' => Options::get_version_control_mode() ? '' : 'hidden',
				'class'       => 'version-control-mode',
			],
		];
	}

	/**
	 * Get the frontend submission settings.
	 *
	 * @return array
	 */
	private function get_frontend_submission_settings() {
		return [
			[
				'title'   => __( 'Enable admin email', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::DOCUMENT_FIELDS_OPTION_KEY . '[fronted_email_admin]',
				'label'   => __( 'Email the site administrator when a new document is submitted', 'document-library-pro' ),
				'default' => false
			],
			[
				'title'   => __( 'Enable moderation', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::DOCUMENT_FIELDS_OPTION_KEY . '[fronted_moderation]',
				'label'   => __( 'Hold new documents for moderation by an administrator', 'document-library-pro' ),
				'default' => false
			],
		];
	}

	/**
	 * Get the folder settings.
	 *
	 * @return array
	 */
	private function get_folders_settings() {
		return [
			[
				'title'   => __( 'Enable', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[folders]',
				'label'   => __( 'Display the document library in folders', 'document-library-pro' ),
				'default' => $this->default_settings['folders']
			],
			[
				'title'   => __( 'Sort by', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[folders_order_by]',
				'options' => [
					'name'       => __( 'Name', 'document-library-pro' ),
					'term_id'    => __( 'Category ID', 'document-library-pro' ),
					'slug'       => __( 'Category slug', 'document-library-pro' ),
					'term_order' => __( 'Category order (menu order)', 'document-library-pro' ),
					'count'      => __( 'Number of terms', 'document-library-pro' ),
				],
				'default' => $this->default_settings['folders_order_by']
			],
			[
				'title'   => __( 'Sort direction', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[folders_order]',
				'options' => [
					'asc'  => __( 'Ascending (A to Z, low to high)', 'document-library-pro' ),
					'desc' => __( 'Descending (Z to A, high to low)', 'document-library-pro' )
				],
				'default' => $this->default_settings['folders_order']
			],
			[
				'title'             => __( 'Default status', 'document-library-pro' ),
				'type'              => 'select',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[folder_status]',
				'options'           => [
					'open'   => __( 'Open', 'document-library-pro' ),
					'closed' => __( 'Closed', 'document-library-pro' ),
					'custom' => __( 'Custom', 'document-library-pro' ),
				],
				'default'           => $this->default_settings['folder_status'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'folder-status',
					'data-toggle-val'  => 'custom',
				]
			],
			[
				'title'   => __( 'Open folders', 'document-library-pro' ),
				'type'    => 'text',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[folder_status_custom]',
				'default' => $this->default_settings['folder_status_custom'],
				'desc'    => __( 'Enter ‘all’ to display all folders as open by default, or list specific folders.', 'document-library-pro' ) . $this->read_more( '/kb/document-library-settings/#open-folders' ),
				'class'   => 'regular-text folder-status'
			],
			[
				'title'             => __( 'Customize folder icon', 'document-library-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[folder_icon_custom]',
				'default'           => $this->default_settings['folder_icon_custom'],
				'custom_attributes' => [
					'data-child-class' => 'folder-icon-customization',
				],
				'class'             => 'dlp-toggle-parent',
			],
			[
				'title'       => __( 'Top-level folder color', 'document-library-pro' ),
				'type'        => 'color',
				'id'          => Options::SHORTCODE_OPTION_KEY . '[folder_icon_color]',
				'default'     => $this->default_settings['folder_icon_color'],
				'field_class' => 'folder-icon-customization',
			],
			[
				'title'       => __( 'Sub-folder color', 'document-library-pro' ),
				'type'        => 'color',
				'id'          => Options::SHORTCODE_OPTION_KEY . '[folder_icon_subcolor]',
				'default'     => $this->default_settings['folder_icon_subcolor'],
				'field_class' => 'folder-icon-customization',
			],
			[
				'title'   => __( 'Closed folder icon', 'document-library-pro' ),
				'type'    => 'textarea',
				'desc'    => __( 'Input the SVG code of the icon you want to use for closed folders.', 'document-library-pro' ) . $this->read_more( 'kb/document-folders/#adding-your-own-folder-icon' ),
				'id'      => Options::FOLDER_CLOSE_SVG_OPTION_KEY,
				'default' => $this->default_settings['folder_icon_svg_closed'],
				'class'   => 'regular-text folder-icon-customization',
			],
			[
				'title'   => __( 'Open folder icon', 'document-library-pro' ),
				'type'    => 'textarea',
				'desc'    => __( 'Input the SVG code of the icon you want to use for open folders.', 'document-library-pro' ) . $this->read_more( 'kb/document-folders/#adding-your-own-folder-icon' ),
				'id'      => Options::FOLDER_OPEN_SVG_OPTION_KEY,
				'default' => $this->default_settings['folder_icon_svg_open'],
				'class'   => 'regular-text folder-icon-customization',
			],
		];
	}

	/**
	 * Get the Document Lists settings.
	 *
	 * @return array
	 */
	private function get_document_lists_settings() {
		return [
			[
				'id'       => Options::DOCUMENT_PAGE_OPTION_KEY,
				'title'    => __( 'Document library page', 'document-library-pro' ),
				'type'     => 'select',
				'desc'     => __( 'The page to display your documents.', 'document-library-pro' ),
				'desc_tip' => __( 'You can also use the [doc_library] shortcode to list documents on other pages.', 'document-library-pro' ),
				'options'  => $this->get_pages(),
				'default'  => '',
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[layout]',
				'title'   => __( 'Default layout', 'document-library-pro' ),
				'type'    => 'radio',
				'options' => [
					'table' => __( 'Table', 'document-library-pro' ),
					'grid'  => __( 'Grid', 'document-library-pro' ),
				],
				'default' => $this->default_settings['layout'],
			],

		];
	}

	/**
	 * Get the Document Links settings.
	 *
	 * @return array
	 */
	private function get_document_links_settings() {
		return [
			[
				'title'   => __( 'Link to document', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[document_link]',
				'label'   => __( 'Include a link to the document.', 'document-library-pro' ),
				'desc'    => __( 'Use the \'Link destination\' option below to control the link behavior.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-settings/#link-to-document' ),
				'default' => $this->default_settings['document_link']
			],
			[
				'title'   => __( 'Link style', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[link_style]',
				'desc'    => __( 'Control the appearance of the link to the document.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-settings#link-style' ),
				'options' => [
					'button'           => __( 'Button with text', 'document-library-pro' ),
					'button_icon_text' => __( 'Button with icon and text', 'document-library-pro' ),
					'button_icon'      => __( 'Button with icon', 'document-library-pro' ),
					'icon_only'        => __( 'Download icon only', 'document-library-pro' ),
					'icon'             => __( 'File type icon', 'document-library-pro' ),
					'text'             => __( 'Text link', 'document-library-pro' ),
				],
				'default' => $this->default_settings['link_style']
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[link_text]',
				'title'   => __( 'Link text', 'document-library-pro' ),
				'type'    => 'text',
				'desc'    => __( 'The text displayed on the button or link.', 'document-library-pro' ),
				'default' => $this->default_settings['link_text'],
			],
			[
				'title'   => __( 'Link destination', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[link_destination]',
				'desc'    => __( 'What happens when someone clicks on a link to a document.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-settings#link-destination' ),
				'options' => [
					'direct' => __( 'Direct access', 'document-library-pro' ),
					'single' => __( 'Open single document page', 'document-library-pro' ),
				],
				'default' => $this->default_settings['link_destination']
			],
			[
				'title'   => __( 'Link target', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[link_target]',
				'label'   => __( 'Open links in a new tab', 'document-library-pro' ),
				'default' => $this->default_settings['link_target']
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[links]',
				'title'   => __( 'Clickable fields', 'document-library-pro' ), // note this in 'links' in PTP
				'type'    => 'text',
				'desc'    => __( 'Control which fields are clickable, in addition to the \'link\' field.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-settings#clickable-fields' ),
				'default' => $this->default_settings['links'],
			],
		];
	}

	/**
	 * Get the Document Preview settings.
	 *
	 * @return array
	 */
	public function get_document_preview_settings() {
		return [
			[
				'title'   => __( 'Document preview', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[preview]',
				'label'   => __( 'Allow users to preview documents in a lightbox', 'document-library-pro' ),
				'desc'    => __( 'The preview option will appear for supported file types only.', 'document-library-pro' ) . $this->read_more( 'kb/document-preview/' ),
				'default' => $this->default_settings['preview']
			],
			[
				'title'   => __( 'Preview style', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[preview_style]',
				'desc'    => __( 'Control the appearance of the preview option.', 'document-library-pro' ) . $this->read_more( 'kb/document-preview/#preview-style' ),
				'options' => [
					'button'           => __( 'Button with text', 'document-library-pro' ),
					'button_icon_text' => __( 'Button with icon and text', 'document-library-pro' ),
					'button_icon'      => __( 'Button with icon', 'document-library-pro' ),
					'icon_only'        => __( 'Icon only', 'document-library-pro' ),
					'link'             => __( 'Text link', 'document-library-pro' ),
				],
				'default' => $this->default_settings['preview_style']
			],
			[
				'title'   => __( 'Preview text', 'document-library-pro' ),
				'type'    => 'text',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[preview_text]',
				'desc'    => __( 'The text displayed on the preview button or link.', 'document-library-pro' ),
				'default' => $this->default_settings['preview_text']
			],
		];
	}

	/**
	 * Get the Library Content settings.
	 *
	 * @return array
	 */
	private function get_library_content_settings() {
		return [
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[lightbox]',
				'title'   => __( 'Image lightbox', 'document-library-pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Display images in a lightbox when opened', 'document-library-pro' ),
				'default' => $this->default_settings['lightbox'],
			],
			[
				'title'   => __( 'Shortcodes', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[shortcodes]',
				'label'   => __( 'Display shortcodes, HTML and other formatting in the document content, excerpt and custom field columns', 'document-library-pro' ),
				'default' => $this->default_settings['shortcodes']
			],
			[
				'id'                => Options::SHORTCODE_OPTION_KEY . '[excerpt_length]',
				'title'             => __( 'Excerpt length', 'document-library-pro' ),
				'type'              => 'number',
				'class'             => 'small-text',
				'suffix'            => __( 'words', 'document-library-pro' ),
				'desc'              => __( 'Enter -1 to show the full excerpt.', 'document-library-pro' ),
				'default'           => $this->default_settings['excerpt_length'],
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'id'                => Options::SHORTCODE_OPTION_KEY . '[content_length]',
				'title'             => __( 'Content length', 'document-library-pro' ),
				'type'              => 'number',
				'class'             => 'small-text',
				'suffix'            => __( 'words', 'document-library-pro' ),
				'desc'              => __( 'Enter -1 to show the full content.', 'document-library-pro' ),
				'default'           => $this->default_settings['content_length'],
				'custom_attributes' => [
					'min' => -1
				]
			],
		];
	}

	/**
	 * Get the Library Control settings.
	 *
	 * @return array
	 */
	private function get_library_controls_settings() {
		return [
			[
				'title'   => __( 'Search box', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[search_box]',
				'desc'    => __( 'The position of the search box above the list of documents. You can also add a search box using a shortcode or widget.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-search/#standalone-search-box' ),
				'options' => [
					'top'    => __( 'Above library', 'document-library-pro' ),
					'bottom' => __( 'Below library', 'document-library-pro' ),
					'both'   => __( 'Above and below library', 'document-library-pro' ),
					'false'  => __( 'Hidden', 'document-library-pro' )
				],
				'default' => $this->default_settings['search_box']
			],
			[
				'title'   => __( 'Reset button', 'document-library-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[reset_button]',
				'label'   => __( 'Show the reset button above the library', 'document-library-pro' ),
				'default' => $this->default_settings['reset_button']
			],
			[
				'id'       => Options::SEARCH_PAGE_OPTION_KEY,
				'title'    => __( 'Search page', 'document-library-pro' ),
				'type'     => 'select',
				'desc'     => sprintf(
				/* translators: 1: link to search article, 2: end of link */
					__( 'When using the %1$sglobal search%2$s, this page will display your search results.', 'document-library-pro' ),
					Lib_Util::format_barn2_link_open( 'kb/document-library-search/#standalone-search-box', true ),
					'</a>'
				),
				'desc_tip' => __( 'Use the widget or shortcode to perform a search from anywhere on your site.', 'document-library-pro' ),
				'options'  => $this->get_pages(),
				'default'  => Options::get_search_page_option(),
			],
		];
	}

	/**
	 * Get the Document Limit settings.
	 *
	 * @return array
	 */
	private function get_document_limit_settings() {
		return [
			[
				'title'             => __( 'Documents per page', 'document-library-pro' ),
				'type'              => 'number',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[rows_per_page]',
				'desc'              => __( 'The number of documents per page of the document library. Enter -1 to display all documents on one page.', 'document-library-pro' ),
				'default'           => $this->default_settings['rows_per_page'],
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'title'   => __( 'Pagination type', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[paging_type]',
				'options' => [
					'numbers'        => __( 'Numbers only', 'document-library-pro' ),
					'simple'         => __( 'Prev|Next', 'document-library-pro' ),
					'simple_numbers' => __( 'Prev|Next + Numbers', 'document-library-pro' ),
					'full'           => __( 'Prev|Next|First|Last', 'document-library-pro' ),
					'full_numbers'   => __( 'Prev|Next|First|Last + Numbers', 'document-library-pro' )
				],
				'default' => $this->default_settings['paging_type']
			],
			[
				'title'   => __( 'Pagination position', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[pagination]',
				'options' => [
					'top'    => __( 'Above library', 'document-library-pro' ),
					'bottom' => __( 'Below library', 'document-library-pro' ),
					'both'   => __( 'Above and below library', 'document-library-pro' ),
					'false'  => __( 'Hidden', 'document-library-pro' )
				],
				'desc'    => __( 'The position of the paging buttons which scroll between results.', 'document-library-pro' ),
				'default' => $this->default_settings['pagination']
			],
			[
				'title'   => __( 'Totals', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[totals]',
				'options' => [
					'top'    => __( 'Above library', 'document-library-pro' ),
					'bottom' => __( 'Below library', 'document-library-pro' ),
					'both'   => __( 'Above and below library', 'document-library-pro' ),
					'false'  => __( 'Hidden', 'document-library-pro' )
				],
				'desc'    => __( "The position of the document total, e.g. '25 documents'.", 'document-library-pro' ),
				'default' => $this->default_settings['totals']
			],
		];
	}

	/**
	 * Get the Document Sorting settings.
	 *
	 * @return array
	 */
	private function get_document_sorting_settings() {
		return [
			[
				'title'             => __( 'Sort by', 'document-library-pro' ),
				'type'              => 'select',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[sort_by]',
				'options'           => [
					'title'         => __( 'Title', 'document-library-pro' ),
					'id'            => __( 'ID', 'document-library-pro' ),
					'date'          => __( 'Date published', 'document-library-pro' ),
					'modified'      => __( 'Date modified', 'document-library-pro' ),
					'menu_order'    => __( 'Page order (menu order)', 'document-library-pro' ),
					'name'          => __( 'Post slug', 'document-library-pro' ),
					'author'        => __( 'Author', 'document-library-pro' ),
					'comment_count' => __( 'Number of comments', 'document-library-pro' ),
					'rand'          => __( 'Random', 'document-library-pro' ),
					'custom'        => __( 'Other', 'document-library-pro' )
				],
				'desc'              => __( 'The initial sort order of the document library.', 'document-library-pro' ) . $this->read_more( 'kb/document-library-pro-sort-options/#sort-by' ),
				'default'           => $this->default_settings['sort_by'],
				'class'             => 'dlp-toggle-parent',
				'custom_attributes' => [
					'data-child-class' => 'custom-sort',
					'data-toggle-val'  => 'custom'
				]
			],
			[
				'title' => __( 'Sort column', 'document-library-pro' ),
				'type'  => 'text',
				'id'    => Options::SHORTCODE_OPTION_KEY . '[sort_by_custom]',
				'class' => 'regular-text custom-sort',
				'desc'  => __( 'Enter any column in your table. Note: only available for the table layout and when lazy load is disabled. Not used for the grid layout.', 'document-library-pro' )
			],
			[
				'title'   => __( 'Sort direction', 'document-library-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[sort_order]',
				'options' => [
					''     => __( 'Automatic', 'document-library-pro' ),
					'asc'  => __( 'Ascending (A to Z, oldest to newest)', 'document-library-pro' ),
					'desc' => __( 'Descending (Z to A, newest to oldest)', 'document-library-pro' )
				],
				'default' => $this->default_settings['sort_order']
			],
		];
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
	 * Get a list of WP Pages for the settings select.
	 *
	 * @return array
	 */
	private function get_pages() {
		$pages = get_pages(
			[
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'hierarchical' => 0,
			]
		);

		$options = [];
		foreach ( $pages as $page ) {
			$options[ $page->ID ] = ! empty( $page->post_title ) ? $page->post_title : '#' . $page->ID;
		}

		return $options;
	}
}
