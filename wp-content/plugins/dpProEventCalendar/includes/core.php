<?php 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/************************************************************************/
/*** DISPLAY SHORTCODE
/************************************************************************/
class dpProEventCalendar_wpress_display {
	
	static $js_flag;
	
	static $js_declaration = array();
	
	static $id_calendar;
	
	static $type;
	
	static $limit;
	
	static $widget;
	
	static $limit_description;
	
	static $category;
	
	static $event_id;
	
	static $event;
	
	static $columns;
	
	static $from;
	
	static $view;
	
	static $author;
	
	static $get;
	
	static $opts;
	
	public $events_html;

	function __construct(
		$id, 
		$type, 
		$limit, 
		$widget, 
		$limit_description = 0, 
		$category, 
		$author, 
		$get = "", 
		$event_id = "", 
		$event = "", 
		$columns = "", 
		$from = "", 
		$view = "", 
		$opts = array()
	) {
	
		self::$id_calendar = $id;
	
		self::$type = $type;
	
		self::$limit = $limit;
	
		self::$widget = $widget;
	
		self::$limit_description = $limit_description;
	
		self::$category = $category;
	
		self::$event_id = $event_id;
	
		self::$event = $event;
	
		self::$columns = $columns;
	
		self::$view = $view;
	
		self::$author = $author;
	
		self::$get = $get;
	
		self::$opts = $opts;
	
		self::return_dpProEventCalendar();
	

		add_action( 'wp_footer', array(__CLASS__, 'add_scripts' ), 100 );

		// Include WP Editor
		if ( ! class_exists( '_WP_Editors', false ) ) 
	        require ABSPATH . WPINC . '/class-wp-editor.php';
	    
		_WP_Editors::enqueue_default_editor();
		
	}
	
	static function add_scripts() 
	{
	
		global $dpProEventCalendar;
		
		if(self::$js_flag) 
		{
		
			foreach( self::$js_declaration as $key) { echo $key; }

			if(!isset($dpProEventCalendar['custom_css']))
				$dpProEventCalendar['custom_css'] = '';
		
			echo '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';
		
		}
	
	}
	
	function return_dpProEventCalendar() 
	{
	
		global $dpProEventCalendar, $post, $pec_init;
		
		$id = self::$id_calendar;
	
		$type = self::$type;
	
		$limit = self::$limit;
	
		$author = self::$author;
	
		$get = self::$get;
	
		$widget = self::$widget;
	
		$limit_description = self::$limit_description;
	
		$category = self::$category;
	
		$event_id = self::$event_id;
	
		$event = self::$event;
	
		$columns = self::$columns;
	
		$view = self::$view;
	
		$from = self::$from;
	
		$opts = self::$opts;
	

		if($id == "") 
			$id = get_post_meta($post->ID, 'pec_id_calendar', true);

		if(!empty($event)) 
			$event_id = $event;

		$opts['id_calendar'] = $id;
		$opts['widget'] = $widget;
		$opts['category'] = $category;
		$opts['event_id'] = $event_id;
		$opts['author'] = $author;
		$opts['event'] = $id;
		$opts['columns'] = $columns;
		$opts['from'] = $from;
		$opts['view'] = $view;
		$opts['limit_description'] = $limit_description;
		$dpProEventCalendar_class = $pec_init->init_base( $opts );

		if( $opts['skin'] == '' && $dpProEventCalendar_class::$calendar_obj->skin == 'dark' )
			$opts['skin'] = 'dark';

		// Include script styles
		dpProEventCalendar_enqueue_scripts( $opts['rtl'], $type, $opts['skin'] );

		if($get != "") 
		{ 
			
			$this->events_html = $dpProEventCalendar_class->getFormattedEventData($get); 
			
			return;

		}

		if( ! empty( $event ) ) 
		{
			
			if( empty($id) ) 
			{
			
				$calendar_id = $dpProEventCalendar_class->getCalendarByEvent( $event );
				$dpProEventCalendar_class->setCalendar( $calendar_id );
			
			}

		}
		
		if($type != "") { $dpProEventCalendar_class->switchCalendarTo($type, $limit, $limit_description, $category, $author, $event_id); }
		
		
		$events_script= $dpProEventCalendar_class->addScripts();

		self::$js_declaration[] = $events_script;
		
		self::$js_flag = true;
		
		$events_html = $dpProEventCalendar_class->output();

		$this->events_html = $events_html;
	}
}

function dpProEventCalendar_simple_shortcode( $atts ) 
{

	global $dpProEventCalendar, $wp_scripts;
	
	// Clear all W3 Total Cache
	if( class_exists('W3_Plugin_TotalCacheAdmin') )
	{
		$plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
	
		$plugin_totalcacheadmin->flush_all();
	
	}

	extract(shortcode_atts(array(
		'id' => '',
		'type' => '',
		'category' => '',
		'event_id' => '',
		'event' => '',
		'columns' => '',
		'from' => '',
		'past' => '',
		'scope' => '',
		'loop' => '',
		'author' => '',
		'get' => '',
		'new_event' => '',
		'modal' => '',
		'gap' => 0,
		'view' => '',
		'hide_old_dates' => '',
		'limit' => '',
		'widget' => '',
		'group' => '',
		'skin' => '',
		'pagination' => '',
		'echo' => '',
		'start_date' => null,
		'end_date' => null,
		'allow_user_edit_remove' => 1,
		'calendar_per_date' => 3,
		'include_all_events' => '',
		'limit_description' => '',
		'force_dates' => '',
		'rtl' => ''
	), $atts));

	if( ! empty( $event ) ) 
		$type = 'cover';

	
	
	if($author == 'current') 
	{
	
		if(is_user_logged_in()) 
		{
		
			global $current_user;
			
			$author = $current_user->ID;
		
		} else {
		
			$author = strval(rand(1, 1000)).'00000000000000000000';
		
		}
	
	}

	if(!is_numeric($author) && $author != "") 
	{
	
		$user_author = get_user_by( 'login', $author );
		$author = $user_author->ID;
	
	}
	
	$opts = array(
		'limit' => $limit,
		'widget' => $widget,
		'limit_description' => $limit_description,
		'category' => $category,
		'author' => $author,
		'get' => $get,
		'new_event' => $new_event,
		'event_id' => $event_id,
		'loop' => $loop,
		'gap' => $gap,
		'event' => $event,
		'columns' => $columns,
		'modal' => $modal,
		'from' => $from,
		'view' => $view,
		'hide_old_dates' => $hide_old_dates,
		'scope' => $scope,
		'start_date' => $start_date,
		'end_date' => $end_date,
		'skin' => $skin,
		'group' => $group,
		'echo' => $echo,
		'calendar_per_date' => $calendar_per_date,
		'allow_user_edit_remove' => $allow_user_edit_remove,
		'include_all_events' => $include_all_events,
		'pagination' => $pagination,
		'force_dates' => $force_dates,
		'rtl' => $rtl
	);
	
	$dpProEventCalendar_wpress_display = new dpProEventCalendar_wpress_display($id, $type, $limit, $widget, $limit_description, $category, $author, $get, $event_id, $event, $columns, $from, $view, $opts);


	if($echo) 
		echo $dpProEventCalendar_wpress_display->events_html;

	else 
		return $dpProEventCalendar_wpress_display->events_html;
	
	
}

add_shortcode('dpProEventCalendar', 'dpProEventCalendar_simple_shortcode');

/************************************************************************/
/*** DISPLAY END
/************************************************************************/

function dpProEventCalendar_enqueue_scripts ( $rtl = 0, $type = '', $skin = '' )
{

	global $dpProEventCalendar, $wp_scripts;

	/* Add JS files */
	if ( !is_admin() )
	{ 

		$plugin_js = 'jquery.dpProEventCalendar.min.js';

		if( DP_PRO_EVENT_CALENDAR_DEBUG )
			$plugin_js = 'jquery.dpProEventCalendar.js';
	
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_script( 'jquery-ui-datepicker'); 
		wp_enqueue_script( 'placeholder.js', dpProEventCalendar_plugin_url( 'js/jquery.placeholder.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'selectric', dpProEventCalendar_plugin_url( 'js/jquery.selectric.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'jquery-form', dpProEventCalendar_plugin_url( 'js/jquery.form.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'icheck', dpProEventCalendar_plugin_url( 'js/jquery.icheck.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		
		
		wp_enqueue_script( 'isotope', dpProEventCalendar_plugin_url( 'js/isotope.pkgd.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 

		wp_enqueue_script( 'pec_touch', dpProEventCalendar_plugin_url( 'js/jquery.touch.min.js' ),
			array('jquery')); 
			
		wp_enqueue_script( 'dpProEventCalendar', dpProEventCalendar_plugin_url( 'js/'.$plugin_js ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 

		if( ! isset($dpProEventCalendar['exclude_fa']) || ! $dpProEventCalendar['exclude_fa'] ) 
		{
			
			if( isset( $dpProEventCalendar['fontawesome_url'] ) && $dpProEventCalendar['fontawesome_url'] != '' ) 

				$fontawesome_url = $dpProEventCalendar['fontawesome_url'];

			else
				
				$fontawesome_url = DP_PRO_EVENT_CALENDAR_FONT_AWESOME_JS;

			wp_enqueue_script( 'font-awesome', $fontawesome_url); 

		}

		
		$data = $wp_scripts->get_data('dpProEventCalendar', 'data');
		if(empty($data)) {

			$localize = array( 
			'ajaxurl' => admin_url( 'admin-ajax.php'.(defined('ICL_LANGUAGE_CODE') ? '?lang='.ICL_LANGUAGE_CODE : '') ), 
			'postEventsNonce' => wp_create_nonce( 'ajax-get-events-nonce' ),
			);

			$localize['recaptcha_enable'] = false;
			$localize['recaptcha_site_key'] = '';
			if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
				$localize['recaptcha_enable'] = true;
				$localize['recaptcha_site_key'] = $dpProEventCalendar['recaptcha_site_key'];
			}

			wp_localize_script( 'dpProEventCalendar', 'ProEventCalendarAjax', $localize);
		}

		if(!isset($dpProEventCalendar['exclude_gmaps']) || !$dpProEventCalendar['exclude_gmaps']) {
			wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.(isset($dpProEventCalendar['google_map_key']) ? $dpProEventCalendar['google_map_key'] : ''),
				array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false); 
		}

		wp_enqueue_script( 'infobubble', dpProEventCalendar_plugin_url( 'js/infobubble.js' ),
			array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false);

		wp_enqueue_script( 'oms', dpProEventCalendar_plugin_url( 'js/oms.min.js' ),
			array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false);
		
		if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
			wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js',
				'dpProEventCalendar', DP_PRO_EVENT_CALENDAR_VER, false); 
		}

	}

	// Layouts Types

	switch ( $type )
	{

		case 'timeline':  
		
			wp_enqueue_style( 'dpProEventCalendar_timeline', dpProEventCalendar_plugin_url( 'css/layouts/timeline.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'modern':
	
			wp_enqueue_style( 'dpProEventCalendar_modern', dpProEventCalendar_plugin_url( 'css/layouts/modern.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'yearly':
	
			wp_enqueue_style( 'dpProEventCalendar_yearly', dpProEventCalendar_plugin_url( 'css/layouts/yearly.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'cover':

			wp_enqueue_style( 'dpProEventCalendar_cover', dpProEventCalendar_plugin_url( 'css/layouts/cover.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'carousel':

			wp_enqueue_style( 'dpProEventCalendar_carousel', dpProEventCalendar_plugin_url( 'css/layouts/carousel.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'slider':
		
			wp_enqueue_style( 'dpProEventCalendar_slider', dpProEventCalendar_plugin_url( 'css/layouts/slider.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'card':
		
			wp_enqueue_style( 'dpProEventCalendar_card', dpProEventCalendar_plugin_url( 'css/layouts/card.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'countdown':
	
			wp_enqueue_style( 'dpProEventCalendar_countdown', dpProEventCalendar_plugin_url( 'css/layouts/countdown.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

		case 'grid-upcoming':
	
			wp_enqueue_style( 'dpProEventCalendar_grid', dpProEventCalendar_plugin_url( 'css/layouts/grid.css' ),
				array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
			break;

	}

	// Skins styles

	if( $skin == 'dark' ) 
	{
		wp_enqueue_style( 'dpProEventCalendar_dark', dpProEventCalendar_plugin_url( 'css/layouts/dark.css' ),
			array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
	}

	if( $skin != '' && $skin != 'dark' ) 
	{
		wp_enqueue_style( 'dpProEventCalendar_skin', dpProEventCalendar_plugin_url( 'css/layouts/skin.css' ),
			array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER );
	}

	// Datepicker Stuff

	wp_enqueue_style( 'jquery-ui-datepicker-style' , dpProEventCalendar_plugin_url( 'css/jquery.datepicker.min.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		
	
	// RTL Right to Left Support

	if((isset($dpProEventCalendar['rtl_support']) && $dpProEventCalendar['rtl_support']) || $rtl || is_rtl()) {
		wp_enqueue_style( 'dpProEventCalendar_rtlcss', dpProEventCalendar_plugin_url( 'css/rtl.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
	}

}


?>