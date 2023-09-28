<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for the singleton pattern
 * 
 * @abstract
 */
 class Singleton {

	/**
	 * Holds instances of all classes, based on Singleton pattern
	 *
	 * @access private
	 * @static
	 *
	 * @var instances
	 */
	private static $instances = [];

	/**
	 * Plugin constructor.
	 * 
	 * It is protected to prevent creating a new instance of the class via the `new` operator from outside of this class.
	 * But it is not private to allow extending the class.
	 *
	 * @access protected
	 */
	protected function __construct() {
	}

	/**
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return object  An instance of the class.
	 */
	public static function instance() {
		$subclass = static::class;
		if ( ! isset( self::$instances[ $subclass ] ) ) {
			// A "static" keyword in PHP is used instead of the class name.
			// When the method is called in a subclass, we want to create an
			// instance of the subclass here.
			self::$instances[ $subclass ] = new static();
		}
		return self::$instances[ $subclass ];
	}

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Clone a singleton is prohibited.', 'trx_addons' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @access public
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Serialize a singleton is prohibited.', 'trx_addons' ), '1.0.0' );
	}
}
