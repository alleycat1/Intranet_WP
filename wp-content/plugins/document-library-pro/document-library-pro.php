<?php
/**
 * The main plugin file for Document Library Pro.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Media <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     Document Library Pro
 * Plugin URI:      https://barn2.com/wordpress-plugins/document-library-pro/
 * Description:     Add documents and display them in a searchable document library with filters.
 * Version:         1.13.3
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     document-library-pro
 * Domain Path:     /languages
 *
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Barn2\Plugin\Document_Library_Pro;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '1.13.3';
const PLUGIN_FILE    = __FILE__;

// Include autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Helper function to access the shared plugin instance.
function document_library_pro() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

// Load the plugin.
document_library_pro()->register();
