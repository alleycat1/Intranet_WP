<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Starter,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin_Activation_Listener,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Starter as Setup_WizardStarter;

/**
 * Plugin Setup
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Setup implements Plugin_Activation_Listener, Registerable {
	/**
	 * Plugin's entry file
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Wizard starter.
	 *
	 * @var Starter
	 */
	private $starter;

	/**
	 * Plugin instance
	 *
	 * @var Licensed_Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param mixed $file
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( $file, Licensed_Plugin $plugin ) {
		$this->file    = $file;
		$this->plugin  = $plugin;
		$this->starter = new Setup_WizardStarter( $this->plugin );
	}

	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register() {
		register_activation_hook( $this->file, [ $this, 'on_activate' ] );
		add_action( 'admin_init', [ $this, 'after_plugin_activation' ] );
	}

	/**
	 * On plugin activation
	 *
	 * @return void
	 */
	public function on_activate() {
		/**
		 * Determine if setup wizard should run.
		 */
		if ( $this->starter->should_start() ) {

			$this->starter->create_transient();
		}
	}

	/**
	 * Do nothing.
	 *
	 * @return void
	 */
	public function on_deactivate() {}

	/**
	 * Detect the transient and redirect to wizard.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {

		if ( ! $this->starter->detected() ) {
			return;
		}

		$this->starter->delete_transient();
		$this->starter->redirect();
	}
}
