<?php
namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Util\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles install routines
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Install {
	/**
	 * Activation hook
	 *
	 * @param mixed $network_wide
	 */
	public static function install( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide && is_super_admin() ) {
			$sites = get_sites();

			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );

				add_option( 'dlp_should_flush_rewrite_rules', true );
				self::create_pages();

				restore_current_blog();
			}
		} else {
			add_option( 'dlp_should_flush_rewrite_rules', true );
			self::create_pages();
		}
	}

	/**
	 * Create pages that the plugin relies on, storing page IDs in variables.
	 */
	public static function create_pages() {
		$pages = [
			'document_page' => [
				'name'    => _x( 'document-library', 'Page slug', 'document-library-pro' ),
				'title'   => _x( 'Document Library', 'Page title', 'document-library-pro' ),
				'content' => '<!-- wp:shortcode -->[doc_library]<!-- /wp:shortcode -->',
			],
			'search_page'   => [
				'name'    => _x( 'document-search', 'Page slug', 'document-library-pro' ),
				'title'   => _x( 'Document Search', 'Page title', 'document-library-pro' ),
				'content' => '',
			],
		];

		foreach ( $pages as $key => $page ) {
			Util::create_page(
				esc_sql( $page['name'] ),
				'dlp_' . $key,
				$page['title'],
				$page['content'],
				''
			);
		}
	}
}
