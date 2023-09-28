<?php
/**
 * The style "default" of the Widget "Popular Posts"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_popular_posts');
extract($args);
		
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
if ( empty($id) ) {
	$id = trx_addons_generate_id( 'trx_addons_tabs_' );
}
?>
<div<?php
	if ( !empty($id) ) {
		?> id="<?php echo esc_attr($id); ?>"<?php
	}
	?>
	class="trx_addons_tabs<?php if ($tabs_count > 1) echo ' trx_addons_tabs_with_titles'; ?>"
><?php
	if ($tabs_count > 1) {
		?><ul class="trx_addons_tabs_titles"><?php
			foreach ($tabs as $k=>$tab) {
				if (empty($tab['title']) || empty($tab['content'])) continue;
				$id_tab = $id . '_' . $k;
				?><li class="trx_addons_tabs_title"><a href="<?php echo esc_url(trx_addons_get_hash_link('#'.$id_tab.'_content')); ?>"><?php
					echo esc_html($tab['title']);
				?></a></li><?php
			}
		?></ul><?php
	}
	foreach ($tabs as $k=>$tab) {
		if (empty($tab['title']) || empty($tab['content'])) continue;
		$id_tab = $id . '_' . $k;
		?>
		<div id="<?php echo esc_attr($id_tab); ?>_content" class="trx_addons_tabs_content">
			<?php trx_addons_show_layout($tab['content']); ?>
		</div>
		<?php
	}
	?>
</div>
<?php
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>