<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WooCommerce;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Template_Loader;
/**
 * A WooCommerce template loader.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2
 */
class Templates implements Template_Loader
{
    private $template_path;
    private $default_path;
    private $templates_loaded_once = [];
    public function __construct($theme_dir = '', $default_path = '')
    {
        if (\function_exists('WC')) {
            $this->template_path = $theme_dir ? \trailingslashit(\WC()->template_path() . $theme_dir) : '';
        }
        $this->default_path = $default_path ? \trailingslashit($default_path) : '';
    }
    /**
     * Return the HTML for the specified template.
     *
     * @param string $template_name The template name (e.g. 'path/to/template.php')
     * @param array $args           The template args.
     * @return string The template HTML.
     */
    public function get_template($template_name, array $args = [])
    {
        return \wc_get_template_html($this->expand_template($template_name), $args, $this->get_template_path(), $this->get_default_path());
    }
    /**
     * Output the HTML for the specified template.
     *
     * @param string $template_name The template name (e.g. 'path/to/template.php')
     * @param array $args           The template args.
     */
    public function load_template($template_name, array $args = [])
    {
        \wc_get_template($this->expand_template($template_name), $args, $this->get_template_path(), $this->get_default_path());
    }
    /**
     * Output the HTML for the specified template, but only once. If this function is called multiple times with the
     * same template name, the template is only rendered once on the page.
     *
     * @param string $template_name The template name (e.g. 'path/to/template.php')
     * @param array $args           The template args.
     */
    public function load_template_once($template_name, array $args = [])
    {
        if (!\in_array($template_name, $this->templates_loaded_once, \true)) {
            $this->templates_loaded_once[] = $template_name;
            $this->load_template($template_name, $args);
        }
    }
    public function get_template_path()
    {
        return $this->template_path;
    }
    public function get_default_path()
    {
        return $this->default_path;
    }
    private function expand_template($template_name)
    {
        /*
         * If the template ends with a folder rather than a PHP file, we expand the template name using the
         * terminating folder to build the full template name.
         * E.g. /my-templates/cool/ becomes /my-templates/cool/cool.php
         */
        if ('.php' !== \substr($template_name, -4)) {
            $template_name = \rtrim($template_name, '/ ');
            $last_backslash = \strrpos($template_name, '/');
            if (\false !== $last_backslash) {
                $last_folder = \substr($template_name, $last_backslash + 1);
                $template_name = "{$template_name}/{$last_folder}.php";
            } else {
                $template_name = "{$template_name}/{$template_name}.php";
            }
        }
        return $template_name;
    }
}
