<?php
/**
 * ThemeREX Addons Layouts: Elementor Pro Document class
 *
 * @package ThemeREX Addons
 * @since v2.6.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( class_exists( '\Elementor\TemplateLibrary\Source_Local' )
	&& class_exists('Elementor\Core\Base\Document')
	&& ! class_exists('TRX_Addons_Elementor_Layouts_Document_Pro')
) {
	class TRX_Addons_Elementor_Layouts_Document_Pro extends Elementor\Core\Base\Document {

		/**
		 * @access public
		 */
		public function get_name() {
			return \Elementor\TemplateLibrary\Source_Local::CPT;
		}

		/**
		 * @access public
		 * @static
		 */
		public static function get_title() {
			return __( 'Elementor Pro TemplateLibrary', 'trx_addons' );
		}

		/**
		 * [register_controls description]
		 * @return [type] [description]
		 */
		protected function register_controls() {

			parent::register_controls();

			$this->start_controls_section(
				'trx_layout_style',
				[
					'label' => __( 'Layout Container', 'trx_addons' ),
					'tab'   => Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'layout_width',
				[
					'label' => esc_html__( 'Width', 'trx_addons' ),
					'description' => esc_html__( "Width of the editor area. Attention! This option does not affect the actual width of the content, and is used only for ease of editing", 'trx_addons' ),
					'type'  => Elementor\Controls_Manager::SLIDER,
					'size_units' => [
						'px', '%'
					],
					'range' => [
						'px' => [
							'min' => 100,
							'max' => 2000,
						],
						'%' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'default' => [
						'size' => '',
						'unit' => 'px',
					],
					'selectors' => [
						'.trx-addons-layout--edit-mode .trx-addons-layout__inner' => 'max-width: {{SIZE}}{{UNIT}};',
						'.trx-addons-layout--single-preview .trx-addons-layout__inner' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'layout_fill_bg',
				[
					'label' => __( 'Fill Background', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'label_on' => __( 'On', 'trx_addons' ),
					'description' => esc_html__( "Adds a solid background for ease of editing, does not affect the actual layout.", 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'on',
					'default' => 'on',
					'selectors' => [
						'.trx-addons-layout--edit-mode .trx-addons-layout__inner' => 'background-color: var(--theme-color-bg_color);',
						'.trx-addons-layout--single-preview .trx-addons-layout__inner' => 'background-color: var(--theme-color-bg_color);',
					],
				]
			);

			$this->add_control(
				'layout_fill_bg_color',
				[
					'label' => __( 'Fill Color', 'trx_addons' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'.trx-addons-layout--edit-mode .trx-addons-layout__inner' => 'background-color: {{VALUE}};',
						'.trx-addons-layout--single-preview .trx-addons-layout__inner' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'layout_fill_bg' => 'on',
					],
				]
			);

			$this->end_controls_section();
		}
	}
}
