<?php
/**
 * ThemeREX Addons Layouts: Template for the Elementor Pro header/footer in the Edit mode
 *
 * @package ThemeREX Addons
 * @since v2.6.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

$post_id = get_the_ID();
$uniq_id = 'trx_addons_layout-' . $post_id;
$template_type = get_post_meta( $post_id, '_elementor_template_type', true );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); ?></title>
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>
	<body <?php trx_addons_cpt_layouts_elm_get_body_classes( $post_id, true ); ?>>
		<div class="trx-addons-layout-edit-area">
			<div id="<?php echo esc_attr($uniq_id); ?>" class="trx-addons-layout trx-addons-layout--edit-mode">
				<div class="trx-addons-layout__inner">
					<div class="trx-addons-layout__container">
						<div class="trx-addons-layout__container-inner"><?php
						while ( have_posts() ) :
							the_post();
							if ( in_array( $template_type, array( 'header', 'footer' ) ) && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
								do_action( 'trx_addons_action_before_show_layout', $post_id );
								trx_addons_sc_stack_push('show_layout');
								$did_location = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location( $template_type );
								trx_addons_sc_stack_pop();
								do_action( 'trx_addons_action_after_show_layout', $post_id );
							} else {
								the_content();
							}
						endwhile;
						wp_footer();
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>