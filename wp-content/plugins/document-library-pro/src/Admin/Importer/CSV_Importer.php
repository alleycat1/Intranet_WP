<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Importer;

use Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Taxonomies,
	Barn2\Plugin\Document_Library_Pro\Util\Util,
	Barn2\Plugin\Document_Library_Pro\Post_Type;

defined( 'ABSPATH' ) || exit;
/**
 * This class is the controller for the CSV Import
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CSV_Importer {

	/**
	 * Tracks current row being parsed.
	 *
	 * @var integer
	 */
	protected $parsing_raw_data_index = 0;

		/**
		 * CSV file.
		 *
		 * @var string
		 */
	protected $file = '';

	/**
	 * The file position after the last read.
	 *
	 * @var int
	 */
	protected $file_position = 0;

	/**
	 * Importer parameters.
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 * Raw keys - CSV raw headers.
	 *
	 * @var array
	 */
	protected $raw_keys = [];

	/**
	 * Mapped keys - CSV headers.
	 *
	 * @var array
	 */
	protected $mapped_keys = [];

	/**
	 * Raw data.
	 *
	 * @var array
	 */
	protected $raw_data = [];

	/**
	 * Raw data.
	 *
	 * @var array
	 */
	protected $file_positions = [];

	/**
	 * Parsed data.
	 *
	 * @var array
	 */
	protected $parsed_data = [];

	/**
	 * Start time of current import.
	 *
	 * (default value: 0)
	 *
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * A list of taxonomies registered to the Document post type.
	 *
	 * @var array
	 */
	protected $taxonomies = [];

	/**
	 * Initialize importer.
	 *
	 * @param string $file   File to read.
	 * @param array  $params Arguments for the parser.
	 */
	public function __construct( $file, $params = [] ) {
		$default_args = [
			'start_pos'        => 0, // File pointer start.
			'end_pos'          => -1, // File pointer end.
			'lines'            => -1, // Max lines to read.
			'mapping'          => [], // Column mapping. csv_heading => schema_heading.
			'parse'            => false, // Whether to sanitize and format data.
			'delimiter'        => ',', // CSV delimiter.
			'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
			'enclosure'        => '"', // The character used to wrap text in the CSV.
			'escape'           => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
		];

		$this->params = wp_parse_args( $params, $default_args );
		$this->file   = $file;

		// get the full list of taxonomies registered to the Document post type
		$this->taxonomies = get_object_taxonomies( Post_Type::POST_TYPE_SLUG, 'objects' );

		if ( isset( $this->params['mapping']['from'], $this->params['mapping']['to'] ) ) {
			$this->params['mapping'] = array_combine( $this->params['mapping']['from'], $this->params['mapping']['to'] );
		}

		$this->read_file();
	}

	/**
	 * Read file.
	 */
	protected function read_file() {
		if ( ! CSV_Controller::is_file_valid_csv( $this->file ) ) {
			wp_die( esc_html__( 'Invalid file type. The importer supports CSV and TXT file formats.', 'document-library-pro' ) );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		$handle = fopen( $this->file, 'r' );

		if ( false !== $handle ) {
			$this->raw_keys = array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) );
			// Remove BOM signature from the first item.
			if ( isset( $this->raw_keys[0] ) ) {
				$this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
			}

			if ( 0 !== $this->params['start_pos'] ) {
				fseek( $handle, (int) $this->params['start_pos'] );
			}

			while ( 1 ) {
				$row = fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] );

				if ( false !== $row ) {
					$this->raw_data[]                                 = $row;
					$this->file_positions[ count( $this->raw_data ) ] = ftell( $handle );

					if ( ( $this->params['end_pos'] > 0 && ftell( $handle ) >= $this->params['end_pos'] ) || 0 === --$this->params['lines'] ) {
						break;
					}
				} else {
					break;
				}
			}

			$this->file_position = ftell( $handle );
		}

		if ( ! empty( $this->params['mapping'] ) ) {
			$this->set_mapped_keys();
		}

		if ( $this->params['parse'] ) {
			$this->set_parsed_data();
		}
	}

	/**
	 * Remove UTF-8 BOM signature.
	 *
	 * @param string $string String to handle.
	 *
	 * @return string
	 */
	protected function remove_utf8_bom( $string ) {
		if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
			$string = substr( $string, 3 );
		}

		return $string;
	}

	/**
	 * Set file mapped keys.
	 */
	protected function set_mapped_keys() {
		$mapping = $this->params['mapping'];

		foreach ( $this->raw_keys as $key ) {
			$this->mapped_keys[] = isset( $mapping[ $key ] ) ? $mapping[ $key ] : $key;
		}
	}

	/**
	 * Parse a field that is generally '1' or '0' but can be something else.
	 *
	 * @param string $value Field value.
	 *
	 * @return bool|string
	 */
	public function parse_bool_field( $value ) {
		if ( '0' === $value ) {
			return false;
		}

		if ( '1' === $value ) {
			return true;
		}

		// Don't return explicit true or false for empty fields or values
		return sanitize_text_field( $value );
	}

	/**
	 * Parse a float value field.
	 *
	 * @param string $value Field value.
	 *
	 * @return float|string
	 */
	public function parse_float_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		return floatval( $value );
	}

	/**
	 * Parse a hierarchical taxonomy field from a CSV.
	 *
	 * @param string $taxonomy    A taxonomy slug.
	 * @param string $value       Field value.
	 *
	 * @return array
	 */
	public function parse_hierarchical_taxonomy_field( $taxonomy, $value ) {
		if ( empty( $value ) ) {
			return [];
		}

		$separator = strpos( $value, '|' ) ? '|' : ',';
		$row_terms = $this->explode_values( $value, $separator );
		$term_ids  = [];

		foreach ( $row_terms as $row_term ) {
			$parent = null;
			$_terms = array_map( 'trim', explode( '>', $row_term ) );
			$total  = count( $_terms );

			foreach ( $_terms as $index => $_term ) {
				// Don't allow users without capabilities to create new categories.
				if ( ! current_user_can( 'manage_categories' ) ) {
					break;
				}

				$term = wp_insert_term( $_term, $taxonomy, [ 'parent' => intval( $parent ) ] );

				if ( is_wp_error( $term ) ) {
					if ( $term->get_error_code() === 'term_exists' ) {
						// When term exists, error data should contain existing term id.
						$term_id = $term->get_error_data();
					} else {
						break; // We cannot continue on any other error.
					}
				} else {
					// New term.
					$term_id = $term['term_id'];
				}

				// Only requires assign the last category.
				if ( ( 1 + $index ) === $total ) {
					$term_ids[] = $term_id;
				} else {
					// Store parent to be able to insert or query categories based in parent ID.
					$parent = $term_id;
				}
			}
		}

		return $term_ids;
	}

	/**
	 * Parse a non-hierarchical taxonomy field from a CSV.
	 *
	 * @param string      $taxonomy     A taxonomy slug.
	 * @param string      $value        Field value.
	 * @param string|null $separator    A separator delimiting different terms.
	 *
	 * @return array
	 */
	public function parse_non_hierarchical_taxonomy_field( $taxonomy, $value, $separator = null ) {
		if ( empty( $value ) ) {
			return [];
		}

		if ( is_null( $separator ) ) {
			$separator = strpos( $value, '|' ) ? '|' : ',';
		}

		$value    = $this->unescape_data( $value );
		$names    = $this->explode_values( $value, $separator );
		$term_ids = [];

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, $taxonomy );

			if ( ! $term || is_wp_error( $term ) ) {
				$term = (object) wp_insert_term( $name, $taxonomy );
			}

			if ( ! is_wp_error( $term ) ) {
				$term_ids[] = $term->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Parse a category field from a CSV.
	 * Categories are separated by commas and subcategories are "parent > subcategory".
	 *
	 * @param string $value Field value.
	 *
	 * @return array of arrays with "parent" and "name" keys.
	 */
	public function parse_categories_field( $value ) {
		return $this->parse_hierarchical_taxonomy_field( Taxonomies::CATEGORY_SLUG, $value );
	}

	/**
	 * Parse a tag field from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_tags_field( $value ) {
		return $this->parse_non_hierarchical_taxonomy_field( Taxonomies::TAG_SLUG, $value );
	}

	/**
	 * Parse a tag field from a CSV with space separators.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_tags_spaces_field( $value ) {
		return $this->parse_non_hierarchical_taxonomy_field( Taxonomies::TAG_SLUG, $value, ' ' );
	}

	/**
	 * Parse a taxonomy field and return a list of term ids.
	 *
	 * This method returns an associative array with the taxonomy slug as a key
	 * so that the Document class can easily add it to the tax_input argument.
	 *
	 * @param string $name_label The name or the label of the taxonomy.
	 * @param string $value    Field value.
	 *
	 * @return array
	 */
	public function parse_taxonomies_field( $name_label, $value ) {
		$taxonomy = $this->get_taxonomy_by_name_or_label( $name_label );

		if ( empty( $taxonomy ) ) {
			return [];
		}

		$parser = $taxonomy->hierarchical ? 'parse_hierarchical_taxonomy_field' : 'parse_non_hierarchical_taxonomy_field';

		$term_ids = $this->{$parser}( $taxonomy->name, $value );

		return [ $taxonomy->name => $term_ids ];
	}

	/**
	 * Parse a tag field from a CSV.
	 *
	 * @param string $value Field value.
	 *
	 * @return array
	 */
	public function parse_author_field( $value ) {
		if ( empty( $value ) ) {
			return [];
		}

		$separator = strpos( $value, '|' ) ? '|' : ',';
		$value     = $this->unescape_data( $value );

		$names = $this->explode_values( $value, $separator );

		$tags = [];

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, Taxonomies::AUTHOR_SLUG );

			if ( ! $term || is_wp_error( $term ) ) {
				$term = (object) wp_insert_term( $name, Taxonomies::AUTHOR_SLUG );
			}

			if ( ! is_wp_error( $term ) ) {
				$tags[] = $term->term_id;
			}
		}

		return $tags;
	}

	/**
	 * Parse dates from a CSV.
	 * Dates requires the format YYYY-MM-DD and time is optional.
	 *
	 * @param string $value Field value.
	 *
	 * @return string|null
	 */
	public function parse_date_field( $value ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])([ 01-9:]*)$/', $value ) ) {
			// Don't include the time if the field had time in it.
			return current( explode( ' ', $value ) );
		}

		return null;
	}

	/**
	 * Just skip current field.
	 *
	 * By default is applied sanitize_text_field() to all not listed fields
	 * in self::get_formatting_callback(), use this method to skip any formatting.
	 *
	 * @param   string $value Field value.
	 * @return  string
	 */
	public function parse_skip_field( $value ) {
		return $value;
	}

	/**
	 * Parse an int value field
	 *
	 * @param   int $value field value.
	 * @return  int
	 */
	public function parse_int_field( $value ) {
		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		return intval( $value );
	}

	/**
	 * Parse a description value field
	 *
	 * @param string $description field value.
	 *
	 * @return string
	 */
	public function parse_description_field( $description ) {
		$parts = explode( "\\\\n", $description );
		foreach ( $parts as $key => $part ) {
			$parts[ $key ] = str_replace( '\n', "\n", $part );
		}

		return implode( '\\\n', $parts );
	}

	/**
	 * Parse the published field. 1 is published, 0 is private, -1 is draft.
	 * Alternatively, 'true' can be used for published and 'false' for draft.
	 *
	 * @param string $value Field value.
	 *
	 * @return float|string
	 */
	public function parse_published_field( $value ) {
		if ( '' === $value ) {
			return $value;
		}

		// Remove the ' prepended to fields that start with - if needed.
		$value = $this->unescape_data( $value );

		if ( 'true' === strtolower( $value ) || 'false' === strtolower( $value ) ) {
			return Util::string_to_bool( $value ) ? 1 : -1;
		}

		return floatval( $value );
	}

	/**
	 * Parse the URL field.
	 *
	 * @param mixed $value
	 *
	 * @return string|false
	 */
	public function parse_url_field( $value ) {
		// Absolute file paths.
		if ( 0 === strpos( $value, 'http' ) ) {
			return esc_url_raw( $value );
		}

		return false;
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 */
	protected function get_formatting_callback() {

		/**
		 * Columns not mentioned here will get parsed with 'sanitize_text_field'.
		 * column_name => callback.
		 */
		$data_formatting = [
			'published'      => [ $this, 'parse_published_field' ],
			'name'           => [ $this, 'parse_skip_field' ],
			'author_ids'     => [ $this, 'parse_author_field' ],
			'excerpt'        => [ $this, 'parse_description_field' ],
			'content'        => [ $this, 'parse_description_field' ],
			'category_ids'   => [ $this, 'parse_categories_field' ],
			'tag_ids'        => [ $this, 'parse_tags_field' ],
			'tag_ids_spaces' => [ $this, 'parse_tags_spaces_field' ],
			'file_url'       => [ $this, 'parse_url_field' ],
			'direct_url'     => [ $this, 'parse_url_field' ],
			'featured_image' => [ $this, 'parse_url_field' ],
		];

		/**
		 * Match special column names.
		 */
		$regex_match_data_formatting = [
			'/cf:*/'  => 'wp_kses_post', // Allow some HTML in meta fields.
			'/tax:*/' => 'strip_tags',
		];

		$callbacks = [];

		// Figure out the parse function for each column.

		foreach ( $this->get_mapped_keys() as $index => $heading ) {
			$callback = 'sanitize_text_field';

			if ( isset( $data_formatting[ $heading ] ) ) {
				$callback = $data_formatting[ $heading ];
			} else {
				foreach ( $regex_match_data_formatting as $regex => $callback ) {
					if ( preg_match( $regex, $heading ) ) {
						$callback = $callback;
						break;
					}
				}
			}

			$callbacks[] = $callback;
		}

		return apply_filters( 'document_library_pro_csv_importer_formatting_callbacks', $callbacks, $this );
	}

		/**
		 * Get file raw headers.
		 *
		 * @return array
		 */
	public function get_raw_keys() {
		return $this->raw_keys;
	}

	/**
	 * Get file mapped headers.
	 *
	 * @return array
	 */
	public function get_mapped_keys() {
		return ! empty( $this->mapped_keys ) ? $this->mapped_keys : $this->raw_keys;
	}

	/**
	 * Get raw data.
	 *
	 * @return array
	 */
	public function get_raw_data() {
		return $this->raw_data;
	}

	/**
	 * Get parsed data.
	 *
	 * @return array
	 */
	public function get_parsed_data() {
		/**
		 * Filter document importer parsed data.
		 *
		 * @param array             $parsed_data Parsed data.
		 * @param Document_Importer $importer Importer instance.
		 */
		return apply_filters( 'document_library_pro_csv_importer_parsed_data', $this->parsed_data, $this );
	}

	/**
	 * Get importer parameters.
	 *
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}

	/**
	 * Get file pointer position from the last read.
	 *
	 * @return int
	 */
	public function get_file_position() {
		return $this->file_position;
	}

	/**
	 * Get file pointer position as a percentage of file size.
	 *
	 * @return int
	 */
	public function get_percent_complete() {
		$size = filesize( $this->file );
		if ( ! $size ) {
			return 0;
		}

		return absint( min( Util::round( ( $this->file_position / $size ) * 100 ), 100 ) );
	}

	/**
	 * Get a taxonomy object by its label (plural)
	 *
	 * @param  string $name_label The name or the label of the taxonomy.
	 * @return WP_Taxonomy
	 */
	public function get_taxonomy_by_name_or_label( $name_label ) {
		$matches = array_filter(
			$this->taxonomies,
			function( $t ) use ( $name_label ) {
				return $t->name === $name_label || strtolower( $t->label ) === strtolower( $name_label );
			}
		);

		if ( empty( $matches ) ) {
			return false;
		}

		// there should be only one result in the array
		// we get the first element anyway
		return reset( array_values( $matches ) );
	}

	/**
	 * Check if strings starts with determined word.
	 *
	 * @param   string $haystack Complete sentence.
	 * @param   string $needle   Excerpt.
	 * @return  bool
	 */
	protected function starts_with( $haystack, $needle ) {
		return substr( $haystack, 0, strlen( $needle ) ) === $needle;
	}

	/**
	 * Expand special and internal data into the correct formats for the product CRUD.
	 *
	 * @param   array $data Data to import.
	 * @return  array
	 */
	protected function expand_data( $data ) {
		$data = apply_filters( 'document_library_pro_csv_importer_pre_expand_data', $data );

		// Handle special column names which span multiple columns.
		$meta_data  = [];
		$acf        = [];
		$ept        = [];
		$taxonomies = [];

		foreach ( $data as $key => $value ) {
			if ( $this->starts_with( $key, 'cf:' ) ) {
				$meta_data[] = [
					'key'   => str_replace( 'cf:', '', $key ),
					'value' => $value,
				];
				unset( $data[ $key ] );
			} elseif ( $this->starts_with( $key, 'acf:' ) ) {
				$acf[] = [
					'key'   => str_replace( 'acf:', '', $key ),
					'value' => $value,
				];
				unset( $data[ $key ] );
			} elseif ( $this->starts_with( $key, 'ept:' ) ) {
				$ept[] = [
					'key'   => str_replace( 'ept:', '', $key ),
					'value' => $value,
				];
				unset( $data[ $key ] );
			} elseif ( $this->starts_with( $key, 'tax:' ) ) {
				$name_label = str_replace( 'tax:', '', $key );
				$taxonomies = $taxonomies + $this->parse_taxonomies_field( $name_label, $value );
				unset( $data[ $key ] );
			}
		}

		if ( ! empty( $meta_data ) ) {
			$data['meta_data'] = $meta_data;
		}

		if ( ! empty( $acf ) ) {
			$data['acf'] = $acf;
		}

		if ( ! empty( $ept ) ) {
			$data['ept'] = $ept;
		}

		if ( ! empty( $taxonomies ) ) {
			$data['taxonomies'] = array_filter( $taxonomies );
		}

		return $data;
	}

	/**
	 * Map and format raw data to known fields.
	 */
	protected function set_parsed_data() {
		$parse_functions = $this->get_formatting_callback();
		$mapped_keys     = $this->get_mapped_keys();
		$use_mb          = function_exists( 'mb_convert_encoding' );

		// Parse the data.
		foreach ( $this->raw_data as $row_index => $row ) {
			// Skip empty rows.
			if ( ! count( array_filter( $row ) ) ) {
				continue;
			}

			$this->parsing_raw_data_index = $row_index;

			$data = [];

			do_action( 'document_library_pro_importer_before_set_parsed_data', $row, $mapped_keys );

			foreach ( $row as $id => $value ) {

				// Skip ignored columns.
				if ( empty( $mapped_keys[ $id ] ) ) {
					continue;
				}

				// Convert UTF8.
				if ( $use_mb ) {
					$encoding = mb_detect_encoding( $value, mb_detect_order(), true );
					if ( $encoding ) {
						$value = mb_convert_encoding( $value, 'UTF-8', $encoding );
					} else {
						$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );
					}
				} else {
					$value = wp_check_invalid_utf8( $value, true );
				}

				$data[ $mapped_keys[ $id ] ] = call_user_func( $parse_functions[ $id ], $value );
			}

			/**
			 * Filter product importer parsed data.
			 *
			 * @param array $parsed_data Parsed data.
			 * @param CSV_Importer $importer Importer instance.
			 */
			$this->parsed_data[] = apply_filters( 'document_library_pro_csv_importer_parsed_data', $this->expand_data( $data ), $this );
		}
	}

	/**
	 * Get a string to identify the row from parsed data.
	 *
	 * @param array $parsed_data Parsed data.
	 * @return string
	 */
	protected function get_row_id( $parsed_data ) {
		$id       = isset( $parsed_data['id'] ) ? absint( $parsed_data['id'] ) : 0;
		$name     = isset( $parsed_data['name'] ) ? esc_attr( $parsed_data['name'] ) : '';
		$row_data = [];

		if ( $name ) {
			$row_data[] = $name;
		}
		if ( $id ) {
			/* translators: %d: product ID */
			$row_data[] = sprintf( __( 'ID %d', 'document-library-pro' ), $id );
		}

		return implode( ', ', $row_data );
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;
		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}
		return apply_filters( 'document_library_pro_csv_importer_memory_exceeded', $return );
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}
		return intval( $memory_limit ) * 1024 * 1024;
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + apply_filters( 'document_library_pro_csv_importer_default_time_limit', 20 ); // 20 seconds
		$return = false;
		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( 'document_library_pro_csv_importer_time_exceeded', $return );
	}

	/**
	 * Explode CSV cell values using commas by default, and handling escaped
	 * separators.
	 *
	 * @param  string $value     Value to explode.
	 * @param  string $separator Separator separating each value. Defaults to comma.
	 * @return array
	 */
	protected function explode_values( $value, $separator = ',' ) {
		$value  = str_replace( $separator, '::separator::', $value );
		$values = explode( '::separator::', $value );
		$values = array_map( [ $this, 'explode_values_formatter' ], $values );

		return $values;
	}

	/**
	 * Remove formatting and trim each value.
	 *
	 * @param  string $value Value to format.
	 * @return string
	 */
	protected function explode_values_formatter( $value ) {
		return trim( str_replace( '::separator::', ',', $value ) );
	}

	/**
	 * The exporter prepends a ' to escape fields that start with =, +, - or @.
	 * Remove the prepended ' character preceding those characters.
	 *
	 * @param  string $value A string that may or may not have been escaped with '.
	 * @return string
	 */
	protected function unescape_data( $value ) {
		$active_content_triggers = [ "'=", "'+", "'-", "'@" ];

		if ( in_array( mb_substr( $value, 0, 2 ), $active_content_triggers, true ) ) {
			$value = mb_substr( $value, 1 );
		}

		return $value;
	}

	/**
	 * Process a single item and save.
	 *
	 * @throws \Exception       If item cannot be processed.
	 * @param  array            $data Raw CSV data.
	 * @return array|\WP_Error
	 */
	protected function process_item( $data ) {
		try {
			do_action( 'document_library_pro_csv_importer_before_process_item', $data );

			$data = apply_filters( 'document_library_pro_csv_importer_process_item_data', $data );

			$document = new Document( 0, $data );

			do_action( 'document_library_pro_csv_importer_after_process_item', $document->get_id(), $data );

			return [
				'id' => $document->get_id(),
			];
		} catch ( \Exception $e ) {
			return new \WP_Error( 'document_library_pro_importer_error', $e->getMessage(), [ 'status' => $e->getCode() ] );
		}
	}

	/**
	 * Process importer.
	 *
	 * @return array
	 */
	public function import() {
		$this->start_time = time();
		$index            = 0;
		$data             = [
			'imported' => [],
			'failed'   => [],
			'skipped'  => [],
		];

		foreach ( $this->parsed_data as $parsed_data_key => $parsed_data ) {
			do_action( 'document_library_pro_import_before_import', $parsed_data );

			$result = $this->process_item( $parsed_data );

			if ( is_wp_error( $result ) ) {
				$result->add_data( [ 'row' => $this->get_row_id( $parsed_data ) ] );
				$data['failed'][] = $result;
			} else {
				$data['imported'][] = $result['id'];
			}

			$index ++;

			if ( $this->params['prevent_timeouts'] && ( $this->time_exceeded() || $this->memory_exceeded() ) ) {
				$this->file_position = $this->file_positions[ $index ];
				break;
			}
		}

		return $data;
	}
}
