<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Interfaces\Bootable;
use JsonSerializable;
/**
 * Create a setup wizard for a given plugin.
 */
class Setup_Wizard implements Bootable, JsonSerializable
{
    /**
     * Plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * List of steps available for this wizard.
     *
     * @var array
     */
    private $steps = [];
    /**
     * Setup wizard slug.
     *
     * @var string
     */
    private $slug;
    /**
     * Holds the EDD_Licensing class.
     *
     * @var object|boolean
     */
    private $edd_api = \false;
    /**
     * Holds the Plugin_License class.
     *
     * @var object|boolean
     */
    private $plugin_license = \false;
    /**
     * Specify the hook to use to which the restart button will be attached.
     *
     * @var string
     */
    private $restart_hook;
    /**
     * Holds the custom path to the library.
     *
     * @var string
     */
    private $lib_path;
    /**
     * Holds the custom url to the library.
     *
     * @var string
     */
    private $lib_url;
    /**
     * Additional configuration parameters.
     *
     * @var array
     */
    public $js_args;
    /**
     * Holds details about the custom assets.
     *
     * @var array
     */
    public $custom_asset_url;
    /**
     * Configure a new plugin setup wizard.
     *
     * @param object $plugin instance of plugin
     * @param array $steps list of steps to add to the wizard
     */
    public function __construct($plugin, $steps = [])
    {
        $this->plugin = $plugin;
        $this->slug = $this->plugin->get_slug() . '-setup-wizard';
        if (!empty($steps)) {
            $this->add_steps($steps);
        }
    }
    /**
     * Manually override the library path.
     * Useful when developing new features for the library.
     *
     * @param string $path
     * @return self
     */
    public function set_lib_path(string $path)
    {
        $this->lib_path = $path;
        return $this;
    }
    /**
     * Manually override the library url.
     * Useful when developing new features for the library.
     *
     * @param string $url
     * @return self
     */
    public function set_lib_url(string $url)
    {
        $this->lib_url = $url;
        return $this;
    }
    /**
     * Get the slug of the wizard.
     *
     * @return void
     */
    public function get_slug()
    {
        return $this->slug;
    }
    /**
     * Configure the barn2_setup_wizard js object for the react app.
     *
     * @param array $args
     * @return Setup_Wizard
     */
    public function configure($args = [])
    {
        $defaults = ['plugin_name' => $this->plugin->get_name(), 'plugin_slug' => $this->plugin->get_slug(), 'plugin_product_id' => $this->plugin->get_id(), 'skip_url' => \admin_url(), 'license_tooltip' => '', 'utm_id' => '', 'premium_url' => '', 'completed' => $this->is_completed(), 'barn2_api' => 'https://barn2.com/wp-json/upsell/v1/settings', 'ready_links' => [], 'is_free' => empty($this->get_licensing())];
        $args = \wp_parse_args($args, $defaults);
        $this->js_args = $args;
        return $this;
    }
    /**
     * Assign a Plugin_License and an EDD_Licensing class to the setup wizard.
     *
     * @param string $plugin_license_class full class path to the barn2lib Plugin_License class.
     * @return Setup_Wizard
     */
    public function add_license_class(string $plugin_license_class)
    {
        $this->plugin_license = new $plugin_license_class($this->plugin->get_id(), $this->edd_api);
        return $this;
    }
    /**
     * Get the Plugin_License class.
     *
     * @return object
     */
    public function get_licensing()
    {
        return $this->plugin_license;
    }
    /**
     * Assign an EDD_Licensing class to the setup wizard.
     *
     * @param string $edd_licensing_class full class path to the barn2lib EDD_Licensing class.
     * @return Setup_Wizard
     */
    public function add_edd_api(string $edd_licensing_class)
    {
        $this->edd_api = $edd_licensing_class::instance();
        return $this;
    }
    /**
     * Get the EDD_Licensing class assigned to the setup wizard.
     *
     * @return object
     */
    public function get_edd_api()
    {
        return $this->edd_api;
    }
    /**
     * Specify the hook to use to which the restart button will be attached.
     *
     * @param string $hook
     * @return void
     */
    public function set_restart_hook(string $hook)
    {
        $this->restart_hook = $hook;
        return $this;
    }
    /**
     * Get the hook to use to which the restart button will be attached.
     *
     * @return string
     */
    public function get_restart_hook()
    {
        return $this->restart_hook;
    }
    /**
     * Add a step to the process.
     *
     * @param Step $step single instance of Step
     * @return Setup_Wizard
     */
    public function add(Step $step)
    {
        $step->with_plugin($this->plugin);
        $this->steps[] = $step;
        return $this;
    }
    /**
     * Add multiple steps to the process.
     *
     * @param array $steps
     * @return Setup_Wizard
     */
    public function add_steps(array $steps)
    {
        foreach ($steps as $step) {
            if (!$step instanceof Step) {
                continue;
            }
            $step->with_plugin($this->plugin);
            $this->steps[] = $step;
        }
        return $this;
    }
    /**
     * URL and path to the custom script that is loaded together with the react app.
     *
     * @param string $url
     * @param array $dependencies Use the `get_script_dependencies` method from the barn2 lib Util class to retrieve the array of dependencies.
     * @return Setup_Wizard
     */
    public function add_custom_asset(string $url, array $dependencies)
    {
        $this->custom_asset_url = ['url' => $url, 'dependencies' => $dependencies];
        return $this;
    }
    /**
     * Get the custom asset url.
     *
     * @return string
     */
    public function get_custom_asset()
    {
        return $this->custom_asset_url;
    }
    /**
     * Determine if the wizard was once completed.
     *
     * @return boolean
     */
    public function is_completed()
    {
        return (bool) \get_option("{$this->get_slug()}_completed");
    }
    /**
     * Mark the wizard as completed.
     *
     * @return void
     */
    public function set_as_completed()
    {
        \update_option("{$this->get_slug()}_completed", \true);
    }
    /**
     * Create list of configuration values of steps used by the react app.
     *
     * @return array
     */
    private function get_steps_configuration()
    {
        $config = [];
        /** @var Step $step */
        foreach ($this->steps as $step) {
            $config[] = ['key' => $step->get_id(), 'label' => $step->get_name(), 'description' => $step->get_description(), 'heading' => $step->get_title(), 'tooltip' => $step->get_tooltip(), 'hidden' => $step->is_hidden()];
        }
        return $config;
    }
    /**
     * Get all initially hidden steps.
     *
     * @return array
     */
    private function get_initially_hidden_steps()
    {
        $steps = [];
        /** @var Step $step */
        foreach ($this->steps as $step) {
            if ($step->is_hidden()) {
                $steps[] = $step->get_id();
            }
        }
        return $steps;
    }
    /**
     * Get steps of the wizard.
     *
     * @return array
     */
    public function get_steps()
    {
        return $this->steps;
    }
    /**
     * Boot the setup wizard.
     *
     * @return void
     */
    public function boot()
    {
        $rest_api = new Api($this->plugin, $this->get_steps());
        // Hook into WP.
        \add_action('admin_menu', [$this, 'register_admin_page']);
        \add_filter('admin_body_class', [$this, 'admin_page_body_class']);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'], 20);
        \add_action('admin_head', [$this, 'admin_head']);
        $rest_api->register_api_routes();
        // Attach the restart button if specified.
        if (!empty($this->get_restart_hook())) {
            \add_action($this->get_restart_hook(), [$this, 'add_restart_btn']);
        }
    }
    /**
     * Register a new page in the dashboard menu.
     *
     * @return void
     */
    public function register_admin_page()
    {
        $menu_slug = $this->get_slug();
        /* translators: %s: The name of the plugin. */
        $page_title = \sprintf(__('%s setup wizard', 'document-library-pro'), $this->plugin->get_name());
        \add_menu_page($page_title, $page_title, 'manage_options', $menu_slug, [$this, 'render_setup_wizard_page']);
    }
    /**
     * Hide the setup wizard page from the menu.
     *
     * @return void
     */
    public function admin_head()
    {
        \remove_menu_page($this->get_slug());
    }
    /**
     * Render the element to which the react app will attach itself.
     *
     * @return void
     */
    public function render_setup_wizard_page()
    {
        echo '<div id="barn2-setup-wizard"></div>';
    }
    /**
     * Setup custom classes for the body tag when viewing the setup wizard page.
     *
     * @param string $class
     * @return string
     */
    public function admin_page_body_class($class)
    {
        $screen = \get_current_screen();
        if ($screen->id !== 'toplevel_page_' . $this->get_slug()) {
            return $class;
        }
        $class .= ' barn2-setup-wizard-page woocommerce-page woocommerce_page_wc-admin woocommerce-onboarding woocommerce-profile-wizard__body woocommerce-admin-full-screen is-wp-toolbar-disabled';
        return $class;
    }
    /**
     * Get the url to the library.
     *
     * @return string
     */
    public function get_library_url()
    {
        $url = \trailingslashit(\plugin_dir_url(__DIR__));
        if (!empty($this->lib_url)) {
            return $this->lib_url;
        }
        return $url;
    }
    /**
     * Get the path to the library.
     *
     * @return string
     */
    public function get_library_path()
    {
        $path = \trailingslashit(\plugin_dir_path(__DIR__));
        if (!empty($this->lib_path)) {
            return $this->lib_path;
        }
        return $path;
    }
    /**
     * Retrieve the url to the wizard's page.
     *
     * @return string
     */
    public function get_wizard_url()
    {
        return \add_query_arg(['page' => $this->get_slug()], \admin_url('admin.php'));
    }
    /**
     * Returns the html for the restart link.
     *
     * @return string
     */
    public function load_wizard_restart_assets()
    {
        \ob_start();
        ?>
		<style>
			.barn2-wiz-restart-btn {
				margin-bottom: 1rem !important;
			}
		</style>

		<script>
			jQuery( '.barn2-wiz-restart-btn' ).on( 'click', function( e ) {
				/* translators: %s: The name of the plugin. */
				return confirm( '<?php 
        echo \esc_html(\sprintf(__('Warning: This will overwrite your existing settings for %s. Are you sure you want to continue?', 'document-library-pro'), $this->plugin->get_name()));
        ?>' );
			});
		</script>
		<?php 
        return \ob_get_clean();
    }
    /**
     * Enqueue required assets.
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_assets($hook)
    {
        if ($hook !== 'toplevel_page_' . $this->get_slug()) {
            return;
        }
        $slug = $this->get_slug();
        $script_path = 'build/setup-wizard.js';
        $script_asset_path = $this->get_library_path() . 'build/setup-wizard.asset.php';
        $script_asset = \file_exists($script_asset_path) ? require $script_asset_path : ['dependencies' => [], 'version' => \filemtime($script_path)];
        $script_url = $this->get_library_url() . $script_path;
        // Register main styling of the wizard.
        \wp_register_style($slug, $this->get_library_url() . 'build/setup-wizard.css', ['wp-components'], \filemtime($this->get_library_path() . '/build/setup-wizard.css'));
        // Register main script of the wizard.
        \wp_register_script($slug, $script_url, $script_asset['dependencies'], $script_asset['version'], \true);
        // Enqueue main script of the wizard.
        \wp_enqueue_script($slug);
        \wp_enqueue_style($slug);
        $custom_asset = $this->get_custom_asset();
        if (isset($custom_asset['url'])) {
            $deps = isset($custom_asset['dependencies']['dependencies']) ? $custom_asset['dependencies']['dependencies'] : [];
            $version = isset($custom_asset['dependencies']['version']) ? $custom_asset['dependencies']['version'] : $script_asset['version'];
            \wp_enqueue_script($slug . '-custom-asset', $custom_asset['url'], $deps, $version, \true);
        }
        \wp_add_inline_script($slug, 'const barn2_setup_wizard = ' . \wp_json_encode($this), 'before');
    }
    /**
     * Attach the restart wizard button.
     *
     * @return void
     */
    public function add_restart_btn()
    {
        $url = \add_query_arg(['page' => $this->get_slug()], \admin_url('admin.php'));
        ?>
		<div class="barn2-setup-wizard-restart">
			<hr>
			<h3><?php 
        esc_html_e('Setup wizard', 'document-library-pro');
        ?></h3>
			<p><?php 
        esc_html_e('If you need to access the setup wizard again, please click on the button below.', 'document-library-pro');
        ?></p>
			<a href="<?php 
        echo \esc_url($url);
        ?>" class="button barn2-wiz-restart-btn"><?php 
        esc_html_e('Setup wizard', 'document-library-pro');
        ?></a>
			<hr>
		</div>

		<style>
			.barn2-wiz-restart-btn {
				margin-bottom: 1rem !important;
			}
		</style>

		<script>
			jQuery( '.barn2-wiz-restart-btn' ).on( 'click', function( e ) {
				/* translators: %s: The name of the plugin. */
				return confirm( '<?php 
        echo \esc_html(\sprintf(__('Warning: This will overwrite your existing settings for %s. Are you sure you want to continue?', 'document-library-pro'), $this->plugin->get_name()));
        ?>' );
			});
		</script>
		<?php 
    }
    /**
     * Determine if we're on the wc settings screen of a plugin.
     *
     * @param string $wc_section_id
     * @return boolean
     */
    public function is_wc_settings_screen(string $wc_section_id)
    {
        $screen = \get_current_screen();
        if ($screen->id === 'woocommerce_page_wc-settings' && isset($_GET['tab']) && ($_GET['tab'] === $wc_section_id || 'products' === $_GET['tab'] && isset($_GET['section']) && $_GET['section'] === $wc_section_id)) {
            return \true;
        }
        return \false;
    }
    /**
     * Add a restart link next to the settings page docs and support link.
     *
     * @param string $wc_section_id
     * @param string $title_option_id
     * @return void
     */
    public function add_restart_link(string $wc_section_id, string $title_option_id)
    {
        \add_filter("woocommerce_get_settings_{$wc_section_id}", function ($settings) use($title_option_id) {
            $url = \add_query_arg(['page' => $this->get_slug()], \admin_url('admin.php'));
            $title_setting = \wp_list_filter($settings, ['id' => $title_option_id]);
            if ($title_setting && isset($title_setting[\key($title_setting)]['desc'])) {
                $desc = $title_setting[\key($title_setting)]['desc'];
                $p_closing_tag = \strrpos($desc, '</p>');
                $new_desc = \substr_replace($desc, ' | <a class="barn2-wiz-restart-btn" href="' . \esc_url($url) . '">' . esc_html__('Setup wizard', 'document-library-pro') . '</a>', $p_closing_tag, 0);
                $settings[\key($title_setting)]['desc'] = $new_desc;
            }
            return $settings;
        });
        \add_action('admin_footer', function () use($wc_section_id) {
            if ($this->is_wc_settings_screen($wc_section_id)) {
                echo $this->load_wizard_restart_assets();
            }
        });
    }
    /**
     * Json configuration for the react app.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return \array_merge(['restNonce' => \wp_create_nonce('wp_rest'), 'apiURL' => \get_rest_url(null, \trailingslashit(Api::API_NAMESPACE . '/' . $this->plugin->get_slug())), 'steps' => $this->get_steps_configuration(), 'hiddenSteps' => $this->get_initially_hidden_steps()], $this->js_args);
    }
}
