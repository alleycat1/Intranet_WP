<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Attachment' ) ) :

	final class WPSC_Attachment {

		/**
		 * Object data in key => val pair.
		 *
		 * @var array
		 */
		private $data = array();

		/**
		 * Set whether or not current object properties modified
		 *
		 * @var boolean
		 */
		private $is_modified = false;

		/**
		 * Schema for this model
		 *
		 * @var array
		 */
		public static $schema;

		/**
		 * Prevent fields to modify
		 *
		 * @var array
		 */
		public static $prevent_modify;

		/**
		 * Catche for search results for ticket list of attachments.
		 *
		 * @var array
		 */
		private static $tl_search_items;

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Apply schema for this model.
			add_action( 'init', array( __CLASS__, 'apply_schema' ), 2 );

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// File upload.
			add_action( 'wp_ajax_wpsc_file_upload', array( __CLASS__, 'file_upload' ) );
			add_action( 'wp_ajax_nopriv_wpsc_file_upload', array( __CLASS__, 'file_upload' ) );

			// Custom image in tinymce.
			add_action( 'wp_ajax_wpsc_add_custom_image_tinymce', array( __CLASS__, 'add_custom_image_tinymce' ) );
			add_action( 'wp_ajax_nopriv_wpsc_add_custom_image_tinymce', array( __CLASS__, 'add_custom_image_tinymce' ) );
			add_action( 'wp_ajax_wpsc_edit_custom_image_tinymce', array( __CLASS__, 'edit_custom_image_tinymce' ) );
			add_action( 'wp_ajax_nopriv_wpsc_edit_custom_image_tinymce', array( __CLASS__, 'edit_custom_image_tinymce' ) );

			// Check download file.
			add_action( 'init', array( __CLASS__, 'check_download_file' ), 100 );

			// Upload tinymce image.
			add_action( 'wp_ajax_wpsc_tinymce_upload_file', array( __CLASS__, 'tinymce_upload_file' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tinymce_upload_file', array( __CLASS__, 'tinymce_upload_file' ) );

			// Attachment garbage collector.
			add_action( 'wpsc_attach_garbage_collector', array( __CLASS__, 'garbage_collector' ) );

		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema       = array(
				'id'           => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'name'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'file_path'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_image'     => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_active'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_uploaded'  => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'date_created' => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'source'       => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'source_id'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'ticket_id'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'customer_id'  => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_attachment_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_attachment_prevent_modify', $prevent_modify );
		}

		/**
		 * Model constructor
		 *
		 * @param int $id - Optional. Data record id to retrive object for.
		 */
		public function __construct( $id = 0 ) {

			global $wpdb;

			$id = intval( $id );

			if ( $id > 0 ) {

				$attachment = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_attachments WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $attachment ) ) {
					return;
				}

				foreach ( $attachment as $key => $val ) {
					$this->data[ $key ] = $val !== null ? $val : '';
				}
			}
		}

		/**
		 * Convert object into an array
		 *
		 * @return array
		 */
		public function to_array() {

			return $this->data;
		}

		/**
		 * Magic get function to use with object arrow function
		 *
		 * @param string $var_name - variable name.
		 * @return mixed
		 */
		public function __get( $var_name ) {

			if ( ! isset( $this->data[ $var_name ] ) ||
				$this->data[ $var_name ] == null ||
				$this->data[ $var_name ] == ''
			) {
				return self::$schema[ $var_name ]['has_multiple_val'] ? array() : '';
			}

			if ( self::$schema[ $var_name ]['has_multiple_val'] ) {

				$response = array();
				$values   = $this->data[ $var_name ] ? explode( '|', $this->data[ $var_name ] ) : array();
				foreach ( $values as $val ) {
					$response[] = self::$schema[ $var_name ]['has_ref'] ?
									WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $val ) :
									$val;
				}
				return $response;

			} else {

				return self::$schema[ $var_name ]['has_ref'] && $this->data[ $var_name ] ?
					WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $this->data[ $var_name ] ) :
					$this->data[ $var_name ];
			}
		}

		/**
		 * Magic function to use setting object field with arrow function
		 *
		 * @param string $var_name - (Required) property slug.
		 * @param mixed  $value - (Required) value to set for a property.
		 * @return void
		 */
		public function __set( $var_name, $value ) {

			if (
				! isset( $this->data[ $var_name ] ) ||
				in_array( $var_name, self::$prevent_modify )
			) {
				return;
			}

			$data_val = '';
			if ( self::$schema[ $var_name ]['has_multiple_val'] ) {

				$data_vals = array_map(
					fn( $val ) => is_object( $val ) ? WPSC_Functions::set_object( self::$schema[ $var_name ]['ref_class'], $val ) : $val,
					$value
				);

				$data_val = $data_vals ? implode( '|', $data_vals ) : '';

			} else {

				$data_val = is_object( $value ) ? WPSC_Functions::set_object( self::$schema[ $var_name ]['ref_class'], $value ) : $value;
			}

			if ( $this->data[ $var_name ] == $data_val ) {
				return;
			}

			$this->data[ $var_name ] = $data_val;
			$this->is_modified       = true;
		}

		/**
		 * Save changes made
		 *
		 * @return boolean
		 */
		public function save() {

			global $wpdb;

			if ( ! $this->is_modified ) {
				return true;
			}

			$data    = $this->data;
			$success = true;

			if ( ! isset( $data['id'] ) ) {

				$at = self::insert( $data );
				if ( $at ) {
					$this->data = $at->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_attachments',
					$data,
					array( 'id' => $this->data['id'] )
				);
			}
			$this->is_modified = false;
			return $success ? true : false;
		}

		/**
		 * Insert new attachment
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Attachment
		 */
		public static function insert( $data ) {

			global $wpdb;

			$data['is_active'] = isset( $data['is_active'] ) ? $data['is_active'] : 0;

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_attachments',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$attachment = new WPSC_Attachment( $wpdb->insert_id );
			return $attachment;
		}

		/**
		 * Make it inactive so that garbage collector will delete files associated in
		 * background and then delete the record. This will improve its performance.
		 *
		 * @param WPSC_Attachment $attachment - attachment object.
		 * @return boolean
		 */
		public static function destroy( $attachment ) {

			$attachment->is_active = 0;
			$attachment->save();
			return true;
		}

		/**
		 * Set data to create new object using direct data. Used in find method
		 *
		 * @param array $data - data to set for object.
		 * @return void
		 */
		private function set_data( $data ) {

			foreach ( $data as $var_name => $val ) {
				$this->data[ $var_name ] = $val !== null ? $val : '';
			}
		}

		/**
		 * Find records based on given filters
		 *
		 * @param array   $filter - array containing array items like search, where, orderby, order, page_no, items_per_page, etc.
		 * @param boolean $is_object - return data as array or object. Default object.
		 * @return mixed
		 */
		public static function find( $filter = array(), $is_object = true ) {

			global $wpdb;

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_attachments ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 5;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'id';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

			$order = WPSC_Functions::parse_order( $filter );
			$limit = WPSC_Functions::parse_limit( $filter );

			$sql = $sql . $where . $order . $limit;

			$results     = $wpdb->get_results( $sql, ARRAY_A );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			$response = WPSC_Functions::parse_response( $results, $total_items, $filter );

			// Return array.
			if ( ! $is_object ) {
				return $response;
			}

			// create and return array of objects.
			$temp_results = array();
			foreach ( $response['results'] as $attachment ) {

				$ob   = new WPSC_Attachment();
				$data = array();
				foreach ( $attachment as $key => $val ) {
					$data[ $key ] = $val;
				}
				$ob->set_data( $data );
				$temp_results[] = $ob;
			}
			$response['results'] = $temp_results;

			return $response;
		}

		/**
		 * Get where for find method
		 *
		 * @param array $filter - user filter.
		 * @return array
		 */
		private static function get_where( $filter ) {

			$where = array( '1=1' );

			// Set user defined filters.
			$meta_query = isset( $filter['meta_query'] ) ? $filter['meta_query'] : array();
			if ( $meta_query ) {
				$where[] = WPSC_Functions::parse_user_filters( __CLASS__, $meta_query );
			}

			// Search.
			$search = WPSC_Functions::get_filter_search_str( $filter );
			if ( $search ) {
				$search_query = array(
					'CONVERT(name USING utf8) LIKE \'%' . $search . '%\'',
				);
				$search_query = apply_filters( 'wpsc_attachment_search_query', $search_query, $filter );
				$where[]      = '( ' . implode( ' OR ', $search_query ) . ' )';
			}

			return 'WHERE ' . implode( ' AND ', $where ) . ' ';
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_attachment'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Clear records and actual files for attachments that are not active for more than 24 hrs.
		 * This uses cron-job for background running to improve performace of load-time while deleting attachments.
		 *
		 * @return void
		 */
		public static function garbage_collector() {

			global $wpdb;
			$d = ( new DateTime() )->sub( new DateInterval( 'P1D' ) );

			$attachments = self::find(
				array(
					'items_per_page' => 20,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'date_created',
							'compare' => '<',
							'val'     => $d->format( 'Y-m-d H:i:s' ),
						),
						array(
							'slug'    => 'is_active',
							'compare' => '=',
							'val'     => '0',
						),
					),
				)
			)['results'];
			foreach ( $attachments as $attachment ) {

				$upload_dir = wp_upload_dir();
				$file_path = $upload_dir['basedir'] . $attachment->file_path;
				if ( file_exists( $file_path ) ) {
					unlink( $file_path );
				}
				$wpdb->query( "DELETE FROM {$wpdb->prefix}psmsc_attachments WHERE id=" . $attachment->id );
			}
		}

		/**
		 * Ajax callback for attachment file upload
		 *
		 * @return void
		 */
		public static function file_upload() {

			if ( check_ajax_referer( 'wpsc_file_upload', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Text_Editor::is_allow_attachments() ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized' ), 401 );
			}

			$recaptcha = get_option( 'wpsc-recaptcha-settings' );
			if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
				WPSC_MS_Recaptcha::validate( 'file_upload' );
			}

			$file = isset( $_FILES['wpscFileAttachment'] ) ? $_FILES['wpscFileAttachment'] : false; // phpcs:ignore
			if ( ! $file ) {
				wp_send_json_error( 'File not found!', 400 );
			}

			$file_settings = get_option( 'wpsc-gs-file-attachments' );
			$filename      = time() . '_' . sanitize_file_name( $file['name'] );
			$extension     = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
			$today         = new DateTime();
			$upload_dir    = wp_upload_dir();

			// Allowed file extension.
			$allowed_file_extensions = explode( ',', $file_settings['allowed-file-extensions'] );
			$allowed_file_extensions = array_map( 'trim', $allowed_file_extensions );
			$allowed_file_extensions = array_map( 'strtolower', $allowed_file_extensions );
			if ( ! ( in_array( $extension, $allowed_file_extensions ) ) ) {
				wp_send_json_error( 'File extension not allowed!', 400 );
			}

			// Allowed file size.
			$allowed_file_size = intval( $file_settings['attachments-max-filesize'] ) * 1000000;
			if ( ! ( isset( $file['size'] ) && $file['size'] <= $allowed_file_size ) ) {
				wp_send_json_error( 'File size exceeds allowed limit!', 400 );
			}

			// Init attachment data.
			$data = array(
				'name'         => sanitize_file_name( $file['name'] ),
				'date_created' => $today->format( 'Y-m-d H:i:s' ),
			);

			// Check for image type. Add a ".txt" extension to non-image file to prevent executing uploaded files on server.
			$img_extensions = array( 'png', 'jpeg', 'jpg', 'bmp', 'pdf', 'gif' );
			if ( ! in_array( $extension, $img_extensions ) ) {
				$filename .= '.txt';
			} else {
				$data['is_image'] = 1;
			}

			// File path.
			$file_path = $upload_dir['basedir'] . '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' );
			if ( ! file_exists( $file_path ) ) {
				mkdir( $file_path, 0755, true );
			}
			$file_path .= '/' . $filename;

			$filepath_short = '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' ) . '/' . $filename;
			$data['file_path'] = $filepath_short;

			// Insert record in database.
			if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {

				$attachment = self::insert( $data );
				if ( ! $attachment->id ) {
					wp_send_json_error( 'Something went wrong 1!', 500 );
				}

				wp_send_json( array( 'id' => $attachment->id ) );

			} else {

				wp_send_json_error( 'Something went wrong 2!', 500 );
			}
		}

		/**
		 * Returns attachment ids to be used for search in ticket list
		 *
		 * @param string $search - search string.
		 * @return array
		 */
		public static function get_tl_search_string( $search ) {

			$search_items = array();
			if ( is_array( self::$tl_search_items ) ) {
				$search_items = self::$tl_search_items;
			} else {
				$attachments = self::find(
					array(
						'search'         => $search,
						'items_per_page' => 0,
					)
				)['results'];
				if ( $attachments ) {
					foreach ( $attachments as $attachment ) {
						$search_items[] = $attachment->id;
					}
				}
				self::$tl_search_items = $search_items;
			}

			return $search_items;
		}

		/**
		 * Custom tinymce image popup
		 *
		 * @return void
		 */
		public static function add_custom_image_tinymce() {

			if ( check_ajax_referer( 'wpsc_add_custom_image_tinymce', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$title = esc_attr__( 'Insert/edit image', 'supportcandy' );

			$editor_id = isset( $_POST['editor_id'] ) ? sanitize_text_field( wp_unslash( $_POST['editor_id'] ) ) : '';
			if ( ! $editor_id ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			ob_start();?>
			<form action="#" onsubmit="return false;" class="wpsc-insert-edit-image">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Source', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<div style="display:flex;">
						<input type="text" accept="image/*" id="wpsc-tinymce-image-url" class="wpsc-tinymce-image-url" style="flex-grow: 1;" autocomplete="off">
						<button id="wpsc-tinymce-get-file-url" class="wpsc-button small secondary" style="margin-left: 2px; width:100px;" onclick="wpsc_tinymce_image_picker();"><?php esc_attr_e( 'Upload', 'supportcandy' ); ?></button>
						<input type='file' accept="image/*" name='fileupload' id='wpsc-fileupload' style='display: none;'>
					</div>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Dimentions', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<div style="display: flex; align-items:center;">
						<input type="text" id="wpsc-tinymce-image-width" aria-label="width" class="wpsc-image-dimention" style="width:70px; margin-right:5px;" autocomplete="off">
						<span class="wpsc-image-dimention-x-sign" style="margin-right:5px;">x</span>
						<input type="text" id="wpsc-tinymce-image-height" aria-label="height" class="wpsc-image-dimention" style="width:70px; margin-right:10px;" autocomplete="off">
						<div class="checkbox-container">
							<?php $unique_id = uniqid( 'wpsc_' ); ?>
							<input id="<?php echo esc_attr( $unique_id ); ?>" type="checkbox" checked value="1"/>
							<label for="<?php echo esc_attr( $unique_id ); ?>"><?php esc_attr_e( 'Constraint properties', 'supportcandy' ); ?></label>
						</div>
					</div>
				</div>
				<script>
					jQuery(".wpsc-tinymce-image-url").change(function(){

						var img = new Image();
						img.src = jQuery(this).val();
						img.onload = function() {
							jQuery('#wpsc-tinymce-image-height').val(this.height);
							jQuery('#wpsc-tinymce-image-width').val(this.width);
							var aspectRatio = this.width/this.height; 
							//Get new height:
							jQuery("#wpsc-tinymce-image-width").on("change", function(){

								if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
									newWidth  = jQuery(this).val();
									newHeight = Math.round(newWidth/aspectRatio);
									jQuery("#wpsc-tinymce-image-height").val(newHeight);
								}
							});
							//Get new width:
							jQuery("#wpsc-tinymce-image-height").on("change", function(){

								if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
									newHeight = jQuery(this).val();
									newWidth  = Math.round(newHeight*aspectRatio);
									jQuery("#wpsc-tinymce-image-width").val(newWidth);
								}
							});
						}
					});

					/**
					 * Tinymce image picker
					 */
					function wpsc_tinymce_image_picker() {

						jQuery("#wpsc-fileupload").trigger("click");
						jQuery("#wpsc-fileupload").unbind('change');

						jQuery("#wpsc-fileupload").on("change", function() {
							var file = this.files[0];
							var dataform = new FormData();
							dataform.append('file', file);
							dataform.append('file_name', file.name);
							dataform.append('action', 'wpsc_tinymce_upload_file');
							dataform.append('_ajax_nonce', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tinymce_upload_file' ) ); ?>');

							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								processData: false,
								contentType: false
							}).done(function (res) {

								jQuery('.wpsc-tinymce-image-url').val(res.imgURL);
								var img = new Image();
								img.src = res.imgURL;
								img.onload = function() {

									jQuery('#wpsc-tinymce-image-height').val(this.height);
									jQuery('#wpsc-tinymce-image-width').val(this.width);
									var aspectRatio = this.width/this.height; 

									//Get new height:
									jQuery("#wpsc-tinymce-image-width").on("change", function() {

										if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
											newWidth  = jQuery(this).val();
											newHeight = Math.round(newWidth/aspectRatio);
											jQuery("#wpsc-tinymce-image-height").val(newHeight);
										}
									});

									//Get new width:
									jQuery("#wpsc-tinymce-image-height").on("change", function() {

										if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
											newHeight = jQuery(this).val();
											newWidth  = Math.round(newHeight*aspectRatio);
											jQuery("#wpsc-tinymce-image-width").val(newWidth);
										}
									});
								};
							});    
						});
					}
				</script>
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_insert_editor_img('<?php echo esc_attr( $editor_id ); ?>');">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Custom tinymce image popup
		 *
		 * @return void
		 */
		public static function edit_custom_image_tinymce() {

			if ( check_ajax_referer( 'wpsc_edit_custom_image_tinymce', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$title = esc_attr__( 'Edit image', 'supportcandy' );

			$editor_id = isset( $_POST['editor_id'] ) ? sanitize_text_field( wp_unslash( $_POST['editor_id'] ) ) : '';
			if ( ! $editor_id ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$height = isset( $_POST['height'] ) ? intval( $_POST['height'] ) : '';
			if ( ! $height ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$width = isset( $_POST['width'] ) ? intval( $_POST['width'] ) : '';
			if ( ! $width ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$src = isset( $_POST['src'] ) ? esc_url_raw( wp_unslash( $_POST['src'] ) ) : '';
			if ( ! $src ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-insert-edit-image">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Source', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<div style="display:flex;">
						<input type="text" id="wpsc-tinymce-image-url" class="wpsc-tinymce-image-url" style="flex-grow: 1;" autocomplete="off" value="<?php echo esc_attr( $src ); ?>">
						<button id="wpsc-tinymce-get-file-url" class="wpsc-button small secondary" style="margin-left: 2px; width:100px" onclick="wpsc_tinymce_image_picker();"><?php esc_attr_e( 'Upload', 'supportcandy' ); ?></button>
						<input type='file' accept="image/*" name='fileupload' id='wpsc-fileupload' style='display: none;'>
					</div>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Dimentions', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<div style="display: flex; align-items:center;">
						<input type="text" id="wpsc-tinymce-image-width" aria-label="width" class="wpsc-image-dimention" style="width:70px; margin-right:5px;" autocomplete="off" value="<?php echo esc_attr( $width ); ?>">
						<span class="wpsc-image-dimention-x-sign" style="margin-right:5px;">x</span>
						<input type="text" id="wpsc-tinymce-image-height" aria-label="height" class="wpsc-image-dimention" style="width:70px; margin-right:10px;" autocomplete="off" value="<?php echo esc_attr( $height ); ?>" >
						<div class="checkbox-container">
							<?php $unique_id = uniqid( 'wpsc_' ); ?>
							<input id="<?php echo esc_attr( $unique_id ); ?>" type="checkbox" checked value="1"/>
							<label for="<?php echo esc_attr( $unique_id ); ?>"><?php esc_attr_e( 'Constraint properties', 'supportcandy' ); ?></label>
						</div>
					</div>
				</div>
				<script>
					jQuery(".wpsc-tinymce-image-url").change(function(){

						var img = new Image();
						img.src = jQuery(this).val();
						img.onload = function() {
							jQuery('#wpsc-tinymce-image-height').val(this.height);
							jQuery('#wpsc-tinymce-image-width').val(this.width);
							var aspectRatio = this.width/this.height; 
							//Get new height:
							jQuery("#wpsc-tinymce-image-width").on("change", function(){

								if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
									newWidth  = jQuery(this).val();
									newHeight = Math.round(newWidth/aspectRatio);
									jQuery("#wpsc-tinymce-image-height").val(newHeight);
								}
							});
							//Get new width:
							jQuery("#wpsc-tinymce-image-height").on("change", function(){

								if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
									newHeight = jQuery(this).val();
									newWidth  = Math.round(newHeight*aspectRatio);
									jQuery("#wpsc-tinymce-image-width").val(newWidth);
								}
							});
						}
					});
					var height = <?php echo esc_attr( $height ); ?>;
					var width  = <?php echo esc_attr( $width ); ?>;
					var aspectRatio = width/height; 
					jQuery("#wpsc-tinymce-image-width").on("change", function(){

						if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
							newWidth  = jQuery(this).val();
							newHeight = Math.round(newWidth/aspectRatio);
							jQuery("#wpsc-tinymce-image-height").val(newHeight);
						}
					});
					//Get new width:
					jQuery("#wpsc-tinymce-image-height").on("change", function(){

						if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
							newHeight = jQuery(this).val();
							newWidth  = Math.round(newHeight*aspectRatio);
							jQuery("#wpsc-tinymce-image-width").val(newWidth);
						}
					});

					/**
					 * Tinymce image picker
					 */
					function wpsc_tinymce_image_picker() {

						jQuery("#wpsc-fileupload").trigger("click");
						jQuery("#wpsc-fileupload").unbind('change');

						jQuery("#wpsc-fileupload").on("change", function() {
							var file = this.files[0];
							var dataform = new FormData();
							dataform.append('file', file);
							dataform.append('file_name', file.name);
							dataform.append('action', 'wpsc_tinymce_upload_file');
							dataform.append('_ajax_nonce', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tinymce_upload_file' ) ); ?>');

							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								processData: false,
								contentType: false
							}).done(function (res) {

								jQuery('.wpsc-tinymce-image-url').val(res.imgURL);
								var img = new Image();
								img.src = res.imgURL;
								img.onload = function() {

									jQuery('#wpsc-tinymce-image-height').val(this.height);
									jQuery('#wpsc-tinymce-image-width').val(this.width);
									var aspectRatio = this.width/this.height; 

									//Get new height:
									jQuery("#wpsc-tinymce-image-width").on("change", function() {

										if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
											newWidth  = jQuery(this).val();
											newHeight = Math.round(newWidth/aspectRatio);
											jQuery("#wpsc-tinymce-image-height").val(newHeight);
										}
									});

									//Get new width:
									jQuery("#wpsc-tinymce-image-height").on("change", function() {

										if(jQuery("#<?php echo esc_attr( $unique_id ); ?>").prop('checked') == true){
											newHeight = jQuery(this).val();
											newWidth  = Math.round(newHeight*aspectRatio);
											jQuery("#wpsc-tinymce-image-width").val(newWidth);
										}
									});
								};
							});    
						});
					}
				</script>
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_insert_editor_img('<?php echo esc_attr( $editor_id ); ?>');">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Check download file
		 *
		 * @return void
		 */
		public static function check_download_file() {

			if ( isset( $_REQUEST['wpsc_attachment'] ) ) { // phpcs:ignore

				if ( WPSC_Functions::is_site_admin() && ! extension_loaded( 'fileinfo' ) ) {
					echo 'The fileinfo extension is not available. Please enable it in your PHP configuration.';
					exit;
				}

				$current_user = WPSC_Current_User::$current_user;
				$has_auth = true;
				if ( isset( $_REQUEST['user'] ) && ! $current_user ) { // phpcs:ignore

					$customer = new WPSC_Customer( intval( $_REQUEST['user'] ) ); // phpcs:ignore
					if ( ! $customer->id ) {
						wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
					}

					$current_user = WPSC_Current_User::change_current_user( $customer->email );
					$has_auth = false;
				}

				$attachment_id = intval( $_REQUEST['wpsc_attachment'] ); // phpcs:ignore
				$attachment = new WPSC_Attachment( $attachment_id );
				if ( ! $attachment->id ) {
					return;
				}
				$auth_code = isset($_REQUEST['auth_code']) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
				$advanced = get_option( 'wpsc-ms-advanced-settings' );
				switch ( $attachment->source ) {

					case 'cf':
						$cf = new WPSC_Custom_Field( $attachment->source_id );
						if ( ! $cf->id ) {
							wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
						}

						if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) { // ticket field.

							$ticket = new WPSC_Ticket( $attachment->ticket_id );
							if ( ! $ticket->id ) {
								wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
							}

							if ( ! $has_auth ) {
								$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
								if ( ! $auth_code || $auth_code != $ticket->auth_code ) {
									wp_send_json_error( 'Unauthorized!', 401 );
								}
							}

							WPSC_Individual_Ticket::$ticket = $ticket;
							if ( ! (
								( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
								WPSC_Individual_Ticket::is_customer() ||
								( ! $advanced['ticket-url-auth'] && $ticket->auth_code == $auth_code )
							) ) {
								wp_send_json_error( 'Unauthorized!', 401 );
							}

							self::file_download( $attachment );

						} else { // customer field.

							$customer       = new WPSC_Customer( intval( $attachment->customer_id ) );
							$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
							$raised_by      = $ticket_widgets['raised-by'];

							if (
								$current_user->customer->id == $customer->id ||
								(
									$current_user->is_agent &&
									in_array( $current_user->agent->role, $raised_by['allowed-agent-roles'] )
								)
							) {
								self::file_download( $attachment );
							}
						}
						break;

					case 'reply':
					case 'report':
						$ticket = new WPSC_Ticket( $attachment->ticket_id );
						if ( ! $ticket->id ) {
							wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
						}

						if ( ! $has_auth ) {
							$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
							if ( ! $auth_code || $auth_code != $ticket->auth_code ) {
								wp_send_json_error( 'Unauthorized!', 401 );
							}
						}

						WPSC_Individual_Ticket::$ticket = $ticket;
						if ( ! (
							( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ||
							WPSC_Individual_Ticket::is_customer() ||
							( ! $advanced['ticket-url-auth'] && $ticket->auth_code == $auth_code )
						) ) {
							wp_send_json_error( 'Unauthorized!', 401 );
						}

						self::file_download( $attachment );
						break;

					case 'note':
						$ticket = new WPSC_Ticket( $attachment->ticket_id );
						if ( ! $ticket->id ) {
							wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
						}

						if ( ! $has_auth ) {
							$auth_code = isset( $_REQUEST['auth_code'] ) ? sanitize_text_field( $_REQUEST['auth_code'] ) : ''; // phpcs:ignore
							if ( ! $auth_code || $auth_code != $ticket->auth_code ) {
								wp_send_json_error( 'Unauthorized!', 401 );
							}
						}

						WPSC_Individual_Ticket::$ticket = $ticket;
						if ( ! (
							$current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'pn' )
						) ) {
							wp_send_json_error( 'Unauthorized!', 401 );
						}

						self::file_download( $attachment );
						break;

					case 'img_editor':
						self::file_download( $attachment );
						break;
				}
			}
		}

		/**
		 * Download attachment
		 *
		 * @param WPSC_Attachment $attachment - attachment object.
		 * @return void
		 */
		public static function file_download( $attachment ) {

			// Turn off output buffering.
			if ( ob_get_level() ) {
				ob_end_clean();
			}

			$upload_dir = wp_upload_dir();
			$file_path = $upload_dir['basedir'] . $attachment->file_path;
			if ( ! file_exists( $file_path ) ) {
				return;
			}

			if ( ob_get_length() > 0 ) {
				ob_clean();
			}

			// Check whether attachment is of image type.
			$attach_settings = get_option( 'wpsc-gs-file-attachments' );
			if ( $attachment->is_image && $attach_settings['image-download-behaviour'] == 'open-browser' ) {
				header( 'Content-Type: ' . mime_content_type( $file_path ) );
				readfile( $file_path ); // phpcs:ignore
				exit( 0 );
			}

			header( 'Content-Description: File Transfer' );
			header( 'Cache-Control: public' );
			header( 'Content-Type: application/force-download' );
			header( 'Content-Disposition: attachment;filename="' . $attachment->name . '"' );
			header( 'Content-Length: ' . filesize( $file_path ) );
			flush();
			readfile( $file_path ); // phpcs:ignore
			exit( 0 );
		}

		/**
		 * Upload tinymce image file to database
		 *
		 * @return void
		 */
		public static function tinymce_upload_file() {

			if ( check_ajax_referer( 'wpsc_tinymce_upload_file', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$file = isset( $_FILES['file'] ) ? $_FILES['file'] : false; // phpcs:ignore
			if ( ! $file ) {
				wp_send_json_error( 'File not found!', 400 );
			}

			$file_settings = get_option( 'wpsc-gs-file-attachments' );
			$filename      = time() . '_' . sanitize_file_name( $file['name'] );
			$extension     = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
			$today         = new DateTime();
			$upload_dir    = wp_upload_dir();

			// Check file extension.
			$img_extensions = array( 'jpg', 'jpeg', 'png', 'gif' );
			if ( ! ( in_array( $extension, $img_extensions ) ) ) {
				wp_send_json_error( 'Invalid file extension!', 400 );
			}

			// Allowed file size.
			$allowed_file_size = intval( $file_settings['attachments-max-filesize'] ) * 1000000;
			if ( ! ( isset( $file['size'] ) && $file['size'] <= $allowed_file_size ) ) {
				wp_send_json_error( 'File size exceeds allowed limit!', 400 );
			}

			// File path.
			$file_path = $upload_dir['basedir'] . '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' );
			if ( ! file_exists( $file_path ) ) {
				mkdir( $file_path, 0755, true );
			}
			$file_path .= '/' . $filename;

			$filepath_short = '/wpsc/' . $today->format( 'Y' ) . '/' . $today->format( 'm' ) . '/' . $filename;

			// Init attachment data.
			$data = array(
				'name'         => sanitize_file_name( $file['name'] ),
				'file_path'    => $filepath_short,
				'is_image'     => 1,
				'date_created' => $today->format( 'Y-m-d H:i:s' ),
				'is_active'    => 0,
				'source'       => 'img_editor',
			);

			// Insert record in database.
			if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {

				$attachment = self::insert( $data );
				if ( ! $attachment->id ) {
					wp_send_json_error( 'Something went wrong!', 500 );
				}

				wp_send_json( array( 'imgURL' => home_url( '/' ) . '?wpsc_attachment=' . $attachment->id ) );

			} else {

				wp_send_json_error( 'Something went wrong!', 500 );
			}
		}
	}
endif;

WPSC_Attachment::init();
