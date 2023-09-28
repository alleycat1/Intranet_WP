<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WP_Scoped_Hooks;

/**
 * Responsible for managing the actions and filter hooks for an individual posts table.
 *
 * Hooks are registered in a temporary hook environment (@see class WP_Scoped_Hooks), and
 * only apply while the data is loaded into the table.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Hooks extends WP_Scoped_Hooks implements Registerable {

	public $args;

	public function __construct( Table_Args $args ) {
		parent::__construct();
		$this->args = $args;
	}

	public function register() {
		// Excerpt column
		$this->add_filter( 'excerpt_length', [ $this, 'set_excerpt_length' ], 99 ); /* this only applies to excerpts auto-generated from content */
		$this->add_filter( 'excerpt_more', [ self::class, 'more_content_text' ] ); /* as above */
		$this->add_filter( 'the_excerpt', [ $this, 'maybe_strip_shortcodes' ], 5 ); /* before do_shortcode (11) */
		$this->add_filter( 'the_excerpt', 'do_shortcode', 11 ); /* after wpautop (10) */
		$this->add_filter( 'the_excerpt', [ $this, 'maybe_trim_excerpt' ], 15 ); /* after wpautop (10) do_shortcode (11) */

		// Content column
		$this->add_filter( 'the_content', [ $this, 'maybe_strip_shortcodes' ], 5 ); /* before do_shortcode (11) */
		$this->add_filter( 'the_content', [ $this, 'maybe_trim_content' ], 15 ); /* after wpautop (10) do_shortcode (11) */

		// Date column
		$this->add_filter( 'document_library_pro_data_date', [ Util::class, 'empty_if_false' ] );

		// Image column
		$this->add_filter( 'wp_get_attachment_image_attributes', [ $this, 'set_featured_image_class' ], 99 );

		// Custom field column
		$this->add_filter( 'document_library_pro_data_custom_field', [ $this, 'maybe_strip_shortcodes' ], 5 );
		$this->add_filter( 'document_library_pro_data_custom_field', 'do_shortcode', 6 );

		// Prevent Hero Knowledge Base content filter from running in table as it breaks everything.
		if ( class_exists( '\HT_Knowledge_Base' ) ) {
			$this->add_filter( 'stop_ht_knowledge_base_custom_content', '__return_true' );
		}

		// Replace WP audio/video/playlist classes with custom versions, to prevent wpmediaelement & wpplaylist
		// scripts running on first page load. We control the loading of the media elements ourselves in the onDraw event.
		$this->add_filter( 'document_library_pro_data_excerpt', [ $this, 'set_custom_video_playlist_class' ] );
		$this->add_filter( 'document_library_pro_data_content', [ $this, 'set_custom_video_playlist_class' ] );
		$this->add_filter( 'document_library_pro_data_custom_field', [ $this, 'set_custom_video_playlist_class' ] );
		$this->add_filter( 'wp_video_shortcode_class', [ $this, 'set_custom_video_shortcode_class' ] );
		$this->add_filter( 'wp_audio_shortcode_class', [ $this, 'set_custom_audio_shortcode_class' ] );

		do_action( 'document_library_pro_hooks_before_register', $this );

		parent::register();

		do_action( 'document_library_pro_hooks_after_register', $this );
	}

	public static function more_content_text() {
		return apply_filters( 'document_library_pro_more_content_text', ' &hellip;' );
	}

	public static function maybe_trim_text( $content, $length ) {
		if ( $length > 0 ) {
			$content = wp_trim_words( $content, $length, self::more_content_text() ); // wp_trim_words will also strip tags
		}
		return $content;
	}

	public function maybe_trim_excerpt( $excerpt ) {
		return self::maybe_trim_text( $excerpt, $this->args->excerpt_length );
	}

	public function maybe_trim_content( $content ) {
		return self::maybe_trim_text( $content, $this->args->content_length );
	}

	public function maybe_strip_shortcodes( $content ) {
		// Always strip [posts_table] shortcodes from content - processing a table shortcode within a shortcode could cause an infinite loop.
		$shortcode = apply_filters( 'document_library_pro_maybe_strip_shortcodes_tag', Table_Shortcode::SHORTCODE );

		$content = preg_replace( sprintf( '#\[(?:%s|%s).*?\]#', $shortcode, 'product_table' ), '', $content );

		if ( ! $this->args->shortcodes && ! apply_filters( 'document_library_pro_process_shortcodes', false ) ) {
			$content = strip_shortcodes( $content );
		}
		return $content;
	}

	public function set_excerpt_length( $excerpt_length ) {
		if ( is_int( $this->args->excerpt_length ) ) {
			$excerpt_length = $this->args->excerpt_length;
		}
		return $excerpt_length;
	}

	/**
	 * Remove wp-post-image class from featured images shown in table.
	 * Prevents CSS conflicts with other plugins & themes.
	 *
	 * @param array $attr The image attributes
	 * @return array The updated attributes
	 */
	public function set_featured_image_class( $attr ) {
		if ( ! empty( $attr['class'] ) ) {
			$attr['class'] = trim( str_replace( 'wp-post-image', '', $attr['class'] ) );
		}
		return $attr;
	}

	public function set_custom_video_playlist_class( $data ) {
		if ( false !== strpos( $data, 'wp-playlist ' ) ) {
			$data = str_replace( 'wp-playlist ', 'ptp-playlist ', $data );
		}

		return $data;
	}

	public function set_custom_video_shortcode_class( $class ) {
		return str_replace( 'wp-video-shortcode', 'ptp-video-shortcode', $class );
	}

	public function set_custom_audio_shortcode_class( $class ) {
		return str_replace( 'wp-audio-shortcode', 'ptp-audio-shortcode', $class );
	}

}
