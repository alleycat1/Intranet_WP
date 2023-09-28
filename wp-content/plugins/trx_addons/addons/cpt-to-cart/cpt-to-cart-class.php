<?php
if ( ! class_exists( 'TrxAddons_WC_Product_Data_Store_CPT' ) && class_exists( 'WC_Product_Data_Store_CPT' ) ) {
	class TrxAddons_WC_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

		/**
		* Method to read a product from the database.
		* 
		* @param WC_Product
		*/
		public function read( &$product ) {

			$product->set_defaults();

			$post_id = $product->get_id();

			if ( ! $post_id
				|| ! ( $post_object = get_post( $post_id ) )
				|| ! in_array( $post_object->post_type, array_merge( array( 'product' ), trx_addons_cpt_to_cart_get_allowed_post_types() ) )
			) {
				throw new Exception( __( 'Invalid product.', 'trx_addons' ) );
			}

			$product->set_props( array(
				'name'              => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
				'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				'short_description' => $post_object->post_excerpt,
				'parent_id'         => $post_object->post_parent,
				'menu_order'        => $post_object->menu_order,
				'reviews_allowed'   => 'open' === $post_object->comment_status,
			) );

			$this->read_attributes( $product );
			$this->read_downloads( $product );
			$this->read_visibility( $product );
			$this->read_product_data( $product );
			$this->read_extra_data( $product );
			$product->set_object_read( true );
		}

		/**
		* Get the product type based on product ID.
		*
		* @param int $product_id
		* 
		* @return bool|string
		*/
		public function get_product_type( $product_id ) {
			$post_type = get_post_type( $product_id );
			if ( 'product_variation' === $post_type ) {
				return 'variation';
			} else if ( 'product' === $post_type ) {
				$terms = get_the_terms( $product_id, 'product_type' );
				return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			} else if ( in_array( $post_type, trx_addons_cpt_to_cart_get_allowed_post_types() ) ) {
				return 'simple';
			}
			return false;
		}
	}

	if ( ! function_exists( 'trx_addons_cpt_to_cart_woocommerce_data_stores' ) ) {
		add_filter( 'woocommerce_data_stores', 'trx_addons_cpt_to_cart_woocommerce_data_stores' );
		/**
		 * Replace a class name to allow CPT to be placed to the cart
		 * 
		 * Hook: add_filter( 'woocommerce_data_stores', 'trx_addons_cpt_to_cart_woocommerce_data_stores' );
		 * 
		 * @param array $stores  An array with a class names for each allowed post type.
		 * 
		 * @return array  A processed array with class names.
		 */
		function trx_addons_cpt_to_cart_woocommerce_data_stores ( $stores ) {
			$stores['product'] = 'TrxAddons_WC_Product_Data_Store_CPT';
			/*
			foreach ( trx_addons_cpt_to_cart_get_allowed_post_types() as $pt ) {
				$stores[ $pt ] = 'TrxAddons_WC_Product_Data_Store_CPT';
			}
			*/
			return $stores;
		}
	}
}
