<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;

/**
 * This class handles our posts table shortcode.
 *
 * Example usage:
 *   [posts_table
 *       post_type="band"
 *       columns="title,content,tax:country,tax:genre,cf:_price,cf:stock"
 *       tag="cool",
 *       term="country:uk,artist:beatles"]
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Shortcode implements Service, Registerable, Conditional {

	const SHORTCODE = 'posts_table';

	public function is_required() {
		return Util::is_front_end();
	}

	public function register() {
		// Register posts table shortcode
		add_shortcode( self::SHORTCODE, [ self::class, 'do_shortcode' ] );

		// Back-compat with free version of plugin
		add_shortcode( 'posts_data_table', [ self::class, 'do_shortcode' ] );
	}

	/**
	 * Handles our posts data table shortcode.
	 *
	 * @param array  $atts    The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		if ( ! self::can_do_shortocde() ) {
			return '';
		}

		// Fill-in missing attributes, and ensure back compat for old attribute names.
		$r = shortcode_atts( Table_Args::get_site_defaults(), self::back_compat_args( (array) $atts ), self::SHORTCODE );

		// Return the table as HTML
		return apply_filters( 'document_library_pro_shortcode_output', dlp_get_posts_table( $r ) );
	}

	private static function can_do_shortocde() {
		// Don't run in the search results page.
		if ( is_search() && in_the_loop() && ! apply_filters( 'document_library_pro_run_in_search', false ) ) {
			return false;
		}

		return true;
	}

	private static function back_compat_args( array $args ) {
		$compat = [
			'post_status' => 'status',
		];

		foreach ( $compat as $old => $new ) {
			if ( isset( $args[ $old ] ) ) {
				$args[ $new ] = $args[ $old ];
				unset( $args[ $old ] );
			}
		}

		return $args;
	}

}
