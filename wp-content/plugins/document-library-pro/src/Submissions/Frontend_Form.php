<?php
namespace Barn2\Plugin\Document_Library_Pro\Submissions;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Template_Loader_Factory;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Template_Loader;
use Barn2\Plugin\Document_Library_Pro\Document;
use Barn2\Plugin\Document_Library_Pro\Util\Media;
use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Exception;
use JsonSerializable;
use WP_Error;

use function Barn2\Plugin\Document_Library_Pro\document_library_pro;

/**
 * Responsible for displaying the frontend submission form.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Form implements Registerable, JsonSerializable {

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Form errors.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Template loader instance.
	 *
	 * @var Template_Loader The template loader.
	 */
	private $template_loader;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->template_loader = Template_Loader_Factory::create();
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'load_posted_form' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_filter( 'upload_dir', [ $this, 'setup_upload_dir' ] );
	}

	/**
	 * Get the assets url.
	 *
	 * @param string $path
	 * @return string
	 */
	private function asset_url( $path ) {
		return document_library_pro()->get_dir_url() . 'assets/' . ltrim( $path, '/' );
	}

	/**
	 * Register assets.
	 *
	 * @return void
	 */
	public function register_scripts() {
		$script_dependencies = array_merge( Lib_Util::get_script_dependencies( document_library_pro(), 'dlp-submission-form.js' )['dependencies'], [ 'jquery' ] );

		wp_register_script( 'dlp-frontend-submission', $this->asset_url( 'js/dlp-submission-form.js' ), $script_dependencies, document_library_pro()->get_version(), true );
		wp_register_style( 'dlp-frontend-submission', $this->asset_url( 'css/dlp-submission-form.css' ), [], document_library_pro()->get_version() );

		wp_add_inline_script( 'dlp-frontend-submission', 'const DLP_Frontend_Submission = ' . wp_json_encode( $this ), 'before' );
	}

	/**
	 * Adds an error.
	 *
	 * @param string $error The error message.
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Detect submission of the form and handle it.
	 *
	 * @return void
	 */
	public function load_posted_form() {
		if ( ! isset( $_POST['dlp_frontend_nonce'] ) || ! wp_verify_nonce( $_POST['dlp_frontend_nonce'], 'dlp_frontend_submission' ) ) {
			return;
		}
		$this->process_form();
	}

	/**
	 * Sorts array by priority value.
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function sort_by_priority( $a, $b ) {
		if ( floatval( $a['priority'] ) === floatval( $b['priority'] ) ) {
			return 0;
		}
		return ( floatval( $a['priority'] ) < floatval( $b['priority'] ) ) ? -1 : 1;
	}

	/**
	 * Define the list of fields for the form.
	 *
	 * @return void
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}

		$enabled_fields = Options::get_document_fields();

		$fields = [
			'title'          => [
				'label'       => __( 'Title', 'document-library-pro' ),
				'type'        => 'text',
				'required'    => true,
				'placeholder' => __( 'Add title', 'document-library-pro' ),
				'priority'    => 1,
			],
			'custom_content' => [
				'label'    => __( 'Content', 'document-library-pro' ),
				'type'     => 'editor',
				'required' => false,
				'priority' => 2,
			],
			'excerpt'        => [
				'label'    => __( 'Excerpt', 'document-library-pro' ),
				'type'     => 'textarea',
				'required' => false,
				'priority' => 3,
			],
			'document_link'  => [
				'label'       => __( 'Document link', 'document-library-pro' ),
				'description' => __( 'Upload a file or add a URL where the document is located', 'document-library-pro' ),
				'type'        => 'select',
				'options'     => [
					'none' => __( 'None', 'document-library-pro' ),
					'file' => __( 'File Upload', 'document-library-pro' ),
					'url'  => __( 'A custom URL', 'document-library-pro' ),
				],
				'required'    => false,
				'priority'    => 4,
			],
			'document_url'   => [
				'label'       => __( 'Document url', 'document-library-pro' ),
				'type'        => 'text',
				'required'    => false,
				'placeholder' => 'https://',
				'priority'    => 5,
			],
			'document_file'  => [
				'label'    => __( 'Document file', 'document-library-pro' ),
				'type'     => 'file',
				'required' => false,
				'priority' => 6,
			],
			'category'       => [
				'label'       => __( 'Category', 'document-library-pro' ),
				'type'        => 'taxonomy',
				'taxonomy'    => 'doc_categories',
				'placeholder' => __( 'Select one or more categories...', 'document-library-pro' ),
				'required'    => false,
				'priority'    => 7,
			],
			'tags'           => [
				'label'       => __( 'Tags', 'document-library-pro' ),
				'type'        => 'taxonomy',
				'placeholder' => __( 'Select one or more tags...', 'document-library-pro' ),
				'required'    => false,
				'taxonomy'    => 'doc_tags',
				'priority'    => 8,
			],
			'authors'        => [
				'label'       => __( 'Authors', 'document-library-pro' ),
				'type'        => 'taxonomy',
				'placeholder' => __( 'Select one or more authors...', 'document-library-pro' ),
				'required'    => false,
				'taxonomy'    => 'doc_author',
				'priority'    => 9,
			],
			'email'          => [
				'label'        => 'Email',
				'type'         => 'text',
				'priority'     => 10,
				'required'     => false,
				'class'        => 'hp',
				'autocomplete' => false
			],
		];

		// Disable excerpt if not enabled.
		if ( ! in_array( 'excerpt', $enabled_fields, true ) ) {
			unset( $fields['excerpt'] );
		}

		if ( ! in_array( 'author', $enabled_fields, true ) ) {
			unset( $fields['authors'] );
		}

		if ( ! in_array( 'editor', $enabled_fields, true ) ) {
			unset( $fields['custom_content'] );
		}

		/**
		 * Filter: allows adjustments to the fields displayed
		 * on the frontend submission form.
		 *
		 * @param array $fields
		 * @return array
		 */
		$fields = apply_filters( 'document_library_pro_form_fields', $fields );

		$this->fields = $fields;
	}

	/**
	 * Gets fields for the form.
	 *
	 * @return array
	 */
	public function get_fields() {
		$fields = $this->fields;

		uasort( $fields, [ $this, 'sort_by_priority' ] );

		return $fields;
	}

	/**
	 * Checks whether a value is empty.
	 *
	 * @param string|numeric|array|boolean $value The value that is being checked.
	 * @param string                       $key   The key of the field that is being checked.
	 * @return bool True if value is empty, false otherwise.
	 */
	protected function is_empty( $value, $key = '' ) {
		/**
		 * Filter values considered as empty or falsy for required fields.
		 * Useful for example if you want to consider zero (0) as a non-empty value.
		 *
		 * @see http://php.net/manual/en/function.empty.php -- standard default empty values
		 *
		 * @param array  $false_vals A list of values considered as falsy.
		 * @param string $key        The key that this is being used for.
		 */
		$false_vals = apply_filters( 'dlp_form_validate_fields_empty_values', [ '', 0, 0.0, '0', null, false, [] ], $key );

		// strict true for type checking.
		if ( in_array( $value, $false_vals, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets post data for fields.
	 *
	 * @return array of data.
	 */
	protected function get_posted_fields() {
		$this->init_fields();

		$values = [];

		foreach ( $this->fields as $key => $field ) {
			// Get the value.
			$field_type = str_replace( '-', '_', $field['type'] );
			$handler    = apply_filters( "dlp_get_posted_{$field_type}_field", false );

			$this->fields[ $key ]['empty'] = false;

			if ( ! isset( $field['before_sanitize'] ) ) {
				$field['before_sanitize'] = function ( $value ) use ( $key ) {
					if ( is_string( $value ) ) {
						$value = trim( $value );
					}
					$this->fields[ $key ]['empty'] = $this->is_empty( $value, $key );
				};
			}

			if ( $handler ) {
				$values[ $key ] = call_user_func( $handler, $key, $field );
			} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
				$values[ $key ] = call_user_func( [ $this, "get_posted_{$field_type}_field" ], $key, $field );
			} else {
				$values[ $key ] = $this->get_posted_field( $key, $field );
			}

			$this->fields[ $key ]['value'] = $values[ $key ];
		}

		/**
		 * Alter values for posted fields.
		 *
		 * @param array  $values  The values that have been submitted.
		 * @param array  $fields  The form fields.
		 */
		return apply_filters( 'dlp_get_posted_fields', $values, $this->fields );
	}

	/**
	 * Sanitize the value of a field.
	 *
	 * @param mixed $value
	 * @param mixed $sanitizer
	 * @return mixed
	 */
	protected function sanitize_posted_field( $value, $sanitizer = null ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $val ) {
				$value[ $key ] = $this->sanitize_posted_field( $val, $sanitizer );
			}

			return $value;
		}

		$value = trim( $value );

		return sanitize_text_field( wp_unslash( $value ) );
	}

	/**
	 * Gets the value of a posted field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string|array
	 */
	protected function get_posted_field( $key, $field ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : false;

		return false !== $value ? $this->sanitize_posted_field( $value ) : '';
	}

	/**
	 * Gets the value of a posted file field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 *
	 * @return string|array
	 * @throws \Exception When the upload fails.
	 */
	protected function get_posted_file_field( $key, $field ) {
		$file = $this->upload_file( $key, $field );

		if ( ! $file ) {
			$file = $this->get_posted_field( 'current_' . $key, $field );
		} elseif ( is_array( $file ) ) {
			$file = array_filter( array_merge( $file, (array) $this->get_posted_field( 'current_' . $key, $field ) ) );
		}

		return $file;
	}

	/**
	 * Gets posted terms for the taxonomy.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return array
	 */
	protected function get_posted_taxonomy_field( $key, $field ) {
		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce check happens elsewhere. Sanitization below.
		$value = isset( $_POST[ $key ] ) && ! empty( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : false;

		if ( ! $value ) {
			return [];
		}

		return is_array( $value ) ? array_map( 'absint', $value ) : array_map( 'absint', explode( ',', $value ) );
	}

	/**
	 * Handles the uploading of files.
	 *
	 * @param string $field_key
	 * @param array  $field
	 * @throws Exception When file upload failed.
	 * @return  string|array
	 */
	protected function upload_file( $field_key, $field ) {
		$value = null;

		if ( isset( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ]['name'] ) ) {
			if ( ! empty( $field['allowed_mime_types'] ) ) {
				$allowed_mime_types = $field['allowed_mime_types'];
			} else {
				$allowed_mime_types = get_allowed_mime_types();
			}

			$file_urls       = [];
			$uploader        = new Uploader( $_FILES[ $field_key ] );
			$files_to_upload = $uploader->prepare_uploaded_files(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- see https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1720.

			foreach ( $files_to_upload as $file_to_upload ) {

				if ( isset( $field['size_limit'] ) && ! empty( $field['size_limit'] ) && $file_to_upload['size'] > $field['size_limit'] ) {
					throw new Exception( 'Uploaded file is too large.' );
				}

				$uploaded_file = $uploader->upload_file(
					$file_to_upload,
					[
						'file_key'           => $field_key,
						'allowed_mime_types' => $allowed_mime_types,
					]
				);

				if ( is_wp_error( $uploaded_file ) ) {
					throw new Exception( $uploaded_file->get_error_message() );
				} else {
					$file_urls[] = $uploaded_file->url;
				}
			}

			if ( ! empty( $field['multiple'] ) ) {
				$value = $file_urls;
			} else {
				$value = current( $file_urls );
			}
		}

		return $value;
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_textarea_field( $key, $field ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification -- Nonce check happens elsewhere.
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : false;

		return false !== $value ? trim( wp_kses_post( $value ) ) : '';
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_editor_field( $key, $field ) {
		return $this->get_posted_textarea_field( $key, $field );
	}

	/**
	 * Validates the posted fields.
	 *
	 * @param array $values
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 * @throws Exception Uploaded file is not a valid mime-type or other validation error
	 */
	protected function validate_fields( $values ) {
		foreach ( $this->fields as $key => $field ) {
			if (
				$field['required']
				&& ( ! isset( $field['empty'] ) || $field['empty'] )
			) {
				// translators: Placeholder %s is the label for the required field.
				return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'document-library-pro' ), $field['label'] ) );
			}
			if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], [ 'taxonomy' ], true ) ) {
				if ( is_array( $values[ $key ] ) ) {
					$check_value = $values[ $key ];
				} else {
					$check_value = empty( $values[ $key ] ) ? [] : [ $values[ $key ] ];
				}
				foreach ( $check_value as $term ) {
					if ( ! term_exists( $term, $field['taxonomy'] ) ) {
						// translators: Placeholder %s is the field label that is did not validate.
						return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'document-library-pro' ), $field['label'] ) );
					}
				}
			}
			if ( 'file' === $field['type'] ) {
				if ( is_array( $values[ $key ] ) ) {
					$check_value = array_filter( $values[ $key ] );
				} else {
					$check_value = array_filter( [ $values[ $key ] ] );
				}
				if ( ! empty( $check_value ) ) {
					foreach ( $check_value as $file_url ) {
						if ( ! is_numeric( $file_url ) ) {
							/**
							 * Set this flag to true to reject files from external URLs during submission.
							 *
							 * @param bool   $reject_external_files  The flag.
							 * @param string $key                    The field key.
							 * @param array  $field                  An array containing the information for the field.
							 */
							$reject_external_files = apply_filters( 'dlp_reject_external_files', false, $key, $field );
							// Check image path.
							$baseurl = wp_upload_dir()['baseurl'];
							if ( $reject_external_files && 0 !== strpos( $file_url, $baseurl ) ) {
								throw new Exception( __( 'Invalid image path.', 'document-library-pro' ) );
							}
						}
						// Check mime types.
						if ( ! empty( $field['allowed_mime_types'] ) ) {
							$file_url  = current( explode( '?', $file_url ) );
							$file_info = wp_check_filetype( $file_url );
							if ( ! is_numeric( $file_url ) && $file_info && ! in_array( $file_info['type'], $field['allowed_mime_types'], true ) ) {
								// translators: Placeholder %1$s is field label; %2$s is the file mime type; %3$s is the allowed mime-types.
								throw new Exception( sprintf( __( '"%1$s" (filetype %2$s) needs to be one of the following file types: %3$s', 'document-library-pro' ), $field['label'], $file_info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
							}
						}
						// Check if attachment is valid.
						if ( is_numeric( $file_url ) ) {
							continue;
						}
						$file_url = esc_url( $file_url, [ 'http', 'https' ] );
						if ( empty( $file_url ) ) {
							throw new Exception( __( 'Invalid attachment provided.', 'document-library-pro' ) );
						}
					}
				}
			}
		}

		/**
		 * Perform additional validation on the fields.
		 *
		 * @param bool  $is_valid Whether the fields are valid.
		 * @param array $fields   Array of all fields being validated.
		 * @param array $values   Submitted input values.
		 */
		return apply_filters( 'dlp_submission_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * Process submission of the form.
	 *
	 * @return void
	 * @throws Exception On validation error
	 */
	public function process_form() {
		try {
			$this->init_fields();

			$values = $this->get_posted_fields();

			$validation_status = $this->validate_fields( $values );

			if ( is_wp_error( $validation_status ) ) {
				throw new Exception( $validation_status->get_error_message() );
			}

			if ( isset( $values['email'] ) && ! empty( $values['email'] ) ) {
				throw new Exception( __( 'Failed honeypot validation.', 'document-library-pro' ) );
			}

			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/media.php';
			include_once ABSPATH . 'wp-admin/includes/image.php';

			$document_type = $values['document_link'];

			if ( $document_type === 'url' && ( ! isset( $values['document_url'] ) || empty( $values['document_url'] ) ) ) {
				throw new Exception( __( 'Please enter a valid document url.', 'document-library-pro' ) );
			} elseif ( $document_type === 'file' && ( ! isset( $values['document_file'] ) || empty( $values['document_file'] ) || ! file_exists( Media::get_file_url( $values['document_file'] ) ) ) ) {
				throw new Exception( __( 'Please select the file you wish to upload.', 'document-library-pro' ) );
			}

			$args = [
				'name'         => $values['title'],
				'excerpt'      => isset( $values['excerpt'] ) ? $values['excerpt'] : '',
				'content'      => isset( $values['custom_content'] ) ? $values['custom_content'] : '',
				'category_ids' => isset( $values['category'] ) ? $values['category'] : [],
				'tag_ids'      => isset( $values['tags'] ) ? $values['tags'] : [],
				'author_ids'   => isset( $values['authors'] ) ? $values['authors'] : [],
				'published'    => Options::is_submission_moderated() ? -1 : 1
			];

			if ( $document_type === 'url' ) {
				$args['direct_url'] = $values['document_url'];
			} elseif ( $document_type === 'file' ) {
				$args['file_url'] = $values['document_file'];
			}

			$document = new Document( 0, $args );

			// Document successfully created.
			if ( $document instanceof Document && ! empty( $document->get_id() ) ) {
				$this->delete_document_file( $values );

				if ( Options::is_submission_admin_email_active() ) {
					( new Admin_Notification( $document ) )->send();
				}

				$redirect = add_query_arg( [ 'success' => true ], get_permalink() );

				wp_safe_redirect( $redirect );
				exit;
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Delete the temporary document file.
	 *
	 * Usually used when validation error occurs or
	 * after successful submission.
	 *
	 * @param array $values
	 * @return void
	 */
	private function delete_document_file( $values ) {
		$document_type = $values['document_link'];

		if ( $document_type === 'file' ) {
			$temporary_file = Media::get_file_url( $values['document_file'] );
			if ( $temporary_file && strpos( $temporary_file, 'dlp-uploads' ) !== false ) {
				wp_delete_file( $temporary_file );
			}
			$this->fields['document_file']['value'] = false;
		}
	}

	/**
	 * Get the slug of the currently active theme.
	 * We'll use this as class name to add theme specific styling.
	 *
	 * @return string
	 */
	private function get_theme_slug() {
		return get_stylesheet();
	}

	/**
	 * Displays the form via the shortcode.
	 *
	 * @return string
	 */
	public function display_form() {
		$this->init_fields();

		return $this->template_loader->get_template(
			'submission-form.php',
			[
				'templates'          => $this->template_loader,
				'fields'             => $this->get_fields(),
				'submit_button_text' => __( 'Submit document', 'document-library-pro' ),
				'theme'              => strtolower( $this->get_theme_slug() ),
				'errors'             => $this->errors
			]
		);
	}

	/**
	 * Filters the upload dir when dlp is making an upload.
	 *
	 * @param  array $pathdata
	 * @return array
	 */
	public function setup_upload_dir( $pathdata ) {
		global $dlp_upload, $dlp_uploading_file;

		if ( ! empty( $dlp_upload ) ) {
			$dir = untrailingslashit( apply_filters( 'dlp_upload_dir', 'dlp-uploads/' . sanitize_key( $dlp_uploading_file ), sanitize_key( $dlp_uploading_file ) ) );

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/' . $dir;
				$pathdata['url']    = $pathdata['url'] . '/' . $dir;
				$pathdata['subdir'] = '/' . $dir;
			} else {
				$new_subdir         = '/' . $dir . $pathdata['subdir'];
				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = $new_subdir;
			}
		}

		return $pathdata;
	}

	/**
	 * Script configuration for the frontend.
	 *
	 * @return array
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'rest'      => trailingslashit( get_rest_url() . Rest_Api::API_NAMESPACE ),
			'labels'    => [
				'removeItem' => __( 'Remove this option', 'document-library-pro' ),
			]
		];
	}

}
