<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'pubzinne_tribe_events_get_css' ) ) {
	add_filter( 'pubzinne_filter_get_css', 'pubzinne_tribe_events_get_css', 10, 2 );
	function pubzinne_tribe_events_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
			
.tribe-events-list .tribe-events-list-event-title {
	{$fonts['h3_font-family']}
}

#tribe-events .tribe-events-button,
.tribe-events-button,
.tribe-events-cal-links a,
.tribe-events-sub-nav li a,
.tribe-common .tribe-common-c-btn-border,
.tribe-common a.tribe-common-c-btn-border,
 .tribe-common .tribe-common-c-btn,
.tribe-common a.tribe-common-c-btn,
.tribe-events .tribe-events-c-ical__link,
.tribe-events-c-top-bar.tribe-events-header__top-bar a.tribe-events-c-top-bar__today-button {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

.tribe-common--breakpoint-medium.tribe-common .tribe-common-h3{
    {$fonts['h3_font-family']}
	{$fonts['h3_font-size']}
	{$fonts['h3_font-weight']}
	{$fonts['h3_font-style']}
	{$fonts['h3_line-height']}
	{$fonts['h3_text-decoration']}
	{$fonts['h3_text-transform']}
	{$fonts['h3_letter-spacing']}
}

#tribe-bar-form button, #tribe-bar-form a,
.tribe-events-read-more {
	{$fonts['button_font-family']}
	{$fonts['button_letter-spacing']}
}
.tribe-events-list .tribe-events-list-separator-month,
.tribe-events-calendar thead th,
.tribe-events-schedule, .tribe-events-schedule h2,
.tribe-common .tribe-common-h3,
.tribe-common .tribe-common-h4,
.tribe-common .tribe-common-h6, .single-tribe_events #tribe-events-content .tribe-events-event-meta .tribe-events-meta-group dt,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-h4{
	{$fonts['h5_font-family']}
}

#tribe-bar-form input, #tribe-events-content.tribe-events-month,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title,
#tribe-mobile-container .type-tribe_events,
.tribe-events-list-widget ol li .tribe-event-title,
.tribe-events-content, .tribe-common .tribe-common-h4,
.tribe-events .tribe-events-c-view-selector__list-item-text,
.single-tribe_events .tribe-events-event-meta .tribe-events-meta-group dd,
.tribe-common .tribe-common-h8,
.tribe-common .tribe-common-h2,
.tribe-common .tribe-common-h5,
.tribe-events .tribe-events-calendar-month__calendar-event-tooltip-datetime,
.tribe-common .tribe-common-b2,
.tribe-common .tribe-common-b3.tribe-events-calendar-month__calendar-event-tooltip-description,
.tribe-common .tribe-common-b2.tribe-events-calendar-day__event-venue,
.tribe-common .tribe-common-b2.tribe-events-calendar-day__event-description,
.tribe-common .tribe-common-b2 .tribe-events-calendar-day__event-datetime{
	{$fonts['p_font-family']}
	{$fonts['p_font-weight']}
}

.tribe-events .tribe-events-c-search__input-control--keyword .tribe-events-c-search__input,
.tribe-events .tribe-events-c-view-selector--tabs .tribe-events-c-view-selector__list-item .tribe-events-c-view-selector__list-item-text,
.tribe-common .tribe-common-b3,
.tribe-common .tribe-common-h7,
 .tribe-events .tribe-events-c-view-selector__list-item-text,
 .tribe-common .tribe-common-b2.tribe-events-c-nav__prev,
 .tribe-common .tribe-common-b2.tribe-events-c-nav__next,
 .tribe-common .tribe-common-b2.tribe-events-c-nav__today,
 .tribe-common .tribe-events-calendar-list__event-date-tag-daynum.tribe-common-h5{
    {$fonts['h6_font-family']}
     {$fonts['h6_font-weight']}
}

.tribe-events-loop .tribe-event-schedule-details,
.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
#tribe-mobile-container .type-tribe_events .tribe-event-date-start {
	{$fonts['info_font-family']};
}

CSS;
		}

		if ( isset( $css['vars'] ) && isset( $args['vars'] ) ) {
			$vars         = $args['vars'];
			$css['vars'] .= <<<CSS

#tribe-bar-form .tribe-bar-submit input[type="submit"],
#tribe-bar-form button,
#tribe-bar-form a,
#tribe-events .tribe-events-button,
#tribe-bar-views .tribe-bar-views-list,
.tribe-events-button,
.tribe-events-cal-links a,
#tribe-events-footer ~ a.tribe-events-ical.tribe-events-button,
.tribe-events-sub-nav li a {
	-webkit-border-radius: {$vars['rad']};
	    -ms-border-radius: {$vars['rad']};
			border-radius: {$vars['rad']};
}

CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

/* Filters bar */
#tribe-bar-form {
	color: {$colors['text_dark']};
}
#tribe-bar-form input[type="text"] {
	color: {$colors['text_dark']};
	border-color: {$colors['bd_color']};
}

.datepicker thead tr:first-child th:hover, .datepicker tfoot tr th:hover {
	color: {$colors['text_link']};
	background: {$colors['text_dark']};
}

/* Content */
.tribe-events-calendar thead th {
	color: {$colors['extra_dark']};
	background: {$colors['extra_bg_color']} !important;
}
.tribe-events-calendar thead th + th:before {
	background: {$colors['extra_dark']};
}
#tribe-events-content .tribe-events-calendar td,
#tribe-events-content .tribe-events-calendar th {
	border-color: {$colors['bd_color']} !important;
}
.tribe-events-calendar td div[id*="tribe-events-daynum-"],
.tribe-events-calendar td div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_dark']};
}
.tribe-events-calendar td.tribe-events-othermonth {
	color: {$colors['alter_light']};
	background: {$colors['alter_bg_color']} !important;
}
.tribe-events-calendar td.tribe-events-othermonth div[id*="tribe-events-daynum-"],
.tribe-events-calendar td.tribe-events-othermonth div[id*="tribe-events-daynum-"] > a {
	color: {$colors['alter_light']};
}
.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"], .tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_light']};
}
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"],
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_link']};
}
.tribe-events-calendar td.tribe-events-present:before {
	border-color: {$colors['text_link']};
}
.tribe-events-calendar .tribe-events-has-events:after {
	background-color: {$colors['text']};
}
.tribe-events-calendar .mobile-active.tribe-events-has-events:after {
	background-color: {$colors['bg_color']};
}
#tribe-events-content .tribe-events-calendar td,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title a {
	color: {$colors['text_dark']};
}
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title a:hover {
	color: {$colors['text_link']};
}
#tribe-events-content .tribe-events-calendar td.mobile-active,
#tribe-events-content .tribe-events-calendar td.mobile-active:hover {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
#tribe-events-content .tribe-events-calendar td.mobile-active div[id*="tribe-events-daynum-"] {
	color: {$colors['bg_color']};
	background-color: {$colors['text_dark']};
}
#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth.mobile-active div[id*="tribe-events-daynum-"] a,
.tribe-events-calendar .mobile-active div[id*="tribe-events-daynum-"] a {
	background-color: transparent;
	color: {$colors['bg_color']};
}
.events-archive.events-gridview #tribe-events-content table .type-tribe_events {
	border-color: {$colors['bd_color']};
}

/* Tooltip */
.recurring-info-tooltip,
.tribe-events-calendar .tribe-events-tooltip,
.tribe-events-week .tribe-events-tooltip,
.tribe-events-shortcode.view-week .tribe-events-tooltip,
.tribe-events-tooltip .tribe-events-arrow {
	color: {$colors['alter_text']};
	background: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
}
#tribe-events-content .tribe-events-tooltip .summary { 
	color: {$colors['extra_dark']};
	background: {$colors['extra_bg_color']};
}
.tribe-events-tooltip .tribe-event-duration {
	color: {$colors['extra_text']};
}

/* Events list */
.tribe-events-list-separator-month {
	color: {$colors['text_dark']};
}
.tribe-events-list-separator-month:after {
	border-color: {$colors['bd_color']};
}
.tribe-events-list .type-tribe_events + .type-tribe_events,
.tribe-events-day .tribe-events-day-time-slot + .tribe-events-day-time-slot + .tribe-events-day-time-slot {
	border-color: {$colors['bd_color']};
}
.tribe-events-list-separator-month span {
	background-color: {$colors['bg_color']};	
}
.tribe-events-list .tribe-events-event-cost span {
	color: {$colors['extra_dark']};
	border-color: {$colors['extra_bg_color']};
	background: {$colors['extra_bg_color']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta {
	color: {$colors['alter_text']};
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta a {
	color: {$colors['alter_link']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta a:hover {
	color: {$colors['alter_hover']};
}
.tribe-mobile .tribe-events-list .tribe-events-venue-details {
	border-color: {$colors['alter_bd_color']};
}

.single-tribe_events #tribe-events-footer,
.tribe-events-day #tribe-events-footer,
.events-list #tribe-events-footer,
.tribe-events-map #tribe-events-footer,
.tribe-events-photo #tribe-events-footer {
	border-color: {$colors['bd_color']};	
}

/* Events day */
.tribe-events-day .tribe-events-day-time-slot h5,
.tribe-events-day .tribe-events-day-time-slot .tribe-events-day-time-slot-heading {
	color: {$colors['extra_dark']};
	background: {$colors['extra_bg_color']};
}



/* Single Event */
.single-tribe_events .tribe-events-venue-map {
	color: {$colors['alter_text']};
	border-color: {$colors['alter_bd_hover']};
	background: {$colors['alter_bg_hover']};
}
.single-tribe_events .tribe-events-schedule .tribe-events-cost {
	color: {$colors['text_dark']};
}
.single-tribe_events .type-tribe_events {
	border-color: {$colors['bd_color']};
}

.tribe-events .tribe-events-c-events-bar, 
.tribe-events .tribe-events-c-events-bar__search-filters-container,
.tribe-events .tribe-events-c-view-selector__content{
    background: {$colors['bg_color']};
}
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-events-bar--border{
    border-color: {$colors['input_bd_color']};
}

.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-top-bar__nav-link:before,
.tribe-common .tribe-common-b2 .tribe-events-calendar-day__event-datetime
/*.tribe-common .tribe-common-c-svgicon.tribe-common-c-svgicon--search */
{
    color: {$colors['text']};
}
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-top-bar__nav-link:hover:before{
    color: {$colors['text_dark']};
}
.tribe-common .tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-c-btn-border, 
.tribe-common--breakpoint-medium.tribe-common a.tribe-common-c-btn-border,
.tribe-common .tribe-common-c-btn-border-small {
    color: {$colors['text']};
	border-color: {$colors['text_hover']};
	background: {$colors['text_hover']};
}
.tribe-common .tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:hover,
.tribe-common .tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:focus,
.tribe-common .tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:focus-within,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-c-btn-border:hover,
.tribe-events-c-top-bar.tribe-events-header__top-bar a.tribe-events-c-top-bar__today-button:hover{
    color: {$colors['extra_dark']};
	border-color: {$colors['text_dark']};
	background: {$colors['text_dark']};
}

.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__button-icon path {
	fill: {$colors['text_dark']};
}

.tribe-common--breakpoint-medium.tribe-common .tribe-common-b3{
    color: {$colors['text_dark']};
}
.tribe-events .datepicker .datepicker-switch{
     color: {$colors['extra_dark']};
}
.tribe-events .datepicker .datepicker-switch:hover,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-h4{
     color: {$colors['extra_link2']};
}
.tribe-events .datepicker .day.active, .tribe-events .datepicker .day.active.focused, .tribe-events .datepicker .day.active:focus, .tribe-events .datepicker .day.active:hover, .tribe-events .datepicker .month.active, .tribe-events .datepicker .month.active.focused, .tribe-events .datepicker .month.active:focus, .tribe-events .datepicker .month.active:hover, .tribe-events .datepicker .year.active, .tribe-events .datepicker .year.active.focused, .tribe-events .datepicker .year.active:focus, .tribe-events .datepicker .year.active:hover{
    background: {$colors['text_hover']} !important;
    color: {$colors['text_link']};
}
.tribe-events .datepicker .dow{
     color: {$colors['extra_dark']};
}
.tribe-events .tribe-events-calendar-month__multiday-event-bar,
.tribe-events .tribe-events-calendar-month__multiday-event-bar-inner:hover{
     background: {$colors['text_hover']};
}
.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-month__day,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-month__week,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-month__body{
    border-color: {$colors['bd_color']};
}

.tooltipster-base.tribe-events-tooltip-theme{
	border-color: {$colors['extra_bd_hover']};
	background: {$colors['extra_bd_hover']};
}
.tribe-events .tribe-events-calendar-day__event-title a,
.tribe-events .tribe-events-calendar-list__event-title a,
.tribe-events .tribe-events-calendar-month__calendar-event-tooltip-title a,
.tribe-events .tribe-events-calendar-month-mobile-events__mobile-event-title a{
    background-image: linear-gradient(to right, {$colors['text_link2']} 0%, {$colors['text_link2']} 100%)!important;
    color: {$colors['text_dark']}!important;
}
.tribe-events .tribe-events-c-nav__next:disabled, .tribe-events .tribe-events-c-nav__prev:disabled{
    background-color: {$colors['bg_color']}!important;
    color: {$colors['text']}!important;
}
.tribe-events .tribe-events-header{
    background-color: {$colors['bg_color']};
}

.tribe-events-c-ical__link{
    color: {$colors['inverse_link']};
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
}

.tribe-events-c-ical__link:hover,
.tribe-events-c-ical__link:focus{
  	color: {$colors['inverse_hover']};
	border-color: {$colors['text_link_blend']};
	background-color: {$colors['text_link_blend']};
}
.tribe-events .tribe-events-c-nav__next:focus, .tribe-events .tribe-events-c-nav__next:hover, .tribe-events .tribe-events-c-nav__prev:focus, .tribe-events .tribe-events-c-nav__prev:hover{
    color: {$colors['text_dark']}!important;
}
.tribe-common .tribe-common-h3,
.tribe-events .tribe-events-c-view-selector__list-item-text,
.tribe-common .tribe-common-h5,
.tribe-events .tribe-events-calendar-month__day-date-link,
.tribe-events .tribe-events-calendar-month__day-date,
.tribe-events .tribe-events-c-events-bar__search-button-icon:before,
.tribe-common .tribe-common-svgicon--day:before,
.tribe-common .tribe-common-svgicon--month:before,
.tribe-common .tribe-common-svgicon--list:before{
     color: {$colors['text_dark']};
}

.tribe-events-calendar-list__event-datetime{
  color: {$colors['text']};
}
.tribe-events .tribe-events-c-events-bar__search-button:before,
.tribe-events .tribe-events-c-view-selector__button:before,
.tribe-events .tribe-events-calendar-month__mobile-events-icon--event{
    background-color: {$colors['text_hover']};
}
.tribe-events .tribe-events-calendar-month__day-cell--selected,
.tribe-events .tribe-events-calendar-month__day-cell--selected:focus,
.tribe-events .tribe-events-calendar-month__day-cell--selected:hover,
.tribe-events .tribe-events-c-view-selector--tabs .tribe-events-c-view-selector__list-item--active .tribe-events-c-view-selector__list-item-link:after,
.tribe-events .tribe-events-c-view-selector--tabs .tribe-events-c-view-selector__list-item:hover .tribe-events-c-view-selector__list-item-link:after,
.tribe-events .tribe-events-calendar-month__day:hover:after{
    background-color: {$colors['text_dark']};
}
.tribe-events .tribe-events-calendar-month__day-cell--selected .tribe-events-calendar-month__day-date .tribe-events-calendar-month__day-date-daynum{
    color: {$colors['extra_dark']};
}
.tribe-events .tribe-events-c-view-selector--tabs .tribe-events-c-view-selector__list-item-link:hover,
.tribe-events .tribe-events-c-view-selector__list-item-link:focus .tribe-events-c-view-selector__list-item-text,
 .tribe-events .tribe-events-c-view-selector__list-item-link:hover .tribe-events-c-view-selector__list-item-text,
.tribe-common .tribe-common-b2,
.tribe-common .tribe-common-h8 a,
.tribe-common .tribe-common-h7{
    color: {$colors['extra_link2']};
}
.tribe-events .tribe-events-c-view-selector__list-item--active .tribe-events-c-view-selector__list-item-link{
     background-color: {$colors['text_hover']};
}
.tribe-events .tribe-events-c-search__input-control--keyword .tribe-events-c-search__input,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-search {
     background-color: {$colors['bg_color']};
}

.tribe-common.tribe-events .tribe-common-anchor-thin-alt:active,
.tribe-common.tribe-events .tribe-common-anchor-thin-alt:focus,
.tribe-common.tribe-events .tribe-common-anchor-thin-alt:hover{
    color: {$colors['alter_link3']};
}

.tribe-events-content,
.single-tribe_events .tribe-events-event-meta .tribe-events-meta-group dd{
    color: {$colors['text']};
}

.tribe-events .tribe-events-view-loader {
    background-color: {$colors['bg_color_07']}; 
}

.tribe-events .datepicker .day.current, .tribe-events .datepicker .day.current.focused,
.tribe-events .datepicker .day.current:focus, .tribe-events .datepicker .day.current:hover,
.tribe-events .datepicker .month.current, .tribe-events .datepicker .month.current.focused,
.tribe-events .datepicker .month.current:focus, .tribe-events .datepicker .month.current:hover,
.tribe-events .datepicker .year.current, .tribe-events .datepicker .year.current.focused,
.tribe-events .datepicker .year.current:focus, .tribe-events .datepicker .year.current:hover{
    background: {$colors['text_hover_07']}; 
}

.tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:hover .tribe-events-c-subscribe-dropdown__button-text{
	color: {$colors['extra_link']}!important;
}

.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-list__event-datetime-featured-text,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-day__event-datetime-featured-text,
.tribe-events .tribe-events-c-messages__message-list-item-link  {
  color: {$colors['text_link']};
}

.tribe-events-calendar-list .tribe-common-c-svgicon,
.tribe-events-calendar-month .tribe-common-c-svgicon,
.tribe-events-calendar-day .tribe-common-c-svgicon,
.tooltipster-base .tribe-common-c-svgicon  {
  color: {$colors['text_link']};
}

.tribe-events .tribe-events-calendar-list__event-row--featured .tribe-events-calendar-list__event-date-tag-datetime::after,
.tribe-events .tribe-events-calendar-day__event--featured::after {
  background-color: {$colors['text_link']};
}

.tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:hover .tribe-events-c-subscribe-dropdown__button-text,
.tribe-common-c-btn-border.tribe-events-c-subscribe-dropdown__button:focus .tribe-events-c-subscribe-dropdown__button-text,
div.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__button--active .tribe-events-c-subscribe-dropdown__button-text {
	color: {$colors['inverse_link']} !important;
}

.tribe-common .tribe-common-c-svgicon--messages-not-found .tribe-common-c-svgicon__svg-stroke {
	stroke: {$colors['text_dark']};
}

.tribe-events-meta-group .tribe-events-single-section-title,
.single-tribe_events .tribe-events-single .tribe-events-event-meta {
	color: {$colors['text_dark']};
}

.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-month__multiday-event-wrapper {
	background-color: {$colors['text_hover']};
}

CSS;
		}

		return $css;
	}
}

