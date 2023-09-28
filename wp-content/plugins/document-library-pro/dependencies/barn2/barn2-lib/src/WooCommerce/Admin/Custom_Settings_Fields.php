<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\WooCommerce\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin\Settings_Util;
use WC_Admin_Settings;
/**
 * Additional field types for WooCommerce settings pages.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.5
 */
class Custom_Settings_Fields implements Registerable
{
    const ALL_FIELDS = ['hidden', 'color_picker', 'color_size', 'help_note', 'multi_text', 'settings_start', 'settings_end', 'checkbox_tooltip', 'image_size', 'radio_input'];
    /**
     * The plugin object.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * Constructor.
     *
     * @param Plugin $plugin The plugin object.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        foreach (self::ALL_FIELDS as $field) {
            if (!\has_action("woocommerce_admin_field_{$field}") && \method_exists($this, "{$field}_field")) {
                \add_action("woocommerce_admin_field_{$field}", [$this, "{$field}_field"]);
            }
            if ($field === 'checkbox_tooltip') {
                \add_filter('woocommerce_admin_settings_sanitize_option', [$this, 'sanitize_checkbox_tooltip_field'], 10, 3);
            }
        }
        \add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }
    public function hidden_field($value)
    {
        // id and default are required.
        if (empty($value['id']) || !isset($value['default'])) {
            return;
        }
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
		<input
				type="hidden"
				id="<?php 
        echo \esc_attr($value['id']);
        ?>"
				name="<?php 
        echo \esc_attr($value['id']);
        ?>"
				value="<?php 
        echo \esc_attr($value['default']);
        ?>"
			<?php 
        echo $custom_attributes;
        // atts escaped
        ?> />
		<?php 
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function color_picker_field($value)
    {
        $this->load_scripts('color_picker');
        $field_description = WC_Admin_Settings::get_field_description($value);
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
		<tr>
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($value['id']);
        ?>"><?php 
        echo \esc_html($value['title']);
        ?></label>
				<?php 
        echo $field_description['tooltip_html'];
        // escaped
        ?>
			</th>
			<td class="forminp forminp-<?php 
        echo \esc_attr(\sanitize_title($value['type']));
        ?> color-picker-field">
				<input
						type="text"
						name="<?php 
        echo \esc_attr($value['id']);
        ?>"
						id="<?php 
        echo \esc_attr($value['id']);
        ?>"
						dir="ltr"
						value="<?php 
        echo \esc_attr($value['value']);
        ?>"
						class="color-picker <?php 
        echo \esc_attr($value['class']);
        ?>"
					<?php 
        echo $custom_attributes;
        // escaped
        ?> />
				<?php 
        echo $field_description['description'];
        // escaped
        ?>
			</td>
		</tr>
		<?php 
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function color_size_field($value)
    {
        $this->load_scripts('color_size');
        $option_value = \array_merge(\array_fill_keys(['color', 'size'], ''), (array) $value['value']);
        if (empty($value['custom_attributes'])) {
            $value['custom_attributes'] = [];
        }
        $value['custom_attributes'] = \array_merge(['min' => 0], $value['custom_attributes']);
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        $field_description = WC_Admin_Settings::get_field_description($value);
        $size_placeholder = !empty($value['placeholder']) ? $value['placeholder'] : __('Size', 'document-library-pro');
        $size_min = isset($value['min']) ? (int) $value['min'] : 0;
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
		<tr>
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($value['id'] . '[color]');
        ?>"><?php 
        echo \esc_html($value['title']);
        ?></label>
				<?php 
        echo $field_description['tooltip_html'];
        ?>
			</th>
			<td class="forminp forminp-<?php 
        echo \esc_attr(\sanitize_title($value['type']));
        ?> color-size-field">
				<input
						type="text"
						name="<?php 
        echo \esc_attr($value['id'] . '[color]');
        ?>"
						id="<?php 
        echo \esc_attr($value['id'] . '[color]');
        ?>"
						dir="ltr"
						value="<?php 
        echo \esc_attr($option_value['color']);
        ?>"
						class="color-picker <?php 
        echo \esc_attr($value['class']);
        ?>" />
				<input
						type="number"
						name="<?php 
        echo \esc_attr($value['id'] . '[size]');
        ?>"
						id="<?php 
        echo \esc_attr($value['id'] . '[size]');
        ?>"
						value="<?php 
        echo \esc_attr($option_value['size']);
        ?>"
						class="size-input"
						min="<?php 
        echo \esc_attr($size_min);
        ?>"
						placeholder="<?php 
        echo \esc_attr($size_placeholder);
        ?>"
					<?php 
        echo $custom_attributes;
        // escaped
        ?> />
				<?php 
        echo $field_description['description'];
        // escaped
        ?>
			</td>
		</tr>
		<?php 
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function help_note_field($value)
    {
        $field_description = WC_Admin_Settings::get_field_description($value);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
		<tr>
			<th scope="row" class="titledesc <?php 
        echo \esc_attr($value['class']);
        ?>" style="padding:0;">
				<?php 
        echo \esc_html($value['title']);
        ?>
				<?php 
        echo $field_description['tooltip_html'];
        // escaped
        ?>
			</th>
			<td class="forminp forminp-<?php 
        echo \esc_attr(\sanitize_title($value['type']));
        ?>" style="padding-top:0;padding-bottom:5px;">
				<?php 
        echo $field_description['description'];
        // escaped
        ?>
			</td>
		</tr>
		<?php 
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function multi_text_field($value)
    {
        // Get current values
        $option_values = (array) \get_option($value['id'], $value['default']);
        if (empty($option_values)) {
            $option_values = [''];
        }
        $field_description = WC_Admin_Settings::get_field_description($value);
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // atts are escaped
        ?>
		<tr>
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($value['id']);
        ?>"><?php 
        echo \esc_html($value['title']);
        ?></label>
				<?php 
        echo $field_description['tooltip_html'];
        ?>
			</th>
			<td class="forminp forminp-<?php 
        echo \esc_attr(\sanitize_title($value['type']));
        ?>">
				<div class="multi-field-container">
					<?php 
        foreach ($option_values as $i => $option_value) {
            ?>
						<?php 
            $first_field = $i === 0;
            ?>
						<div class="multi-field-input">
							<input
									type="text"
									name="<?php 
            echo \esc_attr($value['id']);
            ?>[]"
								<?php 
            if ($first_field) {
                echo 'id="' . \esc_attr($value['id']) . '"';
                echo ' ' . $custom_attributes;
            }
            ?>
									value="<?php 
            echo \esc_attr($option_value);
            ?>"
									class="<?php 
            echo \esc_attr($value['class']);
            ?>"
									placeholder="<?php 
            echo \esc_attr($value['placeholder']);
            ?>"
							/>
							<span class="multi-field-actions">
								<a class="multi-field-add" data-action="add" href="#"><span class="dashicons dashicons-plus"></span></a>
								<?php 
            if ($i > 0) {
                ?>
									<a class="multi-field-remove" data-action="remove" href="#"><span class="dashicons dashicons-minus"></span></a>
								<?php 
            }
            ?>
							</span>
							<?php 
            if ($first_field) {
                echo $field_description['description'];
            }
            ?>
						</div>
					<?php 
        }
        ?>
				</div>
			</td>
		</tr>
		<?php 
    }
    public function settings_start_field($value)
    {
        $id = !empty($value['id']) ? \sprintf(' id="%s"', \esc_attr($value['id'])) : '';
        $class = !empty($value['class']) ? \sprintf(' class="%s"', \esc_attr($value['class'])) : '';
        echo "<div{$id}{$class}><div class='barn2-settings-inner'>";
    }
    public function settings_end_field($value)
    {
        // closes the 'barn2-settings-inner' div element if the settings have no promo
        $close_inner = isset($value['has_promo']) && $value['has_promo'] ? '' : '</div>';
        echo "{$close_inner}</div>";
    }
    public function checkbox_tooltip_field($value)
    {
        $option_value = $value['value'];
        $description = \wp_kses_post($value['desc']);
        $tooltip_html = !empty($value['desc_tip']) ? \wc_help_tip($value['desc_tip']) : '';
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // atts are escaped
        ?>
		<?php 
        if (!isset($value['checkboxgroup']) || 'start' === $value['checkboxgroup']) {
            ?>
			<tr valign="top">
			<th scope="row" class="titledesc">
				<?php 
            echo \esc_html($value['title']);
            ?>
			</th>
			<td class="forminp forminp-checkbox">
		<?php 
        }
        ?>
		<fieldset>
			<?php 
        if (!empty($value['title'])) {
            ?>
				<legend class="screen-reader-text"><span><?php 
            echo \esc_html($value['title']);
            ?></span></legend>
			<?php 
        }
        ?>
			<label for="<?php 
        echo \esc_attr($value['id']);
        ?>">
				<input
						name="<?php 
        echo \esc_attr($value['id']);
        ?>"
						id="<?php 
        echo \esc_attr($value['id']);
        ?>"
						type="checkbox"
						class="<?php 
        echo \esc_attr(isset($value['class']) ? $value['class'] : '');
        ?>"
						value="1"
					<?php 
        \checked($option_value, 'yes');
        ?>
					<?php 
        echo $custom_attributes;
        ?>
				/> <?php 
        echo $description;
        ?>
			</label> <?php 
        echo $tooltip_html;
        ?>
		</fieldset>
		<?php 
        if (!isset($value['checkboxgroup']) || 'end' === $value['checkboxgroup']) {
            ?>
			</td>
			</tr>
		<?php 
        }
        ?>
		<?php 
    }
    public function sanitize_checkbox_tooltip_field($value, $option, $raw_value)
    {
        if ('checkbox_tooltip' !== $option['type']) {
            return $value;
        }
        return '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
    }
    public function image_size_field($value)
    {
        $this->load_scripts('image_size');
        $current_value = $value['value'];
        $empty_size = ['width' => '', 'height' => ''];
        if (\is_scalar($current_value)) {
            $current_value = ['width' => $current_value, 'height' => ''];
        } elseif (!\is_array($current_value)) {
            $current_value = $empty_size;
        }
        if (empty($value['css'])) {
            $value['css'] = 'width:70px';
        }
        if (empty($value['custom_attributes']['min'])) {
            $value['custom_attributes']['min'] = 1;
        }
        if (empty($value['custom_attributes']['step'])) {
            $value['custom_attributes']['step'] = 1;
        }
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // atts are escaped
        $separator = isset($value['separator']) ? $value['separator'] : '&times;';
        $description = WC_Admin_Settings::get_field_description($value);
        $current_value = \array_intersect_key(\array_map('absint', $current_value), $empty_size);
        $suffix_html = !empty($value['suffix']) ? \sprintf('<span class="suffix">%s</span>', \esc_html($value['suffix'])) : '';
        ?>
		<tr>
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($value['id']);
        ?>"><?php 
        echo \esc_html($value['title']);
        echo $description['tooltip_html'];
        ?></label>
			</th>
			<td class="forminp forminp-<?php 
        echo \esc_attr(\sanitize_title($value['type']));
        ?> image-size-field">
				<input
						name="<?php 
        echo \esc_attr($value['id']);
        ?>[width]"
						type="number"
						style="<?php 
        echo \esc_attr($value['css']);
        ?>"
						value="<?php 
        echo \esc_attr($current_value['width']);
        ?>"
						class="<?php 
        echo \esc_attr(\trim($value['class'] . ' image-width'));
        ?>"
						placeholder="<?php 
        echo \esc_attr($value['placeholder']);
        ?>"
					<?php 
        echo $custom_attributes;
        // already escaped
        ?>
				/>
				<span class="separator"><?php 
        echo $separator;
        ?></span>
				<input
						name="<?php 
        echo \esc_attr($value['id']);
        ?>[height]"
						type="number"
						style="<?php 
        echo \esc_attr($value['css']);
        ?>"
						value="<?php 
        echo \esc_attr($current_value['height']);
        ?>"
						class="<?php 
        echo \esc_attr(\trim($value['class'] . ' image-height'));
        ?>"
					<?php 
        echo $custom_attributes;
        // already escaped
        ?>
				/><?php 
        echo $suffix_html;
        echo $description['description'];
        ?>
			</td>
		</tr>
		<?php 
    }
    /**
     * Radio input field.
     *
     * @param array $value Field parameters.
     * @return void
     */
    public function radio_input_field($value)
    {
        $option_value = $value['value'];
        $custom_attributes = Settings_Util::get_custom_attributes($value);
        // atts are escaped
        $description = WC_Admin_Settings::get_field_description($value);
        ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($value['id']);
        ?>"><?php 
        echo \esc_html($value['title']);
        ?></label>
				<?php 
        echo $description['tooltip_html'];
        ?>
			</th>
			<td class="forminp forminp-radio">
				<fieldset>
					<?php 
        if (!empty($value['title'])) {
            ?>
						<legend class="screen-reader-text"><span><?php 
            echo \esc_html($value['title']);
            ?></span></legend>
					<?php 
        }
        ?>
					<?php 
        foreach ($value['options'] as $key => $val) {
            ?>
						<label for="<?php 
            echo \esc_attr($value['id'] . '_' . $key);
            ?>">
							<input
									name="<?php 
            echo \esc_attr($value['id']);
            ?>"
									id="<?php 
            echo \esc_attr($value['id'] . '_' . $key);
            ?>"
									type="radio"
									class="<?php 
            echo \esc_attr(isset($value['class']) ? $value['class'] : '');
            ?>"
									value="<?php 
            echo \esc_attr($key);
            ?>"
								<?php 
            \checked($option_value, $key);
            ?>
								<?php 
            echo $custom_attributes;
            ?>
							/> <?php 
            echo $val;
            ?>
						</label><br/>
					<?php 
        }
        ?>
					<?php 
        echo $description['description'];
        // escaped
        ?>
				</fieldset>
			</td>
		</tr>
		<?php 
    }
    public function register_scripts()
    {
        \wp_register_style('barn2-wc-settings', $this->plugin->get_dir_url() . 'dependencies/barn2/barn2-lib/build/css/wc-settings-styles.css', [], $this->plugin->get_version());
        \wp_register_script('barn2-wc-settings', $this->plugin->get_dir_url() . 'dependencies/barn2/barn2-lib/build/js/wc-settings.js', ['jquery'], $this->plugin->get_version());
    }
    public function load_scripts($field)
    {
        if (\in_array($field, ['image_size', 'color_size', 'color_picker'], \true)) {
            \wp_enqueue_style('barn2-wc-settings');
        }
        if (\in_array($field, ['color_size', 'color_picker'], \true)) {
            \wp_enqueue_style('wp-color-picker');
            \wp_enqueue_script('wp-color-picker');
            \wp_enqueue_script('barn2-wc-settings');
        }
    }
    /**
     * Handle back-compat with the old static methods.
     */
    public static function __callStatic($name, $args)
    {
        if (\method_exists(self::class, $name)) {
            \_deprecated_function(__METHOD__, '1.3', \esc_html("\$this->{$name}() instance method"));
            $settings = new self();
            return \call_user_func_array([$settings, $name], $args);
        }
        return null;
    }
}
