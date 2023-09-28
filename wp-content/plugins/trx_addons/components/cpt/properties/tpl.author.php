<?php
/**
 * The template to display the properties author's page
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
				'post_type' => TRX_ADDONS_CPT_PROPERTIES_PT
				)
			);
if ( ! empty( $wp_query->post ) ) {
	$post = $wp_query->post;
	setup_postdata( $post );
}

do_action('trx_addons_action_before_article', 'properties.author');

?>
<div class="agents_page properties_author_page itemscope"<?php trx_addons_seo_snippets('author', 'Person'); ?>>

	<?php do_action('trx_addons_action_article_start', 'properties.author'); ?>
		
	<section class="properties_page_section properties_page_agent">
		<div class="properties_page_agent_wrap"><?php
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.agent.php',
											'trx_addons_args_properties_agent',
											array(
												'meta' => array(
														'agent_type' => 'author'
														)
											)
										);
		?></div>
	</section>
		
	<section class="properties_page_section properties_page_offers_list">
		<h4 class="properties_page_section_title"><?php esc_html_e('My offers', 'trx_addons'); ?></h4><?php
		?><div class="properties_page_offers_list_wrap"><?php
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.loop.php',
											'trx_addons_args_properties_loop',
											array(
												'blog_style' => trx_addons_get_option('agents_properties_style'),
											)
										);
		?></div>
	</section>

	<?php do_action('trx_addons_action_article_end', 'properties.author'); ?>

</div>

<?php
do_action('trx_addons_action_after_article', 'properties.author');

get_footer();
