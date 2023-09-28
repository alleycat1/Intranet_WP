<?php
/**
 * Add a meta box with a field 'Price' for CPT, specified in settings
 *
 * @addon CPT to Cart
 * @version 1.0
 *
 * @package ThemeREX Addons
 * @since 2.13.0
 */


class TrxAddons_CPT_To_Cart_Metabox {

	/**
	 * The class arguments.
	 *
	 * @since 2.13.0
	 * @access private
	 * 
	 * @var array
	 */
	private $args = array();

	/**
	 * Constructor.
	 *
	 * @since 2.13.0
	 * @access public
	 * 
	 * @param array $args The arguments.
	 * 
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$this->args = wp_parse_args( $args, array(
			'post_type' => '',
			'name'      => esc_attr__( 'Price', 'trx_addons' ),
		) );
		$this->args['metabox_id']       = str_replace( 'trx_addons_options_', '', TRX_ADDONS_CPT_TO_CART_PRICE_FIELD_NAME );
		$this->args['post_meta_key']    = TRX_ADDONS_CPT_TO_CART_PRICE_FIELD_NAME;
		$this->args['nonce_action']     = $this->args['metabox_id'] . '_nonce_action';
		$this->args['nonce_name']       = $this->args['metabox_id'] . '_nonce_name';

		if ( ! empty( $this->args['post_type'] ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ) );
		}
	}

	/**
	 * Add a metabox with a field 'Price' for allowed CPT.
	 *
	 * @since 2.13.0
	 * @access public
	 * 
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->args['metabox_id'],
			$this->args['name'],
			array( $this, 'show_meta_box' ),
			$this->args['post_type'],
			'side',
			'default'
		);
	}

	/**
	 * Output the metabox content.
	 *
	 * @since 2.13.0
	 * @access public
	 * 
	 * @global object $post
	 * 
	 * @return void
	 */
	public function show_meta_box() {
		global $post;

		$price  = get_post_meta( $post->ID, $this->args['post_meta_key'], true );

		$output = '<div class="trx_addons-cpt-to-cart-meta-box">'
						. wp_nonce_field( $this->args['nonce_action'], $this->args['nonce_name'], true, false )
						. '<input type="hidden"'
							. ' id="' . esc_attr( $this->args['post_meta_key'] ) . '_pt"'
							. ' name="' . esc_attr( $this->args['post_meta_key'] ) . '_pt"'
							. ' value="' . esc_attr( $this->args['post_type'] ) . '"'
						. '>'
						. '<input type="text"'
								. ' id="' . esc_attr( $this->args['post_meta_key'] ) . '"'
								. ' name="' . esc_attr( $this->args['post_meta_key'] ) . '"'
								. ' value="' . esc_attr( $price ) . '"'
								. '>'
					. '</div>';

		trx_addons_show_layout( $output );
	}

	/**
	 * Saves the metabox.
	 *
	 * @since 2.13.0
	 * @access public
	 * 
	 * @param string|int $post_id The post ID.
	 * 
	 * @return void.
	 */
	public function save_meta_box( $post_id ) {
		// check nonce
		if ( ! wp_verify_nonce( trx_addons_get_value_gp( $this->args['nonce_name'] ), $this->args['nonce_action'] ) ) {
			return;
		}
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// check permissions
		$capability = 'post';
		$post_type = trx_addons_get_value_gp( $this->args['post_meta_key'] . '_pt', trx_addons_get_value_gp( 'post_type' ) );
		if ( ! empty( $post_type ) ) {
			$post_types = get_post_types( array( 'name' => $post_type ), 'objects' );
			if ( ! empty( $post_types ) && is_array( $post_types ) ) {
				foreach ( $post_types  as $type ) {
					$capability = $type->capability_type;
					break;
				}
			}
		}
		if ( ! current_user_can( "edit_{$capability}", $post_id ) ) {
			return $post_id;
		}
		// save post meta
		$price = sanitize_text_field( trx_addons_get_value_gp( $this->args['post_meta_key'] ) );
		update_post_meta( $post_id, $this->args['post_meta_key'], $price );
	}
}


// Init a meta box support for selected post types
if ( ! function_exists( 'trx_addons_cpt_to_cart_init_meta_boxes' ) ) {
	add_filter( 'init', 'trx_addons_cpt_to_cart_init_meta_boxes' );
	function trx_addons_cpt_to_cart_init_meta_boxes() {
		$post_types = trx_addons_cpt_to_cart_get_allowed_post_types();
		if ( is_array( $post_types ) ) {
			foreach( $post_types as $pt ) {
				new TrxAddons_CPT_To_Cart_Metabox( array(
					'post_type' => $pt
				) );
			}
		}
	}
}
