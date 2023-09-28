<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Importer;

use Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Util\Util,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;

/**
 * This class is the controller for the CSV Import
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CSV_Controller {

	/**
	 * The path to the current file.
	 *
	 * @var string
	 */
	protected $file = '';

	/**
	 * The current import step.
	 *
	 * @var string
	 */
	protected $step = '';

	/**
	 * Progress steps.
	 *
	 * @var array
	 */
	protected $steps = [];

	/**
	 * Errors.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * The current delimiter for the file being read.
	 *
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Whether to use previous mapping selections.
	 *
	 * @var bool
	 */
	protected $map_preferences = false;

	/**
	 * Get importer instance.
	 *
	 * @param  string $file File to import.
	 * @param  array  $args Importer arguments.
	 * @return CSV_Importer
	 */
	public static function get_importer( $file, $args = [] ) {
		return new CSV_Importer( $file, $args );
	}

	/**
	 * Check whether a file is a valid CSV file.
	 *
	 * @param string $file File path.
	 * @param bool   $check_path Whether to also check the file is located in a valid location (Default: true).
	 * @return bool
	 */
	public static function is_file_valid_csv( $file, $check_path = true ) {
		if ( $check_path && apply_filters( 'document_library_pro_csv_importer_check_import_file_path', true ) && false !== stripos( $file, '://' ) ) {
			return false;
		}

		$valid_filetypes = self::get_valid_csv_filetypes();
		$filetype        = wp_check_filetype( $file, $valid_filetypes );
		if ( in_array( $filetype['type'], $valid_filetypes, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get all the valid filetypes for a CSV file.
	 *
	 * @return array
	 */
	protected static function get_valid_csv_filetypes() {
		return apply_filters(
			'document_library_pro_csv_importer_valid_filetypes',
			[
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			]
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->steps = [
			'upload'  => [
				'name'    => __( 'Upload CSV file', 'document-library-pro' ),
				'view'    => [ $this, 'upload_form' ],
				'handler' => [ $this, 'upload_form_handler' ],
			],
			'mapping' => [
				'name'    => __( 'Column mapping', 'document-library-pro' ),
				'view'    => [ $this, 'mapping_form' ],
				'handler' => '',
			],
			'import'  => [
				'name'    => __( 'Import', 'document-library-pro' ),
				'view'    => [ $this, 'import' ],
				'handler' => '',
			],
			'done'    => [
				'name'    => __( 'Done!', 'document-library-pro' ),
				'view'    => [ $this, 'done' ],
				'handler' => '',
			],
		];

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$this->step            = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
		$this->file            = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['file'] ) ) : '';
		$this->delimiter       = ! empty( $_REQUEST['delimiter'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
		$this->map_preferences = isset( $_REQUEST['map_preferences'] ) ? (bool) $_REQUEST['map_preferences'] : false;
		// phpcs:enable

		if ( $this->map_preferences ) {
			add_filter( 'document_library_pro_csv_import_mapped_columns', [ $this, 'auto_map_user_preferences' ], 9999 );
		}
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );

		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );

		if ( false === $step_index ) {
			return '';
		}

		$params = [
			'step'            => $keys[ $step_index + 1 ],
			'file'            => str_replace( DIRECTORY_SEPARATOR, '/', $this->file ),
			'delimiter'       => $this->delimiter,
			'map_preferences' => $this->map_preferences,
			'_wpnonce'        => wp_create_nonce( 'dlp-csv-importer' ), // wp_nonce_url() escapes & to &amp; breaking redirects.
		];

		return add_query_arg( $params );
	}

	/**
	 * Output header view.
	 */
	protected function output_header() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Import Documents', 'document-library-pro' ); ?></h1>

			<div class="dlp-progress-form-wrapper">
		<?php
	}

	/**
	 * Output steps view.
	 */
	protected function output_steps() {
		?>
		<ol class="dlp-progress-steps">
			<?php foreach ( $this->steps as $step_key => $step ) : ?>
				<?php
				$step_class = '';
				if ( $step_key === $this->step ) {
					$step_class = 'active';
				} elseif ( array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
					$step_class = 'done';
				}
				?>
				<li class="<?php echo esc_attr( $step_class ); ?>">
					<?php echo esc_html( $step['name'] ); ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	/**
	 * Output footer view.
	 */
	protected function output_footer() {
		?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add error message.
	 *
	 * @param string $message Error message.
	 * @param array  $actions List of actions with 'url' and 'label'.
	 */
	protected function add_error( $message, $actions = [] ) {
		$this->errors[] = [
			'message' => $message,
			'actions' => $actions,
		];
	}

	/**
	 * Add error message.
	 */
	protected function output_errors() {
		if ( ! $this->errors ) {
			return;
		}

		foreach ( $this->errors as $error ) {
			echo '<div class="error inline">';
			echo '<p>' . esc_html( $error['message'] ) . '</p>';

			if ( ! empty( $error['actions'] ) ) {
				echo '<p>';
				foreach ( $error['actions'] as $action ) {
					echo '<a class="button button-primary" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['label'] ) . '</a> ';
				}
				echo '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Render the current step and show correct view.
	 */
	public function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['save_step'] ) && ! empty( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		$this->output_header();
		$this->output_steps();
		$this->output_errors();
		call_user_func( $this->steps[ $this->step ]['view'], $this );
		$this->output_footer();
	}

	/**
	 * Output information about the uploading process.
	 */
	protected function upload_form() {
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		?>
		<form class="dlp-progress-form-content dlp-importer" enctype="multipart/form-data" method="post">
			<header>
				<h2><?php esc_html_e( 'Import documents from a CSV file', 'document-library-pro' ); ?></h2>
				<p><?php esc_html_e( 'This tool allows you to import document data to your site from a CSV or TXT file.', 'document-library-pro' ); ?></p>
			</header>
			<section>
				<table class="form-table dlp-csv-importer-options">
					<tbody>
						<tr>
							<th scope="row">
								<label for="upload">
									<?php esc_html_e( 'Choose a CSV file from your computer:', 'document-library-pro' ); ?>
								</label>
							</th>
							<td>
								<?php
								if ( ! empty( $upload_dir['error'] ) ) {
									?>
									<div class="inline error">
										<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'document-library-pro' ); ?></p>
										<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
									</div>
									<?php
								} else {
									?>
									<input type="file" id="upload" name="import" size="25" />
									<input type="hidden" name="action" value="save" />
									<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
									<br>
									<small>
										<?php
										printf(
											/* translators: %s: maximum upload size */
											esc_html__( 'Maximum size: %s', 'document-library-pro' ),
											esc_html( $size )
										);
										?>
									</small>
									<?php
								}
								?>
							</td>
						</tr>
						<tr class="dlp-importer-advanced hidden">
							<th>
								<label for="dlp-importer-file-url"><?php esc_html_e( 'Alternatively, enter the path to a CSV file on your server:', 'document-library-pro' ); ?></label>
							</th>
							<td>
								<label for="dlp-importer-file-url" class="dlp-importer-file-url-field-wrapper">
									<code><?php echo esc_html( ABSPATH ) . ' '; ?></code><input type="text" id="dlp-importer-file-url" name="file_url" />
								</label>
							</td>
						</tr>
						<tr class="dlp-importer-advanced hidden">
							<th><label><?php esc_html_e( 'CSV Delimiter', 'document-library-pro' ); ?></label><br/></th>
							<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
						</tr>
						<tr class="dlp-importer-advanced hidden">
							<th><label><?php esc_html_e( 'Use previous column mapping preferences?', 'document-library-pro' ); ?></label><br/></th>
							<td><input type="checkbox" id="dlp-importer-map-preferences" name="map_preferences" value="1" /></td>
						</tr>
					</tbody>
				</table>
			</section>
			<script type="text/javascript">
				jQuery(function() {
					jQuery( '.dlp-importer-toggle-advanced-options' ).on( 'click', function() {
						var elements = jQuery( '.dlp-importer-advanced' );
						if ( elements.is( '.hidden' ) ) {
							elements.removeClass( 'hidden' );
							jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
						} else {
							elements.addClass( 'hidden' );
							jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
						}
						return false;
					} );
				});
			</script>
			<div class="dlp-actions">
				<a href="#" class="dlp-importer-toggle-advanced-options" data-hidetext="<?php esc_attr_e( 'Hide advanced options', 'document-library-pro' ); ?>" data-showtext="<?php esc_attr_e( 'Show advanced options', 'document-library-pro' ); ?>"><?php esc_html_e( 'Show advanced options', 'document-library-pro' ); ?></a>
				<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'document-library-pro' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'document-library-pro' ); ?></button>
				<?php wp_nonce_field( 'dlp-csv-importer' ); ?>
			</div>
		</form>
		<?php
	}

	/**
	 * Handle the upload form and store options.
	 */
	public function upload_form_handler() {
		check_admin_referer( 'dlp-csv-importer' );

		$file = $this->handle_upload();

		if ( is_wp_error( $file ) ) {
			$this->add_error( $file->get_error_message() );
			return;
		} else {
			$this->file = $file;
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Handles the CSV upload and initial parsing of the file to prepare for
	 * displaying author import options.
	 *
	 * @return string|WP_Error
	 */
	public function handle_upload() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce already verified in CSV_Controller::upload_form_handler()
		$file_url = isset( $_POST['file_url'] ) ? sanitize_text_field( wp_unslash( $_POST['file_url'] ) ) : '';

		if ( empty( $file_url ) ) {
			if ( ! isset( $_FILES['import'] ) ) {
				return new \WP_Error( 'document_library_pro_csv_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'document-library-pro' ) );
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( ! self::is_file_valid_csv( sanitize_text_field( wp_unslash( $_FILES['import']['name'] ) ), false ) ) {
				return new \WP_Error( 'document_library_pro_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'document-library-pro' ) );
			}

			$overrides = [
				'test_form' => false,
				'mimes'     => self::get_valid_csv_filetypes(),
			];
			$import    = $_FILES['import']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$upload    = wp_handle_upload( $import, $overrides );

			if ( isset( $upload['error'] ) ) {
				return new \WP_Error( 'document_library_pro_csv_importer_upload_error', $upload['error'] );
			}

			// Construct the object array.
			$object = [
				'post_title'     => basename( $upload['file'] ),
				'post_content'   => $upload['url'],
				'post_mime_type' => $upload['type'],
				'guid'           => $upload['url'],
				'context'        => 'import',
				'post_status'    => 'private',
			];

			// Save the data.
			$id = wp_insert_attachment( $object, $upload['file'] );

			/*
			 * Schedule a cleanup for one day from now in case of failed
			 * import or missing wp_import_cleanup() call.
			 */
			wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', [ $id ] );

			return $upload['file'];
		} elseif ( ( 0 === stripos( realpath( ABSPATH . $file_url ), ABSPATH ) ) && file_exists( ABSPATH . $file_url ) ) {
			if ( ! self::is_file_valid_csv( ABSPATH . $file_url ) ) {
				return new \WP_Error( 'document_library_pro_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'document-library-pro' ) );
			}

			return ABSPATH . $file_url;
		}
		// phpcs:enable

		return new \WP_Error( 'document_library_pro_csv_importer_upload_invalid_file', __( 'Please upload or provide the link to a valid CSV file.', 'document-library-pro' ) );
	}

	/**
	 * Mapping step.
	 */
	protected function mapping_form() {
		check_admin_referer( 'dlp-csv-importer' );
		$args = [
			'lines'     => 1,
			'delimiter' => $this->delimiter,
		];

		$importer     = self::get_importer( $this->file, $args );
		$headers      = $importer->get_raw_keys();
		$mapped_items = $this->auto_map_columns( $headers );
		$sample       = current( $importer->get_raw_data() );

		if ( empty( $sample ) ) {
			$this->add_error(
				__( 'The file is empty or using a different encoding than UTF-8, please try again with a new file.', 'document-library-pro' ),
				[
					[
						'url'   => admin_url( 'admin.php?page=dlp_import_csv' ),
						'label' => __( 'Upload a new file', 'document-library-pro' ),
					],
				]
			);

			// Force output the errors in the same page.
			$this->output_errors();
			return;
		}

		?>
		<form class="wc-progress-form-content dlp-importer" method="post" action="<?php echo esc_url( $this->get_next_step_link() ); ?>">
			<header>
				<h2><?php esc_html_e( 'Map CSV fields to documents', 'document-library-pro' ); ?></h2>
				<p>
				<?php
				printf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( 'Select fields from your CSV file to map against documents fields, or to ignore during import. %1$sRead more%2$s', 'document-library-pro' ),
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/add-import-documents/#3-bulk-import-documents' ), true ),
					'</a>'
				);
				?>
					</p>
			</header>
			<section class="dlp-importer-mapping-table-wrapper">
				<table class="widefat dlp-importer-mapping-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Column name', 'document-library-pro' ); ?></th>
							<th><?php esc_html_e( 'Map to field', 'document-library-pro' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $headers as $index => $name ) : ?>
							<?php $mapped_value = $mapped_items[ $index ]; ?>
							<tr>
								<td class="dlp-importer-mapping-table-name">
									<?php echo esc_html( $name ); ?>
									<?php if ( ! empty( $sample[ $index ] ) ) : ?>
										<span class="description"><?php esc_html_e( 'Sample:', 'document-library-pro' ); ?> <code><?php echo esc_html( $sample[ $index ] ); ?></code></span>
									<?php endif; ?>
								</td>
								<td class="dlp-importer-mapping-table-field">
									<input type="hidden" name="map_from[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $name ); ?>" />
									<select name="map_to[<?php echo esc_attr( $index ); ?>]">
										<option value=""><?php esc_html_e( 'Do not import', 'document-library-pro' ); ?></option>
										<option value="">--------------</option>
										<?php foreach ( $this->get_mapping_options( $mapped_value ) as $key => $value ) : ?>
											<?php if ( is_array( $value ) ) : ?>
												<optgroup label="<?php echo esc_attr( $value['name'] ); ?>">
													<?php foreach ( $value['options'] as $sub_key => $sub_value ) : ?>
														<option value="<?php echo esc_attr( $sub_key ); ?>" <?php selected( strtolower( str_replace( ' ', '-', str_replace( 'cf:dlp_document_', '', $mapped_value ) ) ), strtolower( str_replace( [ 'acf:', 'ept:', 'tax:dlp_document_' ], '', $sub_key ) ) ); ?>><?php echo esc_html( $sub_value ); ?></option>
													<?php endforeach ?>
												</optgroup>
											<?php else : ?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $mapped_value, $key ); ?>><?php echo esc_html( $value ); ?></option>
											<?php endif; ?>
										<?php endforeach ?>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</section>
			<div class="dlp-actions">
				<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Run the importer', 'document-library-pro' ); ?>" name="save_step"><?php esc_html_e( 'Run the importer', 'document-library-pro' ); ?></button>
				<input type="hidden" name="file" value="<?php echo esc_attr( $this->file ); ?>" />
				<input type="hidden" name="delimiter" value="<?php echo esc_attr( $this->delimiter ); ?>" />
				<?php wp_nonce_field( 'dlp-csv-importer' ); ?>
			</div>
		</form>
		<?php
	}

	/**
	 * Import step
	 */
	public function import() {
		// Displaying this page triggers Ajax action to run the import with a valid nonce,
		// therefore this page needs to be nonce protected as well.
		check_admin_referer( 'dlp-csv-importer' );

		if ( ! self::is_file_valid_csv( $this->file ) ) {
			$this->add_error( __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'document-library-pro' ) );
			$this->output_errors();
			return;
		}

		if ( ! is_file( $this->file ) ) {
			$this->add_error( __( 'The file does not exist, please try again.', 'document-library-pro' ) );
			$this->output_errors();
			return;
		}

		if ( ! empty( $_POST['map_from'] ) && ! empty( $_POST['map_to'] ) ) {
			$mapping_from = Util::sanitize_array_recursive( wp_unslash( $_POST['map_from'] ) );
			$mapping_to   = Util::sanitize_array_recursive( wp_unslash( $_POST['map_to'] ) );

			// Save mapping preferences for future imports.
			update_user_option( get_current_user_id(), 'dlp_csv_import_mapping', $mapping_to );
		} else {
			wp_safe_redirect( esc_url_raw( $this->get_next_step_link( 'upload' ) ) );
			exit;
		}

		Util::add_inline_script_params(
			'dlp-csv-import',
			'dlp_import_params',
			[
				'import_nonce' => wp_create_nonce( 'dlp-csv-import' ),
				'mapping'      => [
					'from' => $mapping_from,
					'to'   => $mapping_to,
				],
				'file'         => $this->file,
				'delimiter'    => $this->delimiter,
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
			]
		);
		wp_enqueue_script( 'dlp-csv-import' );

		?>
		<div class="wc-progress-form-content dlp-importer dlp-importer__importing">
			<header>
				<span class="spinner is-active"></span>
				<h2><?php esc_html_e( 'Importing', 'document-library-pro' ); ?></h2>
				<p><?php esc_html_e( 'Your documents are now being imported...', 'document-library-pro' ); ?></p>
			</header>
			<section>
				<progress class="dlp-importer-progress" max="100" value="0"></progress>
			</section>
		</div>
		<?php
	}

	/**
	 * Done step.
	 */
	protected function done() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$imported  = isset( $_GET['documents-imported'] ) ? absint( $_GET['documents-imported'] ) : 0;
		$failed    = isset( $_GET['documents-failed'] ) ? absint( $_GET['documents-failed'] ) : 0;
		$skipped   = isset( $_GET['documents-skipped'] ) ? absint( $_GET['documents-skipped'] ) : 0;
		$file_name = isset( $_GET['file-name'] ) ? sanitize_text_field( wp_unslash( $_GET['file-name'] ) ) : '';
		// phpcs:enable

		$errors = array_filter( (array) get_user_option( 'document_import_error_log' ) );

		?>
		<div class="wc-progress-form-content dlp-importer">
			<section class="dlp-importer-done">
				<?php
				$results = [];

				if ( 0 < $imported ) {
					$results[] = sprintf(
						/* translators: %d: documents count */
						_n( '%s document imported', '%s documents imported', $imported, 'document-library-pro' ),
						'<strong>' . number_format_i18n( $imported ) . '</strong>'
					);
				}

				if ( 0 < $skipped ) {
					$results[] = sprintf(
						/* translators: %d: documents count */
						_n( '%s document was skipped', '%s documents were skipped', $skipped, 'document-library-pro' ),
						'<strong>' . number_format_i18n( $skipped ) . '</strong>'
					);
				}

				if ( 0 < $failed ) {
					$results [] = sprintf(
						/* translators: %d: documents count */
						_n( 'Failed to import %s document', 'Failed to import %s documents', $failed, 'document-library-pro' ),
						'<strong>' . number_format_i18n( $failed ) . '</strong>'
					);
				}

				if ( 0 < $failed || 0 < $skipped ) {
					$results[] = '<a href="#" class="dlp-importer-done-view-errors">' . __( 'View import log', 'document-library-pro' ) . '</a>';
				}

				if ( ! empty( $file_name ) ) {
					$results[] = sprintf(
						/* translators: %s: File name */
						__( 'File uploaded: %s', 'document-library-pro' ),
						'<strong>' . $file_name . '</strong>'
					);
				}

				/* translators: %d: import results */
				echo wp_kses_post( __( 'Import complete!', 'document-library-pro' ) . ' ' . implode( '. ', $results ) );
				?>
			</section>
			<section class="dlp-importer-error-log" style="display:none">
				<table class="widefat dlp-importer-error-log-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Document', 'document-library-pro' ); ?></th>
							<th><?php esc_html_e( 'Reason for failure', 'document-library-pro' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( count( $errors ) ) {
							foreach ( $errors as $error ) {
								if ( ! is_wp_error( $error ) ) {
									continue;
								}
								$error_data = $error->get_error_data();
								?>
								<tr>
									<th><code><?php echo esc_html( $error_data['row'] ); ?></code></th>
									<td><?php echo esc_html( $error->get_error_message() ); ?></td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</section>
			<script type="text/javascript">
				jQuery(function() {
					jQuery( '.dlp-importer-done-view-errors' ).on( 'click', function() {
						jQuery( '.dlp-importer-error-log' ).slideToggle();
						return false;
					} );
				} );
			</script>
			<div class="dlp-actions">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . Post_Type::POST_TYPE_SLUG ) ); ?>">
					<?php esc_html_e( 'View documents', 'document-library-pro' ); ?>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Columns to normalize.
	 *
	 * @param  array $columns List of columns names and keys.
	 * @return array
	 */
	protected function normalize_columns_names( $columns ) {
		$normalized = [];

		foreach ( $columns as $key => $value ) {
			$normalized[ $key ] = $value;
		}

		return $normalized;
	}

	/**
	 * Auto map column names.
	 *
	 * @param  array $raw_headers Raw header columns.
	 * @param  bool  $num_indexes If should use numbers or raw header columns as indexes.
	 * @return array
	 */
	protected function auto_map_columns( $raw_headers, $num_indexes = true ) {
		$default_columns = $this->normalize_columns_names(
			apply_filters(
				'document_library_pro_csv_importer_mapping_default_columns',
				[
					__( 'Name', 'document-library-pro' )               => 'name',
					__( 'Published', 'document-library-pro' )          => 'published',
					__( 'Excerpt', 'document-library-pro' )            => 'excerpt',
					__( 'Content', 'document-library-pro' )            => 'content',
					__( 'Categories', 'document-library-pro' )         => 'category_ids',
					__( 'Tags', 'document-library-pro' )               => 'tag_ids',
					__( 'Document Authors', 'document-library-pro' )   => 'author_ids',
					__( 'File URL', 'document-library-pro' )           => 'file_url',
					__( 'Direct URL', 'document-library-pro' )         => 'direct_url',
					__( 'Featured Image URL', 'document-library-pro' ) => 'featured_image',
					__( 'File Size', 'document-library-pro' )          => 'file_size',
				],
				$raw_headers
			)
		);

		$special_columns = $this->get_special_columns(
			$this->normalize_columns_names(
				apply_filters(
					'document_library_pro_csv_importer_mapping_special_columns',
					[
						/* translators: %s: Meta key */
						__( 'Custom Field: %s', 'document-library-pro' ) => 'cf:',
						/* translators: %s: Taxonomy name */
						__( 'Taxonomies: %s', 'document-library-pro' ) => 'tax:',
					],
					$raw_headers
				)
			)
		);

		$headers = [];
		foreach ( $raw_headers as $key => $field ) {
			$normalized_field  = $field;
			$index             = $num_indexes ? $key : $field;
			$headers[ $index ] = $normalized_field;

			if ( isset( $default_columns[ $normalized_field ] ) ) {
				$headers[ $index ] = $default_columns[ $normalized_field ];
			} else {
				foreach ( $special_columns as $regex => $special_key ) {
					// Don't use the normalized field in the regex since meta might be case-sensitive.
					if ( preg_match( $regex, $field, $matches ) ) {
						$headers[ $index ] = $special_key . $matches[1];
						break;
					}
				}
			}
		}

		return apply_filters( 'document_library_pro_csv_importer_mapped_columns', $headers, $raw_headers );
	}

	/**
	 * Map columns using the user's lastest import mappings.
	 *
	 * @param  array $headers Header columns.
	 * @return array
	 */
	public function auto_map_user_preferences( $headers ) {
		$mapping_preferences = get_user_option( 'document_library_pro_import_mapping' );

		if ( ! empty( $mapping_preferences ) && is_array( $mapping_preferences ) ) {
			return $mapping_preferences;
		}

		return $headers;
	}

	/**
	 * Sanitize special column name regex.
	 *
	 * @param  string $value Raw special column name.
	 * @return string
	 */
	protected function sanitize_special_column_name_regex( $value ) {
		return '/' . str_replace( [ '%d', '%s' ], '(.*)', trim( quotemeta( $value ) ) ) . '/i';
	}

	/**
	 * Get special columns.
	 *
	 * @param  array $columns Raw special columns.
	 * @return array
	 */
	protected function get_special_columns( $columns ) {
		$formatted = [];

		foreach ( $columns as $key => $value ) {
			$regex = $this->sanitize_special_column_name_regex( $key );

			$formatted[ $regex ] = $value;
		}

		return $formatted;
	}

	/**
	 * Get mapping options.
	 *
	 * @param  string $item Item name.
	 * @return array
	 */
	protected function get_mapping_options( $item = '' ) {
		// Get index for special column names.
		$index = $item;

		if ( preg_match( '/\d+/', $item, $matches ) ) {
			$index = $matches[0];
		}

		// Available options.
		$options = [
			'name'             => __( 'Name', 'document-library-pro' ),
			'published'        => __( 'Published', 'document-library-pro' ),
			'excerpt'          => __( 'Excerpt', 'document-library-pro' ),
			'content'          => __( 'Content', 'document-library-pro' ),
			'category_ids'     => __( 'Categories (comma or pipe separated)', 'document-library-pro' ),
			'tag_ids'          => __( 'Tags (comma or pipe separated)', 'document-library-pro' ),
			'tag_ids_spaces'   => __( 'Tags (space separated)', 'document-library-pro' ),
			'author_ids'       => __( 'Document Authors', 'document-library-pro' ),
			'file_url'         => __( 'File URL', 'document-library-pro' ),
			'direct_url'       => __( 'Direct URL', 'document-library-pro' ),
			'featured_image'   => __( 'Featured Image URL', 'document-library-pro' ),
			'file_size'        => __( 'File Size', 'document-library-pro' ),
			'menu_order'       => __( 'Position', 'document-library-pro' ),
		];

		// Import as meta data.
		if ( strpos( $item, 'cf:' ) === 0 ) {
			$options = array_merge( $options, [
				$item => sprintf( __( 'Import as meta data (%s)', 'document-library-pro' ), str_replace( 'cf:', '', $item ) ),
			] );
		}

		// Import as taxonomy.
		if ( strpos( $item, 'tax:' ) === 0 ) {
			$options = array_merge( $options, [
				$item => sprintf( __( 'Import as taxonnomy (%s)', 'document-library-pro' ), str_replace( 'tax:', '', $item ) ),
			] );
		}

		// Custom fields.
		$custom_fields = $this->get_custom_fields( 'dlp_document' );
		if ( $custom_fields ) {
			$custom_fields_options['custom_fields'] = [
				'name'    => __( 'Custom fields', 'document-library-pro' ),
				'options' => $custom_fields
			];
			$options = array_merge( $options, $custom_fields_options );
		}

		// Taxonomies.
		$taxonomies = get_object_taxonomies( 'dlp_document', 'objects' );

		unset( $taxonomies['doc_categories'] );
		unset( $taxonomies['doc_tags'] );
		unset( $taxonomies['doc_author'] );
		unset( $taxonomies['file_type'] );

		$taxonomies = array_map( function( $value ) {
			return $value->label;
		}, $taxonomies );

		if ( $taxonomies ) {

			foreach ( $taxonomies as $key => $taxonomy ) {
				$tax[ 'tax:' . $key ] = sprintf( __( '%s (comma or pipe separated)', 'document-library-pro' ), $taxonomy );
			}

			$taxonomies_options['taxonomies'] = [
				'name'    => __( 'Taxonomies', 'document-library-pro' ),
				'options' => $tax
			];
			$options = array_merge( $options, $taxonomies_options );
		}

		return apply_filters( 'document_library_pro_csv_importer_mapping_options', $options, $item );
	}

	/**
	 * Get custom fields.
	 *
	 * @param string $post_type
	 * @return array
	 */
	private function get_custom_fields( $post_type ) {
		$custom_fields = [];

		// Get ACF custom fields.
		if ( Util::is_acf_active() ) {
			$custom_fields = $this->get_acf_custom_fields( $post_type );
		}

		// Get Easy Post Types custom fields.
		if ( Util::is_ept_active() ) {
			$custom_fields = array_merge( $custom_fields, $this->get_ept_custom_fields( $post_type ) );
		}

		// Sort fields by name.
		asort( $custom_fields );

		return $custom_fields;
	}

	/**
	 * Get ACF custom fields.
	 *
	 * @param string $post_type
	 * @return array
	 */
	private function get_acf_custom_fields( $post_type ) {
		$acf_fields = [];

		$groups = acf_get_field_groups( [ 'post_type' => $post_type ] );

		foreach ( (array) $groups as $group ) {
			$fields = acf_get_fields( $group['key'] );
			foreach ( (array) $fields as $field ) {
				$acf_fields[ 'acf:' . $field['name'] ] = $field['label'];
			}
		}

		return $acf_fields;
	}

	/**
	 * Get Easy Post Types custom fields.
	 *
	 * @param string $post_type
	 * @return array
	 */
	private function get_ept_custom_fields( $post_type ) {
		$ept_fields = [];

		$fields = \Barn2\Plugin\Easy_Post_Types_Fields\Util::get_custom_fields( $post_type );

		foreach ( (array) $fields as $field ) {
			$ept_fields[ 'ept:' . $field['slug'] ] = $field['name'];
		}

		return $ept_fields;
	}

}
