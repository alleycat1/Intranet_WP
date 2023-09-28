<?php
/**
 * Elementor extension: Add button "Edit layout in the new tab" to the preview area
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_elm_add_layout_editor_link' ) ) {
	add_filter( 'trx_addons_filter_sc_layout_content_from_builder', 'trx_addons_elm_add_layout_editor_link', 10, 3 );
	/**
	 * Add button "Edit layout in the new tab" to the preview area
	 *
	 * @hooked trx_addons_filter_sc_layout_content_from_builder
	 * 
	 * @trigger trx_addons_filter_layout_editor_selector_supported
	 *
	 * @param string $post_content  Content of the post
	 * @param int $post_id	Post ID
	 * @param string $builder Builder name
	 * 
	 * @return string  Content of the post
	 */
	function trx_addons_elm_add_layout_editor_link($post_content, $post_id, $builder) {
		$output = '';
		if ( $builder == 'elementor' && trx_addons_elm_is_preview() && strpos( $post_content, 'trx_addons_layout_editor_mask' ) === false ) {
			$meta = (array)get_post_meta( $post_id, 'trx_addons_options', true );
			if ( ! empty( $meta['layout_type'] ) && in_array( $meta['layout_type'], array( 'header', 'footer', 'sidebar' ) ) ) {
				if ( trx_addons_get_value_gp( 'elementor-preview' ) != $post_id ) {
					$output = '<div class="trx_addons_layout_editor_mask">'
								. '<div class="trx_addons_layout_editor_selector">'
									. '<a href="' . esc_url( admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $post_id ) ) ) . '"'
										. ' target="_blank"'
										. ' class="trx_addons_layout_editor_link"'
										. ' data-layout-type="' . esc_attr( $meta['layout_type'] ) . '"'
									. '>'
										. sprintf( esc_html__('Edit "%s" in a new tab', 'trx_addons'),
													//$meta['layout_type'] == 'header' ? esc_html__( 'Header', 'trx_addons' ) : ( $meta['layout_type'] == 'footer' ? esc_html__( 'Footer', 'trx_addons' ) : esc_html__( 'Sidebar', 'trx_addons' ) )
													trx_addons_strshort( get_the_title($post_id), 30 )
												)
									. '</a>';
					// Add layouts list (if change layouts is supported)
					if ( apply_filters( 'trx_addons_filter_layout_editor_selector_supported', true ) ) {
						$list = trx_addons_get_list_layouts( false, $meta['layout_type'], 'title' );
						if ( isset( $list[$post_id] ) ) {
							unset( $list[ $post_id ] );
						}
						if ( count( $list ) > 0 ) {
							$output .= '<span class="trx_addons_layout_editor_selector_trigger"></span>'
										. '<span class="trx_addons_layout_editor_selector_list">';
							foreach( $list as $id => $title ) {
								$output .= '<span class="trx_addons_layout_editor_selector_list_item"'
												. ' data-post-id="' . esc_attr( $post_id ) . '"'
												. ' data-layout-id="' . esc_attr( $id ) . '"'
												. ' data-layout-type="' . esc_attr( $meta['layout_type'] ) . '"'
												. ' data-layout-url="' . esc_url( admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $id ) ) ) . '"'
											. '>'
												. esc_html( $title )
											. '</span>';
							}
							$output .= '</span>';
						}
					}
					$output .= '</div>'
							. '</div>';
				}
			}
		}
		return $post_content . $output;
	}
}
