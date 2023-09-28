<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'pubzinne_woocommerce_get_css' ) ) {
	add_filter( 'pubzinne_filter_get_css', 'pubzinne_woocommerce_get_css', 10, 2 );
	function pubzinne_woocommerce_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

.woocommerce .checkout table.shop_table .product-name .variation,
.woocommerce div.product form.cart .variations label,
.woocommerce .shop_table.order_details td.product-name .variation,
 .woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price{
	{$fonts['p_font-family']}
}
.woocommerce ul.products li.product .post_header, .woocommerce-page ul.products li.product .post_header,
.woocommerce .shop_table th,
.woocommerce div.product p.price, .woocommerce div.product span.price,
.woocommerce div.product .summary .stock,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce table.cart td.product-name a, .woocommerce-page table.cart td.product-name a, 
.woocommerce #content table.cart td.product-name a, .woocommerce-page #content table.cart td.product-name a,
.woocommerce .checkout table.shop_table .product-name,
.woocommerce .shop_table.order_details td.product-name,
.woocommerce .order_details li strong,
.woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-content .woocommerce-Address-title a,
.woocommerce ul.products li.product .outofstock_label,
.woocommerce #review_form #respond #reply-title{
	{$fonts['h5_font-family']}
}
.woocommerce ul.products li.product .button, .woocommerce div.product form.cart .button,
.woocommerce .woocommerce-message .button,
.woocommerce #review_form #respond p.form-submit input[type="submit"],
.woocommerce-page #review_form #respond p.form-submit input[type="submit"],
.woocommerce table.my_account_orders .order-actions .button,
.woocommerce .button, .woocommerce-page .button,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button,
.woocommerce-page a.added_to_cart,
.woocommerce #respond input#submit,
.woocommerce input[type="button"], .woocommerce-page input[type="button"],
.woocommerce input[type="submit"], .woocommerce-page input[type="submit"] {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.woocommerce-input-wrapper,
.woocommerce table.cart td.actions .coupon .input-text,
.woocommerce #content table.cart td.actions .coupon .input-text,
.woocommerce-page table.cart td.actions .coupon .input-text,
.woocommerce-page #content table.cart td.actions .coupon .input-text {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}
.woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale,
.woocommerce-mini-cart__total.total,
.woocommerce .widget_price_filter .price_slider_amount,
.woocommerce.widget_shopping_cart ul.cart_list li,
.woocommerce .product_meta span,
.woocommerce .product_meta span span,
.woocommerce .product_meta a,
.woocommerce div.product .product_meta span>span,
.woocommerce div.product .product_meta span > a,
.single-product div.product .woocommerce-tabs .wc-tabs li a,
.woocommerce span.onsale,
.sc_layouts_cart_items_short{
    {$fonts['h6_font-family']}
}
.related.products h2{
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_line-height']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_text-transform']}
	{$fonts['h1_letter-spacing']}
	{$fonts['h1_margin-top']}
	{$fonts['h1_margin-bottom']}
}

.woocommerce ul.products li.product .post_header .post_tags,
.woocommerce div.product form.cart .reset_variations,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta time, .woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta time{
	{$fonts['h5_font-family']}
}

CSS;
		}

		if ( isset( $css['vars'] ) && isset( $args['vars'] ) ) {
			$vars         = $args['vars'];
			$css['vars'] .= <<<CSS

.woocommerce .button, .woocommerce-page .button,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button
.woocommerce #respond input#submit,
.woocommerce input[type="button"], .woocommerce-page input[type="button"],
.woocommerce input[type="submit"], .woocommerce-page input[type="submit"],
.woocommerce .woocommerce-message .button,
.woocommerce ul.products li.product .button,
.woocommerce div.product form.cart .button,
.woocommerce #review_form #respond p.form-submit input[type="submit"],
.woocommerce-page #review_form #respond p.form-submit input[type="submit"],
.woocommerce table.my_account_orders .order-actions .button,
.yith-woocompare-widget a.clear-all,
.single-product div.product .woocommerce-tabs .wc-tabs li a,
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container-single .chosen-single {
	-webkit-border-radius: {$vars['rad']};
	    -ms-border-radius: {$vars['rad']};
			border-radius: {$vars['rad']};
}
.woocommerce div.product form.cart div.quantity span.q_inc, .woocommerce-page div.product form.cart div.quantity span.q_inc,
.woocommerce .shop_table.cart div.quantity span.q_inc, .woocommerce-page .shop_table.cart div.quantity span.q_inc {
	-webkit-border-radius: 0 {$vars['rad']} 0 0;
	    -ms-border-radius: 0 {$vars['rad']} 0 0;
			border-radius: 0 {$vars['rad']} 0 0;
}
.woocommerce div.product form.cart div.quantity span.q_dec, .woocommerce-page div.product form.cart div.quantity span.q_dec,
.woocommerce .shop_table.cart div.quantity span.q_dec, .woocommerce-page .shop_table.cart div.quantity span.q_dec {
	-webkit-border-radius: 0 0 {$vars['rad']} 0;
	    -ms-border-radius: 0 0 {$vars['rad']} 0;
			border-radius: 0 0 {$vars['rad']} 0;
}
.woocommerce ul.products li.product .yith_buttons_wrap,
.woocommerce ul.products li.product .yith_buttons_wrap a > .tooltip {
	-webkit-border-radius: {$vars['rad3']};
	    -ms-border-radius: {$vars['rad3']};
			border-radius: {$vars['rad3']};
}
CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

/* Page header */
.woocommerce .woocommerce-breadcrumb {
	color: {$colors['text']};
}
.woocommerce .woocommerce-breadcrumb a {
	color: {$colors['text_link']};
}
.woocommerce .woocommerce-breadcrumb a:hover {
	color: {$colors['text_hover']};
}
.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
.woocommerce .widget_price_filter .ui-slider .ui-slider-handle {
	background-color: {$colors['text_hover']};
}

/* List and Single product */
.single_product_layout_stretched .page_content_wrap {
	background-color: {$colors['alter_bg_color_04']};
}
.woocommerce.single_product_layout_stretched #content div.product div.summary, .woocommerce-page.single_product_layout_stretched #content div.product div.summary,
.woocommerce.single_product_layout_stretched div.product div.summary, .woocommerce-page.single_product_layout_stretched div.product div.summary {
	background-color: {$colors['bg_color']};
}
.woocommerce.single_product_layout_stretched.sidebar_show #content div.product .woocommerce-tabs, .woocommerce-page.single_product_layout_stretched.sidebar_show #content div.product .woocommerce-tabs,
.woocommerce.single_product_layout_stretched.sidebar_show div.product .woocommerce-tabs, .woocommerce-page.single_product_layout_stretched.sidebar_show div.product .woocommerce-tabs {
	background-color: {$colors['bg_color']};
}

.woocommerce .woocommerce-ordering select {
	border-color: {$colors['bd_color']};
}
.woocommerce span.onsale {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.woocommerce .shop_mode_thumbs ul.products li.product .post_item, .woocommerce-page .shop_mode_thumbs ul.products li.product .post_item {
	background-color: {$colors['extra_bd_hover']};
}
.woocommerce .shop_mode_thumbs ul.products li.product .post_item:hover, .woocommerce-page .shop_mode_thumbs ul.products li.product .post_item:hover {
	background-color: {$colors['extra_bd_hover']};
}

.woocommerce ul.products li.product .post_featured {
	border-color: {$colors['extra_bd_hover']};
	background-color: {$colors['extra_bd_hover']};
}

.woocommerce ul.products li.product .post_header a {
	color: {$colors['text_dark']};
}
.woocommerce ul.products li.product .post_header a:hover {
	color: {$colors['text_dark']};
}
.woocommerce ul.products li.product .post_header .post_tags,
.woocommerce ul.products li.product .post_header .post_tags a {
	color: {$colors['alter_link']};
}
.woocommerce ul.products li.product .post_header .post_tags a:hover {
	color: {$colors['alter_hover']};
}

.woocommerce ul.products li.product .yith_buttons_wrap {
	border-color: {$colors['extra_bd_color']};
	background-color: {$colors['extra_bg_color']};
}
.woocommerce ul.products li.product .yith_buttons_wrap > :nth-child(n+1) {
	border-color: {$colors['extra_bd_color']};
}
.woocommerce ul.products li.product .yith_buttons_wrap a {
	color: {$colors['extra_link']};
}
.woocommerce ul.products li.product .yith_buttons_wrap a:hover {
	color: {$colors['extra_hover']};
}
.woocommerce ul.products li.product .yith_buttons_wrap a > .tooltip {
	color: {$colors['extra_text']};
	background-color: {$colors['extra_bg_color']};
	border-color: {$colors['extra_bg_color']};
}
.woocommerce.single-product ul.products li.product .yith_buttons_wrap {
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.woocommerce.single-product ul.products li.product .yith_buttons_wrap > :nth-child(n+1) {
	border-color: {$colors['alter_bd_color']};
}
.woocommerce.single-product ul.products li.product .yith_buttons_wrap a {
	color: {$colors['alter_link']};
}
.woocommerce.single-product ul.products li.product .yith_buttons_wrap a:hover {
	color: {$colors['alter_hover']};
}
.woocommerce.single-product ul.products li.product .yith_buttons_wrap a > .tooltip {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bg_color']};
}

.woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price,
.woocommerce ul.products li.product .price ins, .woocommerce-page ul.products li.product .price ins {
	color: {$colors['text']};
}
.woocommerce ul.products li.product .price del, .woocommerce-page ul.products li.product .price del {
	color: {$colors['alter_light']};
}

.woocommerce div.product p.price, .woocommerce div.product span.price,
.woocommerce .post_item_single  span.amount, .woocommerce-page .post_item_single span.amount,
.woocommerce div.product p.price ins{
	color: {$colors['text_dark']};
}
.woocommerce table.shop_table td span.amount {
	color: {$colors['text']};
}
.woocommerce div.product .out-of-stock{
    color: {$colors['text_dark']};
}
aside.woocommerce del,
.woocommerce del, .woocommerce del > span.amount, 
.woocommerce-page del, .woocommerce-page del > span.amount {
	color: {$colors['text']} !important;
}
.woocommerce div.product p.price ins span.amount{
    color: {$colors['text_dark']} !important;
}
.woocommerce .price del:before {
	background-color: {$colors['text']};
}
.woocommerce div.product form.cart div.quantity span, .woocommerce-page div.product form.cart div.quantity span,
.woocommerce .shop_table.cart div.quantity span, .woocommerce-page .shop_table.cart div.quantity span {
	color: {$colors['text']};
	background-color: {$colors['input_bg_hover']};
}
.woocommerce div.product form.cart div.quantity span:hover, .woocommerce-page div.product form.cart div.quantity span:hover,
.woocommerce .shop_table.cart div.quantity span:hover, .woocommerce-page .shop_table.cart div.quantity span:hover {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color']};
}
.woocommerce div.product form.cart div.quantity input[type="number"], .woocommerce-page div.product form.cart div.quantity input[type="number"],
.woocommerce .shop_table.cart input[type="number"], .woocommerce-page .shop_table.cart div.quantity input[type="number"] {
	border-color: {$colors['bd_color']};
}
.woocommerce div.product div.images.woocommerce-product-gallery{
    background-color: {$colors['extra_bd_hover']};
}

.woocommerce table > tbody > tr:nth-child(2n+1) > td+td a,
.woocommerce-page.woocommerce-cart table.cart td+td span{
	color: {$colors['text']};
}
.woocommerce table > tbody > tr:nth-child(2n+1) > td+td a:hover {
	color: {$colors['text_hover2']};
}
.woocommerce table > tbody > tr:nth-child(2n) > td+td a:hover {
	color: {$colors['text']};
}

.woocommerce div.product .product_meta span > a,
.woocommerce div.product .product_meta span > span {
	color: {$colors['text_dark']};
}
.woocommerce div.product .product_meta a:hover {
	color: {$colors['text_hover']};
}

.woocommerce div.product div.images .flex-viewport,
.woocommerce div.product div.images img {
	border-color: {$colors['bd_color']};
}
.woocommerce div.product div.images a:hover img {
	border-color: {$colors['text_link']};
}

.woocommerce div.product .woocommerce-tabs ul.tabs li, .woocommerce #content div.product .woocommerce-tabs ul.tabs li, .woocommerce-page div.product .woocommerce-tabs ul.tabs li, .woocommerce-page #content div.product .woocommerce-tabs ul.tabs li,
.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
	background: {$colors['bg_color']};
}

.single_product_layout_stretched div.product .trx-stretch-width {
	background-color: {$colors['bg_color']};	
}
.single_product_layout_stretched div.product .woocommerce-tabs,
.woocommerce div.product .woocommerce-tabs .panel, .woocommerce-page div.product .woocommerce-tabs .panel,
.woocommerce #content div.product .woocommerce-tabs .panel, .woocommerce-page #content div.product .woocommerce-tabs .panel {
	border-color: {$colors['bd_color']};
}
.single-product div.product .woocommerce-tabs .wc-tabs li a {
	color: {$colors['text_dark']};
	background-color: {$colors['extra_bd_hover']};
}
.single-product div.product .woocommerce-tabs .wc-tabs li.active a {
	color: {$colors['extra_link']};
	background-color: {$colors['text_dark']};
}
.single-product div.product .woocommerce-tabs .wc-tabs li:not(.active) a:hover {
	color: {$colors['text_dark']};
	background-color: {$colors['extra_hover']};
}

.single_product_layout_stretched div.product .woocommerce-tabs .wc-tabs li a {
	color: {$colors['text_dark']};
}
.single_product_layout_stretched div.product .woocommerce-tabs .wc-tabs li.active a {
	color: {$colors['text_link']};
}
.single_product_layout_stretched div.product .woocommerce-tabs .wc-tabs li:not(.active) a:hover {
	color: {$colors['text_hover']};
}

.single-product div.product .woocommerce-tabs .panel {
	color: {$colors['text']};
}
.single_product_layout_stretched div.product .woocommerce-tabs .panel {
	border-color: {$colors['bd_color']};
}
.woocommerce table.shop_attributes tr:nth-child(2n+1) > * {
	background-color: {$colors['alter_bg_color_04']};
}
.woocommerce table.shop_attributes tr:nth-child(2n) > *,
.woocommerce table.shop_attributes tr.alt > * {
	background-color: {$colors['alter_bg_color_02']};
}
.woocommerce table.shop_attributes th {
	color: {$colors['text_dark']};
}


/* Related Products */
.single-product .related {
	border-color: {$colors['bd_color']};
}
.single-product ul.products li.product .post_data {
	color: {$colors['extra_text']};
	background-color: {$colors['extra_bd_hover']};
}
.single-product ul.products li.product .post_data .price span.amount {
	color: {$colors['extra_text']};
}
.single-product ul.products li.product .post_data .post_header .post_tags,
.single-product ul.products li.product .post_data .post_header .post_tags a,
.single-product ul.products li.product .post_data a {
	color: {$colors['text_dark']};
}
.single-product ul.products li.product .post_data .post_header .post_tags a:hover,
.single-product ul.products li.product .post_data a:hover {
	color: {$colors['text_dark']};
}
.single-product ul.products li.product .post_data .button {
	color: {$colors['inverse_link']};
	background-color: {$colors['extra_link']};
}
.single-product ul.products li.product .post_data .button:hover {
	color: {$colors['extra_link']} !important;
	background-color: {$colors['extra_hover']};
}
.woocommerce .products.related ul.products li.product > .post_item.post_layout_thumbs{
    background-color: {$colors['extra_bd_hover']};
}

/* Rating */
.star-rating span,
.star-rating:before {
	color: {$colors['text_hover']};
}
#review_form #respond p.form-submit input[type="submit"] {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
#review_form #respond p.form-submit input[type="submit"]:hover,
#review_form #respond p.form-submit input[type="submit"]:focus {
	color: {$colors['bg_color']};
	background-color: {$colors['text_dark']};
}

/* Shop mode selector */
.pubzinne_shop_mode_buttons a {
		color: {$colors['text_dark']};
	background-color: {$colors['text_hover']};
}
.pubzinne_shop_mode_buttons a:hover{
	color: {$colors['text']};
	background-color: {$colors['alter_bg_hover']};
}
.shop_mode_thumbs .pubzinne_shop_mode_buttons a.woocommerce_thumbs,
.shop_mode_list .pubzinne_shop_mode_buttons a.woocommerce_list {
	color: {$colors['text']};
	background-color: {$colors['alter_bg_hover']};
}


/* Messages */
.woocommerce .woocommerce-message,
.woocommerce .woocommerce-info {
	background-color: {$colors['alter_bg_color']};
	border-top-color: {$colors['alter_dark']};
}
.woocommerce .woocommerce-error {
	background-color: {$colors['alter_bg_color']};
	border-top-color: {$colors['alter_link']};
}
.woocommerce .woocommerce-message:before,
.woocommerce .woocommerce-info:before {
	color: {$colors['alter_dark']};
}
.woocommerce .woocommerce-error:before {
	color: {$colors['alter_link']};
}


/* Cart */
.woocommerce table.shop_table td {
	border-color: {$colors['alter_bd_color']} !important;
}
.woocommerce table.shop_table th {
	border-color: {$colors['alter_bd_color_02']} !important;
}
.woocommerce table.shop_table tfoot th, .woocommerce-page table.shop_table tfoot th {
	color: {$colors['text_dark']};
	border-color: transparent !important;
	background-color: transparent;
}
.woocommerce .quantity input.qty, .woocommerce #content .quantity input.qty, .woocommerce-page .quantity input.qty, .woocommerce-page #content .quantity input.qty {
	color: {$colors['text']};
}
.woocommerce .cart-collaterals .cart_totals table select,
.woocommerce-page .cart-collaterals .cart_totals table select {
	color: {$colors['input_text']};
	background-color: {$colors['input_bg_color']};
}
.woocommerce .cart-collaterals .cart_totals table select:focus, .woocommerce-page .cart-collaterals .cart_totals table select:focus {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']};
}
.woocommerce .cart-collaterals .shipping_calculator .shipping-calculator-button:after,
.woocommerce-page .cart-collaterals .shipping_calculator .shipping-calculator-button:after {
	color: {$colors['text_dark']};
}
.woocommerce table.shop_table .cart-subtotal .amount, .woocommerce-page table.shop_table .cart-subtotal .amount,
.woocommerce table.shop_table .shipping td, .woocommerce-page table.shop_table .shipping td {
	color: {$colors['inverse_bd_color']};
}
.woocommerce table.cart td+td a, .woocommerce #content table.cart td+td a, .woocommerce-page table.cart td+td a, .woocommerce-page #content table.cart td+td a,
.woocommerce table.cart td+td span, .woocommerce #content table.cart td+td span, .woocommerce-page table.cart td+td span, .woocommerce-page #content table.cart td+td span {
	color: {$colors['text_dark']};
}
.woocommerce table.cart td+td a:hover, .woocommerce #content table.cart td+td a:hover, .woocommerce-page table.cart td+td a:hover, .woocommerce-page #content table.cart td+td a:hover {
	color: {$colors['text_link']};
}
#add_payment_method table.cart td.actions .coupon .input-text, .woocommerce-cart table.cart td.actions .coupon .input-text, .woocommerce-checkout table.cart td.actions .coupon .input-text {
	border-color: {$colors['input_bd_color']};
}


/* Checkout */
#add_payment_method #payment ul.payment_methods, .woocommerce-cart #payment ul.payment_methods, .woocommerce-checkout #payment ul.payment_methods {
	border-color:{$colors['bd_color']};
}
#add_payment_method #payment div.payment_box, .woocommerce-cart #payment div.payment_box, .woocommerce-checkout #payment div.payment_box {
	color:{$colors['input_dark']};
	background-color:{$colors['input_bg_hover']};
}
#add_payment_method #payment div.payment_box:before, .woocommerce-cart #payment div.payment_box:before, .woocommerce-checkout #payment div.payment_box:before {
	border-color: transparent transparent {$colors['input_bg_hover']};
}
.woocommerce .order_details li strong, .woocommerce-page .order_details li strong {
	color: {$colors['text_dark']};
}
.woocommerce .order_details.woocommerce-thankyou-order-details {
	color:{$colors['alter_text']};
	background-color:{$colors['alter_bg_color']};
}
.woocommerce .order_details.woocommerce-thankyou-order-details strong {
	color:{$colors['alter_dark']};
}

/* My Account */
.woocommerce-account .woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-navigation ul li,
.woocommerce-MyAccount-navigation li+li {
	border-color: {$colors['bd_color']};
}
.woocommerce-MyAccount-navigation li.is-active a {
	color: {$colors['text_link']};
}
.woocommerce-MyAccount-content .my_account_orders .button {
	color: {$colors['text_link']};
}
.woocommerce-MyAccount-content .my_account_orders .button:hover {
	color: {$colors['text_hover']};
}

/* Widgets */
.widget_product_search form:after {
	color: {$colors['input_light']};
}
.widget_product_search form:hover:after {
	color: {$colors['input_dark']};
}
.widget_shopping_cart .total {
	color: {$colors['text']};
	border-color: {$colors['bd_color']};
}
.widget_shopping_cart .total .woocommerce-Price-amount {
    color: {$colors['text_dark']};
}
.woocommerce ul.cart_list li a:not(.remove){
    color: {$colors['text_dark']}!important; 
}
.woocommerce ul.cart_list li a:not(.remove){
    background-image: linear-gradient(to right, {$colors['text_link2']} 0%, {$colors['text_link2']} 100%);
}

.woocommerce ul.cart_list li a:not(.remove):hover{
    background-image: linear-gradient(to right, {$colors['text_link2']} 0%, {$colors['text_link2']} 100%);
    color: {$colors['text_dark']}!important;
}


.woocommerce ul.cart_list li a.remove{
    color: {$colors['text_dark']}!important; 
}

.woocommerce ul.cart_list li dl,
.woocommerce-page ul.cart_list li dl,
.woocommerce ul.product_list_widget li dl,
.woocommerce-page ul.product_list_widget li dl {
	border-color: {$colors['bd_color']};
}
.widget_layered_nav ul li.chosen a {
	color: {$colors['text_dark']};
}
.widget_price_filter .price_slider_wrapper .ui-widget-content { 
	background: {$colors['alter_bg_hover']};
}
.widget_price_filter .price_label span {
	color: {$colors['text_dark']};
}


/* WooCommerce Search widget */
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field input[type="text"],
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field .trx_addons_woocommerce_search_form_field_label {
	border-color: {$colors['text_link']};
	color: {$colors['text_link']};
}
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field input[type="text"]:focus,
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field .trx_addons_woocommerce_search_form_field_label:hover {
	border-color: {$colors['text_hover']};
	color: {$colors['text_hover']};
}
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field_list {
	color: {$colors['alter_text']};
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.trx_addons_woocommerce_search_type_inline .trx_addons_woocommerce_search_form_field_list li:hover {
	color: {$colors['alter_dark']};
	background-color: {$colors['alter_bg_hover']};
}



/* Third-party plugins
---------------------------------------------- */
.yith_magnifier_zoom_wrap .yith_magnifier_zoom_magnifier {
	border-color: {$colors['bd_color']};
}

.yith-woocompare-widget a.clear-all {
	color: {$colors['inverse_link']};
	background-color: {$colors['alter_link']};
}
.yith-woocompare-widget a.clear-all:hover {
	color: {$colors['inverse_hover']};
	background-color: {$colors['alter_hover']};
}

.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container-single .chosen-single {
	color: {$colors['input_text']};
	background: {$colors['input_bg_color']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container-single .chosen-single:hover {
	color: {$colors['input_dark']};
	background: {$colors['input_bg_hover']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-drop {
	color: {$colors['input_dark']};
	background: {$colors['input_bg_hover']};
	border-color: {$colors['input_bd_hover']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li {
	color: {$colors['input_dark']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li:hover,
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li.highlighted,
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li.result-selected {
	color: {$colors['alter_link']} !important;
}
.woocommerce ul.products li.product .outofstock_label{
    color: {$colors['input_dark']};
    background: {$colors['bg_color']}; 
}
.woocommerce a.remove, .woocommerce a.remove:hover{
     color: {$colors['text_dark']};
}
.woocommerce ul.cart_list li img, .woocommerce-page ul.cart_list li img{
    background: {$colors['alter_bg_hover']}; 
}
del{
     color: {$colors['text_light']};
}
.sc_layouts_item_icon.sc_layouts_cart_icon:hover{
    color: {$colors['text_hover']};
}
.woocommerce #review_form #respond #reply-title{
    color: {$colors['text_dark']};
}

CSS;
		}

		return $css;
	}
}

