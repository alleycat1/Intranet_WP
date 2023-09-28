<?php
/**
 * The template to display the cars author's page
 *
 * @package ThemeREX Addons
 * @since v1.74.0
 */

get_header();

// Query author's posts
// (standard WordPress author's query return only 'post' type)
global $wp_query, $post;
$author = get_queried_object();
query_posts( array(
				'author' => $author->ID,
				'post_type' => TRX_ADDONS_CPT_CARS_PT
				)
			);
if ( ! empty( $wp_query->post ) ) {
	$post = $wp_query->post;
	setup_postdata( $post );
}

do_action('trx_addons_action_before_article', 'cars.author');

?>
<div class="agents_page cars_author_page itemscope"<?php trx_addons_seo_snippets('author', 'Person'); ?>>

	<?php do_action('trx_addons_action_article_start', 'cars.author'); ?>
		
	<section class="cars_page_section cars_page_agent">
		<div class="cars_page_agent_wrap"><?php
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.agent.php',
											'trx_addons_args_cars_agent',
											array(
												'meta' => array(
														'agent_type' => 'author'
														)
											)
										);
		?></div>
	</section>
		
	<section class="cars_page_section cars_page_offers_list">
		<h4 class="cars_page_section_title"><?php esc_html_e('My offers', 'trx_addons'); ?></h4><?php
		?><div class="cars_page_offers_list_wrap"><?php
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.loop.php',
											'trx_addons_args_cars_loop',
											array(
												'blog_style' => trx_addons_get_option('cars_agents_list_style'),
											)
										);
		?></div>
	</section>

	<?php do_action('trx_addons_action_article_end', 'cars.author'); ?>

</div>

<?php
do_action('trx_addons_action_after_article', 'cars.author');

get_footer();
