<?php
/**
 * Fivo Docs Admin.
 *
 * @package     Fivo_Docs/Admin/Classes
 * @since       1.0
 * @author      apalodi
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Fivo_Docs_Admin' ) ) :

class Fivo_Docs_Admin {

    /**
     * Constructor
     */
    public function __construct() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'media_buttons', array( $this, 'add_documents_button' ) );
        add_action( 'restrict_manage_posts', array( $this, 'add_category_dropdown_filter' ) );

        add_filter( 'post_mime_types', array( $this, 'add_document_post_mime_type' ) );
        add_filter( 'ajax_query_attachments_args', array( $this, 'ajax_query_attachments_args' ) );
        add_filter( 'pre_get_posts', array( $this, 'media_pre_get_posts' ) );

        add_action( 'admin_footer', array( $this, 'print_modal_template' ) );
        add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_modal_template' ) );

        add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'elementor/editor/footer', array( $this, 'print_modal_template' ) );
    }

    /**
     * Enqueue scripts.
     *
     * @since   1.0
     * @access  public
     */
    public function enqueue_scripts() {

        $assets_url     = fivo_docs()->admin_dir_url . 'assets/';
        $plugin_version = fivo_docs()->version;
        $tax_name       = fivo_docs()->category_taxonomy;
        $taxonomy       = get_taxonomy( $tax_name );

        // CSS
        wp_enqueue_style( 'chosen', $assets_url . 'css/chosen.min.css', array(), $plugin_version );
        wp_enqueue_style( 'fivo-docs-admin', $assets_url . 'css/admin.css', array( 'media-views' ), $plugin_version );
        //add_editor_style( $assets_url . 'css/editor-style.css' );

        // JS
        wp_register_script( 'jquery-chosen', $assets_url . 'js/chosen.jquery.min.js', array(), $plugin_version, true );
        wp_register_script( 'fivo-docs-admin', $assets_url . 'js/admin.js', array( 'jquery-chosen', 'media-editor', 'media-views' ), $plugin_version, true );
        wp_localize_script(
            'fivo-docs-admin',
            'fivo_docs_admin',
            array(
                'media_frame_title'     => esc_html__( 'Choose Documents', 'fivo-docs' ),
                'media_frame_button'    => esc_html__( 'Add Documents', 'fivo-docs' ),
                'filter_label'          => esc_html_x( 'Filter by categories', 'grid layout filter label', 'fivo-docs' ),
                'category_taxonomy'     => $tax_name,
                'taxonomy_all_items'    => $taxonomy->labels->all_items,
                'terms'                 => $this->get_terms_for_category_dropdown_filter(),
            )
        );

        wp_enqueue_script( 'fivo-docs-admin' );
    }

    /**
     * Add our custom button next to the "Add Media" button.
     *
     * @since   1.0.
     * @access  public
     * @return  string Button HTML
     */
    public function add_documents_button() {
        echo '<button type="button" id="insert-fivo-docs-button" class="button insert-fivo-docs add_media" data-editor="content"><span class="wp-media-buttons-icon fivo-docs-buttons-icon"></span>' . esc_html__( 'Add Docs', 'fivo-docs' ) . '</button>';
    }

    /**
     * Generate category taxonomy javascript object for the filter dropdown media grid view.
     *
     * @since   1.0.
     * @access  public
     * @return  array $filters Category filter terms
     */
    public function get_terms_for_category_dropdown_filter() {

        //if ( false === ( $filters = get_transient( 'fivo_docs_category_dropdown' ) ) ) {

            $tax_name = fivo_docs()->category_taxonomy;
            $terms = get_terms( array(
                'taxonomy' => $tax_name,
                'orderby' => 'menu_order name',
                'hide_empty' => false,
                'pad_counts' => true
            ) );
            $filters = array();

            if ( is_wp_error( $terms ) ) { return $filters; }

            foreach ( $terms as $key => $term ) {
                if ( '0' == $term->parent ) {

                    $filters[] = array(
                        'id' => esc_attr( $term->term_id ),
                        'name' => esc_attr( $term->name . "&nbsp;&nbsp;(" . $term->count . ")" ),
                        'slug' => esc_attr( $term->slug )
                    );

                    $children = get_terms( array(
                        'taxonomy' => $tax_name,
                        'child_of' => $term->term_id,
                        'orderby' => 'menu_order name',
                        'hide_empty' => false
                    ) );

                    foreach ( $children as $key => $child ) {
                        $filters[] = array(
                            'id' => esc_attr( $child->term_id ),
                            'name' => esc_attr( "&nbsp;&nbsp;&nbsp;" . $child->name . "&nbsp;&nbsp;(" . $child->count . ")" ),
                            'slug' => esc_attr( $child->slug )
                        );
                    }
                }
            }

        //}

        return array_filter( $filters );
    }

    /**
     * Renders category taxonomy filter dropdown only in the media list view.
     *
     * For the grid layout we need javascript wp.media.view.AttachmentFilters
     *
     * @since   1.0.
     * @access  public
     * @param   string $post_type Current post type
     */
    public function add_category_dropdown_filter() {
        global $wp_query;

        $screen = get_current_screen();

        if ( 'upload' == $screen->base ) {

            $tax_name = fivo_docs()->category_taxonomy;
            $taxonomy = get_taxonomy( $tax_name );
            $current = isset( $wp_query->query[$tax_name] ) ? $wp_query->query[$tax_name] : 0;

            echo '<label for="filter-by-' . esc_attr( $tax_name ) . '" class="screen-reader-text">' . esc_html_x( 'Filter by categories', 'list layout filter label', 'fivo-docs' ) . '</label>' . "\n";

            wp_dropdown_categories( array(
                'show_option_all'   => $taxonomy->labels->all_items,
                'option_none_value' => '',
                'taxonomy'          => $tax_name,
                'name'              => $tax_name,
                'id'                => 'filter-by-' . $tax_name,
                'selected'          => $current,
                'value_field'       => 'slug',
                'orderby'           => 'menu_order name',
                'show_count'        => true,
                'pad_counts'        => true,
                'hide_empty'        => false,
                'hierarchical'      => true,
                'depth'             => 2,
                'hide_if_empty'     => true,
            ));
        }
    }

    /**
     * Add a 'document' mime type to the default list of post mime types.
     *
     * @since   1.0.
     * @access  public
     * @param   array $post_mime_types Array of mime types
     * @return  array $post_mime_types Array of mime types
     */
    public function add_document_post_mime_type( $post_mime_types ) {

        $post_mime_types['document'] = array(
            esc_html__( 'Documents', 'fivo-docs' ),
            esc_html__( 'Manage Documents', 'fivo-docs' ),
            _n_noop( 'Documents <span class="count">(%s)</span>', 'Documents <span class="count">(%s)</span>', 'fivo-docs' )
        );

        return $post_mime_types;
    }

    /**
     * Because we have added a 'document' mime type we need
     * to change the query to get the results.
     *
     * This is only for the media grid layout.
     *
     * @since   1.0.
     * @access  public
     * @param   array $query Query
     * @return  array $query Query
     */
    public function ajax_query_attachments_args( $query ) {

        if ( 'document' == $query['post_mime_type'] ) {
            $query['post_mime_type'] = fivo_docs()->mime_types;
        }

        return $query;
    }

    /**
     * Because we have added a 'document' mime type we need
     * to change the query to get the results.
     *
     * This is only for the media list layout.
     *
     * @since   1.0.
     * @access  public
     * @param   array $query Query
     * @return  array $query Query
     */
    public function media_pre_get_posts( $query ) {

        if ( ! $query->is_main_query() ) {
            return;
        }

        if ( 'document' == $query->get( 'post_mime_type' ) ) {
            $query->set( 'post_mime_type', fivo_docs()->mime_types );
        }

        return $query;
    }

    /**
     * Modal with our options
     *
     * @since   1.0.
     * @access  public
     * @return  string $html Modal HTML
     */
    public function print_modal_template() {

        $terms = $this->get_terms_for_category_dropdown_filter();
        $categories = '<span class="fivo-docs-label">' . esc_html__( 'Select Categories', 'fivo-docs' ) . '</span><select name="fivo-docs-modal-categories[]" class="fivo-docs-modal-categories fivo-docs-option-value" multiple data-placeholder="' . esc_html__( 'If empty use all categories', 'fivo-docs' ) . '">';
        //$categories .= '<option value="0"></option>';
        foreach ( $terms as $key => $term ) {
            $categories .= '<option value="' . esc_attr( $term['id'] ) . '">' . esc_html( $term['name'] ) . '</option>';
        }
        $categories .= '</select>';

        $html = '
        <script type="text/html" id="tmpl-fivo-docs-modal">
            <div class="fivo-docs-modal">
                <div class="fivo-docs-modal-content" data-fivo-docs-is-open="">

                    <div class="fivo-docs-modal-header">
                        <div class="fivo-docs-modal-title">' . esc_html_x( 'Fivo Docs', 'modal title', 'fivo-docs' ) . '</div>
                        <span class="fivo-docs-modal-back"></span>
                        <span class="fivo-docs-modal-close"></span>
                    </div>

                    <div class="fivo-docs-modal-select">
                        <span class="fivo-docs-modal-options fivo-docs-modal-category" data-fivo-docs-open=".fivo-docs-categories">' . esc_html_x( 'Categories', 'modal selection', 'fivo-docs' ) . '</span>
                        <span class="fivo-docs-modal-options fivo-docs-modal-selection" data-fivo-docs-open=".fivo-docs-custom-selection">' . esc_html_x( 'Custom Selection', 'modal selection', 'fivo-docs' ) . '</span>
                    </div>

                    <div class="fivo-docs-modal-inner fivo-docs-categories">
                    <form>
                        <div class="fivo-docs-option">
                            ' . $categories . '
                        </div>
                        <div class="fivo-docs-option">
                            <span class="fivo-docs-label">' . esc_html__( 'Align', 'fivo-docs' ) . '</span>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-cats-align" class="fivo-docs-radio-item fivo-docs-option-value" value="" checked>
                                <span>' . esc_html__( 'None', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-cats-align" class="fivo-docs-radio-item fivo-docs-option-value" value="left">
                                <span>' . esc_html__( 'Left', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-cats-align" class="fivo-docs-radio-item fivo-docs-option-value" value="right">
                                <span>' . esc_html__( 'Right', 'fivo-docs' ) . '</span>
                            </label>
                        </div>
                        <div class="fivo-docs-option">
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-date" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Show Date', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-masonry" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Enable Masonry Layout', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-open" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Open First Subcategories', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-scrollbar" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Show Scrollbar for Long Lists', 'fivo-docs' ) . '</span>
                            </label>
                        </div>
                        <div class="fivo-docs-option">
                            <span class="fivo-docs-label">' . esc_html__( 'Columns', 'fivo-docs' ) . '</span>
                            <label class="fivo-docs-columns">
                                <span class="dashicons dashicons-smartphone"></span>
                                <input type="text" class="fivo-docs-small-input fivo-docs-option-value" name="fivo-docs-col[320]" value="1">
                            </label>
                            <label class="fivo-docs-columns">
                                <span class="dashicons dashicons-tablet"></span>
                                <input type="text" class="fivo-docs-small-input fivo-docs-option-value" name="fivo-docs-col[768]" value="2">
                            </label>
                            <label class="fivo-docs-columns">
                                <span class="dashicons dashicons-tablet fivo-docs-tablet-rotate"></span>
                                <input type="text" class="fivo-docs-small-input fivo-docs-option-value" name="fivo-docs-col[992]" value="3">
                            </label>
                            <label class="fivo-docs-columns">
                                <span class="dashicons dashicons-desktop"></span>
                                <input type="text" class="fivo-docs-small-input fivo-docs-option-value" name="fivo-docs-col[1200]" value="3">
                            </label>
                        </div>

                        <div class="fivo-docs-modal-footer">
                            <div class="fivo-docs-modal-footer-left">
                                <input onfocus="this.select();" readonly="readonly" type="text" class="widefat fivo-docs-usage-shortcode" value="[fivo_docs]">
                            </div>
                            <div class="fivo-docs-modal-footer-right">
                                <button type="button" class="button button-primary fivo-docs-modal-insert" data-fivo-docs-type="categories">' . esc_html_x( 'Insert Documents', 'modal insert categories button', 'fivo-docs' ) . '</button>
                            </div>
                        </div>
                    </form>
                    </div>

                    <div class="fivo-docs-modal-inner fivo-docs-custom-selection">
                    <form>
                        <div class="fivo-docs-option">
                            <label>
                                <span class="fivo-docs-label">' . esc_html__( 'Title', 'fivo-docs' ) . '</span>
                                <input type="text" name="fivo-docs-title" class="fivo-docs-text-item widefat fivo-docs-option-value" value="">
                            </label>
                        </div>
                        <div class="fivo-docs-option">
                            <span class="fivo-docs-label">' . esc_html__( 'Align', 'fivo-docs' ) . '</span>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-custom-align" class="fivo-docs-radio-item fivo-docs-option-value" value="" checked>
                                <span>' . esc_html__( 'None', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-custom-align" class="fivo-docs-radio-item fivo-docs-option-value" value="left">
                                <span>' . esc_html__( 'Left', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="fivo-docs-inline">
                                <input type="radio" name="fivo-docs-custom-align" class="fivo-docs-radio-item fivo-docs-option-value" value="right">
                                <span>' . esc_html__( 'Right', 'fivo-docs' ) . '</span>
                            </label>
                        </div>
                        <div class="fivo-docs-option">
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-custom-date" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Show Date', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-boxed" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Use Boxed Style', 'fivo-docs' ) . '</span>
                            </label>
                            <label class="checkboxes">
                                <input type="checkbox" name="fivo-docs-custom-scrollbar" class="fivo-docs-option-value" value="1">
                                <span>' . esc_html__( 'Show Scrollbar for Long Lists', 'fivo-docs' ) . '</span>
                            </label>
                        </div>
                        <div class="fivo-docs-option">
                            <input type="hidden" name="fivo-docs-ids" class="fivo-docs-ids fivo-docs-option-value" value="">
                            <button type="button" class="button button-secondary fivo-docs-add">' . esc_html_x( 'Add Documents', 'custom selection button', 'fivo-docs' ) . '</button>
                            <span class="fivo-docs-info fivo-docs-ui-color">' . esc_html__( 'Please add documents', 'fivo-docs' ) . '</span>
                            <ul class="fivo-docs-thumbs"></ul>
                            <span class="howto">' . esc_html__( 'Drag and drop documents to organize them.', 'fivo-docs' ) . '</span>
                        </div>
                        <div class="fivo-docs-modal-footer">
                            <div class="fivo-docs-modal-footer-left">
                                <input onfocus="this.select();" readonly="readonly" type="text" class="widefat fivo-docs-usage-shortcode" value="[fivo_docs]">
                            </div>
                            <div class="fivo-docs-modal-footer-right">
                                <button type="button" class="button button-primary fivo-docs-modal-insert" data-fivo-docs-type="custom">' . esc_html_x( 'Insert Documents', 'modal insert custom button', 'fivo-docs' ) . '</button>
                            </div>
                        </div>
                    </form>
                    </div>

                </div>
                <div class="fivo-docs-modal-backdrop"></div>
            </div>
        </script>';

        echo $html;
    }

}

new Fivo_Docs_Admin();

endif;
