<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PEC_Import_Events_Facebook {

	/**
	 * Facebook app ID
	 *
	 * @var string
	 */
	public $fb_app_id;

	/**
	 * Facebook app Secret
	 *
	 * @var string
	 */
	public $fb_app_secret;

	/**
	 * Facebook Graph URL
	 *
	 * @var string
	 */
	public $fb_graph_url;

	/**
	 * Facebook Access Token
	 *
	 * @var string
	 */
	private $fb_access_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() 
	{
	
		global $dpProEventCalendar;

		$this->fb_app_id     = isset( $dpProEventCalendar['facebook_app_id'] ) ? $dpProEventCalendar['facebook_app_id'] : '';
		$this->fb_app_secret = isset( $dpProEventCalendar['facebook_app_secret'] ) ? $dpProEventCalendar['facebook_app_secret'] : '';
		$this->fb_graph_url  = 'https://graph.facebook.com/v3.0/';

	}

	/**
	 * Import facebook events by oraganization or facebook page.
	 *
	 * @since  1.0.0
	 * @param  array $event_data  import event data.
	 * @return array/boolean
	 */
	public function import_events( $event_data = array() ) 
	{

		global $dpProEventCalendar_errors;
		$imported_events    = array();
		$facebook_event_ids = array();

		if ( empty( $this->fb_app_id ) || empty( $this->fb_app_secret ) ) 
		{
		
			$dpProEventCalendar_errors[] = __( 'Please insert Facebook app ID and app Secret.', 'dpProEventCalendar' );
			return;
		
		}

		$import_by = isset( $event_data['import_by'] ) ? esc_attr( $event_data['import_by'] ) : '';

		if ( 'facebook_page' === $import_by ) 
		{
		
			$page_username = isset( $event_data['page_username'] ) ? $event_data['page_username'] : '';
		
			if ( empty( $page_username ) ) 
			{
			
				$dpProEventCalendar_errors[] = __( 'Please insert valid Facebook page.', 'dpProEventCalendar' );
				return false;
			
			}
			
			$imported_events = $this->get_events_for_facebook_page( $page_username, 'page' );

			return $imported_events;
			
		} elseif ( 'facebook_group' === $import_by ) {

			$facebook_group_id = isset( $event_data['facebook_group_id'] ) ? $event_data['facebook_group_id'] : '';
			if ( empty( $facebook_group_id ) ) 
			{
			
				$dpProEventCalendar_errors[] = __( 'Please insert valid Facebook Group URL or ID.', 'dpProEventCalendar' );
				return false;
			
			}

			$imported_events = $this->get_events_for_facebook_page( $facebook_group_id, 'group' );

			return $imported_events;
			
		} elseif ( 'facebook_event_id' === $import_by ) {

			$facebook_event_ids = isset( $event_data['event_ids'] ) ? $event_data['event_ids'] : array();
		}
		
		if ( ! empty( $facebook_event_ids ) ) {
			foreach ( $facebook_event_ids as $facebook_event_id ) {
				if ( ! empty( $facebook_event_id ) ) {
					$imported_event = $this->import_event_by_event_id( $facebook_event_id, $event_data );
					return $imported_event;
				}
			}
		}
		return $imported_events;
	}

	/**
	 * Import facebook event by ID.
	 *
	 * @since  1.0.0
	 * @param int   $facebook_event_id Facebook Event ID.
	 * @param array $event_data  import event data.
	 * @return int/boolean
	 */
	public function import_event_by_event_id( $facebook_event_id, $event_data = array() ) 
	{

		global $dpProEventCalendar_errors, $dpProEventCalendar;
		$options       = $dpProEventCalendar;
		$update_events = isset( $options['update_events'] ) ? $options['update_events'] : 'no';

		if ( empty( $facebook_event_id ) || empty( $this->fb_app_id ) || empty( $this->fb_app_secret ) ) {
			if ( empty( $this->fb_app_id ) || empty( $this->fb_app_secret ) ) {
				$dpProEventCalendar_errors[] = esc_attr__( 'Please insert Facebook app ID and app Secret.', 'dpProEventCalendar' );
				return;
			}
			return false;
		}
		if ( empty( $facebook_event_id ) || ! is_numeric( $facebook_event_id ) ) {
			// translators: %s is Facebook event ID.
			$dpProEventCalendar_errors[] = sprintf( esc_attr__( 'Please provide valid Facebook event ID: %s.', 'dpProEventCalendar' ), $facebook_event_id );
			return false;
		}

		$facebook_event_object = $this->get_facebook_event_by_event_id( $facebook_event_id );
		if ( isset( $facebook_event_object->error ) ) {
			// translators: %s is Facebook event ID.
			$dpProEventCalendar_errors[] = sprintf( esc_html__( 'We are not able to access Facebook event: %s. Possible reasons: - App Credentials are wrong - Facebook event is not public or some restrictions are there like age,country etc.', 'dpProEventCalendar' ), $facebook_event_id );
			return false;
		}

		return $facebook_event_object;
	}

	public function get_events_for_facebook_page ( $id, $type )
	{
		
		$facebook_page_id = $id;
		if( $facebook_page_id == '' ){ return array(); }
		$max_events = 10;

		$fields = array(
			'id',
			'name',
			'description',
			'start_time',
			'end_time',
			'event_times',
			'cover',
			'ticket_uri',
			'timezone',
			'place',
		);

		$include_owner = apply_filters( 'dpProEventCalendar_import_owner', false );
		if( $include_owner ){
			$fields[] = 'owner';
		}

		$args = array(
			'limit'       => 999,
			'fields'      => implode(
				',',
				$fields
			)
		);

		if ( $facebook_page_id === 'me' ){
			$args['since'] = current_time('timestamp');
		}else{
			$args['time_filter'] = 'upcoming';
		}

		return $this->get_facebook_response_data(
			$facebook_page_id . '/events',
			array(
				'fields' => implode(
					',',
					$fields
				),
			)
		);

	}

	public function get_my_fb_pages ( )
	{

		$fields = array(
			'id',
			'name',
			'events',
		);

		$include_owner = apply_filters( 'dpProEventCalendar_import_owner', false );
		if( $include_owner ){
			$fields[] = 'owner';
		}

		$args = array(
			'limit'       => 999,
			'fields'      => implode(
				',',
				$fields
			)
		);

		return $this->get_facebook_response_data(
			'me/accounts',
			array(
				'fields' => implode(
					',',
					$fields
				),
			)
		);

	}


	/**
	 * Get access token
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_access_token() {

		if ( ! empty( $this->fb_access_token ) ) {

			return $this->fb_access_token;

		} else {

			$args                       = array(
				'grant_type'    => 'client_credentials',
				'client_id'     => $this->fb_app_id,
				'client_secret' => $this->fb_app_secret,
			);
			$access_token_url           = add_query_arg( $args, $this->fb_graph_url . 'oauth/access_token' );
			$access_token_response      = wp_remote_get( $access_token_url );
			$access_token_response_body = wp_remote_retrieve_body( $access_token_response );
			$access_token_data          = json_decode( $access_token_response_body );
			$access_token               = ! empty( $access_token_data->access_token ) ? $access_token_data->access_token : null;

			$dpProEventCalendar_user_token_options = get_option( 'dpProEventCalendar_user_token_options', array() );

			if ( ! empty( $dpProEventCalendar_user_token_options ) && ! empty( $access_token ) ) 
			{
			
				$authorize_status  = isset( $dpProEventCalendar_user_token_options['authorize_status'] ) ? $dpProEventCalendar_user_token_options['authorize_status'] : 0;
				$user_access_token = isset( $dpProEventCalendar_user_token_options['access_token'] ) ? $dpProEventCalendar_user_token_options['access_token'] : '';
				
				if ( 1 === $authorize_status && ! empty( $user_access_token ) ) 
				{

					$args                       = array(
						'input_token'  => $user_access_token,
						'access_token' => $access_token,
					);
					$access_token_url           = add_query_arg( $args, $this->fb_graph_url . 'debug_token' );
					$access_token_response      = wp_remote_get( $access_token_url );
					$access_token_response_body = wp_remote_retrieve_body( $access_token_response );
					$access_token_data          = json_decode( $access_token_response_body );
					// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( ! isset( $access_token_data->error ) && 1 == $access_token_data->data->is_valid ) {
						$access_token = $user_access_token;
					} else {
						$dpProEventCalendar_user_token_options['authorize_status'] = 0;
						update_option( 'dpProEventCalendar_user_token_options', $dpProEventCalendar_user_token_options );
					}
				}
			
			}

			$this->fb_access_token = apply_filters( 'dpProEventCalendar_facebook_access_token', $access_token );
			return $this->fb_access_token;
		}
	}

	/**
	 * Generate Facebook api URL for grab Event.
	 *
	 * @since 1.0.0
	 * @param string $path API Path.
	 * @param array  $query_args Array of query arguments.
	 * @param string $access_token Access Token.
	 * @return string $url Generated URL.
	 */
	public function generate_facebook_api_url( $path = '', $query_args = array(), $access_token = '' ) {
		$query_args = array_merge( $query_args, array( 'access_token' => $this->get_access_token() ) );
		if ( ! empty( $access_token ) ) {
			$query_args['access_token'] = $access_token;
		}
		$url = add_query_arg( $query_args, $this->fb_graph_url . $path );

		return $url;
	}

	/**
	 * Get a facebook object.
	 *
	 * @since 1.0.0
	 * @param int   $event_id Event ID.
	 * @param array $args Arguments array.
	 * @return object $event_data.
	 */
	public function get_facebook_response_data( $event_id, $args = array() ) {
		$url        = $this->generate_facebook_api_url( $event_id, $args );
		$event_data = $this->get_json_response_from_url( $url );
		return $event_data;
	}

	/**
	 * Get a facebook event object
	 *
	 * @since 1.0.0
	 * @param int $event_id Event ID.
	 * @return object
	 */
	public function get_facebook_event_by_event_id( $event_id ) {
		$fields        = array(
			'id',
			'name',
			'description',
			'start_time',
			'end_time',
			'event_times',
			'cover',
			'ticket_uri',
			'timezone',
			'place',
		);
		$include_owner = apply_filters( 'dpProEventCalendar_import_owner', false );
		if ( $include_owner ) {
			$fields[] = 'owner';
		}

		return $this->get_facebook_response_data(
			$event_id,
			array(
				'fields' => implode(
					',',
					$fields
				),
			)
		);
	}

	/**
	 * Get body data from url and return decoded data.
	 *
	 * @since 1.0.0
	 * @param string $url API URL.
	 * @return object $response
	 */
	public function get_json_response_from_url( $url ) {
		$args     = array( 'timeout' => 15 );
		$response = wp_remote_get( $url, $args );
		$response = json_decode( wp_remote_retrieve_body( $response ) );
		return $response;
	}


	/**
	 * Get organizer args for event.
	 *
	 * @since    1.0.0
	 * @param array $facebook_event Facebook event.
	 * @return array
	 */
	public function get_organizer( $facebook_event ) {

		if ( ! isset( $facebook_event->owner->id ) ) {
			return null;
		}

		$organizer_raw_data = $this->get_facebook_response_data(
			$facebook_event->owner->id,
			array(
				'fields' => implode(
					',',
					array(
						'id',
						'name',
						'link',
					)
				),
			)
		);

		if ( ! isset( $organizer_raw_data->id ) ) {
			return null;
		}

		$event_organizer = array(
			'ID'          => isset( $organizer_raw_data->id ) ? $organizer_raw_data->id : '',
			'name'        => isset( $organizer_raw_data->name ) ? $organizer_raw_data->name : '',
			'description' => '',
			'email'       => '',
			'phone'       => isset( $organizer_raw_data->phone ) ? $organizer_raw_data->phone : '',
			'url'         => isset( $organizer_raw_data->link ) ? $organizer_raw_data->link : '',
			'image_url'   => '',
		);
		return $event_organizer;
	}

	/**
	 * Get location args for event
	 *
	 * @since    1.0.0
	 * @param array $facebook_event Facebook event.
	 * @return array
	 */
	public function get_location( $facebook_event ) {

		if ( ! isset( $facebook_event->place->id ) ) {
			return null;
		}
		$event_venue    = $facebook_event->place;
		$event_location = array(
			'ID'           => isset( $facebook_event->place->id ) ? $facebook_event->place->id : '',
			'name'         => isset( $event_venue->name ) ? $event_venue->name : '',
			'description'  => '',
			'address_1'    => isset( $event_venue->location->street ) ? $event_venue->location->street : '',
			'address_2'    => '',
			'city'         => isset( $event_venue->location->city ) ? $event_venue->location->city : '',
			'state'        => isset( $event_venue->location->state ) ? $event_venue->location->state : '',
			'country'      => isset( $event_venue->location->country ) ? $event_venue->location->country : '',
			'zip'          => isset( $event_venue->location->zip ) ? $event_venue->location->zip : '',
			'lat'          => isset( $event_venue->location->latitude ) ? $event_venue->location->latitude : '',
			'long'         => isset( $event_venue->location->longitude ) ? $event_venue->location->longitude : '',
			'full_address' => isset( $event_venue->location->street ) ? $event_venue->location->street : '',
			'url'          => '',
			'image_url'    => '',
		);
		return $event_location;
	}

	/**
	 * Get organizer Name based on Organiser ID.
	 *
	 * @since    1.0.0
	 * @param array   $organizer_id Organizer event.
	 * @param boolean $full_data Need return full data?.
	 * @return array
	 */
	public function get_organizer_name_by_id( $organizer_id, $full_data = false ) {
		global $dpProEventCalendar_errors;
		if ( ! $organizer_id || empty( $organizer_id ) ) {
			return;
		}

		$organizer_raw_data = $this->get_facebook_response_data( $organizer_id, array() );
		if ( isset( $organizer_raw_data->error->message ) ) {
			return false;
		}

		if ( ! isset( $organizer_raw_data->name ) ) {
			return false;
		}

		if ( $full_data ) {
			return $organizer_raw_data;
		}

		$oraganizer_name = isset( $organizer_raw_data->name ) ? $organizer_raw_data->name : '';
		return $oraganizer_name;

	}

	/**
	 * Get UTC offset
	 *
	 * @since    1.0.0
	 * @param string $datetime DateTime.
	 */
	public function get_utc_offset( $datetime ) {
		try {
			$datetime = new DateTime( $datetime );
		} catch ( Exception $e ) {
			return '';
		}

		$timezone = $datetime->getTimezone();
		$offset   = $timezone->getOffset( $datetime ) / 60 / 60;

		if ( $offset >= 0 ) {
			$offset = '+' . $offset;
		}

		return 'UTC' . $offset;
	}
}
