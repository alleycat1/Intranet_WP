<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//Admin functions
class DPPEC_Admin {

	public $menu_items 	= array();
	public $layouts 	= array();
	public $form_fields = array();

	private $form;
	
	/**
	 * Class Constructor
	 * 
	 * @return void
	 */
	function __construct( ) 
	{

		// Admin Stuff
		$this->admin_stuff();

		// Check Updates
		$this->update_check();

		// Admin notices
		add_action('admin_notices', array($this, 'admin_notices'));

    }

    /**
	 * Admin Functions / actions / filters
	 * 
	 * @return void
	 */
    private function admin_stuff()
    {

    	// Define Admin Const
    	define( 'DP_PRO_EVENT_CALENDAR_ADMIN_CALENDARS_LIST_LIMIT', 10 );
    	define( 'DP_PRO_EVENT_CALENDAR_ADMIN_BOOKINGS_LIST_LIMIT', 30 );

    	// Set menu items 
		$this->set_menu_items();

		// Set layouts 
		$this->set_layouts();

    	// Adds settings to Network Settings
		add_filter( 'wpmu_options'       , array( $this, 'show_network_settings' ) );
		add_action( 'update_wpmu_options', array( $this, 'save_network_settings' ) );

		// Enqueue Scripts
		add_action( 'admin_init', array($this, 'admin_scripts') );
		add_action( 'pec_enqueue_admin', array($this, 'admin_scripts') );

		add_action( 'admin_head', array($this, 'admin_head') );

		// Register the wp admin dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		// Load Admin Panel
		$this->load_admin_panel();

		// Event list Columns filter and order
		$this->event_columns();

		// Duplicate bulk action
		add_action('admin_footer-edit.php', array( $this, 'bulk_admin_footer' ) );
		add_action('load-edit.php', array( $this, 'bulk_action' ) );

		// Title text filter
		add_filter( 'enter_title_here', array( $this, 'change_title_text' ) );

		// Plugin list link
		add_filter( 'plugin_action_links_dpProEventCalendar/dpProEventCalendar.php', array( $this, 'settings_link' ) );

		// After update / publish Event Hook
		add_action('edit_post', array( $this, 'edit_event_hook' ) );
		add_action('publish_post', array( $this, 'edit_event_hook' ) );
		add_action('wp_trash_post', array( $this, 'edit_event_hook' ) );
		add_action('untrash_post', array( $this, 'edit_event_hook' ) );
		add_action('delete_post', array( $this, 'edit_event_hook' ) );

		// Category custom fields
		add_action( 'pec_events_category_edit_form_fields', array( $this, 'category_edit_form_fields' ) );
		add_action( 'edited_pec_events_category', array( $this, 'category_edit_save' ) );
		add_action( 'create_pec_events_category', array( $this, 'category_edit_save' ) );
		add_action( 'pec_events_category_add_form_fields', array( $this, 'category_edit_form_fields' ) );

		// Add metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );

		$this->form = new DPPEC_AdminForm();

    }

    /**
	 * Add links to plugin list
	 * 
	 * @return void
	 */
	function settings_link( $links ) 
	{
	
		// Build and escape the URL.
	
		$url_settings = esc_url( add_query_arg(
			'page',
			'dpProEventCalendar-settings',
			get_admin_url() . 'admin.php'
		) );

		$start_tour = esc_url( add_query_arg(
			'page',
			'dpProEventCalendar-admin&pec-tour=1',
			get_admin_url() . 'admin.php'
		) );
	
		// Create the link.
	
		$settings_link = "<a href='$url_settings'>" . __( 'Settings', 'dpProEventCalendar' ) . '</a>';
		//$tour_link = "<a href='$start_tour'>" . __( 'Take a Tour', 'dpProEventCalendar' ) . '</a>';
	
		// Adds the link to the end of the array.
	
		array_push(
			$links,
			$settings_link
		);
	
		return $links;
	
	}

    /**
	 * Check if vars are set and return default value if not
	 * 
	 * @return void
	 */
    function get( $var, $default = '' )
    {

    	if( isset( $_GET[$var] ) )
			return $_GET[$var];

		return $default;

    }

    /**
	 * Load Admin panel pages
	 * 
	 * @return void
	 */
    private function load_admin_panel()
    {

    	global $pagenow;

    	// Import FB events
    	require_once( DP_PRO_EVENT_CALENDAR_CLASSES_DIR . 'import-facebook.class.php' );

    	// Include Admin Pages
    	require_once( DP_PRO_EVENT_CALENDAR_SETTINGS_DIR . 'events-meta.php' );

		require_once( DP_PRO_EVENT_CALENDAR_SETTINGS_DIR . 'settings.php' );

		if( $pagenow == 'admin.php' && $this->get( 'page' ) == 'dpProEventCalendar-admin' )
			require_once( DP_PRO_EVENT_CALENDAR_SETTINGS_DIR . 'calendars.php' );
		
		if( $pagenow == 'admin.php' && $this->get( 'page' ) == 'dpProEventCalendar-special' )
			require_once( DP_PRO_EVENT_CALENDAR_SETTINGS_DIR . 'special.php' );

		if( $pagenow == 'admin.php' && $this->get( 'page' ) == 'dpProEventCalendar-custom-shortcodes' )
			require_once( DP_PRO_EVENT_CALENDAR_SETTINGS_DIR . 'custom_shortcodes.php' );

    }

    /**
	 * Filters / Actions for Event post type list columns
	 * 
	 * @return void
	 */
    private function event_columns()
    {

    	add_action( 'manage_posts_columns', array( $this, 'add_column_to_events_list' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'column_for_events_list' ), 10, 2 );
		add_filter( 'manage_edit-pec-events_sortable_columns', array( $this, 'column_register_sortable' ) );
		add_filter( 'request', array( $this, 'column_orderby' ) );
		add_filter( 'manage_posts_columns' , array( $this, 'manage_columns' ) );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_events_by_calendar' ) );
		add_filter( 'parse_query', array( $this, 'convert_filter' ) );

    }

    /**
	 * Display an update notice after form is submited
	 * 
	 * @return void
	 */
    function admin_notices()
    {

    	global $post_type, $pagenow;
	
		if( $pagenow == 'edit.php' && $post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE &&
		
			isset( $_REQUEST['duplicated'] ) && (int) $_REQUEST['duplicated'] ) {
			
			$message = sprintf( _n( 'Post duplicated.', '%s posts duplicated.', $_REQUEST['duplicated'] ), number_format_i18n( $_REQUEST['duplicated'] ) );
			
			echo "<div class='updated'><p>{$message}</p></div>";
		
		}
		
		if( $this->get( 'settings-updated' ) && ( strpos( $this->get( 'page' ), 'dpProEventCalendar' ) !== false ) ) 
		{

		    echo '<div class="updated">';

		    echo 	'<p>' . __('Updated Succesfully.', 'dpProEventCalendar') . '</p>';

		    echo '</div>';

		}

		if ( wp_script_is( 'dpProEventCalendar' ) && false ) {

			if( ! $this->get( 'pec-tour' ) ) {

				if ( ! esc_attr( get_option( 'pec-hide-tour' ) ) ) 
				{
				
			   		echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('Welcome!', 'dpProEventCalendar') . '
						<a href="admin.php?page=dpProEventCalendar-admin&pec-tour=1" class="pec-tour-start">' . __('Take a Tour', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('No, thanks!', 'dpProEventCalendar') . '</a>
						</h3>
			   			<hr />
			          	<p>' . __('Thank you for installing the Pro Event Calendar plugin.', 'dpProEventCalendar') . '</p>
						<p>' . __('If it\'s your first time using this plugin, you can use a guided tour to get started.', 'dpProEventCalendar') . '</p>
						
			         </div>';
				
				}

			} else {

				switch( $this->get( 'pec-tour' ) ) {

					case 1:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('1. Create a Calendar', 'dpProEventCalendar') . '
			   			<a href="admin.php?add=1&page=dpProEventCalendar-admin&pec-tour=2" class="pec-tour-next">' . __('Next - Calendar Form', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('Cancel Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('First you need to create a calendar. Use the button in the top right corner.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 2:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('2. Fill Calendar Form', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-admin&pec-tour=3" class="pec-tour-next">' . __('Next - Copy Shortcode', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('To add a new calendar, fill the form below and click on Save.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 3:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('3. Insert the calendar in a post or page', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-custom-shortcodes&pec-tour=4" class="pec-tour-next">' . __('Next - Custom Shortcodes', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 4:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('4. Custom Shortcodes', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-admin&pec-tour=5" class="pec-tour-next">' . __('Next - Add New Events', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 5:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('5. Add New Events', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-special&pec-tour=6" class="pec-tour-next">' . __('Next - Create Colors', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 6:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('6. Create Colors', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-admin&pec-tour=7" class="pec-tour-next">' . __('Next - Set Special Dates', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 7:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('7. Set Special Dates', 'dpProEventCalendar') . '
			   			<a href="admin.php?page=dpProEventCalendar-settings&pec-tour=8" class="pec-tour-next">' . __('Next - General Settings', 'dpProEventCalendar') . '</a>
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

					case 8:

						echo '<div class="notice notice-info pec-tour-msg">
			   			<h3>
			   			' . __('8. General Settings', 'dpProEventCalendar') . '
			   			<a href="#" class="pec-tour-cancel">' . __('End Tour', 'dpProEventCalendar') . '</a>
			   			</h3>
			   			<hr />
			   			<p>' . __('After the creation of a calendar, you can copy it\'s default shortcode and paste it in the content of a post or a page.', 'dpProEventCalendar') . '</p>
			   			<p>' . __('You can also create custom shortcodes with different layouts from the "Custom Shortcodes" menu.', 'dpProEventCalendar') . '</p>
			   			</div>';
						break;

				}

			}

		}
	
	}

	/**
	 * Print a tour pointer
	 * 
	 * @return void
	 */
	function tour_pointer( $css ) 
	{

		echo '<span class="pec-tour-pointer ' . $css . '"></span>';

	}

	/**
	 * Check for new plugin updates
	 * 
	 * @return void
	 */
    function update_check() 
    {

		/* Update checker */
		if( pec_setting( 'purchase_code' ) != "" || esc_attr( get_site_option( 'pec-purchase-code' ) ) != "" ) 
		{

			require_once ( DP_PRO_EVENT_CALENDAR_INCLUDES_DIR . 'plugin-update-checker/plugin-update-checker.php' );

			$myUpdateChecker = PucFactoryCustom::buildUpdateChecker(
			    DP_PRO_EVENT_CALENDAR_VERSION_CHECKER,
			    DP_PRO_EVENT_CALENDAR_PLUGIN_FILE
			);

			// Payments plugin active

			if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) 
			{

				if( function_exists( 'pec_payments_setting' ) && pec_payments_setting ( 'purchase_code' ) != "" )
				{
					$myUpdateChecker_payments = PucFactoryCustom::buildUpdateChecker(
					    DP_PRO_EVENT_CALENDAR_PAYMENTS_VERSION_CHECKER,
					    DP_PRO_EVENT_CALENDAR_PAYMENTS_FILE
					);
				}
				
			}

		}

	}

	function set_form_fields ()
	{

		$this->form_fields[] = array( 'name' => 'form_show_end_date', 'title' => __('Show End date', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_start_time', 'title' => __('Show Start Time', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_end_time', 'title' => __('Show End time', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_extra_dates', 'title' => __('Show Extra Dates', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_description', 'title' => __('Show Description', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_category', 'title' => __('Show Category Dropdown', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_hide_time', 'title' => __('Show \'Hide Time\' option', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_frequency', 'title' => __('Show Frequency', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_all_day', 'title' => __('Show All Day Option', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_image', 'title' => __('Allow to upload an image', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_link', 'title' => __('Show Link Field', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_speakers', 'title' => __('Show Speakers', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_location', 'title' => __('Show Location / Venue dropdown', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_location_options', 'title' => __('Allow users to add new locations / venues', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_phone', 'title' => __('Show Phone option', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_color', 'title' => __('Show \'Color\' option', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_timezone', 'title' => __('Show Timezone', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_booking_enable', 'title' => __('Show Booking Enable checkbox', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_booking_limit', 'title' => __('Show Booking Limite', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_booking_price', 'title' => __('Show Booking Price', 'dpProEventCalendar') );
		$this->form_fields[] = array( 'name' => 'form_show_booking_block_hours', 'title' => __('Show Booking Block Hours', 'dpProEventCalendar') );

	}

	/**
	 * Set the included Layouts
	 * 
	 * @return void
	 */
    function set_layouts ()
    {

    	$this->layouts[] = array( 'title' => __('Default','dpProEventCalendar'), 'value' => '');
    	$this->layouts[] = array( 'title' => __('Upcoming Events','dpProEventCalendar'), 'value' => 'upcoming');
    	$this->layouts[] = array( 'title' => __('Past Events','dpProEventCalendar'), 'value' => 'past');
    	$this->layouts[] = array( 'title' => __('Accordion List','dpProEventCalendar'), 'value' => 'accordion');
    	$this->layouts[] = array( 'title' => __('Accordion Upcoming Events','dpProEventCalendar'), 'value' => 'accordion-upcoming');
    	$this->layouts[] = array( 'title' => __('Add Event','dpProEventCalendar'), 'value' => 'add-event');
    	$this->layouts[] = array( 'title' => __('List Events by Author','dpProEventCalendar'), 'value' => 'list-author');
    	$this->layouts[] = array( 'title' => __('Calendar by Author','dpProEventCalendar'), 'value' => 'calendar-author');
    	$this->layouts[] = array( 'title' => __('List of Bookings by Logged in User','dpProEventCalendar'), 'value' => 'bookings-user');
    	$this->layouts[] = array( 'title' => __('Cover (Single Event)','dpProEventCalendar'), 'value' => 'cover');
    	$this->layouts[] = array( 'title' => __('Today Events','dpProEventCalendar'), 'value' => 'today-events');
    	$this->layouts[] = array( 'title' => __('Google Map Upcoming Events','dpProEventCalendar'), 'value' => 'gmap-upcoming');
    	$this->layouts[] = array( 'title' => __('Grid Upcoming Events','dpProEventCalendar'), 'value' => 'grid-upcoming');
    	$this->layouts[] = array( 'title' => __('Booking Button by Event','dpProEventCalendar'), 'value' => 'book-btn');
    	$this->layouts[] = array( 'title' => __('Card','dpProEventCalendar'), 'value' => 'card');
    	$this->layouts[] = array( 'title' => __('Slider','dpProEventCalendar'), 'value' => 'slider');
    	$this->layouts[] = array( 'title' => __('Slider 2','dpProEventCalendar'), 'value' => 'slider-2');
    	$this->layouts[] = array( 'title' => __('Slider 3','dpProEventCalendar'), 'value' => 'slider-3');
    	$this->layouts[] = array( 'title' => __('Carousel','dpProEventCalendar'), 'value' => 'carousel');
    	$this->layouts[] = array( 'title' => __('Carousel 2','dpProEventCalendar'), 'value' => 'carousel-2');
    	$this->layouts[] = array( 'title' => __('Carousel 3','dpProEventCalendar'), 'value' => 'carousel-3');
    	$this->layouts[] = array( 'title' => __('Yearly','dpProEventCalendar'), 'value' => 'yearly');
    	$this->layouts[] = array( 'title' => __('Compact','dpProEventCalendar'), 'value' => 'compact');
    	$this->layouts[] = array( 'title' => __('Modern','dpProEventCalendar'), 'value' => 'modern');
    	$this->layouts[] = array( 'title' => __('Compact Upcoming Events','dpProEventCalendar'), 'value' => 'compact-upcoming');
    	$this->layouts[] = array( 'title' => __('List Upcoming Events','dpProEventCalendar'), 'value' => 'list-upcoming');
    	$this->layouts[] = array( 'title' => __('Countdown','dpProEventCalendar'), 'value' => 'countdown');
    	$this->layouts[] = array( 'title' => __('Timeline','dpProEventCalendar'), 'value' => 'timeline');

    }

    /**
	 * Set the menu items
	 * 
	 * @return void
	 */
    function set_menu_items ()
    {

    	add_action( 'admin_menu', array( $this, 'menu_settings' ) );

    	$this->menu_items[] = array( 'title' => __('General Settings','dpProEventCalendar'), 'href' => 'admin.php?page=dpProEventCalendar-settings');
    	$this->menu_items[] = array( 'title' => __('Calendars','dpProEventCalendar'), 'href' => 'admin.php?page=dpProEventCalendar-admin');
    	$this->menu_items[] = array( 'title' => __('Events','dpProEventCalendar'), 'href' => 'edit.php?post_type=pec-events');
    	$this->menu_items[] = array( 'title' => __('Categories','dpProEventCalendar'), 'href' => 'edit-tags.php?taxonomy=pec_events_category');
    	$this->menu_items[] = array( 'title' => __('Venues','dpProEventCalendar'), 'href' => 'edit.php?post_type=pec-venues');
    	$this->menu_items[] = array( 'title' => __('Organizers','dpProEventCalendar'), 'href' => 'edit.php?post_type=pec-organizers');
    	$this->menu_items[] = array( 'title' => __('Speakers','dpProEventCalendar'), 'href' => 'edit.php?post_type=pec-speakers');
    	$this->menu_items[] = array( 'title' => __('Special Dates / Event Color','dpProEventCalendar'), 'href' => 'admin.php?page=dpProEventCalendar-special');
    	$this->menu_items[] = array( 'title' => __('Custom Shortcodes'), 'href' => 'admin.php?page=dpProEventCalendar-custom-shortcodes');

    	if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) 
    	{
    		$this->menu_items[] = array( 'title' => __('Payments Options'), 'href' => 'admin.php?page=dpProEventCalendar-payments');
    		$this->menu_items[] = array( 'title' => __('Coupons'), 'href' => 'edit.php?post_type=pec-coupons');
    	}

    	$this->menu_items[] = array( 'title' => __('Documentation'), 'href' => DP_PRO_EVENT_CALENDAR_DOCUMENTATION);


    }

    /**
	 * Print the menu for admin panel
	 * 
	 * @return void
	 */
    function template_left ()
    {

    	$html = '
    	<div id="leftSide">
        	<div id="dp_logo"><h1><i class="dashicons dashicons-calendar"></i> ' . DP_PRO_EVENT_CALENDAR_TITLE . '</h1></div>
            <p>
                Version: ' . DP_PRO_EVENT_CALENDAR_VER . '<br />
            </p>
            <ul id="menu" class="nav">';

        	if(is_array($this->menu_items))
        	{
            	foreach ($this->menu_items as $key)
            	{

            		$html .= '<li>
            					<a href="' . $key['href'] . '" ' . ( strpos($_SERVER['REQUEST_URI'], $key['href']) ? 'class="active"' : '') . ' title="' . $key['title'] . '">
            						<span>' . $key['title'] . '</span>
            					</a>
            				</li>';

            	}
            }
        
        $html .= '
            </ul>

            <a href="https://codecanyon.net/downloads" target="_blank" class="rate_plugin"> ' .
                __('Rate this plugin!','dpProEventCalendar') . '
                <br>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
            </a>
            
            <div class="clear"></div>
		</div>';

		echo $html;

    }

    /**
	 * Print tooltips for admin fields
	 * 
	 * @return void
	 */
    function show_info ( $text )
    {

    	$html = '<span class="pec_info dashicons dashicons-info"><span>' . $text . '</span></span>';

    	echo $html;

    }

    /**
	 * Duplicate calendar
	 * 
	 * @return void
	 */
   	private function duplicate_calendar ( ) 
    {

		global $wpdb;
		
		$calendar = $this->get( 'duplicate' );

		$results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . ' WHERE id = %d', $calendar), ARRAY_A );

		unset( $results['id'] );

		$wpdb->insert( DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS, $results );
		
		wp_redirect( admin_url( '/admin.php?page=dpProEventCalendar-admin&settings-updated=1' ) );
			
		exit();

	}

    /**
	 * Export calendar to JSON
	 * 
	 * @return void
	 */
    private function export_calendar ( ) 
    {

    	global $wpdb;

		$calendar = $this->get( 'export_calendar' );
		
		header( 'Content-Disposition: attachment; filename="calendar_'.$calendar.'.json";' );

		$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . ' WHERE id = %d', $calendar ), OBJECT );

		echo json_encode( $results );
		
		die();

	}

	/**
	 * Import Calendar from JSON file
	 * 
	 * @return void
	 */
	function import_calendar ( ) 
    {

    	global $wpdb;
		
    	$filename = $_FILES['pec_calendar_json']['tmp_name'];

    	// Get File content
    	$content = file_get_contents($filename);

    	// JSON decode
    	$calendar_data = json_decode($content, true);

    	if( ! isset( $_POST['pec_keep_calendar_id'] ) || $_POST['pec_keep_calendar_id'] != 1 )
	    	unset( $calendar_data['id'] );

		$wpdb->insert( DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS, $calendar_data );


	}

    /**
	 * Include admin scripts
	 * 
	 * @return void
	 */
	function admin_scripts( $force = false ) 
	{

		// Remove unnecessary metaboxes
		$this->remove_meta_box();

		// Export Calendar
		if( is_numeric( $this->get( 'export_calendar' ) ) && 'dpProEventCalendar-admin' == $this->get( 'page' ) )
			$this->export_calendar();

		// Duplicate Calendar
		if( is_numeric( $this->get( 'duplicate' ) ) && 'dpProEventCalendar-admin' == $this->get( 'page' ) )
			$this->duplicate_calendar();

		// Specify pages

		if ( $force || ( ('dpProEventCalendar-admin' == $this->get( 'page' )
		or 'dpProEventCalendar-settings' == $this->get( 'page' ) 
		or 'dpProEventCalendar-events' == $this->get( 'page' )
		or 'dpProEventCalendar-special' == $this->get( 'page' ) 
		or 'dpProEventCalendar-import' == $this->get( 'page' ) 
		or 'dpProEventCalendar-custom-shortcodes' == $this->get( 'page' )
		or 'dpProEventCalendar-eventdata' == $this->get( 'page' ) 
		or 'dpProEventCalendar-payments' == $this->get( 'page' ) ))  ) {


		wp_enqueue_script( 'jquery' );
	
		wp_enqueue_script( 'jquery-ui-datepicker'); 

		wp_enqueue_style( 'dpProEventCalendar_headcss', dpProEventCalendar_plugin_url( 'css/dpProEventCalendar.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');

		// Admin Styles
		wp_enqueue_style( 'dpProEventCalendar_admin_head_css', dpProEventCalendar_plugin_url( 'css/admin-styles.css' ),
			array( 'dpProEventCalendar_headcss' ), DP_PRO_EVENT_CALENDAR_VER, 'all');

		// Datepicker Styles
		wp_enqueue_style( 'jquery-ui-datepicker-style' , dpProEventCalendar_plugin_url( 'css/jquery.datepicker.min.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		
		wp_enqueue_script( 'dpProEventCalendar', dpProEventCalendar_plugin_url( 'js/jquery.dpProEventCalendar.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_localize_script( 'dpProEventCalendar', 'ProEventCalendarAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'postEventsNonce' => wp_create_nonce( 'ajax-get-events-nonce' ) ) );

		wp_enqueue_script( 'colorpicker2', dpProEventCalendar_plugin_url( 'js/colorpicker.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 

		wp_enqueue_script( 'selectric', dpProEventCalendar_plugin_url( 'js/jquery.selectric.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 

		wp_enqueue_media();

		// Admin JS Scripts
		wp_enqueue_script ( 'dpProEventCalendar_admin', dpProEventCalendar_plugin_url( 'js/admin_settings.js' ), array('jquery-ui-dialog') ); 
    	wp_enqueue_style ( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( array( 'jquery', 'editor', 'thickbox', 'media-upload', 'word-count', 'post' ) );
		
		wp_enqueue_style( 'colorpicker', dpProEventCalendar_plugin_url( 'css/colorpicker.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		};
		
		wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=' . pec_setting( 'google_map_key' ),
			null, DP_PRO_EVENT_CALENDAR_VER, false ); 

		// Set Font Awesome URL

		$fontawesome_url = DP_PRO_EVENT_CALENDAR_FONT_AWESOME_JS;

		if( pec_setting( 'fontawesome_url' ) != '' ) 
			$fontawesome_url = pec_setting( 'fontawesome_url' );
			
		wp_enqueue_script( 'font-awesome', $fontawesome_url); 
		
		wp_enqueue_style('thickbox');

	}

	/**
	 * Print Admin JS code
	 * 
	 * @return void
	 */
	function admin_head() 
	{

	  	// Special Dates page only
		if ( 'dpProEventCalendar-special' == $this->get( 'page' ) ) 
		{
		
		?>
			<script type="text/javascript">
			// <![CDATA[
				function confirmSpecialDelete()
				{
					var agree = confirm( "<?php _e("Delete this Special Date?", "dpProEventCalendar");?>" );
					if ( agree )
					return true ;
					else
					return false ;
				}
				
				function special_checkform ()
				{
					if (document.getElementById('dpProEventCalendar_title').value == "") {
						alert( "<?php _e("Please enter the title of the special date.", "dpProEventCalendar");?>" );
						document.getElementById('dpProEventCalendar_title').focus();
						return false ;
					}
					return true ;
				}
				
				function special_checkform_edit ()
				{
					if (document.getElementById( 'dpPEC_special_title' ).value == "") {
						alert( "<?php _e("Please enter the title of the special date.", "dpProEventCalendar");?>" );
						document.getElementById( 'dpPEC_special_title' ).focus();
						return false ;
					}
					return true ;
				}
				
				jQuery(document).ready(function() {
					jQuery('#specialDate_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn('fast');
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut('fast');
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#specialDate_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_color').val('#' + hex);
						}
					});
					
					jQuery('#specialDate_colorSelector_Edit').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn('fast');
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut('fast');
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#specialDate_colorSelector_Edit div').css('backgroundColor', '#' + hex);
							jQuery('#dpPEC_special_color').val('#' + hex);
						}
					});
				});
			//]]>
			</script>
	<?php
	   } 
	   
	   // Calendars page only
		if ( 'dpProEventCalendar-admin' == $this->get( 'page' )
				|| 'dpProEventCalendar-payments' == $this->get( 'page' ) 
				|| 'dpProEventCalendar-settings' == $this->get( 'page' )
				|| 'dpProEventCalendar-custom-shortcodes' == $this->get( 'page' ) ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				function confirmCalendarDelete()
				{
					var agree=confirm("<?php echo __("Are you sure?", "dpProEventCalendar")?>");
					if (agree)
					return true ;
					else
					return false ;
				}
				
				function confirmCalendarEventsDelete()
				{
					var agree=confirm("<?php echo __("All the events in this calendar will be deleted. Are you sure?", "dpProEventCalendar")?>");
					if (agree)
					return true ;
					else
					return false ;
				}
				
				function calendar_checkform ()
				{
					if (document.getElementById('dpProEventCalendar_title').value == "") {
						alert( "Please enter the title of the calendar." );
						document.getElementById('dpProEventCalendar_title').focus();
						return false ;
					}
					
					return true ;
				}
				
				function toggleFormat() {
					if(jQuery('#dpProEventCalendar_show_time').prop('checked')) {
						jQuery('#div_time_extended').slideDown('fast');
					} else {
						jQuery('#div_time_extended').slideUp('fast');
					}
				}
				
				function toggleTranslations() {
					if(jQuery('#dpProEventCalendar_enable_wpml').prop('checked')) {
						jQuery('#div_translations_fields').slideUp('fast');
					} else {
						jQuery('#div_translations_fields').slideDown('fast');
					}
				}
				
				function toggleNewEventRoles() {
					if(jQuery('#dpProEventCalendar_allow_user_add_event').prop('checked')) {
						jQuery('#allow_user_add_event_roles').slideDown('fast');
					} else {
						jQuery('#allow_user_add_event_roles').slideUp('fast');
					}
				}
				
				function toggleFormatCategories() {
					if(jQuery('#dpProEventCalendar_show_category_filter').prop('checked')) {
						jQuery('#div_category_filter').slideDown('fast');
					} else {
						jQuery('#div_category_filter').slideUp('fast');
					}
				}

				function toggleFormatVenues() {
					if(jQuery('#dpProEventCalendar_show_location_filter').prop('checked')) {
						jQuery('#div_venue_filter').slideDown('fast');
					} else {
						jQuery('#div_venue_filter').slideUp('fast');
					}
				}
				
				function showAccordion(div, elem) {
					if(jQuery('#'+div).css('display') == 'none') {
						jQuery('#'+div).addClass('pec_admin_accordion_show');
						jQuery(elem).addClass('dp_ui_on');
					} else {
						jQuery('#'+div).removeClass('pec_admin_accordion_show');
						jQuery(elem).removeClass('dp_ui_on');
					}
				}
				
				jQuery(document).ready(function() {
					
					var custom_uploader;


				    jQuery('#upload_image_button').click(function(e) {

				        e.preventDefault();

				        //If the uploader object has already been created, reopen the dialog
				        if (custom_uploader) {
				            custom_uploader.open();
				            return;
				        }

				        //Extend the wp.media object
				        custom_uploader = wp.media.frames.file_frame = wp.media({
				            title: '<?php esc_attr_e('Choose Image', 'dpProEventCalendar')?>',
				            button: {
				                text: '<?php esc_attr_e('Choose Image', 'dpProEventCalendar')?>'
				            },
				            multiple: true
				        });

				        //When a file is selected, grab the URL and set it as the text field's value
				        custom_uploader.on('select', function() {
				            attachment = custom_uploader.state().get('selection').first().toJSON();
				            jQuery('#dpProEventCalendar_options_map_marker').val(attachment.url);
				        });

				        //Open the uploader dialog
				        custom_uploader.open();

				    });
					
					jQuery('#currentDate_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#currentDate_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_current_date_color').val('#' + hex);
						}
					});

					jQuery('#bookedEvent_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#bookedEvent_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_booking_event_color').val('#' + hex);
						}
					});

					jQuery(".pec_calendar_shortcode, .pec_custom_shortcode").on("focus", function () {
					    jQuery(this).selectText();
					});
					jQuery(".pec_calendar_shortcode, .pec_custom_shortcode").on("click", function () {
					    jQuery(this).selectText();
					});
					
				});

				jQuery.fn.selectText = function(){
				   var doc = document;
				   var element = this[0];
				   
				   if (doc.body.createTextRange) {
				       var range = document.body.createTextRange();
				       range.moveToElementText(element);
				       range.select();
				   } else if (window.getSelection) {
				       var selection = window.getSelection();        
				       var range = document.createRange();
				       range.selectNodeContents(element);
				       selection.removeAllRanges();
				       selection.addRange(range);
				   }
				};
			//]]>
			</script>
	<?php
		}
	   // Settings page only
		if ( 'dpProEventCalendar-settings' == $this->get( 'page' ) ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function() {
					jQuery('#holidays_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#holidays_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_holidays_color').val('#' + hex);
						}
					});
				});
			//]]>
			</script>
	<?php
	   } //Settings page only
	   
	   // Import page only
		if ( 'dpProEventCalendar-import' == $this->get( 'page' ) ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				function import_checkform ()
				{
					return true;
				}
			//]]>
			</script>
	<?php
	   } //Settings page only
	   
	}


	/**
	 * Events Listing column
	 * 
	 * @return void
	 */
	function add_column_to_events_list( $posts_columns ) 
	{
	
	    global $typenow;

	    if ( $typenow != DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE && $typenow != DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE && $typenow != DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE && $typenow != DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE ) return $posts_columns;

		if ( $typenow == DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE ) 
		{
		
			$new_posts_columns = array();
	        $index = 0;
	        foreach( $posts_columns as $key => $posts_column ) 
	        {
	            if ( $key=='date' ) 
	            {
	                $new_posts_columns['address'] = null;
	                $new_posts_columns['link'] = null;
	                $new_posts_columns['events'] = null;
	            }

	            $new_posts_columns[$key] = $posts_column;
	        }

			$new_posts_columns['address'] = __('Address', 'dpProEventCalendar');
			$new_posts_columns['link'] = __('Link', 'dpProEventCalendar');
			$new_posts_columns['events'] = __('Events', 'dpProEventCalendar');

		}

		if ( $typenow == DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE ) 
		{
		
			$new_posts_columns = array();
	        $index = 0;
	        foreach( $posts_columns as $key => $posts_column ) 
	        {
	            if ( $key=='date' ) 
	            {
	                $new_posts_columns['events'] = null;
	            }

	            $new_posts_columns[$key] = $posts_column;
	        }

			$new_posts_columns['events'] = __('Events', 'dpProEventCalendar');

		}

		if ( $typenow == DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE ) 
		{
		
			$new_posts_columns = array();
	        $index = 0;
	        foreach( $posts_columns as $key => $posts_column ) 
	        {
	            if ( $key=='date' ) 
	            {
	                $new_posts_columns['events'] = null;
	            }

	            $new_posts_columns[$key] = $posts_column;
	        }

			$new_posts_columns['events'] = __('Events', 'dpProEventCalendar');

		}

	    if ( $typenow == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
	    {

			if ( !isset( $posts_columns['author'] ) )
		        $new_posts_columns = $posts_columns;
		    else {

		        $new_posts_columns = array();
		        $index = 0;
		        foreach($posts_columns as $key => $posts_column) 
		        {
		            if ($key=='author')
		                $new_posts_columns['calendar'] = null;

		            $new_posts_columns[$key] = $posts_column;
		        }

		    }
		    $new_posts_columns['calendar'] = __('Calendar', 'dpProEventCalendar');
			$new_posts_columns['start_date'] = __('Date', 'dpProEventCalendar');
			$new_posts_columns['end_date'] = __('End Date', 'dpProEventCalendar');
			$new_posts_columns['frequency'] = __('Frequency', 'dpProEventCalendar');
			$new_posts_columns['bookings'] = __('Bookings', 'dpProEventCalendar');

		}

	    return $new_posts_columns;
	}

	function column_for_events_list( $column_id,$post_id ) 
	{

	    global $typenow, $current_user, $wpdb, $pec_init;
	    
	    if( $typenow == DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE ) 
	    {
	    
	    	switch ($column_id) 
	    	{
			
				case 'address':
			
					$address = get_post_meta($post_id, 'pec_venue_address', true);
					echo $address;
					break;
			
				case 'link':
			
					$link = get_post_meta($post_id, 'pec_venue_link', true);
					echo $link;
					break;
			
				case 'events':
			
					$args = array( 
						'posts_per_page' => -1, 
						'fields' => 'ids',
						'no_found_rows' => true,
						'post_type' => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
						'meta_key' => 'pec_location',
						'meta_value' => $post_id
					);

					$events = '0';

				    $events_count = get_posts( $args );

					$events = count($events_count);
					if( $events != 0 )
						$events = '<a href="' . admin_url( 'edit.php?post_type=pec-events&pec_id_location=' . $post_id ) . '">' . $events . '</a>';

					echo $events;
					break;
			}
	    }

	    if( $typenow == DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE ) 
	    {
	    
	    	switch ($column_id) 
	    	{
			
				case 'events':
			
					$args = array( 
						'posts_per_page' => -1, 
						'fields' => 'ids',
						'no_found_rows' => true,
						'post_type' => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
						'meta_query' => array( 
							'relation' => 'OR',
							array('key' => "pec_speaker", "value" => '(,'.$post_id.',)', 'compare' => 'REGEXP'),
							array('key' => "pec_speaker", "value" => '(,'.$post_id.')$', 'compare' => 'REGEXP'),
							array('key' => "pec_speaker", "value" => '^('.$post_id.',)', 'compare' => 'REGEXP'),
							array('key' => "pec_speaker", "value" => $post_id)
						)
					);

					$events = '0';

				    $events_count = get_posts( $args );

					$events = count( $events_count );
					if( $events != 0 )
						$events = '<a href="' . admin_url( 'edit.php?post_type=pec-events&pec_id_speaker=' . $post_id ) . '">' . $events . '</a>';

					echo $events;
					break;
			}
	    }

	    if( $typenow == DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE ) 
	    {
	    
	    	switch ($column_id) 
	    	{
			
				case 'events':
			
					$args = array( 
						'posts_per_page' => -1, 
						'fields' => 'ids',
						'no_found_rows' => true,
						'post_type' => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 
						'meta_key' => 'pec_organizer',
						'meta_value' => $post_id
					);

					$events = '0';

				    $events_count = get_posts( $args );

					$events = count( $events_count );
					if( $events != 0 )
						$events = '<a href="' . admin_url( 'edit.php?post_type=pec-events&pec_id_organizer=' . $post_id ) . '">' . $events . '</a>';

					echo $events;
					break;
			}
	    }

	    if( $typenow == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
	    {
			
	        switch ( $column_id ) 
	        {
			
				case 'calendar':
			
					$id_calendar = get_post_meta( $post_id, 'pec_id_calendar', true );
					
					if ( isset( $id_calendar ) ) 
					{
						
						$cal_list = explode( ",", $id_calendar );
						
						$calendar = "";
						
						$count = 0;
						foreach( $cal_list as $key ) 
						{
							$opts = array();
							$opts['id_calendar'] = $key;
							$opts['is_admin'] = true;
							$dpProEventCalendar_class = $pec_init->init_base( $opts );
							
							if($count > 0) 
								$calendar .= ' - ';	
							
							$calendar .= '<a href="'.admin_url( 'admin.php?page=dpProEventCalendar-admin&edit=' . $key ) . '">' . $dpProEventCalendar_class->getCalendarName() . '</a>';
							$count++;
						}
						echo $calendar;
					}
					break;
				
				case 'start_date':

					$pec_date = get_post_meta( $post_id, 'pec_date', true );
					echo '<abbr title="' . date_i18n( get_option( 'date_format' ), strtotime( $pec_date ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $pec_date ) ).'">'.date_i18n( get_option( 'date_format' ), strtotime( $pec_date ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $pec_date ) ) . '</abbr><br>' . ucfirst( get_post_status( $post_id ) );
					break;
				
				case 'end_date':

					$end_time_hh = get_post_meta($post_id, 'pec_end_time_hh', true);
					$end_time_mm = get_post_meta($post_id, 'pec_end_time_mm', true);
					
					if(empty($end_time_hh)) $end_time_hh = '00';
					if(empty($end_time_mm)) $end_time_mm = '00';
					
					$pec_end_date = get_post_meta($post_id, 'pec_end_date', true);
					$pec_date = $pec_end_date . ' ' . $end_time_hh . ':' . $end_time_mm . ':00';

					if($pec_end_date != "" && $pec_end_date != "0000-00-00") {
						echo '<abbr title="'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.($end_time_hh != "" ? date_i18n(get_option('time_format'), strtotime($pec_date)) : '' ).'">'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.($end_time_hh != "" ? date_i18n(get_option('time_format'), strtotime($pec_date)) : '' ).'</abbr>';
					}
					break;
				
				case 'frequency':

					$frequency = get_post_meta( $post_id, 'pec_recurring_frecuency', true );
					if( $frequency != "" && $frequency > 0 ) 
					{
						switch( $frequency ) 
						{
							case 1: 
								echo __('Daily', 'dpProEventCalendar');
								break;	
							case 2: 
								echo __('Weekly', 'dpProEventCalendar');
								break;	
							case 3: 
								echo __('Monthly', 'dpProEventCalendar');
								break;	
							case 4: 
								echo __('Yearly', 'dpProEventCalendar');
								break;	
							
						}
					}
					break;

				case 'bookings':
					
					$querystr = "
						SELECT SUM(quantity) as counter
						FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
						WHERE id_event = %d AND status <> 'pending';";

					$bookings_obj = $wpdb->get_row( $wpdb->prepare( $querystr, $post_id ), OBJECT );
					
					echo '<abbr>' . ($bookings_obj->counter ? '<strong>' . $bookings_obj->counter . '</strong>' : 0) . '</abbr>';

					break;
	        }		
	    }
	}

	/**
	 * Make column sortable
	 * 
	 * @return void
	 */
	function column_register_sortable( $columns ) 
	{

		global $typenow;
	    if ( $typenow != DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) return $columns;

		$columns['start_date'] = 'start_date';
	 

		return $columns;

	}

	/**
	 * Set Column order 
	 * 
	 * @return void
	 */
	function column_orderby( $vars ) 
	{

		global $typenow;

		if ( $typenow != DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) return $vars;

		if ( isset( $vars['orderby'] ) && 'start_date' == $vars['orderby'] ) 
		{
		
			$vars = array_merge( $vars, array(
				'meta_key' => 'pec_date',
				'orderby' => 'meta_value_num meta_value'
			) );
		
		}
	 
		return $vars;

	}

	
	/**
	 * Remove unnecessary columns
	 * 
	 * @return void
	 */
	function manage_columns($columns) 
	{

		global $typenow, $wpdb;

		if( $typenow == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
		{
		
			unset( $columns['comments'] );
			unset( $columns['date'] );
		
		}
	    
	    return $columns;

	}

	/**
	 * Filter events by calendar / category
	 * 
	 * @return void
	 */
	function restrict_events_by_calendar() 
	{

	    global $typenow;
	    global $wp_query;
		global $wpdb;
		
	    if ( $typenow == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
	    {
	    
	        ?>
	        <select name="pec_id_calendar" id="pec_id_calendar">
	            <option value=""><?php _e( 'Show all calendars...','dpProEventCalendar' ); ?></option>
	            <?php
	            $querystr = "
	            SELECT *
	            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . "
	            ORDER BY title ASC
	            ";
	            $calendars_obj = $wpdb->get_results( $querystr, OBJECT );
				if( is_array( $calendars_obj ) ) 
				{
				
					foreach( $calendars_obj as $calendar ) {
	            ?>

	                <option value="<?php echo $calendar->id?>" <?php if($calendar->id == $this->get( 'pec_id_calendar' ) ) { ?> selected="selected"<?php }?>><?php echo $calendar->title?></option>

	            <?php }

				}?>
	        </select>

	        <select name="pec_events_category" id="pec_events_category">
	            <option value=""><?php _e('Show all categories...','dpProEventCalendar'); ?></option>
	            <?php
	            $categories = get_categories( array( 'taxonomy' => 'pec_events_category', 'hide_empty' => 0 ) ); 
				$category_color = "";

				if( !empty( $categories ) ) {
				
					foreach ( $categories as $cat )
					{
	            
	            ?>
	            
	                <option value="<?php echo $cat->slug?>" <?php if($cat->slug == $this->get( 'pec_events_category' ) ) { ?> selected="selected"<?php }?>><?php echo $cat->cat_name?></option>
	            
	            <?php }
				
				}?>

	        </select>

	        <?php

	    }

	}

	
	/**
	 * Filter calendars in events list
	 * 
	 * @return void
	 */
	function convert_filter( $query ) 
	{

	    global $pagenow;

	    $qv = &$query->query_vars;

	    if ($pagenow=='edit.php' &&
	            isset( $qv['post_type'] ) && $qv['post_type'] == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
	    {
			
			if( is_numeric( $this->get( 'pec_id_calendar' ) ) )
			{

				$id_calendar = $this->get( 'pec_id_calendar' );
				$query->query_vars['meta_query'] = array( 
					'relation' => 'OR',
					array('key' => "pec_id_calendar", "value" => '(,'.$id_calendar.',)', 'compare' => 'REGEXP'),
					array('key' => "pec_id_calendar", "value" => '(,'.$id_calendar.')$', 'compare' => 'REGEXP'),
					array('key' => "pec_id_calendar", "value" => '^('.$id_calendar.',)', 'compare' => 'REGEXP'),
					array('key' => "pec_id_calendar", "value" => $id_calendar)
				);
				
				if ( 'start_date' == $this->get( 'orderby' ) ) 
				{
				
					$query->query_vars['meta_key'] = "pec_date";
					unset($query->query_vars['meta_value']);
					$query->query_vars['orderby'] = 'meta_value_num meta_value';
				
				}

			}

			if( is_numeric( $this->get( 'pec_id_location' ) ) )
			{
				
				$id_location = $this->get( 'pec_id_location' );
				$query->query_vars['meta_query'] = array( 
					'relation' => 'AND',
					array('key' => "pec_location", "value" => $id_location ),
				);

			}

			if( is_numeric( $this->get( 'pec_id_organizer' ) ) )
			{
				
				$organizer = $this->get( 'pec_id_organizer' );
				$query->query_vars['meta_query'] = array( 
					'relation' => 'AND',
					array('key' => "pec_organizer", "value" => $organizer ),
				);

			}

			if( is_numeric( $this->get( 'pec_id_speaker' ) ) )
			{
				
				$id_speaker = $this->get( 'pec_id_speaker' );
				$query->query_vars['meta_query'] = array( 
					'relation' => 'OR',
					array('key' => "pec_speaker", "value" => '(,'.$id_speaker.',)', 'compare' => 'REGEXP'),
					array('key' => "pec_speaker", "value" => '(,'.$id_speaker.')$', 'compare' => 'REGEXP'),
					array('key' => "pec_speaker", "value" => '^('.$id_speaker.',)', 'compare' => 'REGEXP'),
					array('key' => "pec_speaker", "value" => $id_speaker)
				);

			}
	    
	    }
		
	    if ( is_admin() && $pagenow == 'edit.php' && $this->get( 'ADMIN_FILTER_FIELD_NAME' ) != '') 
	    {
	    
	        $query->query_vars['meta_key'] = $this->get( 'ADMIN_FILTER_FIELD_NAME' );

		    if ( $this->get( 'ADMIN_FILTER_FIELD_VALUE' ) != '')
		        $query->query_vars['meta_value'] = $this->get( 'ADMIN_FILTER_FIELD_VALUE' );

	    }

	}

	/**
	 * Network stuff
	 * 
	 * @return void
	 */
	function show_network_settings() 
	{
	    $settings = $this->get_network_settings();
	?>
	    <h3><?php _e( 'Pro Event Calendar Settings', 'dpProEventCalendar' ); ?></h3>
	    <table id="menu" class="form-table">
        <?php
        foreach ( $settings as $setting ) :
        ?>

	        <tr valign="top">
	            <th scope="row"><?php echo $setting['name']; ?></th>
	            <td>
	                <input type="<?php echo $setting['type'];?>" name="dpProEventCalendar[<?php echo $setting['id']; ?>]" value="<?php echo esc_attr( get_site_option( $setting['id'] ) ); ?>" />
	                <br /><?php echo $setting['desc']; ?>
	            </td>
	        </tr>

	    <?php
	    endforeach;
	    echo '</table>';
	}

	/**
	 * Save Network Settings
	 * 
	 * @return void
	 */
	function save_network_settings() 
	{
	
	    $posted_settings  = array_map( 'sanitize_text_field', $_POST['dpProEventCalendar'] );

	    foreach ( $posted_settings as $name => $value ) {
	        update_site_option( $name, $value );
	    }
	
	}

	/**
	 * Get Network Settings
	 * 
	 * @return void
	 */
	function get_network_settings() 
	{

	    $settings[] = array(
	        'id'   => 'pec-purchase-code',
	        'name' => __( 'Purchase Code', 'dpProEventCalendar' ),
	        'desc' => __('Introduce the purchase code to get automatic updates.','dpProEventCalendar'),
	        'type' => 'text',
	        'size' => 'regular'
	    );

	    return apply_filters( 'plugin_settings', $settings );
	}

	/**
	 * Menu settings for wp-admin
	 * 
	 * @return void
	 */
	function menu_settings() 
	{
	    
	    global $current_user;
	    
		$user_roles = pec_setting( 'user_roles', array() );
		if( ! in_array( dpProEventCalendar_get_user_role(), $user_roles ) && dpProEventCalendar_get_user_role() != "administrator" && !is_super_admin( $current_user->ID ) ) { return; }
	    // Add a new submenu under Options:

	    $args = array( 'posts_per_page' => 21, 'post_type'=> DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'post_status' => 'pending' );

	    $pending = get_posts( $args );
	    $pending_counter = 0;
	    $pending_counter = count( $pending );

		add_menu_page( 'Event Calendar', __('Event Calendar', 'dpProEventCalendar'). ( $pending_counter > 0 ? '<span class="update-plugins count-'.($pending_counter > 20 ? '20+' : $pending_counter).'"><span class="plugin-count">'.($pending_counter > 20 ? '20+' : $pending_counter).'</span></span>' : '' ), 'edit_posts','dpProEventCalendar-admin', 'dpProEventCalendar_calendars_page', 'dashicons-calendar-alt', '139.2' );
		add_submenu_page('dpProEventCalendar-admin', __('Categories', 'dpProEventCalendar'), __('Categories', 'dpProEventCalendar'), 'edit_posts', 'edit-tags.php?taxonomy=pec_events_category');

		if( dpProEventCalendar_get_user_role() != 'editor' && dpProEventCalendar_get_user_role() != 'contributor' && dpProEventCalendar_get_user_role() != 'author' ) 
	    {
		
	    	add_submenu_page( 'dpProEventCalendar-admin', __('Calendars', 'dpProEventCalendar'), __('Calendars', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-admin', 'dpProEventCalendar_calendars_page');
			add_submenu_page( 'dpProEventCalendar-admin', __('Special Dates', 'dpProEventCalendar'), __('Special Dates / Event Color', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-special', 'dpProEventCalendar_special_page');
			add_submenu_page( 'dpProEventCalendar-admin', __('Settings', 'dpProEventCalendar'), __('Settings', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-settings', 'dpProEventCalendar_settings_page');
			add_submenu_page( 'dpProEventCalendar-admin', __('Custom Shortcodes', 'dpProEventCalendar'), __('Custom Shortcodes', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-custom-shortcodes', 'dpProEventCalendar_custom_shortcodes_page');
		
	    }

	}

	/**
	 * Prints Dashboard Bookings List Widget
	 * 
	 * @return void
	 */
	function dashboard_widget( $post, $callback_args ) 
	{

		global $wpdb;
		
		echo '<ul>';
		
		$booking_count = 0;
		$querystr = "SELECT * FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . " ORDER BY id DESC LIMIT 10;";

		$bookings_obj = $wpdb->get_results($querystr, OBJECT);

		foreach( $bookings_obj as $booking ) 
		{
		
			if(is_numeric($booking->id_user) && $booking->id_user > 0) 
			{
			
				$userdata = get_userdata($booking->id_user);
			
			} else {
			
				$userdata = new stdClass();
				$userdata->display_name = $booking->name;
				$userdata->user_email = $booking->email;	
			
			}
			
			if( get_the_title( $booking->id_event ) == '' ) 
				continue;
				
			echo '<li>The user <strong>' . $userdata->display_name . '</strong> has booked the event <a href="'.get_edit_post_link($booking->id_event).'">'.get_the_title($booking->id_event).'</a> ('.date(get_option( 'date_format' ), strtotime( $booking->event_date ) ).')</li>';
		}

		echo '</ul>';

	}

	/**
	 * Add Dashboard Widgets
	 * 
	 * @return void
	 */
	function add_dashboard_widgets() 
	{

		wp_add_dashboard_widget( 'dashboard_widget', __('Latest Event Bookings', 'dpProEventCalendar'), array( $this, 'dashboard_widget' ) );

	}

	/**
	 * Duplicate Bulk Action
	 * 
	 * @return void
	 */
	function bulk_admin_footer() {

		global $post_type;

		if( $post_type == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
		{
		  echo '
		  <script type="text/javascript">
		  	jQuery(document).ready(function() {
				jQuery("select[name=\'action\']").append("<option value=\'duplicate\'>' . __( 'Duplicate', 'dpProEventCalendar' ) . '</option>");

			});
		  </script>';
		}
	  
	}

	
	/**
	 * Bulk Actions
	 * 
	 * @return void
	 */
	function bulk_action() 
	{
		
		$action = $this->get( 'action' );
		
		// 2. security check
		if ( $action == "duplicate" && $this->get( 'post_type' ) == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
		{
			
			
			$post_ids = $this->get( 'post' );
			
			switch($action) {
				
				// 3. Perform the action
				
				case 'duplicate':
								
					$duplicated = 0;
					
					foreach( $post_ids as $post_id ) 
					{
					
						$my_post = get_post( $post_id, "ARRAY_A" );
						unset($my_post['ID']);
						$my_post['post_category'] = array();
						$my_post['post_date'] = date('Y-m-d H:i:s');

						$category = get_the_terms( $post_id, 'pec_events_category' ); 
						if( ! empty( $category ) ) {
							foreach ( $category as $cat ){
								$my_post['post_category'][] =  $cat->term_id;
							}
						}

						if ( !$inserted = wp_insert_post( $my_post, false ) )
						
							wp_die( __('Error duplicating post.') );
						
						$meta_values = get_post_meta( $post_id );
						
						foreach( $meta_values as $key => $value ) {
							foreach( $value as $val ) 
							{
							
								if( $key == 'pec_code' )
									$val = dpProeventCalendar_generate_code();

								add_post_meta( $inserted, $key, $val );
							}
						}
						wp_set_post_terms( $inserted, $my_post['post_category'], 'pec_events_category' );
						$duplicated++;
					
					}
					
					// build the redirect url
					
					$sendback = esc_url_raw( add_query_arg( array( 'post_type' => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'duplicated' => $duplicated, 'ids' => join( ',', $post_ids ) ), $sendback ) );
					
				break;
				
				default: return;
				
			}
			
			// 4. Redirect client
			
			wp_redirect( $sendback );
			
			exit();
		}

	}

	/**
	 * Title Text Filter for organizers post type
	 * 
	 * @return void
	 */
	function change_title_text( $title )
	{

	    $screen = get_current_screen();

	    if  ( DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE == $screen->post_type )

	    	$title = __('Organizer Name', 'dpProEventCalendar');

	    return $title;
	}

	
	/**
	 * Edit Event and process emails for published pending events
	 * 
	 * @return void
	 */
	function edit_event_hook( $post_ID ) 
	{

		@header( "HTTP/1.1 200 OK" );
	
		global $dpProEventCalendar_cache, $pec_init;
		
		$post_type = get_post_type( $this->get( 'post' ) );

		if( ( isset( $_POST['post_type'] ) && DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE != $_POST['post_type'] ) && DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE != $post_type ) return;

		if( isset( $_POST['pec_id_calendar'] ) ) 
		{
		
			$calendar_id = explode( ",", $_POST['pec_id_calendar'] ); 
		
			if( is_array( $calendar_id ) ) 
				$calendar_id = $calendar_id[0];	
			
		
		} else {
		
			$calendar_id = explode(",", get_post_meta($post_ID, 'pec_id_calendar', true)); 
		
			if( is_array( $calendar_id ) ) 
				$calendar_id = $calendar_id[0];	
		
		}

		if( isset( $_POST['hidden_post_status'] ) && $_POST['hidden_post_status'] == 'pending' && $_POST['post_status'] == 'publish' ) 
		{
		
			// Send email to event author
			
			$opts = array();
			$opts['id_calendar'] = $calendar_id;
			$dpProEventCalendar_class = $pec_init->init_base( $opts );

			$calendar_obj = $dpProEventCalendar_class->get_calendar();
			
			$userdata = get_userdata( $_POST['post_author'] );
			
			if( $calendar_obj->new_event_email_template_published == '' ) 
				@$calendar_obj->new_event_email_template_published = "Hi #USERNAME#,\n\nThe event #EVENT_TITLE# has been approved.\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
			
			
			add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
			add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
			
			// Email to User
			
			if( $calendar_obj->new_event_email_enable ) 
			{
			
				wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters( 'pec_new_event_published', $calendar_obj->new_event_email_template_published, get_the_title( $post_ID ), $userdata->display_name ) );

				// Action hook
				do_action( 'pec_event_published', $post_ID );
			
			}
					
		}
		
		if( isset( $dpProEventCalendar_cache['calendar_id_'.$calendar_id] ) ) 
		{
		
		   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	   	
	   	}

	}

	/**
	 * Category color custom field
	 * 
	 * @return void
	 */
	function category_edit_save( $term_id ) 
	{
		
		if ( isset( $_POST['color'] ) ) 

	        update_term_meta( $term_id, 'color', $_POST['color'] );

	}

	/**
	 * Form Fields for categories
	 * 
	 * @return void
	 */
	function category_edit_form_fields () 
	{

		global $wpdb;

		$pec_color = "";
		$edit = false;


		if( is_numeric( $this->get( 'tag_ID' ) ) )
			$edit = true;

		if( $edit )
			$pec_color = get_term_meta( $this->get( 'tag_ID' ), 'color', true );

		$counter = 0;
		$querystr = "
		SELECT *
		FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES . " 
		ORDER BY title ASC";

		$sp_dates_obj = $wpdb->get_results( $querystr, OBJECT );

		if( ! $edit )
		{
		?>
		<div class="form-field term-description-wrap">
            <label for="color"><?php _e('Color Code', 'dpProEventCalendar'); ?></label>
        <?php } else { ?>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="color"><?php _e('Color Code', 'dpProEventCalendar'); ?></label>
            </th>
            <td>
        <?php }?>

        	<select name="color">
	        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
	             <?php 
				
				foreach( $sp_dates_obj as $sp_dates ) 
				{
				?>
	            
	            	<option value="<?php echo $sp_dates->id?>" <?php echo ( $pec_color == $sp_dates->id ? 'selected="selected"' : '' )?>><?php echo $sp_dates->title?></option>
	            
	            <?php }?>
	        </select>
	    <?php 
	    if( ! $edit )
		{?>
	        <p><?php _e('Select a color. To create a new one, go to the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-special' ).'" target="_blank">special dates</a> section','dpProEventCalendar'); ?></p>
        </div>
    	<?php } else { ?>

		        <label class="dp_ui_pec_content_desc"><?php _e('Select a color. To create a new one, go to the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-special' ).'" target="_blank">special dates</a> section','dpProEventCalendar'); ?></label>
            </td>
        </tr>
	    <?php 
		}
	}

	/**
	 * Remove unnecessary Metaboxes
	 * 
	 * @return void
	 */
	function remove_meta_box() 
	{

		remove_meta_box( 'slugdiv', DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE, 'normal' );
		
	}

	/**
	 * Remove Metaboxes
	 * 
	 * @return void
	 */
	function remove_meta_boxes( $context = 'advanced', $priority = 'default' ) 
	{

	    global $wp_meta_boxes;

	    $screens = array( DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE, DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE );
	    foreach( $screens as $screen ) 
	    {
	        $screen = convert_to_screen( $screen );
		    

		    $page = $screen->id;
		    
		    if( isset($wp_meta_boxes[$page]) && isset($wp_meta_boxes[$page][$context]) && isset($wp_meta_boxes[$page][$context][$priority]) ) 
		    {
			
			    if( is_array($wp_meta_boxes[$page][$context][$priority]) ) 
			    {
				
				    foreach( $wp_meta_boxes[$page][$context][$priority] as $key ) 
				    {
				    	
					    unset( $wp_meta_boxes[$page][$context][$priority][$key['id']] );

					}
				}
			}
		}

	}

	/**
	 * Metabox for Venues
	 * 
	 * @return void
	 */

	function venues_metabox( $post ) 
	{
		
		$values = get_post_custom( $post->ID );
		$pec_venue_address = isset( $values['pec_venue_address'] ) ? $values['pec_venue_address'][0] : '';
		$pec_venue_phone = isset( $values['pec_venue_phone'] ) ? $values['pec_venue_phone'][0] : '';
		$pec_venue_link = isset( $values['pec_venue_link'] ) ? $values['pec_venue_link'][0] : '';
		$pec_venue_map = isset( $values['pec_venue_map'] ) ? $values['pec_venue_map'][0] : '';
		$pec_venue_map_lnlat = isset( $values['pec_venue_map_lnlat'] ) ? $values['pec_venue_map_lnlat'][0] : '';

		do_action( 'pec_enqueue_admin', 1);

		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

		$map_lat = 0;
		$map_lng = 0;

		if( pec_setting( 'map_default_latlng' ) != "" ) 
		{
			
			$map_lnlat = explode( ",", pec_setting( 'map_default_latlng' ) );
			$map_lat = $map_lnlat[0];
			$map_lng = $map_lnlat[1];
		
		}

		if( $pec_venue_map_lnlat != "" ) 
		{
		
			$map_lnlat = explode( ",", $pec_venue_map_lnlat );
			$map_lat = $map_lnlat[0];
			$map_lng = $map_lnlat[1];
		
		}


		// Address Field
		$this->form->input( array( 'lbl' => __('Address', 'dpProEventCalendar'), 'name' => 'pec_venue_address', 'value' => $pec_venue_address, 'placeholder' => __('Introduce the venue\'s address','dpProEventCalendar'), 'size' => 80 ) );

		// Phone Field
		$this->form->input( array( 'lbl' => __('Phone', 'dpProEventCalendar'), 'name' => 'pec_venue_phone', 'value' => $pec_venue_phone, 'placeholder' => __('Introduce the venue\'s phone','dpProEventCalendar','dpProEventCalendar'), 'size' => 80 ) );

		// Link Field
		$this->form->input( array( 'lbl' => __('Link', 'dpProEventCalendar'), 'name' => 'pec_venue_link', 'value' => $pec_venue_link, 'placeholder' => __('The URL of the location\'s official website.','dpProEventCalendar'), 'size' => 80 ) );

		// Map Field
		$this->form->input( array( 'lbl' => __('Map', 'dpProEventCalendar'), 'name' => 'pec_venue_map', 'id' => 'pec_map', 'value' => $pec_venue_map, 'placeholder' => __('Introduce the country, city, address of the event. i.e: Spain, Madrid, Street x','dpProEventCalendar'), 'pec_venue_map_lnlat' => $pec_venue_map_lnlat, 'map_lat' => $map_lat, 'map_lng' => $map_lng, 'size' => 80 ), 'map' );
		
	}

	/**
	 * Add metaboxes for custom post types
	 * 
	 * @return void
	 */
	function add_meta_boxes() 
	{

		add_meta_box( 'dpProEventCalendar_booking_meta', __('Booking', 'dpProEventCalendar'), 'dpProEventCalendar_booking_display', DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'normal', 'high' );
		add_meta_box( 'dpProEventCalendar_events_meta', __('Event Data', 'dpProEventCalendar'), 'dpProEventCalendar_events_display', DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'normal', 'high' );
		add_meta_box( 'dpProEventCalendar_excerpt_meta', __('Excerpt', 'dpProEventCalendar'), 'dpProEventCalendar_excerpt_display', DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'normal', 'high' );
		add_meta_box( 'dpProEventCalendar_events_side_meta', __('Event Date Info', 'dpProEventCalendar'), 'dpProEventCalendar_events_side_display', DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'side', 'high' );
		
		$this->remove_meta_boxes( 'side', 'default' );
		$this->remove_meta_boxes( 'normal', 'default' );
		$this->remove_meta_boxes( 'normal', 'high' );
		$this->remove_meta_boxes( 'advanced', 'default' );

		add_meta_box( 'dpProEventCalendar_events_meta', __('Venue Data', 'dpProEventCalendar'), array( $this, 'venues_metabox' ) , DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE, 'normal', 'high' );

	}

	/**
	 * Get Admin url
	 * 
	 * @return void
	 */
	function admin_url( $query = array() ) 
	{

		global $plugin_page;

		if ( ! isset( $query['page'] ) )
			$query['page'] = $plugin_page;

		$path = 'admin.php';

		if ( $query = build_query( $query ) )
			$path .= '?' . $query;

		$url = admin_url( $path );

		return esc_url_raw( $url );

	}

}

?>