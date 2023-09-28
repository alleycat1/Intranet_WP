<?php
/**
 * The template to display the cars agent's single page
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

get_header();

while ( have_posts() ) { the_post();
	$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
	
	do_action('trx_addons_action_before_article', 'cars.agents.single');
	?>
	<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'agents_page itemscope' ); trx_addons_seo_snippets('author', 'Person'); ?>>

		<?php do_action('trx_addons_action_article_start', 'cars.agents.single'); ?>
		
		<section class="cars_page_section cars_page_agent">
			<div class="cars_page_agent_wrap"><?php
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.agent.php',
												'trx_addons_args_cars_agent',
												array(
													'meta' => array(
															'agent_type' => 'agent',
															'agent' => get_the_ID()
															)
												)
											);
			?></div>
		</section>
		
		<?php
		$page_number = get_query_var('paged') 
							? get_query_var('paged') 
							: (get_query_var('page') 
								? get_query_var('page') 
								: 1);
		if ( $page_number == 1 && trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
			?><section class="cars_page_section cars_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				?><h4 class="cars_page_section_title"><?php esc_html_e('About me', 'trx_addons'); ?></h4><?php
				the_content( );
			?></section><?php
		}
		?>

		<section class="cars_page_section cars_page_offers_list">
			<h4 class="cars_page_section_title"><?php esc_html_e('My offers', 'trx_addons'); ?></h4><?php
			?><div class="cars_page_offers_list_wrap"><?php
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.loop.php',
												'trx_addons_args_cars_loop',
												array(
													'blog_style' => trx_addons_get_option('cars_agents_list_style'),
													'query_params' => array(
																		'cars_agent' => get_the_ID()
																		)
													)
											);
			?></div>
		</section>

		<?php do_action('trx_addons_action_article_end', 'cars.agents.single'); ?>

	</article>
	<?php
	do_action('trx_addons_action_after_article', 'cars.agents.single');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
