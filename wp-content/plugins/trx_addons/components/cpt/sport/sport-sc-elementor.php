<?php
/**
 * ThemeREX Addons: Sports Reviews Management (SRM).
 *                  Support different sports, championships, rounds, matches and players.
 *                  (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_matches_add_in_elementor')) {
	
	// Load required styles and scripts for Elementor Editor mode
	if ( !function_exists( 'trx_addons_sc_matches_elm_editor_load_scripts' ) ) {
		add_action("elementor/editor/before_enqueue_scripts", 'trx_addons_sc_matches_elm_editor_load_scripts');
		function trx_addons_sc_matches_elm_editor_load_scripts() {
			wp_enqueue_script( 'trx_addons-sc_sport-elementor-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'sport/sport.elementor.editor.js'), array('jquery'), null, true );
		}
	}
	
	// Register widgets
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_matches_add_in_elementor' );
	function trx_addons_sc_matches_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Matches extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_matches';
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
				return __( 'Matches', 'trx_addons' );
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
				return 'eicon-accordion';
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
				return ['trx_addons-cpt'];
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
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists                                                          
				$sports_list = ! $is_edit_mode ? array() : trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				$sport_default = trx_addons_get_option('sport_favorite');
				$sport = !empty($params['sport']) ? $params['sport'] : $sport_default;
				if (empty($sport) && count($sports_list) > 0) {
					$keys = array_keys($sports_list);
					$sport = $keys[0];
				}
				$competitions_list = ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_COMPETITIONS_PT,
																'taxonomy' => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
																'taxonomy_value' => $sport,
																'meta_key' => 'trx_addons_competition_date',
																'orderby' => 'meta_value',
																'order' => 'ASC',
																'not_selected' => false
																));
				$competition = !empty($params['competition']) ? $params['competition'] : '';
				if ((empty($competition) || !isset($competitions_list[$competition])) && count($competitions_list) > 0) {
					$competition = trx_addons_array_get_first($competitions_list);
				}
				$rounds_list = ! $is_edit_mode ? array() : trx_addons_array_merge(array(
													'last' => esc_html__('Last round', 'trx_addons'),
													'next' => esc_html__('Next round', 'trx_addons')
													), trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
																'post_parent' => $competition,
																'meta_key' => 'trx_addons_round_date',
																'orderby' => 'meta_value',
																'order' => 'ASC',
																'not_selected' => false
																)
													)
								);

				$this->start_controls_section(
					'section_sc_matches',
					[
						'label' => __( 'Matches', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
																			'default' => esc_html__('Default', 'trx_addons')
																			), 'trx_sc_matches'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'sport',
					[
						'label' => __( 'Sport', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $sports_list,
						'default' => $sport_default
					]
				);

				$this->add_control(
					'competition',
					[
						'label' => __( 'Competition', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $competitions_list
					]
				);

				$this->add_control(
					'round',
					[
						'label' => __( 'Round', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $rounds_list
					]
				);

				$this->add_control(
					'main_matches',
					[
						'label' => __( 'Main matches', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show large items marked as main match of the round", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'position',
					[
						'label' => __( 'Position of the matches list', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_matches_positions(),
						'default' => 'top',
						'condition' => [
							'main_matches' => '1'
						]
					]
				);

				$this->add_control(
					'slider',
					[
						'label' => __( 'Slider', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show main matches as slider (if two and more)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'main_matches' => '1'
						]
					]
				);

				$this->add_query_param('', ['columns' => false], array(TRX_ADDONS_CPT_COMPETITIONS_PT,
																		TRX_ADDONS_CPT_ROUNDS_PT,
																		TRX_ADDONS_CPT_PLAYERS_PT,
																		TRX_ADDONS_CPT_MATCHES_PT));

				$this->end_controls_section();
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Matches' );
	}
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_points_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_points_add_in_elementor' );
	function trx_addons_sc_points_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Points extends TRX_Addons_Elementor_Widget {

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
					'accented_top' => 'size',
					'accented_bottom' => 'size'
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
				return 'trx_sc_points';
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
				return __( 'Points', 'trx_addons' );
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
				return 'eicon-table';
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
				return ['trx_addons-cpt'];
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
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists                                                          
				$sports_list = ! $is_edit_mode ? array() : trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				$sport_default = trx_addons_get_option('sport_favorite');
				$sport = !empty($params['sport']) ? $params['sport'] : $sport_default;
				if (empty($sport) && count($sports_list) > 0) {
					$sport = trx_addons_array_get_first($sports_list);
				}
				$competitions_list = ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_COMPETITIONS_PT,
																'taxonomy' => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
																'taxonomy_value' => $sport,
																'meta_key' => 'trx_addons_competition_date',
																'orderby' => 'meta_value',
																'order' => 'ASC',
																'not_selected' => false
																));

				$this->start_controls_section(
					'section_sc_points',
					[
						'label' => __( 'Points', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
																			'default' => esc_html__('Default', 'trx_addons')
																			), 'trx_sc_points'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'sport',
					[
						'label' => __( 'Sport', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $sports_list,
						'default' => $sport_default
					]
				);

				$this->add_control(
					'competition',
					[
						'label' => __( 'Competition', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $competitions_list
					]
				);

				$this->add_control(
					'logo',
					[
						'label' => __( 'Show logo', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'return_value' => '1'
					]
				);
				
				$this->add_control(
					'accented_top',
					[
						'label' => __( 'Accented top', 'trx_addons' ),
						'description' => wp_kses_data( __("How many rows should be accented at the top of the table?", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10
							]
						]
					]
				);
				
				$this->add_control(
					'accented_bottom',
					[
						'label' => __( 'Accented bottom', 'trx_addons' ),
						'description' => wp_kses_data( __("How many rows should be accented at the bottom of the table?", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10
							]
						]
					]
				);

				$this->end_controls_section();
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Points' );
	}
}
