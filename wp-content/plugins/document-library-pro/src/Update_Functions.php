<?php
namespace Barn2\Plugin\Document_Library_Pro;

defined( 'ABSPATH' ) || exit;

/**
 * Update functions to be used on plugin updates.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Update_Functions {

	public static $updates = [
		'1.11.0' => [
			'update_1_11_0_delete_all_dlp_transients',
		],
	];

	/**
	 * Delete all Document Library Pro transients.
	 */
	public static function update_1_11_0_delete_all_dlp_transients() {
		global $wpdb;
		$wpdb->query( "DELETE FROM `" . $wpdb->options ."` WHERE `option_name` LIKE '\_transient\_dlp\_%'" );
		$wpdb->query( "DELETE FROM `" . $wpdb->options ."` WHERE `option_name` LIKE '\_transient\_timeout\_dlp\_%'" );
	}

}
