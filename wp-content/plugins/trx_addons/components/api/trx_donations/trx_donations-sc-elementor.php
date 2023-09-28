<?php
/**
 * Plugin support: ThemeREX Donations (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widgets
//------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_elementor_df' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_trx_donations_add_in_elementor_df' );
	/**
	 * Register a widget 'Donations form' for Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_trx_donations_add_in_elementor_df() {

		if ( ! trx_addons_exists_trx_donations() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Trx_Donations_Form extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_donations_form';
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
				return __( 'Donations form', 'trx_addons' );
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
				return 'eicon-price-table';
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
				
				$this->start_controls_section(
					'section_sc_trx_donations_form',
					[
						'label' => __( 'Donations form', 'trx_addons' ),
					]
				);

				$donations_list = ! is_admin()
									? array()
									: trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select form', 'trx_addons' ) ) ), trx_addons_get_list_posts( false, array( 'post_type' => TRX_DONATIONS::POST_TYPE, 'not_selected' => false ) ) );

				$this->add_control(
					'donation',
					[
						'label' => __( 'Donation', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $donations_list,
						'default' => 0
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'subtitle',
					[
						'label' => __( 'Subtitle', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'description',
					[
						'label' => __( 'Description', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => ''
					]
				);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_trx_donations_form_paypal',
					[
						'label' => __( 'PayPal Details', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'client_id',
					[
						'label' => __( 'PayPal Client ID', 'trx_addons' ),
						'description' => wp_kses_data( __("Client ID from the PayPay application. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);
				
				$this->add_control(
					'amount',
					[
						'label' => __( 'Default amount', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify default amount to make donation. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'sandbox',
					[
						'label' => __( 'Sandbox', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Enable sandbox mode to testing your payments without real money transfer", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'' => esc_html__('Inherit', 'trx_addons'),
							'on' => esc_html__('On', 'trx_addons'),
							'off' => esc_html__('Off', 'trx_addons')
						],
						'default' => ''
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'trx_donations_form' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf( '[trx_donations_form'
										. ' donation="%1$s"'
										. ' title="%2$s"'
										. ' subtitle="%3$s"'
										. ' description="%4$s"'
										. ' client_id="%5$s"'
										. ' amount="%6$s"'
										. ' sandbox="%7$s"'
										. ']',
										$atts['donation'],
										$atts['title'],
										$atts['subtitle'],
										str_replace('"', "'", $atts['description']),
										$atts['client_id'],
										$atts['amount'],
										$atts['sandbox']
							)
						)
					);
				} else
					$this->shortcode_not_exists('trx_donations_form', 'ThemeREX Donations');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Trx_Donations_Form' );
	}
}

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_elementor_dl' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_trx_donations_add_in_elementor_dl' );
	/**
	 * Register a widget 'Donations list' for Elementor.
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_trx_donations_add_in_elementor_dl() {

		if ( ! trx_addons_exists_trx_donations() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Trx_Donations_List extends TRX_Addons_Elementor_Widget {

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
					'link' => 'url'
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
				return 'trx_donations_list';
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
				return __( 'Donations list', 'trx_addons' );
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
				return 'eicon-post-list';
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
					'section_sc_trx_donations_list',
					[
						'label' => __( 'Donations list', 'trx_addons' ),
					]
				);

				$this->add_control(
					'post_type',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_DONATIONS::POST_TYPE
					]
				);
				$this->add_control(
					'taxonomy',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_DONATIONS::TAXONOMY
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_DONATIONS::TAXONOMY ) ),
						'default' => '0'
					]
				);

				$this->add_query_param('', array(), TRX_DONATIONS::POST_TYPE);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_trx_donations_list_titles',
					[
						'label' => __( 'Title & Button', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'subtitle',
					[
						'label' => __( 'Subtitle', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'description',
					[
						'label' => __( 'Description', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => ''
					]
				);

				$this->add_control(
					'link',
					[
						'label' => __( 'Link', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::URL,
						'default' => ['url' => '']
					]
				);

				$this->add_control(
					'link_caption',
					[
						'label' => __( 'Link text', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'trx_donations_list' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf( '[trx_donations_list'
										. ' cat="%1$s"'
										. ' title="%2$s"'
										. ' subtitle="%3$s"'
										. ' description="%4$s"'
										. ' link="%5$s"'
										. ' link_caption="%6$s"'
										. ' ids="%7$s"'
										. ' count="%8$s"'
										. ' columns="%9$s"'
										. ' offset="%10$s"'
										. ' orderby="%11$s"'
										. ' order="%12$s"'
										. ']',
										$atts['cat'],
										$atts['title'],
										$atts['subtitle'],
										str_replace('"', "'", $atts['description']),
										$atts['link'],
										$atts['link_caption'],
										$atts['ids'],
										$atts['count'],
										$atts['columns'],
										$atts['offset'],
										$atts['orderby'],
										$atts['order']
							)
						)
					);
				} else
					$this->shortcode_not_exists('trx_donations_list', 'ThemeREX Donations');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Trx_Donations_List' );
	}
}

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_elementor_di' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_trx_donations_add_in_elementor_di' );
	/**
	 * Register widget 'Donations info' for Elementor
	 * 
	 * @hooked elementor/widgets/register
	 */
	function trx_addons_sc_trx_donations_add_in_elementor_di() {

		if ( ! trx_addons_exists_trx_donations() || ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}
		
		class TRX_Addons_Elementor_Widget_Trx_Donations_Info extends TRX_Addons_Elementor_Widget {

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
					'show_supporters' => 'size'
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
				return 'trx_donations_info';
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
				return __( 'Donations info', 'trx_addons' );
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
				return 'eicon-info-box';
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
					'section_sc_trx_donations_info',
					[
						'label' => __( 'Donations info', 'trx_addons' ),
					]
				);

				$donations_list = ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, ['post_type' => TRX_DONATIONS::POST_TYPE, 'not_selected' => false]);
				$default_donation = trx_addons_array_get_first($donations_list);

				$this->add_control(
					'donation',
					[
						'label' => __( 'Donation', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $donations_list,
						'default' => ''.$default_donation	// Make string for Elementor Editor
					]
				);

				$this->add_control(
					'show_featured',
					[
						'label' => __( 'Show image', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_title',
					[
						'label' => __( 'Show title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_excerpt',
					[
						'label' => __( 'Show excerpt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_goal',
					[
						'label' => __( 'Show goal', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_raised',
					[
						'label' => __( 'Show raised', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_scale',
					[
						'label' => __( 'Show scale', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'default' => '1'
					]
				);

				$this->add_control(
					'show_supporters',
					[
						'label' => __( 'Supporters', 'trx_addons' ),
						'description' => wp_kses_data( __("How many supporters show in the list", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 20
							]
						]
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_trx_donations_list_titles',
					[
						'label' => __( 'Title & Button', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'subtitle',
					[
						'label' => __( 'Subtitle', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'description',
					[
						'label' => __( 'Description', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => ''
					]
				);

				$this->add_control(
					'link',
					[
						'label' => __( 'Link', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::URL,
						'default' => ['url' => '']
					]
				);

				$this->add_control(
					'link_caption',
					[
						'label' => __( 'Link text', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if ( shortcode_exists( 'trx_donations_info' ) ) {
					$atts = $this->sc_prepare_atts( $this->get_settings(), $this->get_sc_name() );
					trx_addons_show_layout(
						do_shortcode(
							sprintf( '[trx_donations_info'
										. ' donation="%1$s"'
										. ' title="%2$s"'
										. ' subtitle="%3$s"'
										. ' description="%4$s"'
										. ' link="%5$s"'
										. ' link_caption="%6$s"'
										. ' show_featured="%7$s"'
										. ' show_title="%8$s"'
										. ' show_excerpt="%9$s"'
										. ' show_goal="%10$s"'
										. ' show_raised="%11$s"'
										. ' show_scale="%12$s"'
										. ' show_supporters="%13$s"'
										. ']',
										$atts['donation'],
										$atts['title'],
										$atts['subtitle'],
										str_replace('"', "'", $atts['description']),
										$atts['link'],
										$atts['link_caption'],
										$atts['show_featured'],
										$atts['show_title'],
										$atts['show_excerpt'],
										$atts['show_goal'],
										$atts['show_raised'],
										$atts['show_scale'],
										$atts['show_supporters']
							)
						)
					);
				} else
					$this->shortcode_not_exists('trx_donations_info', 'ThemeREX Donations');
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Trx_Donations_Info' );
	}
}
