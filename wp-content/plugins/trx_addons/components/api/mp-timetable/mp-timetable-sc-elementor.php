<?php
/**
 * Plugin support: MP Timetable (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.30
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_sc_mptt_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_mptt_add_in_elementor' );
	/**
	 * Register MP Timetable widget in Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_mptt_add_in_elementor() {

		if ( ! trx_addons_exists_mptt() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}

		class TRX_Addons_Elementor_Widget_MP_Timetable extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'row_height' => 'size',
					'font_size' => 'size+unit'
				]);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_mptt';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'MP Timetable', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-countdown';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-support'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_mptt',
					[
						'label' => __( 'MP Timetable', 'trx_addons' ),
					]
				);

				$this->add_control(
					'col',
					[
						'label' => __( 'Column', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_MPTT_PT_COLUMN,
													'not_selected' => false,
													'orderby' => 'none'
													)
												),
						'multiple' => true,
						'default' => []
					]
				);

				$this->add_control(
					'events',
					[
						'label' => __( 'Events', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_MPTT_PT_EVENT,
													'not_selected' => false,
													'orderby' => 'title'
													)
												),
						'multiple' => true,
						'default' => []
					]
				);

				$this->add_control(
					'event_categ',
					[
						'label' => __( 'Event categories', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_terms(false, TRX_ADDONS_MPTT_TAXONOMY_CATEGORY),
						'multiple' => true,
						'default' => []
					]
				);

				$this->add_control(
					'increment',
					[
						'label' => __( 'Hour measure', 'trx_addons' ),
						'label_block' => false,
						'description' => esc_html__("Select the time interval for the left column.", "trx_addons"),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'4' => __( '4 hours', 'trx_addons' ),
							'2' => __( '2 hours', 'trx_addons' ),
							'1' => __( '1 hour', 'trx_addons' ),
							'0.5' => __( 'Half hour (30 min)', 'trx_addons' ),
							'0.25' => __( 'Quarter hour (15 min)', 'trx_addons' )
						),
						'default' => '1'
					]
				);

				$this->add_control(
					'view',
					[
						'label' => __( 'Filter style', 'trx_addons' ),
						'label_block' => false,
						'description' => esc_html__("Select the filter style for the content.", "trx_addons"),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
							'dropdown_list' => __( 'Dropdown list', 'trx_addons' ),
							'tabs' => __( 'Tabs', 'trx_addons' )
						),
						'default' => 'tabs'
					]
				);

				$this->add_control(
					'label',
					[
						'label' => __( 'Filter label', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Filter label", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'mptt_id',
					[
						'label' => __( 'Timetable ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "ID", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_mptt_lsyout',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'text_align',
					[
						'label' => __( 'Text align', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
						'default' => 'center'
					]
				);

				$this->add_control(
					'row_height',
					[
						'label' => __( 'Row height', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify row height (in px)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500
							]
						]
					]
				);

				$this->add_control(
					'font_size',
					[
						'label' => __( 'Base font size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
							'unit' => 'em'
						],
						'size_units' => ['px', 'em', '%'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50
							],
							'em' => [
								'min' => 0.1,
								'max' => 3,
								'step' => 0.1
							],
							'%' => [
								'min' => 10,
								'max' => 300
							]
						]
					]
				);

				$this->add_control(
					'responsive',
					[
						'label' => __( "Responsive", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_mptt_details',
					[
						'label' => __( 'Details', 'trx_addons' ),
					]
				);

				$this->add_control(
					'hide_label',
					[
						'label' => __( "Hide 'All Events' view", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'hide_hrs',
					[
						'label' => __( "Hide first (hours) column", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1'
					]
				);				

				$this->add_control(
					'hide_empty_rows',
					[
						'label' => __( "Hide empty rows", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( "Show titles", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'sub-title',
					[
						'label' => __( "Show subtitles", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'time',
					[
						'label' => __( "Show event's time", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'description',
					[
						'label' => __( "Show description", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'user',
					[
						'label' => __( "Show user", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'disable_event_url',
					[
						'label' => __( "Disable event URL", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Enable', 'trx_addons' ),
						'label_on' => __( 'Disable', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'mp-timetable' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf('[mp-timetable'
										. ' col="%1$s"'
										. ' events="%2$s"'
										. ' event_categ="%3$s"'
										. ' increment="%4$s"'
										. ' view="%5$s"'
										. ' label="%6$s"'
										. ' hide_label="%7$s"'
										. ' hide_hrs="%8$s"'
										. ' hide_empty_rows="%9$s"'
										. ' title="%10$s"'
										. ' sub-title="%11$s"'
										. ' time="%12$s"'
										. ' description="%13$s"'
										. ' user="%14$s"'
										. ' disable_event_url="%15$s"'
										. ' text_align="%16$s"'
										. ' row_height="%17$s"'
										. ' font_size="%18$s"'
										. ' id="%19$s"'
										. ' responsive="%20$s"'
									. ']',
									is_array($atts['col']) ? join(',', $atts['col']) : '',
									is_array($atts['events']) ? join(',', $atts['events']) : '',
									is_array($atts['event_categ']) ? join(',', $atts['event_categ']) : '',
									$atts['increment'],
									$atts['view'],
									$atts['label'],
									$atts['hide_label'],
									$atts['hide_hrs'],
									$atts['hide_empty_rows'],
									$atts['title'],
									$atts['sub-title'],
									$atts['time'],
									$atts['description'],
									$atts['user'],
									$atts['disable_event_url'],
									$atts['text_align'],
									$atts['row_height'],
									$atts['font_size'],
									$atts['mptt_id'],
									$atts['responsive']
							)
						)
					);
				} else
					$this->shortcode_not_exists('mp-timetable', 'MP Timetable');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_MP_Timetable' );
	}
}
