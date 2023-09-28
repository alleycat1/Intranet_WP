<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Page;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util,
	Barn2\Plugin\Document_Library_Pro\Admin\Settings_Tab;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles our plugin settings page in the admin.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings implements Service, Registerable, Conditional {
	const MENU_SLUG = 'document_library_pro';

	private $plugin;
	private $license;
	private $registered_settings = [];

	/**
	 * Constructor.
	 *
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin              = $plugin;
		$this->license             = $plugin->get_license();
		$this->registered_settings = $this->get_settings_tabs();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->register_settings_tabs();

		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
	}

	/**
	 * Retrieves the settings tab classes.
	 *
	 * @return array
	 */
	private function get_settings_tabs() {
		$settings_tabs = [
			Settings_Tab\General::TAB_ID => new Settings_Tab\General( $this->plugin ),
		];

		if ( $this->license->is_valid() ) {
			$settings_tabs = array_merge(
				$settings_tabs,
				[
					Settings_Tab\Document_Table::TAB_ID  => new Settings_Tab\Document_Table( $this->plugin ),
					Settings_Tab\Document_Grid::TAB_ID   => new Settings_Tab\Document_Grid( $this->plugin ),
					Settings_Tab\Single_Document::TAB_ID => new Settings_Tab\Single_Document( $this->plugin ),
				]
			);
		}

		return $settings_tabs;
	}

	/**
	 * Register the settings tab classes.
	 */
	private function register_settings_tabs() {
		array_map(
			function( $setting_tab ) {
				if ( $setting_tab instanceof Registerable ) {
					$setting_tab->register();
				}
			},
			$this->registered_settings
		);
	}

	/**
	 * Register the Settings submenu page.
	 */
	public function add_settings_page() {
		add_submenu_page(
			'document_library_pro',
			__( 'Document Library Settings', 'document-library-pro' ),
			__( 'Settings', 'document-library-pro' ),
			'manage_options',
			'document_library_pro',
			[ $this, 'render_settings_page' ],
			10
		);
	}

	/**
	 * Render the Settings page.
	 */
	public function render_settings_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		?>

		<div class="wrap">

			<?php do_action( 'barn2_before_plugin_settings', $this->plugin->get_id() ); ?>

			<h1><?php esc_html_e( 'Document Library Pro Settings', 'document-library-pro' ); ?></h1>

			<?php settings_errors( 'general' ); ?>

			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->registered_settings as $setting_tab ) {
					$active_class = $active_tab === $setting_tab->get_id() ? ' nav-tab-active' : '';
					?>
					<a href="<?php echo esc_url( add_query_arg( 'tab', $setting_tab->get_id(), $this->plugin->get_settings_page_url() ) ); ?>" class="<?php echo esc_attr( sprintf( 'nav-tab%s', $active_class ) ); ?>">
						<?php echo esc_html( $setting_tab->get_title() ); ?>
					</a>
					<?php
				}
				?>
			</h2>

			<form action="options.php" method="post">
				<?php
				settings_errors( 'document-library-pro' );
				settings_fields( $this->registered_settings[ $active_tab ]::OPTION_GROUP );
				do_settings_sections( $this->registered_settings[ $active_tab ]::MENU_SLUG );
				?>

				<p class="submit">
					<input name="Submit" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'document-library-pro' ); ?>" />
				</p>
			</form>

			<?php do_action( 'barn2_after_plugin_settings', $this->plugin->get_id() ); ?>

		</div>
		<?php

	}

}
