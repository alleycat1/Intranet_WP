<?php
/**
 * Save data to files in the uploads folder for the specified time (a-la temporary files)
 *
 * @package ThemeREX Addons
 * @since v2.25.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_uploads_get_option_name' ) ) {
	/**
	 * Return the option's name with the list of the uploaded files
	 *
	 * @return string  Name of the option with the list of the uploaded files
	 */
	function trx_addons_uploads_get_option_name() {
		return 'trx_addons_uploads_list_' . get_stylesheet();
	}
}

if ( ! function_exists( 'trx_addons_uploads_get_folder' ) ) {
	/**
	 * Return the path to the folder inside the uploads folder
	 *
	 * @return string  Path to the folder with uploads
	 */
	function trx_addons_uploads_get_folder( $url = false) {
		$upload_dir = wp_upload_dir();
		return $upload_dir[ $url ? 'baseurl' : 'basedir'] . '/trx_addons/uploads/' . get_stylesheet() . '/';
	}
}

if ( ! function_exists( 'trx_addons_uploads_get_file_dir' ) ) {
	/**
	 * Return the path to the uploaded file inside the uploads folder by the file name
	 *
	 * @param string $name  File name
	 * 
	 * @return string  Path to the file
	 */
	function trx_addons_uploads_get_file_dir( $name ) {
		return trailingslashit( trx_addons_uploads_get_folder() ) . trx_addons_esc( $name );
	}
}

if ( ! function_exists( 'trx_addons_uploads_get_file_url' ) ) {
	/**
	 * Return the URL of the uploaded file inside the uploads folder by the file name
	 *
	 * @param string $name  File name
	 * 
	 * @return string  URL of the file
	 */
	function trx_addons_uploads_get_file_url( $name ) {
		return trailingslashit( trx_addons_uploads_get_folder( true ) ) . trx_addons_esc( $name );
	}
}

if ( ! function_exists( 'trx_addons_uploads_create_storage' ) ) {
	/**
	 * Create uploads storage folder if it not exists
	 */
	function trx_addons_uploads_create_storage() {
		$uploads_dir = trx_addons_uploads_get_folder();
		if ( ! is_dir( $uploads_dir ) ) {
			wp_mkdir_p( $uploads_dir );
		}
    }
}

if ( ! function_exists( 'trx_addons_uploads_delete_file' ) ) {
	/**
	 * Delete file from the uploads storage folder by name
	 * 
	 * @param string $name  File name
	 */
	function trx_addons_uploads_delete_file( $name ) {
		$file = trx_addons_uploads_get_file_dir( $name );
		if ( file_exists( $file) ) {
			unlink( $file );
		}
	}
}

if ( ! function_exists( 'trx_addons_uploads_save_file' ) ) {
	/**
	 * Save data to the uploads storage file with the specified name
	 * 
	 * @param string $name  File name
	 * @param mixed  $data  Data to save
	 */
	function trx_addons_uploads_save_file( $name, $data ) {
		trx_addons_uploads_create_storage();
		$file = trx_addons_uploads_get_file_dir( $name );
		trx_addons_fpc( $file, $data );
	}
}

if ( ! function_exists( 'trx_addons_uploads_save_data' ) ) {
	/**
	 * Save data to the file in the uploads storage folder with the specified name and return URL of this file.
	 * Each file has unique name, so it can be used as temporary file.
	 * Each file has expiration time, so it can be used as temporary file.
	 * 
	 * @param mixed  $data  Data to save
	 * @param array  $args  Additional arguments (expire - expiration time in seconds, name - file name, ext - file extension)
	 * 
	 * @return string  URL of the uploaded file
	 */
	function trx_addons_uploads_save_data( $data, $args = array() ) {
		$args = array_merge(
			array(
				'expire' => 0,
				'name'   => '',
				'ext'    => 'png'
			),
			$args
		);
		if ( $args['expire'] > 0 ) {
			$args['expire'] = time() + $args['expire'];
		}
		// Generate unique file name
		$name = ( ! empty( $args['name'] ) ? $args['name'] : trx_addons_get_uuid() ) . '.' . $args['ext'];
		// Save data to the file
		trx_addons_uploads_save_file( $name, $data );
		// Get list of the uploaded files
		$uploads_list = get_option( trx_addons_uploads_get_option_name(), array() );
		// Clear entries with expired files
		foreach( $uploads_list as $n => $expire ) {
			if ( $expire < time() ) {
				trx_addons_uploads_delete_file( $n );
				unset( $uploads_list[ $n ] );
			}
		}
		// Save file name and expiration time to the list
		if ( $args['expire'] > 0 ) {
			$uploads_list[$name] = $args['expire'];
		}
		// Save list to the DB
		update_option( trx_addons_uploads_get_option_name(), $uploads_list );
		// Return URL of the uploaded file
		return trx_addons_uploads_get_file_url( $name );
	}
}
