<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib;

/**
 * The class contains a collection of caching functions.
 *
 * The functions in the class have been borrowed from the
 * "wp-cache-remember" package: https://github.com/stevegrunwell/wp-cache-remember.
 *
 * The functions have been moved into this class so that they
 * can be easily used with php-scoper.
 *
 * @package Barn2\barn2-lib
 */
class Cache
{
    /**
     * Retrieve a value from the object cache. If it doesn't exist, run the $callback to generate and
     * cache the value.
     *
     * @param string   $key      The cache key.
     * @param callable $callback The callback used to generate and cache the value.
     * @param string   $group    Optional. The cache group. Default is empty.
     * @param int      $expire   Optional. The number of seconds before the cache entry should expire.
     *                           Default is 0 (as long as possible).
     *
     * @return mixed The value returned from $callback, pulled from the cache when available.
     */
    public static function remember($key, $callback, $group = '', $expire = 0)
    {
        $found = \false;
        $cached = \wp_cache_get($key, $group, \false, $found);
        if (\false !== $found) {
            return $cached;
        }
        $value = $callback();
        if (!\is_wp_error($value)) {
            \wp_cache_set($key, $value, $group, $expire);
        }
        return $value;
    }
    /**
     * Retrieve and subsequently delete a value from the object cache.
     *
     * @param string $key     The cache key.
     * @param string $group   Optional. The cache group. Default is empty.
     * @param mixed  $default Optional. The default value to return if the given key doesn't
     *                        exist in the object cache. Default is null.
     *
     * @return mixed The cached value, when available, or $default.
     */
    public static function forget($key, $group = '', $default = null)
    {
        $found = \false;
        $cached = \wp_cache_get($key, $group, \false, $found);
        if (\false !== $found) {
            \wp_cache_delete($key, $group);
            return $cached;
        }
        return $default;
    }
    /**
     * Retrieve a value from transients. If it doesn't exist, run the $callback to generate and
     * cache the value.
     *
     * @param string   $key      The transient key.
     * @param callable $callback The callback used to generate and cache the value.
     * @param int      $expire   Optional. The number of seconds before the cache entry should expire.
     *                           Default is 0 (as long as possible).
     *
     * @return mixed The value returned from $callback, pulled from transients when available.
     */
    public static function remember_transient($key, $callback, $expire = 0)
    {
        $cached = \get_transient($key);
        if (\false !== $cached) {
            return $cached;
        }
        $value = $callback();
        if (!\is_wp_error($value)) {
            \set_transient($key, $value, $expire);
        }
        return $value;
    }
    /**
     * Retrieve and subsequently delete a value from the transient cache.
     *
     * @param string $key     The transient key.
     * @param mixed  $default Optional. The default value to return if the given key doesn't
     *                        exist in transients. Default is null.
     *
     * @return mixed The cached value, when available, or $default.
     */
    public static function forget_transient($key, $default = null)
    {
        $cached = \get_transient($key);
        if (\false !== $cached) {
            \delete_transient($key);
            return $cached;
        }
        return $default;
    }
    /**
     * Retrieve a value from site transients. If it doesn't exist, run the $callback to generate
     * and cache the value.
     *
     * @param string   $key      The site transient key.
     * @param callable $callback The callback used to generate and cache the value.
     * @param int      $expire   Optional. The number of seconds before the cache entry should expire.
     *                           Default is 0 (as long as possible).
     *
     * @return mixed The value returned from $callback, pulled from transients when available.
     */
    public static function remember_site_transient($key, $callback, $expire = 0)
    {
        $cached = \get_site_transient($key);
        if (\false !== $cached) {
            return $cached;
        }
        $value = $callback();
        if (!\is_wp_error($value)) {
            \set_site_transient($key, $value, $expire);
        }
        return $value;
    }
    /**
     * Retrieve and subsequently delete a value from the site transient cache.
     *
     * @param string $key     The site transient key.
     * @param mixed  $default Optional. The default value to return if the given key doesn't
     *                        exist in the site transients. Default is null.
     *
     * @return mixed The cached value, when available, or $default.
     */
    public static function forget_site_transient($key, $default = null)
    {
        $cached = \get_site_transient($key);
        if (\false !== $cached) {
            \delete_site_transient($key);
            return $cached;
        }
        return $default;
    }
}
