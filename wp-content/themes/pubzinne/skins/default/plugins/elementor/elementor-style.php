<?php
// Add plugin-specific vars to the custom CSS
if ( ! function_exists( 'pubzinne_elm_add_theme_vars' ) ) {
	add_filter( 'pubzinne_filter_add_theme_vars', 'pubzinne_elm_add_theme_vars', 10, 2 );
	function pubzinne_elm_add_theme_vars( $rez, $vars ) {
		foreach ( array( 10, 20, 30, 40, 60 ) as $m ) {
			if ( substr( $vars['page'], 0, 2 ) != '{{' ) {
				$rez[ "page{$m}" ]    = ( $vars['page'] + $m ) . 'px';
				$rez[ "content{$m}" ] = ( $vars['page'] - $vars['gap'] - $vars['sidebar'] + $m ) . 'px';
			} else {
				$rez[ "page{$m}" ]    = "{{ data.page{$m} }}";
				$rez[ "content{$m}" ] = "{{ data.content{$m} }}";
			}
		}
		return $rez;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'pubzinne_elm_get_css' ) ) {
	add_filter( 'pubzinne_filter_get_css', 'pubzinne_elm_get_css', 10, 2 );
	function pubzinne_elm_get_css( $css, $args ) {

		if ( isset( $css['vars'] ) && isset( $args['vars'] ) ) {
			$vars = $args['vars'];
			$css['vars'] .= <<<CSS
/* No gap */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-no {
	max-width: {$vars['page']};
}
/* Narrow: 5px */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-narrow {
	max-width: {$vars['page10']};
}
.elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-narrow,
.elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-narrow {
	width: {$vars['page10']};
}
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-narrow,
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-narrow {
	width: {$vars['content10']};
}

/* Default: 10px */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-default {
	max-width: {$vars['page20']};
}
.elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-default,
.elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-default {
	width: {$vars['page20']};
}
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-default,
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-default {
	width: {$vars['content20']};
}

/* Extended: 15px */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-extended {
	max-width: {$vars['page30']};
}
.elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-extended,
.elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-extended {
	width: {$vars['page30']};
}
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-extended,
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-extended {
	width: {$vars['content30']};
}

/* Wide: 20px */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-wide {
	max-width: {$vars['page40']};
}
.elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wide,
.elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wide {
	width: {$vars['page40']};
}
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wide,
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wide {
	width: {$vars['content40']};
}

/* Wider: 30px */
.elementor-section.elementor-section-boxed > .elementor-container.elementor-column-gap-wider {
	max-width: {$vars['page60']};
}
.elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wider,
.elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wider {
	width: {$vars['page60']};
}
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-boxed:not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wider,
.sidebar_show .content_wrap .elementor-section.elementor-section-justified.elementor-section-full_width:not(.elementor-section-stretched):not(.elementor-inner-section) > .elementor-container.elementor-column-gap-wider {
	width: {$vars['content60']};
}

CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

/* Shape above and below rows */
.elementor-shape .elementor-shape-fill {
	fill: {$colors['bg_color']};
}

/* Divider */
.elementor-widget-divider {
	--divider-border-color: {$colors['bd_color']};
}
.elementor-divider-separator {
	border-color: {$colors['bd_color']};
}

CSS;
		}

		return $css;
	}
}

