<?php 
/**
 * The template to display blog archive
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

/*
Template Name: WeeklyTradingView
*/

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role='main">
    </main>
    <?php get_sidebar('content-bottom');?>
</div>
<?php
    get_sidebar();
    get_footer();
?>