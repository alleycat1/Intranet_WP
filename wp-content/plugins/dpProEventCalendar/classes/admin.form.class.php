<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//Admin Form functions
class DPPEC_AdminForm {

	/**
	 * Class Constructor
	 * 
	 * @return void
	 */
	function __construct( ) 
	{


    }

    /**
	 * Admin Functions / actions / filters
	 * 
	 * @return void
	 */
    public function input( $opts = array( 'lbl' => '', 'name' => '', 'value' => '', 'placeholder' => '', 'id' => '', 'size' => 80 ), $type = 'text' )
    {

    	$id = ( ! isset( $opts['id'] ) ? $opts['name'] : $opts['id'] );
    	$required = ( isset( $opts['required'] ) ? 1 : 0 );

    	switch( $type )
    	{

    		case 'text':
		    	$html = '<div class="pec-input-section">';
				
				$html .= '		<input' . ( $required ? ' required' : '' ) . ' class="pec-input-text" type="text" name="' . $opts["name"] . '" size="' . $opts["size"] . '" id="' . $id . '" value="' . $opts["value"] . '" placeholder="' . $opts["placeholder"] . '" />';
				$html .= '<div class="pec-input-label">' . $opts["lbl"] . '</div>';
				$html .= '</div>';

				break;

			case 'map':

		    	$html = '<div class="pec-input-section">';
				$html .= '		<input type="text"  class="pec-input-text" name="' . $opts["name"] . '" size="' . $opts["size"] . '" id="' . $id . '" value="' . $opts["value"] . '" placeholder="' . $opts["placeholder"] . '" />';
				$html .= '<div class="pec-input-label">' . $opts["lbl"] . '</div>';
				
				$html .= '		<div class="clear"></div>';
		        $html .= '		<label for="pec_venue_map_lnlat">' . __('Drag the marker to set a specific position (Lat, Lng)', 'dpProEventCalendar') . '</label>';
		        $html .= '		<input type="text" name="pec_venue_map_lnlat" size="80" id="pec_map_lnlat" value="' . $opts["pec_venue_map_lnlat"] . '" readonly="readonly" />';
		        $html .= '		<div class="dp_pec_date_event_map_overlay" onclick="style.pointerEvents=\'none\'" style="height:400px; margin-top: -400px; top: 400px;"></div>';
				$html .= '		<div id="mapCanvas" data-map-lat="' . $opts["map_lat"] . '" data-map-lng="' . $opts["map_lng"] . '" style="height: 400px;"></div>';
				$html .= '</div>';

				break;

		}

		echo $html;

	}

}

?>