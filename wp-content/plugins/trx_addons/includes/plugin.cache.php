<?php
/**
 * Cache data to the files or database
 *
 * @package ThemeREX Addons
 * @since v1.87.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

/**
 * Local cache storage
 */
$TRX_ADDONS_CACHE_STORAGE = array();

if ( ! function_exists( 'trx_addons_cache_get_option_name' ) ) {
	/**
	 * Return the name of the option to store cache data
	 *
	 * @return string  Option name
	 */
	function trx_addons_cache_get_option_name() {
		return 'trx_addons_cache_' . get_stylesheet();
	}
}

if ( ! function_exists( 'trx_addons_cache_get_folder' ) ) {
	/**
	 * Return the path to the cache folder inside the uploads folder
	 *
	 * @return string  Path to the cache folder
	 */
	function trx_addons_cache_get_folder() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/trx_addons/cache/' . get_stylesheet() . '/';
	}
}

if ( ! function_exists( 'trx_addons_cache_get_file_dir' ) ) {
	/**
	 * Return the path to the cache file inside the uploads folder by cache key
	 *
	 * @param string $cache_key  Cache key
	 * 
	 * @return string  Path to the cache file
	 */
	function trx_addons_cache_get_file_dir( $cache_key ) {
		return trailingslashit( trx_addons_cache_get_folder() ) . trx_addons_esc( $cache_key );
	}
}

if ( ! function_exists( 'trx_addons_cache_create_storage' ) ) {
	/**
	 * Create cache storage folder if it not exists
	 */
	function trx_addons_cache_create_storage() {
		$cache_dir = trx_addons_cache_get_folder();
		if ( ! is_dir( $cache_dir ) ) {
			wp_mkdir_p( $cache_dir );
		}
    }
}

if ( ! function_exists( 'trx_addons_cache_delete_storage' ) ) {
	/**
	 * Delete file from the cache storage folder by cache key
	 * 
	 * @param string $key  Cache key
	 */
	function trx_addons_cache_delete_storage( $key ) {
		$file = trx_addons_cache_get_file_dir( $key );
		if ( file_exists( $file) ) {
			unlink( $file );
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_save_storage' ) ) {
	/**
	 * Save data to the cache storage file by cache key
	 * 
	 * @param string $key   Cache key
	 * @param mixed  $data  Data to save
	 */
	function trx_addons_cache_save_storage( $key, $data ) {
		trx_addons_cache_create_storage();
		$file = trx_addons_cache_get_file_dir( $key );
		trx_addons_fpc( $file, $data );
	}
}

if ( ! function_exists( 'trx_addons_cache_load_storage' ) ) {
	/**
	 * Load data from the cache storage file by cache key
	 * 
	 * @param string $key       Cache key
	 * @param mixed  $default   Default value to return if file not exists
	 */
	function trx_addons_cache_load_storage( $key, $default = false ) {
		$result = $default;
		$file = trx_addons_cache_get_file_dir( $key );
		if ( is_readable( $file ) ) {
			$result = trx_addons_fgc( $file );
		}
		return $result;
	}
}

if ( ! function_exists( 'trx_addons_cache_put_storage' ) ) {
	/**
	 * Save data to the internal storage and to the cache storage file
	 * 
	 * @param string $key   Cache key
	 * @param mixed  $data  Data to save
	 */
	function trx_addons_cache_put_storage( $key, $data ) {
		global $TRX_ADDONS_CACHE_STORAGE;
		$data_serialized = serialize( $data );
		if ( ! isset( $TRX_ADDONS_CACHE_STORAGE[ $key ] ) || serialize( $TRX_ADDONS_CACHE_STORAGE[ $key ] ) != $data_serialized ) {
			$TRX_ADDONS_CACHE_STORAGE[ $key ] = $data;
			trx_addons_cache_save_storage( $key, $data_serialized );
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_get_storage' ) ) {
	/**
	 * Get data from the internal storage or from the cache storage file
	 * 
	 * @param string $key       Cache key
	 * @param mixed  $default   Default value to return if file not exists
	 */
	function trx_addons_cache_get_storage( $key, $default = false ) {
		global $TRX_ADDONS_CACHE_STORAGE;
		if ( ! isset( $TRX_ADDONS_CACHE_STORAGE[ $key ] ) ) {
			$tmp = trx_addons_cache_load_storage( $key, $default );
			if ( $tmp !== $default ) {
				$TRX_ADDONS_CACHE_STORAGE[ $key ] = unserialize( $tmp );
			}
		}
		return isset( $TRX_ADDONS_CACHE_STORAGE[ $key ] )
					? $TRX_ADDONS_CACHE_STORAGE[ $key ]
					: $default;
	}
}

if ( ! function_exists( 'trx_addons_cache_load' ) ) {
	/**
	 * Load data from the cache storage file by cache key
	 * 
	 * @param string $cache_key  Cache key
	 * @param mixed  $default    Default value to return if file not exists
	 */
	function trx_addons_cache_load( $cache_key, $default = false ) {
		$cache = $default;
		// Try to get value from the local cache
		$found = null;
		$cache = wp_cache_get( $cache_key, trx_addons_cache_get_option_name(), false, $found );
		if ( ! $found ) {
			$cache = $default;
			// If not found - get from external storage
			//$cache_list = get_option( trx_addons_cache_get_option_name(), false );
			$cache_list = trx_addons_cache_get_storage( trx_addons_cache_get_option_name() );
			if ( ! empty($cache_list[$cache_key]) ) {
				// If key is expired - remove data
				if ( time() > $cache_list[$cache_key]['expired'] ) {
					trx_addons_cache_delete( $cache_key );
				} else {
					// Get data from database (WordPress transients are used)
					if ( $cache_list[$cache_key]['handler'] == 'database' ) {
						$cache = get_transient( trx_addons_cache_get_option_name() . '_' . $cache_key );
					// Get data from a local file (stored in the uploads folder)
					} else if ( $cache_list[$cache_key]['handler'] == 'files' ) {
						$cache = trx_addons_cache_load_storage( $cache_key, $default );
						if ( is_string( $cache ) && is_serialized($cache) && $cache_list[$cache_key]['hash'] == md5( $cache ) ) {
							$cache = trx_addons_unserialize( $cache );
						} else {
							$cache = $default;
						}
					}
				}
			}
		}
		return $cache;
	}
}

// Save data to the cache (for 12 hours by default)
if ( ! function_exists( 'trx_addons_cache_save' ) ) {
	/**
	 * Save data to the cache storage file by cache key
	 * 
	 * @param string $cache_key  Cache key
	 * @param mixed  $cache_data Data to save
	 * @param int    $cache_time Cache time (in seconds). If time == 0 - internal cache is used only for current session. Default - 12 hours
	 */
	function trx_addons_cache_save( $cache_key, $cache_data, $cache_time = 12 * 60 * 60 ) {

		// If time == 0 - internal cache is used only for current session
		if ( $cache_time === 0 ) {
			wp_cache_set( $cache_key, $cache_data, trx_addons_cache_get_option_name() );

		// Otherwise external storage is used
		} else {

			// Load cache list
			//$cache_list = get_option( trx_addons_cache_get_option_name(), false );
			$cache_list = trx_addons_cache_get_storage( trx_addons_cache_get_option_name() );
			if ( ! is_array($cache_list) ) {
				$cache_list = array();
			}
			$cache_list[$cache_key] = array(
				'expired' => time() + $cache_time,
				'handler' => trx_addons_get_option('cache_handler')
			);

			// Save data to the cache
			if ( $cache_list[$cache_key]['handler'] == 'database' ) {		// To the database (WordPress transients are used)
				set_transient( trx_addons_cache_get_option_name() . '_' . $cache_key, $cache_data, $cache_time );
			} else if ( $cache_list[$cache_key]['handler'] == 'files' ) {	// To the local file (stored in the uploads folder)
				$cache_data = serialize( $cache_data );
				$cache_list[$cache_key]['hash'] = md5( $cache_data );
				trx_addons_cache_save_storage( $cache_key, $cache_data );
			}

			// Save cache list
			//update_option( trx_addons_cache_get_option_name(), $cache_list );
			trx_addons_cache_put_storage( trx_addons_cache_get_option_name(), $cache_list );
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_delete' ) ) {
	/**
	 * Delete data (single entry) from the cache storage file by cache key
	 * 
	 * @param string $cache_key  Cache key
	 * @param array  $cache_item Cache item from the cache list
	 */
	function trx_addons_cache_delete( $cache_key, $cache_item=false ) {
		$from_list = $cache_item === false;
		if ( $from_list ) {
			//$cache_list = get_option( trx_addons_cache_get_option_name(), false );
			$cache_list = trx_addons_cache_get_storage( trx_addons_cache_get_option_name() );
			$cache_item = ! empty( $cache_list[$cache_key] ) && is_array($cache_list[$cache_key]) ? $cache_list[$cache_key] : false;
		}
		if ( is_array( $cache_item ) ) {
			if ( $cache_item['handler'] == 'database' ) {
				delete_transient( trx_addons_cache_get_option_name() . '_' . $cache_key );
			} else if ( $cache_item['handler'] == 'files' ) {
				trx_addons_cache_delete_storage( $cache_key );
			}
			if ( $from_list ) {
				unset( $cache_list[$cache_key] );
				//update_option( trx_addons_cache_get_option_name(), $cache_list );
				trx_addons_cache_put_storage( trx_addons_cache_get_option_name(), $cache_list );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_clear' ) ) {
	/**
	 * Clear whole cache storage
	 */
	function trx_addons_cache_clear() {
		//$cache_list = get_option( trx_addons_cache_get_option_name(), false );
		$cache_list = trx_addons_cache_get_storage( trx_addons_cache_get_option_name() );
		if ( is_array( $cache_list ) ) {
			foreach( $cache_list as $cache_key => $cache_item ) {
				trx_addons_cache_delete( $cache_key, $cache_item );
			}
			//update_option( trx_addons_cache_get_option_name(), false );
			trx_addons_cache_put_storage( trx_addons_cache_get_option_name(), false );
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_save_post' ) ) {
	add_action( 'save_post',	'trx_addons_cache_clear_on_save_post', 10, 1 );
	/**
	 * Clear cache when save/publish post
	 * 
	 * @hooked save_post
	 * 
	 * @param int $id Post ID
	 */
	function trx_addons_cache_clear_on_save_post( $id ) {
		global $post_type, $post;
		// check autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $id;
		}
		// check permissions
		if ( empty( $post_type ) || ! is_string( $post_type ) || ! current_user_can( 'edit_' . $post_type, $id ) ) {
			return $id;
		}
		if ( ! empty( $post->ID ) && $id == $post->ID ) {
			trx_addons_cache_clear();
		}
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_save_post_from_elementor' ) ) {
	add_action( 'trx_addons_action_save_post_from_elementor', 'trx_addons_cache_clear_on_save_post_from_elementor', 10, 2 );
	/**
	 * Clear cache when save/publish post from Elementor
	 * 
	 * @hooked trx_addons_action_save_post_from_elementor
	 * 
	 * @param int $id        Post ID. Not used
	 * @param array $actions List of actions. Not used
	 */
	function trx_addons_cache_clear_on_save_post_from_elementor($id, $actions) {
		trx_addons_cache_clear();
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_save_taxonomy' ) ) {
	add_action( 'trx_addons_action_clear_cache_taxonomy', 'trx_addons_cache_clear_on_save_taxonomy', 10, 1 );
	/**
	 * Clear cache when save/publish taxonomy
	 * 
	 * @hooked trx_addons_action_clear_cache_taxonomy
	 * 
	 * @param string $tax Taxonomy name. Not used
	 */
	function trx_addons_cache_clear_on_save_taxonomy( $tax ) {
		trx_addons_cache_clear();
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_save_menu' ) ) {
	add_action( 'wp_update_nav_menu', 'trx_addons_cache_clear_on_save_menu', 10, 2 );
	/**
	 * Clear cache when save menu
	 * 
	 * @hooked wp_update_nav_menu
	 * 
	 * @param int $menu_id      Menu ID. Not used
	 * @param array $menu_data  Menu data. Not used
	 */
	function trx_addons_cache_clear_on_save_menu( $menu_id = 0, $menu_data = array() ) {
		trx_addons_cache_clear();
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_save_options' ) ) {
	add_action( 'trx_addons_action_just_save_options', 'trx_addons_cache_clear_on_save_options' );
	/**
	 * Clear cache when save plugin and/or theme options
	 * 
	 * @hooked trx_addons_action_just_save_options
	 */
	function trx_addons_cache_clear_on_save_options() {
		trx_addons_cache_clear();
	}
}

if ( ! function_exists( 'trx_addons_cache_clear_on_demand' ) ) {
	add_action( 'trx_addons_action_clear_cache',	'trx_addons_cache_clear_on_demand', 10, 1 );
	/**
	 * Clear cache on demand
	 * 
	 * @hooked trx_addons_action_clear_cache
	 * 
	 * @param string $cc Cache component
	 */
	function trx_addons_cache_clear_on_demand( $cc ) {
		if ( $cc == 'menu' || $cc == 'all' ) {
			trx_addons_cache_clear();
		}
	}
}
