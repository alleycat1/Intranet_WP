<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Choice control.
 *
 * A base control for creating a theme-specific choice control - list of images (pictograms).
 *
 * Creating new control in the editor (inside `Widget_Base::_register_controls()`
 * method):
 *
 *    $this->add_control(
 *    	'body_style',
 *    	[
 *    		'label'   => __( 'Body style', 'theme-domain' ),
 *    		'type'    => 'choice',
 * 			'options' => [
 * 							'boxed' => [
 * 										'title' => __( 'Boxed', 'theme-domain' ),
 * 										'icon'  => 'URL_of_the_image_for_this_choice',
 * 										]
 * 							],
 * 							'wide' => [
 * 										'title' => __( 'Wide', 'theme-domain' ),
 * 										'icon'  => 'URL_of_the_image_for_this_choice',
 * 										]
 * 							]
 *    	]
 *    );
 *
 * @since 1.70.0
 *
 * @param string $label       Optional. The label that appears above of the
 *                            field. Default is empty.
 * @param string $description Optional. The description that appears below the
 *                            field. Default is empty.
 * @param string $default     Optional. Default icon name. Default is empty.
 * @param array  $options     Required. An associative array of available choices.
 * @param string $separator   Optional. Set the position of the control separator.
 *                            Available values are 'default', 'before', 'after'
 *                            and 'none'. 'default' will position the separator
 *                            depending on the control type. 'before' / 'after'
 *                            will position the separator before/after the
 *                            control. 'none' will hide the separator. Default
 *                            is 'default'.
 * @param bool   $show_label  Optional. Whether to display the label. Default is
 *                            true.
 * @param bool   $label_block Optional. Whether to display the label in a
 *                            separate line. Default is false.
 */
class PUBZINNE_Elementor_Control_Choice extends \Elementor\Base_Data_Control {

	/**
	 * Retrieve icon control type.
	 *
	 * @since 1.70.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'choice';
	}

	/**
	 * Retrieve control's default settings.
	 *
	 * Get the default settings of the control, used while initializing the control.
	 *
	 * @since 1.70.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => array()
		];
	}

	
	/**
	 * Enqueue control required scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by this control.
	 *
	 * @since 1.70.0
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'pubzinne-elementor-choice-control', pubzinne_get_file_url( 'plugins/elementor/params/choice/choice-control.js'), array('jquery'), null, true );
	}

	/**
	 * Render control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.70.0
	 * @access public
	 *
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="pubzinne_param_choice">
					<input type="hidden" id="<?php echo esc_attr($control_uid); ?>"
							data-setting="{{ data.name }}"
							class="{{ data.name }} {{ data.type }}_field"
							value="{{ data.controlValue }}" />
					<div class="pubzinne_list_choice"><#
						_.each( data.options, function( params, slug ) {
							print( ( params.new_row !== undefined ? '<br>' : '' )
									+ '<span tabindex="0" class="pubzinne_list_choice_item'
												+ ( ( slug == data.controlValue && '' !== data.controlValue ) || ( 'inherit' == slug && '' === data.controlValue ) ? ' pubzinne_list_active' : '' )
												+ '"'
										+ ' data-choice="' + slug + '"'
										+ ( params.description ? ' title="' + params.description + '"' : '' )
									+ '>'
										+ '<span class="pubzinne_list_choice_item_icon">'
											+ '<img src="' + ( params.icon.indexOf( '//' ) == -1 ? '<?php echo esc_url( trailingslashit( get_template_directory_uri() ) ); ?>' : '' ) + params.icon + '" alt="' + params.title + '">'
										+ '</span>'
										+ '<span class="pubzinne_list_choice_item_title">'
											+ params.title
										+ '</span>'
									+ '</span>');
						} );
					#></div>
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
