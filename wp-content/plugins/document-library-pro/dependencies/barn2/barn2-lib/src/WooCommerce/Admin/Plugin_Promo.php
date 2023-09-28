<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WooCommerce\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Abstract_Plugin_Promo;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Plugin_Promo as OG_Promo;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
/**
 * Provides functions to add the plugin promo to the relevant section of the WooCommerce settings page.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.3
 */
class Plugin_Promo extends Abstract_Plugin_Promo implements Registerable
{
    /**
     * @var string The WooCommerce settings tab to display the promo, e.g. products
     */
    private $tab;
    /**
     * @var string The WooCommerce settings section to display the promo, e.g. inventory
     */
    private $section;
    /**
     * Constructor.
     *
     * @param Plugin $plugin           The plugin object
     * @param string $settings_tab     The WooCommerce settings tab to display the promo, e.g. products
     * @param string $settings_section The WooCommerce settings section to display the promo, e.g. inventory
     */
    public function __construct(Plugin $plugin, $settings_tab, $settings_section = '')
    {
        parent::__construct($plugin);
        $this->tab = $settings_tab;
        $this->section = $settings_section;
    }
    public function register()
    {
        if (!Settings_Util::is_current_wc_settings_page($this->section, $this->tab)) {
            return;
        }
        foreach (['barn2_promo_start', 'barn2_promo_end'] as $promo_field) {
            if (!\has_action("woocommerce_admin_field_{$promo_field}")) {
                \add_action("woocommerce_admin_field_{$promo_field}", [$this, "{$promo_field}_field"]);
            }
        }
        \add_filter('woocommerce_get_settings_' . $this->tab, [$this, 'add_plugin_promo_field'], 11, 2);
        \add_action('admin_enqueue_scripts', [$this, 'parent::load_styles']);
    }
    public function add_plugin_promo_field($settings, $current_section)
    {
        // Bail if we're not in the correct settings section.
        if ($this->section && $current_section !== $this->section) {
            return $settings;
        }
        $promo_fields = \array_filter($settings, function ($field) {
            return isset($field['type']) && 'barn2_promo_start' === $field['type'];
        });
        // Bail if a 'barn2_promo_start' field is already present.
        if (!empty($promo_fields)) {
            return $settings;
        }
        // Bail if there's no promo content.
        if (empty(parent::get_promo_content())) {
            return $settings;
        }
        // Wrap the settings in a promo start and promo end field.
        \array_unshift($settings, ['id' => 'barn2_promo_start', 'type' => 'barn2_promo_start']);
        $settings[] = ['id' => 'barn2_promo_end', 'type' => 'barn2_promo_end'];
        return $settings;
    }
    public function barn2_promo_start_field($field)
    {
        ?>
		<div class="barn2-promo-wrap">
		<div class="barn2-promo-inner barn2-settings <?php 
        echo \esc_attr($this->plugin->get_slug() . '-settings');
        ?>">
		<?php 
    }
    public function barn2_promo_end_field($field)
    {
        $promo_sidebar = parent::get_promo_sidebar();
        if (!empty($promo_sidebar)) {
            $GLOBALS['hide_save_button'] = \true;
            ?>
			<p class="submit barn2-settings-submit">
				<button name="save" class="button-primary woocommerce-save-button" type="submit"
						value="<?php 
            esc_attr_e('Save changes', 'document-library-pro');
            ?>"><?php 
            esc_html_e('Save changes', 'document-library-pro');
            ?></button>
			</p>
			</div><!-- barn2-promo-inner -->
			<?php 
            // Promo content is sanitized via barn2_kses_post.
            // phpcs:ignore WordPress.Security.EscapeOutput
            echo $promo_sidebar;
            ?>
			</div><!-- barn2-promo-wrap -->
			<?php 
        } else {
            ?>
			</div><!-- barn2-promo-inner -->
			</div><!-- barn2-promo-wrap -->
			<?php 
        }
    }
}
