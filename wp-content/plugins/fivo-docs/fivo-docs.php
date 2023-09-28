<?php
/**
 * Plugin Name: Fivo Docs
 * Plugin URI:  http://plugins.apalodi.com/fivo-docs/
 * Description: Advanced document managment plugin that helps you showcase your documents beatifully.
 * Tags:        attachment, documents
 * Version:     1.2.2
 *
 * Author:      APALODI
 * Author URI:  http://apalodi.com
 *
 * Text Domain: fivo-docs
 * Domain Path: /languages
 *
 * License:     CodeCanyon Licence
 * License URI: https://codecanyon.net/licenses
 *
 * @package     Fivo_Docs
 * @version     1.2.2
 * @author      apalodi
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Fivo_Docs' ) ) :

/**
 * Main Fivo_Docs Class.
 *
 * @since   1.0
 */
final class Fivo_Docs {

    /**
     * @var     string
     * @access  public
     */
    public $version = '1.2.2';

    /**
     * The single instance of the class.
     *
     * @var     object
     * @since   1.0
     * @access  protected
     */
    protected static $_instance = null;

    /**
	 * Render instance.
	 *
	 * @var Fivo_Docs_Render
	 */
	public $render = null;

    /**
     * A dummy magic method to prevent Fivo Docs from being cloned.
     *
     * @since   1.0
     * @access  public
     */
    public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'fivo-docs' ), '1.0' ); }

    /**
     * A dummy magic method to prevent Fivo Docs from being unserialized.
     *
     * @since   1.0
     * @access  public
     */
    public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'fivo-docs' ), '1.0' ); }

    /**
     * Main Fivo Docs Instance.
     *
     * Insures that only one instance of Fivo Docs exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since   1.0
     * @access  public
     * @see     fivo_docs()
     * @return  object $instance The one true Fivo Docs
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new Fivo_Docs();
        }
        return self::$_instance;
    }

    /**
     * Fivo Docs Constructor.
     *
     * @since   1.0
     * @access  public
     */
    public function __construct() {
        $this->setup_globals();
        $this->setup_plugin();
        $this->includes();
    }

    /**
     * Setup the plugin globals, hooks, actions and include plugin files.
     *
     * @since   1.0
     * @access  private
     */
    private function setup_plugin() {

        do_action( 'fivo_docs_before_init' );

        add_action( 'init', array( $this, 'setup_globals'   ), 0 ); // necessary for apply_filters to work
        add_action( 'init', array( $this, 'load_textdomain' ), 0 ); // themes have time to filter textdomain
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        add_action( 'get_the_generator_html', array( $this, 'generator_tag' ), 10, 2 );
        add_action( 'get_the_generator_xhtml', array( $this, 'generator_tag' ), 10, 2 );

        // Loaded action
        do_action( 'fivo_docs_loaded' );

    }

    /**
     * Set some smart defaults to class variables. Allow some of them to be
     * filtered to allow for early overriding.
     *
     * @since   1.0
     * @access  private
     */
    public function setup_globals() {

        $this->debug                = apply_filters( 'fivo_docs_debug', false );

        $this->category_taxonomy    = apply_filters( 'fivo_docs_category_taxonomy', 'fivo_docs_category' );
        $this->mime_types           = apply_filters( 'fivo_docs_mime_types', array( 'application', 'text' ) );

        // Setup some base path and URL information
        $this->basename             = plugin_basename( __FILE__ );
        $this->plugin_dir_path      = plugin_dir_path( __FILE__ );
        $this->plugin_dir_url       = plugin_dir_url ( __FILE__ );

        // Languages
        $this->lang_dir_rel_path    = trailingslashit( dirname( $this->basename ). '/languages' );

        // Admin
        $this->admin_dir_path       = trailingslashit( $this->plugin_dir_path . 'includes/admin' );
        $this->admin_dir_url        = trailingslashit( $this->plugin_dir_url . 'includes/admin' );
    }

    /**
     * Include required files.
     *
     * @since   1.0
     * @access  private
     */
    private function includes() {

        if ( is_admin() ) {
            include_once( 'includes/admin/class-fivo-docs-admin.php' );  // Main Admin Class
        }

        include_once( 'includes/class-fivo-docs-render.php' );
        include_once( 'includes/class-fivo-docs-block.php' );
        include_once( 'includes/class-fivo-docs-shortcode.php' );

        $this->render = new Fivo_Docs_Render();
    }

    /**
     * Add category taxonomy to the attachment post type.
     *
     * @since   1.0
     * @access  public
     */
    public function register_taxonomies() {

        $category_taxonomy_args = apply_filters( 'fivo_docs_category_taxonomy_args',
            array(
                'public'                => false,
                'hierarchical'          => true,
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'show_admin_column'     => true,
                'rewrite'               => false,
                'show_in_rest'          => true,
                'update_count_callback' => '_update_generic_term_count',
                'capabilities'          => array(
                    'manage_terms'          => 'upload_files',
                    'edit_terms'            => 'upload_files',
                    'delete_terms'          => 'upload_files',
                    'assign_terms'          => 'upload_files',
                ),
            )
        );

        register_taxonomy( $this->category_taxonomy, 'attachment', $category_taxonomy_args );

        add_post_type_support( 'attachment', 'page-attributes' );
    }

    /**
     * Load the translation file for current language. Checks the default WordPress
     * languages folder first, and then the languages folder inside the plugin folder.
     * This way when user creates translations outside the plugin folder those
     * translations will be used as default.
     *
     * Note that custom translation files inside the plugin folder
     * will be removed on updates. If you're creating custom
     * translation files, please use the global language folder.
     *
     * @since   1.0
     * @access  public
     */
    public function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'fivo-docs' );

        // 1. Look in global /wp-content/languages/fivo-docs/ folder
        load_textdomain( 'fivo-docs', WP_LANG_DIR . '/fivo-docs/fivo-docs-' . $locale . '.mo' );

        /*
         * 2. Look in local /wp-content/plugins/fivo-docs/languages/ folder
         * 3. If 2. is empty look in global /wp-content/languages/plugins/
         */
        load_plugin_textdomain( 'fivo-docs', false, $this->lang_dir_rel_path );
    }

    /**
     * Output generator tag to aid debugging.
     *
     * @since   1.1.0
     * @access  public
     * @param   string $gen The HTML markup output to wp_head().
     * @param   string $type The type of generator. Accepts 'html', 'xhtml', 'atom', 'rss2', 'rdf', 'comment', 'export'.
     * @return  string $gen
     */
    public function generator_tag( $gen, $type ) {
        switch ( $type ) {
            case 'html':
                $gen .= "\n" . '<meta name="generator" content="Fivo Docs ' . esc_attr( self::$_instance->version ) . '">';
                break;
            case 'xhtml':
                $gen .= "\n" . '<meta name="generator" content="Fivo Docs ' . esc_attr( self::$_instance->version ) . '" />';
                break;
        }
        return $gen;
    }

    /**
     * Get responsive breakpoints.
     *
     * @since   1.2.0
     * @access  public
     * @return  array $breakpoints
     */
    public function get_breakpoints() {
        return apply_filters( 'fivo_docs_breakpoints', array(
            'xs' => 320,
            'sm' => 576,
            'md' => 768,
            'lg' => 992,
            'xl' => 1200,
        ) );
    }
}

/**
 * The main function responsible for returning the one true Fivo Docs Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $docs = fivo_docs(); ?>
 *
 * @since   1.0
 * @return  object The one true Fivo_Docs Instance
 */
function fivo_docs() {
    return Fivo_Docs::instance();
}

/**
 * Hook Fivo Docs early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Fivo Docs, to get their
 * actions, filters, and overrides setup without Fivo Docs being in the way.
 */
if ( defined( 'FIVO_DOCS_LATE_LOAD' ) ) {
    add_action( 'plugins_loaded', 'fivo_docs', (int) FIVO_DOCS_LATE_LOAD );
} else {
    fivo_docs();
}

endif;
