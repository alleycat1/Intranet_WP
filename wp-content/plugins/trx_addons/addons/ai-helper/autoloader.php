<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ThemeREX Addons autoloader handler class is responsible for loading the different
 * classes needed to run the plugin
 */
class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps plugin classes to file names.
	 *
	 * @access private
	 * @static
	 *
	 * @var array Classes used by plugin.
	 */
	private static $classes_map;

	/**
	 * Folders map.
	 *
	 * Base folders for plugin classes.
	 *
	 * @access private
	 * @static
	 *
	 * @var array Folders with classes used by plugin.
	 */
	private static $folders_map;

	/**
	 * Default path with classes folders and files.
	 *
	 * @access private
	 * @static
	 *
	 * @var string  Default path for classes.
	 */
	private static $default_path;

	/**
	 * Default namespace for classes.
	 *
	 * @access private
	 * @static
	 *
	 * @var string  Default namespace for classes.
	 */
	private static $default_namespace;

	/**
	 * Run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @access public
	 * @static
	 */
	public static function run( $default_path = '', $default_namespace = '' ) {
		if ( '' === $default_path ) {
			$default_path = dirname( __FILE__ );
		}

		if ( '' === $default_namespace ) {
			$default_namespace = __NAMESPACE__;
		}

		self::$default_path = $default_path . ( ! in_array( substr( $default_path, -1 ), array( '/', '\\' ) ) ? DIRECTORY_SEPARATOR : '' );
		self::$default_namespace = $default_namespace;

		spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}

	/**
	 * Get classes map.
	 *
	 * Retrieve the classes map.
	 *
	 * @access public
	 * @static
	 *
	 * @return array Classes map.
	 */
	public static function get_classes_map() {
		if ( ! self::$classes_map ) {
			self::init_classes_map();
		}
		return self::$classes_map;
	}

	private static function init_classes_map() {
		self::$classes_map = array(
			// For example:
			// 'UpdateManager' => 'vendors/update/manager.php',
		);
	}

	/**
	 * Get folders map.
	 *
	 * Retrieve the classes folders map: 'namespace' => 'base-folder'
	 *
	 * @access public
	 * @static
	 *
	 * @return array Folders map.
	 */
	public static function get_folders_map() {
		if ( ! self::$folders_map ) {
			self::init_folders_map();
		}
		return self::$folders_map;
	}

	private static function init_folders_map() {
		self::$folders_map = array(
			'MediaLibrary' => 'support',
			'Gutenberg' => 'support',
			'Elementor' => 'support',
			'Orhanerday\\OpenAi' => 'vendors',
			'Rahul900day\\Gpt3Encoder' => 'vendors',
			'StableDiffusion\\Api' => 'vendors',
			'StabilityAi\\Api' => 'vendors',
		);
	}

	/**
	 * Load class.
	 *
	 * For a given class name, require the class file.
	 *
	 * @access private
	 * @static
	 *
	 * @param string $relative_class_name Class name.
	 */
	private static function load_class( $relative_class_name ) {
		$classes_map = self::get_classes_map();
		$folders_map = self::get_folders_map();

		if ( isset( $classes_map[ $relative_class_name ] ) ) {			// Class name is alias: 'UpdateManager' -> 'core/update/manager'
			$filename = self::$default_path . $classes_map[ $relative_class_name ];
		} else {														// Class name contain relative path
			$base_folder = '';
			foreach ( $folders_map as $namespace => $folder ) {
				if ( strpos( $relative_class_name, $namespace . '\\' ) === 0 ) {
					$base_folder = $folder;
					break;
				}
			}
			$filename = preg_replace(
				[ '/_/', '/\\\/' ], 				// '/([a-z])([A-Z])/',
				[ '-', DIRECTORY_SEPARATOR ],		// '$1-$2',
				$relative_class_name
			);
			$filename = self::$default_path . ( ! empty( $base_folder ) ? $base_folder : 'classes' ) . DIRECTORY_SEPARATOR . $filename . '.php';
		}
		if ( is_readable( $filename ) ) {
			require_once $filename;
		}
	}

	/**
	 * Autoload.
	 *
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $class Class name.
	 */
	private static function autoload( $class ) {
		$allowed_namespaces = array(
			self::$default_namespace,
			'Orhanerday\\OpenAi',
			'Rahul900day\\Gpt3Encoder',
			'StableDiffusion\\Api',
			'StabilityAi\\Api'
		);
		$found = false;
		foreach ( $allowed_namespaces as $namespace ) {
			if ( strpos( $class, $namespace . '\\' ) === 0 ) {
				$found = true;
				break;
			}
		}
		if ( ! $found ) {
			return;
		}
		if ( strpos( $class, self::$default_namespace . '\\' ) === 0 ) {
			$relative_class_name = substr( $class, strlen( self::$default_namespace . '\\' ) );
			$final_class_name = self::$default_namespace . '\\' . $relative_class_name;
		} else {
			$relative_class_name = $class;
			$final_class_name = $class;
		}
		if ( ! class_exists( $final_class_name ) ) {
			self::load_class( $relative_class_name );
		}
	}
}
