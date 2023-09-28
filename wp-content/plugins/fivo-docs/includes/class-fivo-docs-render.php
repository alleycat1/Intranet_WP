<?php
/**
 * Fivo Docs Render.
 *
 * @package     Fivo_Docs/Classes
 * @since       1.2
 * @author      apalodi
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Fivo_Docs_Render' ) ) :

class Fivo_Docs_Render {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( __CLASS__, 'javascript_detection' ), 0 );
    }

    /**
     * Enqueue scripts.
     *
     * @since   1.0
     * @access  public
     */
    public static function enqueue_scripts() {

        $assets_url     = fivo_docs()->plugin_dir_url . 'assets/';
        $plugin_version = fivo_docs()->version;

        wp_register_style( 'fivo-docs', $assets_url . 'css/style.css', array(), $plugin_version );
        wp_register_script( 'fivo-docs', $assets_url . 'js/main.js', array( 'jquery' ), $plugin_version, true );

        wp_enqueue_style( 'fivo-docs' );
    }

    /**
     * Adds a 'fivo-js' class to the <html> element when JavaScript is detected.
     *
     * @since   1.0
     * @access  public
     */
    public static function javascript_detection() {
        echo "<script>document.documentElement.classList ? document.documentElement.classList.add('fivo-js') : document.documentElement.className += ' fivo-js';</script>\n";
    }

    /**
     * Get query args.
     *
     * @since   1.0
     * @access  private
     * @param   string $taxonomy
     * @param   string $terms
     * @param   array $not_in
     * @return  array $query_args
     */
    private function get_query_args( $taxonomy, $terms, $not_in = array() ) {
        $mime_types = fivo_docs()->mime_types;
        $query_args = array(
            'posts_per_page' => -1,
            'post_type'=> 'attachment',
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => $taxonomy,
                    'terms' => $terms,
                    'field' => 'term_id',
                    'include_children' => 0
                ),
                array(
                    'taxonomy' => $taxonomy,
                    'terms'    => $not_in,
                    'field'    => 'term_id',
                    'operator' => 'NOT IN',
                ),
            ),
            'post_mime_type' => $mime_types,
            'suppress_filters' => 0
        );
        return apply_filters( 'fivo_docs_query_args', $query_args, $taxonomy, $terms, $not_in, $mime_types );
    }

    /**
     * Convert some filetypes, e.g. 'docx'=>'doc'.
     *
     * @since   1.0
     * @access  private
     * @param   string $type
     * @return  string $type
     */
    private function convert_filetype( $type ) {
        $defaults = apply_filters( 'fivo_docs_convert_filetype', array(
            'docx' => 'doc',
            'docm' => 'doc',
            'dotm' => 'doc',
            'xlsx' => 'xls',
            'xlsm' => 'xls',
            'xlsb' => 'xls',
            'pptx' => 'ppt',
            'pptm' => 'ppt',
            'ppsx' => 'pps',
            'ppsm' => 'pps',
        ) );

        if ( array_key_exists( $type, $defaults ) ) {
            return $defaults[$type];
        }

        return $type;
    }

    /**
     * Get terms in a hierarchy.
     *
     * @since   1.0
     * @access  private
     * @param   string $cats
     * @return  array $terms Terms in a hierarchical order
     */
    private function get_terms( $cats ) {

        $taxonomy = fivo_docs()->category_taxonomy;
        $all_terms = get_terms( apply_filters( 'fivo_docs_terms_args', array(
            'taxonomy' => $taxonomy,
            'include' => $cats,
            'orderby' => 'menu_order name',
            'hide_empty' => false
        ), $taxonomy, $cats ) );

        $terms = array();

        foreach ( $all_terms as $key => $term ) {
            $ids[] = $term->term_id;
        }

        foreach ( $all_terms as $key => $term ) {
            if ( ! in_array( $term->parent, $ids ) ) {
                $terms[$term->term_id] = (array) $term;
            }
        }

        $hierarchy = array();

        foreach ( $terms as $id => $term ) {
            $hierarchy[$id] = get_term_children( $id, $taxonomy );
        }

        foreach ( $hierarchy as $parent_id => $children_ids ) {
            if ( ! empty( $children_ids ) ) {
                $children = get_terms( apply_filters( 'fivo_docs_children_terms_args', array(
                    'taxonomy' => $taxonomy,
                    'include' => $children_ids,
                    'orderby' => 'menu_order name',
                    'hide_empty' => false
                ), $taxonomy, $children_ids ) );
                $terms[$parent_id]['children'] = $children;
                $terms[$parent_id]['children_ids'] = $children_ids;
            } else {
                $terms[$parent_id]['children'] = array();
                $terms[$parent_id]['children_ids'] = array();
            }
        }

        return apply_filters( 'fivo_docs_terms', $terms, $cats );
    }

    /**
     * Get categories with the attachments.
     *
     * @since   1.0
     * @access  private
     * @param   string $cats
     * @return  array $categories Categories with attachments
     */
    private function get_categories_data( $cats ) {

        $categories = array();
        $taxonomy = fivo_docs()->category_taxonomy;
        $terms = $this->get_terms( $cats );

        foreach ( $terms as $key => $term ) {

            $categories[$term['slug']] = array(
                'name' => $term['name'],
                'attachments' => array(),
                'children' => array()
            );

            $query_args = $this->get_query_args( $taxonomy, $term['term_id'], $term['children_ids'] );
            $attachments = get_posts( $query_args );

            if ( $attachments ) {
                foreach ( $attachments as $key => $attachment ) {
                    // post_title shouldn't ever be empty, but just in case
                    $name = ( ! empty( $attachment->post_title ) ) ? $attachment->post_title : $attachment->post_name;
                    $file = get_attached_file( $attachment->ID );
                    $type = wp_check_filetype( $file );
                    $categories[$term['slug']]['attachments'][] = array(
                        'name' => $name,
                        'link' => wp_get_attachment_url( $attachment->ID ),
                        'type' => $this->convert_filetype( $type['ext'] ),
                        'size' => file_exists( $file ) ? size_format( filesize( $file ), 1 ) : '0 B',
                        'date' => get_the_date( '', $attachment ),
                    );
                }
            }

            foreach ( $term['children'] as $key => $child ) {
                $categories[$term['slug']]['children'][$child->slug] = array(
                    'name' => $child->name
                );
                $query_args = $this->get_query_args( $taxonomy, $child->term_id );
                $attachments = get_posts( $query_args );

                if ( $attachments ) {
                    foreach ( $attachments as $key => $attachment ) {
                        // post_title shouldn't ever be empty, but just in case
                        $name = ( ! empty( $attachment->post_title ) ) ? $attachment->post_title : $attachment->post_name;
                        $file = get_attached_file( $attachment->ID );
                        $type = wp_check_filetype( $file );
                        $categories[$term['slug']]['children'][$child->slug]['attachments'][] = array(
                            'name' => $name,
                            'link' => wp_get_attachment_url( $attachment->ID ),
                            'type' => $this->convert_filetype( $type['ext'] ),
                            'size' => file_exists( $file ) ? size_format( filesize( $file ), 1 ) : '0 B',
                            'date' => get_the_date( '', $attachment ),
                        );
                    }
                }
            }
        }

        return apply_filters( 'fivo_docs_categories_data', $categories, $cats );
    }

    /**
     * Generate associative array with columns breakpoints.
     *
     * @since   1.2
     * @access  private
     * @param   array $columns
     * @return  array $breakpoints_columns
     */
    private function get_columns_breakpoints( $columns ) {
        $breakpoints_columns = array();
        $breakpoints = fivo_docs()->get_breakpoints();
        $breakpoint = 'xs';

        foreach ( $breakpoints as $key => $value ) {
            if ( isset( $columns[$key] ) ) {
                $breakpoint = $key;
            }
            $breakpoints_columns[$value] = $columns[$breakpoint];
        }

        return $breakpoints_columns;
    }

    /**
     * Display categories.
     *
     * @since   1.0
     * @access  public
     * @param   array $atts
     * @return  string $content Categories HTML
     */
    public function display_categories( $atts ) {

        $categories = $atts['categories'];
        $align = $atts['align'];
        $date = $atts['date'];
        $masonry = $atts['masonry'];
        $open = $atts['open'];
        $scrollbar = $atts['scrollbar'];
        $columns = $atts['columns'];

        $data = $this->get_categories_data( $categories );

        if ( $data ) {

            $columns_data_attr = wp_json_encode( $this->get_columns_breakpoints( $columns ) );

            $classes = '';
            $safe_attr = '';
            $has_scrollbar = '';

            if ( '' != $align ) {
                $classes .= ' fivo-docs-align-' . $align;
            }

            if ( '' != $masonry ) {
                $classes .= ' fivo-docs-masonry';
                $safe_attr = ' data-fivo-docs-columns="' . esc_attr( $columns_data_attr ) . '"';
            }

            if ( '' != $scrollbar ) {
                $has_scrollbar = ' fivo-docs-has-scrollbar';
            }

            foreach ( $columns as $size => $col) {
                $classes .= ' fivo-docs-col-' . esc_attr( $size ) . '-' . esc_attr( $col );
            }

            $content = '<div class="fivo-docs fivo-docs-categories fds ' . $align . ' after ' . esc_attr( $classes ) . '"' . $safe_attr /*escaped already*/ . '>';

            foreach ( $data as $key => $value ) {

                $count = 1;

                $content .= '<div class="fivo-docs-category">';

                    $content .= '<h3 class="fivo-docs-category-title">' . esc_html( $value['name'] ) . '</h3>';
                    $content .= '<div class="fivo-docs-subcategories">';

                    if ( $value['attachments'] ) {
                        $count = 2;
                        $content .= '<div class="fivo-docs-subcategory fivo-docs-is-uncategorized' . esc_attr( $has_scrollbar ) . '">';
                        foreach ( $value['attachments'] as $key => $attachment ) {
                            $content .= '<a target="_blank" href="' . esc_url( $attachment['link'] ) . '" class="fivo-docs-item">';
                            $content .= '<span class="fivo-docs-file-icon" data-fivo-docs-file-type="' . esc_attr( $attachment['type'] ) . '"><span></span></span>';
                            $content .= esc_html( $attachment['name'] );
                            $content .= '<span class="fivo-docs-info">';
                            $content .= '<span class="fivo-docs-size">' . esc_html( $attachment['size'] ) . '</span>';
                            if ( '' != $date ) {
                                $content .= '<span class="fivo-docs-date">' . esc_html( $attachment['date'] ) . '</span>';
                            }
                            $content .= '</span>';
                            $content .= '</a>';
                        }
                        $content .= '</div>';
                    }

                    foreach ( $value['children'] as $key => $value ) {
                        $is_open = $count == 1 && '1' == $open ? ' is-active is-open' : '';
                        $content .= '<div class="fivo-docs-subcategory">';
                            $content .= '<h4 class="fivo-docs-subcategory-title' . esc_attr( $is_open ) . '">' . esc_html( $value['name'] ) . '<span class="fivo-docs-subcategory-action"></span></h4>';
                            $content .= '<div class="fivo-docs-list fivo-docs-categories-list' . esc_attr( $has_scrollbar ) . '">';
                            if ( isset( $value['attachments'] ) ) {
                                foreach ( $value['attachments'] as $key => $attachment ) {
                                    $content .= '<a target="_blank" href="' . esc_url( $attachment['link'] ) . '" class="fivo-docs-item">';
                                    $content .= '<span class="fivo-docs-file-icon" data-fivo-docs-file-type="' . esc_attr( $attachment['type'] ) . '"><span></span></span>';
                                    $content .= esc_html( $attachment['name'] );
                                    $content .= '<span class="fivo-docs-info">';
                                    $content .= '<span class="fivo-docs-size">' . esc_html( $attachment['size'] ) . '</span>';
                                    if ( '' != $date ) {
                                        $content .= '<span class="fivo-docs-date">' . esc_html( $attachment['date'] ) . '</span>';
                                    }
                                    $content .= '</span>';
                                    $content .= '</a>';
                                }
                            }
                            $content .= '</div><!-- .fivo-docs-list -->';
                        $content .= '</div><!-- .fivo-docs-subcategory -->';
                        $count++;
                    }

                    $content .= '</div><!-- .fivo-docs-subcategories -->';
                $content .= '</div><!-- .fivo-docs-category -->';
            }

            $content .= '</div><!-- .fivo-docs-wrapper -->';

        } else {
            $content = '<p class="fivo-docs-none-found">' . esc_html__( 'No document categories were found.', 'fivo-docs' ) . '</p>';
        }

        return $content;
    }

    /**
     * Display custom selection.
     *
     * @since   1.0
     * @access  public
     * @param   array $atts
     * @return  string $content
     */
    public function display_custom_selection( $atts ) {

        $ids = $atts['ids'];
        $title = $atts['title'];
        $align = $atts['align'];
        $date = $atts['date'];
        $boxed = $atts['boxed'];
        $scrollbar = $atts['scrollbar'];

        $mime_types = fivo_docs()->mime_types;

        $query_args = apply_filters( 'fivo_docs_custom_query_args', array(
            'posts_per_page' => -1,
            'post_type'=> 'attachment',
            'orderby' => 'post__in',
            'post_mime_type' => $mime_types,
            'post__in' => $ids,
            'suppress_filters' => 0
        ), $ids, $mime_types );

        $attachments = get_posts( $query_args );

        if ( $attachments ) {

            $classes = '';
            $variation = 'attachments';
            $has_scrollbar = '';

            if ( '' != $align ) {
                $classes .= ' fivo-docs-align-' . $align;
            }

            if ( '' != $boxed ) {
                $variation = 'boxed';
            }

            if ( '' != $scrollbar ) {
                $has_scrollbar = ' fivo-docs-has-scrollbar';
            }

            $content = '<div class="fivo-docs fivo-docs-' . esc_attr( $variation ) . '' . esc_attr( $classes ) . '">';
                if ( '' != $title ) {
                    $content .= '<h3 class="fivo-docs-' . esc_attr( $variation ) . '-title">' . esc_html( $title ) . '</h3>';
                }
                $content .= '<div class="fivo-docs-list fivo-docs-' . esc_attr( $variation ) . '-list' . esc_attr( $has_scrollbar ) . '">';
                foreach ( $attachments as $key => $attachment ) {
                    // post_title shouldn't ever be empty, but just in case
                    $name = ( ! empty( $attachment->post_title ) ) ? $attachment->post_title : $attachment->post_name;
                    $file = get_attached_file( $attachment->ID );
                    $type = wp_check_filetype( $file );
                    $type = $this->convert_filetype( $type['ext'] );
                    $link = wp_get_attachment_url( $attachment->ID );
                    $size = file_exists( $file ) ? size_format( filesize( $file ), 1 ) : '0 B';
                    $date = '' != $date ? get_the_date( '', $attachment ) : '';

                    $content .= '<a target="_blank" href="' . esc_url( $link ) . '" class="fivo-docs-item">';
                        $content .= '<span class="fivo-docs-file-icon" data-fivo-docs-file-type="' . esc_attr( $type ) . '"><span></span></span>';
                        $content .= esc_html( $name );
                        $content .= '<span class="fivo-docs-info">';
                        $content .= '<span class="fivo-docs-size">' . esc_html( $size ) . '</span>';
                        if ( '' != $date ) {
                            $content .= '<span class="fivo-docs-date">' . esc_html( $date ) . '</span>';
                        }
                        $content .= '</span>';
                    $content .= '</a>';
                }
                $content .= '</div>';
            $content .= '</div>';
        } else {
            $content = '<p class="fivo-docs-none-found">' . esc_html__( 'No attachments were found.', 'fivo-docs' ) . '</p>';
        }

        return $content;
    }
}

endif;
