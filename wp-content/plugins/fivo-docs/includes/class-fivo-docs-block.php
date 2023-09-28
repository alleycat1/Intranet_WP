<?php
/**
 * Fivo Docs Block.
 *
 * @package     Fivo_Docs/Classes
 * @since       1.2
 * @author      apalodi
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Fivo_Docs_Block' ) ) :

class Fivo_Docs_Block {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'init', array( $this, 'register_block_type' ) );
    }

    /**
     * Enqueue scripts.
     *
     * @since   1.2
     * @access  public
     */
    public function enqueue_scripts() {

        $assets_url     = fivo_docs()->admin_dir_url . 'assets/';
        $plugin_version = fivo_docs()->version;
        $tax_name       = fivo_docs()->category_taxonomy;
        $taxonomy       = get_taxonomy( $tax_name );

        // CSS
        wp_enqueue_style( 'fivo-docs-block', $assets_url . 'css/block.css', array( 'wp-editor', 'wp-edit-blocks' ), $plugin_version );

        // JS
        wp_register_script( 'fivo-docs-block', $assets_url . 'js/block.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), $plugin_version, true );

        wp_localize_script(
            'fivo-docs-block',
            'fivo_docs_block',
            array(
                'category_taxonomy'     => $tax_name,
                'taxonomy_all_items'    => $taxonomy->labels->all_items,
            )
        );

        wp_enqueue_script( 'fivo-docs-block' );
    }

    /**
     * Register block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued w8hen the editor loads.
     *
     * @since   1.2.
     * @access  public
     * @return  void
     */
    public function register_block_type() {
        $defalt_string_attr = array(
            'type' => 'string',
            'default' => '',
        );

        $defalt_array_attr = array(
            'type' => 'array',
            'default' => array(),
        );

        $defalt_boolean_attr = array(
            'type' => 'boolean',
            'default' => false,
        );

        register_block_type(
            'fivo-docs/docs', array(
                'style'         => 'fivo-docs',
                'editor_script' => 'fivo-docs-block',
                'editor_style'  => 'fivo-docs-block',
                'render_callback' => array( $this, 'render_callback' ),
                'attributes' => [
                    'type' => [
                        'type' => 'string',
                        'default' => 'categories',
                    ],
                    'categories' => $defalt_array_attr,
                    'align' => $defalt_string_attr,
                    'date' => $defalt_boolean_attr,
                    'masonry' => $defalt_boolean_attr,
                    'open' => $defalt_boolean_attr,
                    'scrollbar' => $defalt_boolean_attr,
                    'columns' => [
                        'type' => 'object',
                        'default' => array(
                            'xs' => 1,
                            'sm' => 2,
                            'md' => 2,
                            'lg' => 3,
                            'xl' => 3,
                        ),
                    ],
                    'title' => $defalt_string_attr,
                    'boxed' => $defalt_boolean_attr,
                    'ids' => $defalt_array_attr,
                ],
            )
        );
    }

    public function render_callback( $atts ) {
        wp_enqueue_script( 'fivo-docs' );

        if ( $atts['ids'] ) {
            return fivo_docs()->render->display_custom_selection( $atts );
        } else {
            return fivo_docs()->render->display_categories( $atts );
        }
    }
}

new Fivo_Docs_Block();

endif;
