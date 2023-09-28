<?php
/**
	Template Name: File Away iFrame
*/
?>
<html>
<head>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<?php if(is_page_template('file-away-iframe-template.php')) show_admin_bar(false); ?><?php wp_head(); ?>
<style>body {display:none;}</style>
<script>
jQuery(document).ready(function($){
	$("body").fadeIn(500);
	$drawer = $('tr.ssfa-drawers td[id^=folder-ssfa-dir-] a, tr.ssfa-drawers td[id^=name-ssfa-dir-] a');
	$drawer.on('click', function(){
		$("body").fadeOut(500);
	});
	$crumb = $('div[class^="ssfa-crumbs"] a');
	$crumb.on('click', function(){
		$("body").fadeOut(500);
	});
});
</script>
</head>
<body>
<?php while(have_posts()): the_post(); ?>
<div id="page-content"><?php the_content(); endwhile; ?></div>
<?php wp_footer(); ?>
</body>
</html>