<?php
/**
 * Fivo Docs Shortcode.
 *
 * @package     FivoDocs/Classes
 * @since       1.0
 * @author      apalodi
 */

if ( ! class_exists( 'Fivo_Docs_Shortcode' ) ) :

class Fivo_Docs_Shortcode {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode( 'fivo_docs', array( $this, 'shortcode' ) );
    }

    /**
     * Get the shortcode content.
     *
     * @since   1.0
     * @access  public
     * @param   array $atts
     * @param   string $content
     * @param   string $code
     * @return  string $shortcode
     */
    public function shortcode( $atts, $content = null, $code ) {

        $atts = shortcode_atts( array(
            'cats'      => '',
            'align'     => '',
            'date'      => '',
            'masonry'   => '',
            'open'      => '',
            'scrollbar' => '',
            'col'       => '320:1,768:2,992:3,1200:3',
            'title'     => '',
            'boxed'     => '',
            'ids'       => '',
        ), $atts, 'fivo_docs' );

        wp_enqueue_script( 'fivo-docs' );

        $atts['ids'] = array_filter( explode( ',', $atts['ids'] ) );
        $atts['categories'] = array_filter( explode( ',', $atts['cats'] ) );
        $atts['columns'] = $this->prepare_columns( $atts['col'] );

        unset( $atts['cats'] );
        unset( $atts['col'] );

        if ( $atts['ids'] ) {
            $shortcode = fivo_docs()->render->display_custom_selection( $atts );
        } else {
            $shortcode = fivo_docs()->render->display_categories( $atts );
        }

        return $shortcode;
    }

    /**
     * Prepare columns data from the shortcode.
     *
     * @since   1.2
     * @access  private
     * @param   string $col
     * @return  array $columns
     */
    private function prepare_columns( $col ) {

        $columns = array();
        $cols = explode( ',', $col );
        $breakpoints = array_flip( fivo_docs()->get_breakpoints() );

        foreach ( $cols as $col ) {
            $val = explode( ':', $col );
            $breakpoint = $val[0];
            $columns_number = $val[1];
            if ( isset($breakpoints[$breakpoint]) ) {
                $columns[$breakpoints[$breakpoint]] = $columns_number;
            }
        }

        return $columns;
    }
}

new Fivo_Docs_Shortcode();

endif;
