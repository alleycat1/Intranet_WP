<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ThemeREX Addons Icons control.
 *
 * A base control for creating a plugin-specific icon control. Displays font icon select box.
 * Available icons are listed in @see Control_Icon::get_icons().
 *
 * Creating new control in the editor (inside `Widget_Base::register_controls()`
 * method):
 *
 *    $this->add_control(
 *    	'icons',
 *    	[
 *    		'label' => __( 'Social Icon', 'plugin-domain' ),
 *    		'type' => 'trx_icons',
 *    	]
 *    );
 *
 * PHP usage (inside `Widget_Base::render()` method):
 *
 *    echo '<i class="' . esc_attr( $this->get_settings( 'icon' ) ) . '"></i>';
 *
 * JS usage (inside `Widget_Base::content_template()` method):
 *
 *    <i class="{{ settings.icon }}"></i>
 *
 * @since 1.6.41
 *
 * @param string $label       Optional. The label that appears above of the
 *                            field. Default is empty.
 * @param string $description Optional. The description that appears below the
 *                            field. Default is empty.
 * @param string $default     Optional. Default icon name. Default is empty.
 * @param array  $options     Optional. An associative array of available icons.
 *                            `[ 'class-name' => 'nicename', ... ]`
 *                            Default is a list of Font Awesome icons @see Control_Icon::get_icons()
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
 * @param string $style       Optional. Possible values are: 'icons' | 'images'
 *                            Default is 'icons'
 * @param string $mode        Optional. Possible values are: 'inline' | 'dropdown'
 *                            Default is 'dropdown'
 * @param string $return      Optional. Possible values are: 'slug' | 'full'
 *                            Default is 'full'
 */
class Trx_Addons_Elementor_Control_Trx_Icons extends \Elementor\Base_Data_Control {

	/**
	 * Retrieve icon control type.
	 *
	 * @since 1.6.41
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'trx_icons';
	}

	/**
	 * Retrieve control's default settings.
	 *
	 * Get the default settings of the control, used while initializing the control.
	 *
	 * @since 1.6.41
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'style' => 'icons',
			'mode' => 'dropdown',
			'return' => 'full',
			'options' => array()
		];
	}

	
	/**
	 * Enqueue control required scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by this control.
	 *
	 * @since 1.6.41
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'trx_addons-elementor-control-trx-icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/params/trx-icons/trx-icons-control.js'), array('jquery'), null, true );
	}

	/**
	 * Render icons control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.6.41
	 * @access public
	 *
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<# var icon = trx_addons_get_settings_icon( data.controlValue ); #>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="trx_addons_vc_param_icons">
					<input type="hidden" id="<?php echo esc_attr($control_uid); ?>"
							data-setting="{{ data.name }}"
							class="{{ data.name }} {{ data.type }}_field"
							value="{{ icon }}" />
					<# if (data.mode == 'dropdown') { #>
						<span class="trx_addons_icon_selector<#
									if (data.style == 'icons' && icon) { print(' '+icon); }
									#>"
							 title="<?php esc_attr_e('Select icon', 'trx_addons'); ?>"
							 data-style="{{ data.style }}"
							 <# if ((data.style == 'images' || data.style == 'svg') && icon) { #>
							 	style="background-image: url(<# print(data.return == 'slug' ? data.options[icon] 
																					: icon); #>);"
							<# } #>
						></span>
					<# } #>
					<div class="trx_addons_list_icons trx_addons_list_icons_{{ data.mode }}">
						<#
						if (data.mode == 'dropdown') {
							#><input type="text" class="trx_addons_list_icons_search" placeholder="<?php esc_attr_e('Search icon ...', 'trx_addons'); ?>"><#
						}
						#><div class="trx_addons_list_icons_wrap"><#
							#><div class="trx_addons_list_icons_inner"><#
							_.each(data.options, function(icon, slug) {
								print('<span class="'
													+ (data.style=='icons' ? icon : slug)
													+ ((data.return=='full' ? icon : slug) == icon ? ' trx_addons_active' : '')
													+ '"'
												+ ' title="' + slug + '"'
												+ ' data-icon="' + (data.return=='full' ? icon : slug) + '"'
												+ ((data.style == 'images' || data.style == 'svg') ? ' style="background-image: url(' + icon + ');"' : '')
												+ '>'
													+ (data.mode != 'dropdown'
														? '<i>' + slug + '</i>' 
														: ''
														)
												+ '</span>');
							});
							#>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
