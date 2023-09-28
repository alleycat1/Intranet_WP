<?php

namespace Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
/**
 * Helper functions for the WordPress Settings API.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.5
 */
class Settings_API_Helper implements Registerable, Conditional
{
    /**
     * Responsible for registering any additional settings scripts.
     *
     * @var Settings_Scripts
     */
    private $scripts;
    /**
     * Constructor.
     *
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->scripts = new Settings_Scripts($plugin);
    }
    /**
     * {@inheritdoc}
     */
    public function is_required()
    {
        return Util::is_admin();
    }
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->scripts->register();
    }
    /**
     * Adds a setting section.
     *
     * @param string $section
     * @param string $page
     * @param string $title
     * @param mixed $description_callback
     * @param bool $settings
     */
    public static function add_settings_section($section, $page, $title, $description_callback, $settings = \false)
    {
        if (!\is_callable($description_callback)) {
            $description_callback = '__return_false';
        }
        \add_settings_section($section, $title, $description_callback, $page);
        self::add_settings_fields($settings, $section, $page);
    }
    /**
     * Adds the setting fields to a section.
     *
     * @param mixed $settings
     * @param mixed $section
     * @param mixed $page
     */
    public static function add_settings_fields($settings, $section, $page)
    {
        if (!$settings || !\is_array($settings)) {
            return;
        }
        foreach ($settings as $setting) {
            if (!\is_array($setting) || empty($setting['id'])) {
                continue;
            }
            $args = \wp_parse_args($setting, \array_fill_keys(['id', 'type', 'desc', 'label', 'title', 'class', 'field_class', 'default', 'suffix', 'custom_attributes'], ''));
            $args['input_class'] = $args['class'];
            unset($args['class']);
            $args['class'] = $args['field_class'];
            $args['label_for'] = $args['id'];
            $setting_callback = [__CLASS__, 'settings_field_' . $args['type']];
            if (\is_callable($setting_callback)) {
                \add_settings_field($args['id'], $args['title'], $setting_callback, $page, $section, $args);
            }
        }
    }
    /**
     * Retrieve the value for an option.
     *
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    private static function get_value($option, $default = \false)
    {
        $value = '';
        $matches = [];
        $subkey_match = \preg_match('/(\\w+)\\[(\\w+)\\]/U', $option, $matches);
        if ($subkey_match && isset($matches[1], $matches[2])) {
            $subkey = $matches[2];
            $parent_option = \get_option($matches[1], []);
            $value = isset($parent_option[$subkey]) ? $parent_option[$subkey] : $default;
        } else {
            $value = \get_option($option, $default);
        }
        return $value;
    }
    /**
     * Output custom attributes.
     *
     * @param array $args
     */
    private static function custom_attributes($args)
    {
        /* Note: This is escaped in get_custom_attributes */
        echo self::get_custom_attributes($args);
    }
    /**
     * Retrieve custom attributes.
     *
     * @param array $args
     * @return string
     */
    private static function get_custom_attributes($args)
    {
        if (empty($args['custom_attributes'])) {
            return '';
        }
        $custom_atts = $args['custom_attributes'];
        $result = '';
        foreach ($custom_atts as $att => $value) {
            $result .= \sprintf(' %s="%s"', \sanitize_key($att), \esc_attr($value));
        }
        return $result;
    }
    /**
     * Output field description.
     *
     * @param array $args
     */
    private static function field_description($args)
    {
        if (!empty($args['desc'])) {
            $allowed_html = ['a' => ['class' => [], 'style' => [], 'target' => [], 'href' => []], 'span' => ['class' => [], 'style' => []], 'button' => ['class' => [], 'style' => [], 'type' => [], 'name' => [], 'value' => []], 'br' => [], 'strong' => [], 'img' => ['src' => [], 'alt' => [], 'width' => [], 'height' => [], 'class' => []]];
            echo '<p class="description">' . \wp_kses($args['desc'], $allowed_html) . '</p>';
        }
    }
    /**
     * Tooltip field.
     *
     * @param array $args
     */
    private static function field_tooltip($args)
    {
        if (!empty($args['desc_tip'])) {
            if (!\wp_script_is('barn2-tiptip')) {
                \wp_enqueue_script('barn2-tiptip');
                \wp_add_inline_script('barn2-tiptip', 'jQuery( function() { jQuery( \'.barn2-help-tip\' ).tipTip( { "attribute": "data-tip" } ); } );');
                \wp_enqueue_style('barn2-tooltip');
            }
            $allowed_html = ['br' => [], 'em' => [], 'strong' => [], 'small' => [], 'span' => [], 'ul' => [], 'li' => [], 'ol' => [], 'p' => [], 'a' => []];
            echo '<span class="barn2-help-tip" data-tip="' . \wp_kses($args['desc_tip'], $allowed_html) . '"></span>';
        }
    }
    /**
     * Number field.
     *
     * @param array $args
     */
    public static function settings_field_number($args)
    {
        $args['input_class'] = !empty($args['input_class']) ? $args['input_class'] : 'small-text';
        $args['type'] = 'number';
        self::settings_field_text($args);
    }
    /**
     * Text field.
     *
     * @param array $args
     */
    public static function settings_field_text($args)
    {
        $class = !empty($args['input_class']) ? $args['input_class'] : 'regular-text';
        $type = !empty($args['type']) ? $args['type'] : 'text';
        ?>
		<input
			id="<?php 
        echo \esc_attr($args['id']);
        ?>"
			name="<?php 
        echo \esc_attr($args['id']);
        ?>"
			class="<?php 
        echo \esc_attr($class);
        ?>"
			type="<?php 
        echo \esc_attr($type);
        ?>"
			value="<?php 
        echo \esc_attr(self::get_value($args['id'], $args['default']));
        ?>"<?php 
        self::custom_attributes($args);
        ?>
		/>
		<?php 
        if (!empty($args['suffix'])) {
            echo ' ' . \esc_html($args['suffix']) . ' ';
        }
        self::field_tooltip($args);
        self::field_description($args);
    }
    /**
     * Textarea field.
     *
     * @param array $args
     */
    public static function settings_field_textarea($args)
    {
        $class = !empty($args['input_class']) ? $args['input_class'] : 'large-text';
        $rows = isset($args['rows']) ? \absint($args['rows']) : 4;
        ?>
		<textarea
			id="<?php 
        echo \esc_attr($args['id']);
        ?>"
			name="<?php 
        echo \esc_attr($args['id']);
        ?>"
			class="<?php 
        echo \esc_attr($class);
        ?>"
			rows="<?php 
        echo \esc_attr($rows);
        ?>"
			<?php 
        self::custom_attributes($args);
        ?>
		><?php 
        echo \esc_textarea(self::get_value($args['id'], $args['default']));
        ?></textarea>
		<?php 
        self::field_tooltip($args);
        self::field_description($args);
    }
    /**
     * Select field.
     *
     * @param array $args
     */
    public static function settings_field_select($args)
    {
        $current_value = self::get_value($args['id'], $args['default']);
        ?>
		<select id="<?php 
        echo \esc_attr($args['id']);
        ?>" name="<?php 
        echo \esc_attr($args['id']);
        ?>"
				class="<?php 
        echo \esc_attr($args['input_class']);
        ?>"<?php 
        self::custom_attributes($args);
        ?>>
			<?php 
        foreach ($args['options'] as $value => $option) {
            ?>
				<option value="<?php 
            echo \esc_attr($value);
            ?>"<?php 
            \selected($value, $current_value);
            ?>><?php 
            echo \esc_html($option);
            ?></option>
			<?php 
        }
        ?>
		</select>
		<?php 
        self::field_tooltip($args);
        if (!empty($args['suffix'])) {
            echo ' ' . \esc_html($args['suffix']);
        }
        self::field_description($args);
    }
    /**
     * Checkbox field.
     *
     * @param array $args
     */
    public static function settings_field_checkbox($args)
    {
        $current_value = self::get_value($args['id'], $args['default']);
        ?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php 
        echo \esc_html($args['title']);
        ?></span></legend>
			<label for="<?php 
        echo \esc_attr($args['id']);
        ?>">
				<input
					type="checkbox"
					id="<?php 
        echo \esc_attr($args['id']);
        ?>"
					name="<?php 
        echo \esc_attr($args['id']);
        ?>"
					class="<?php 
        echo \esc_attr($args['input_class']);
        ?>"
					value="1"
					<?php 
        \checked($current_value);
        ?>
					<?php 
        self::custom_attributes($args);
        ?> />
				<?php 
        echo \esc_html($args['label']);
        ?>
			</label>
			<?php 
        self::field_tooltip($args);
        self::field_description($args);
        ?>
		</fieldset>
		<?php 
    }
    /**
     * Radio field.
     *
     * @param array $args
     */
    public static function settings_field_radio($args)
    {
        $current_value = self::get_value($args['id'], $args['default']);
        ?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php 
        echo \esc_html($args['title']);
        ?></span></legend>
			<?php 
        foreach ($args['options'] as $value => $label) {
            ?>
				<label>
					<input
						type="radio"
						id="<?php 
            echo \esc_attr($args['id']);
            ?>"
						name="<?php 
            echo \esc_attr($args['id']);
            ?>"
						class="<?php 
            echo \esc_attr($args['input_class']);
            ?>"
						<?php 
            \checked($value, $current_value);
            ?>
						value="<?php 
            echo \esc_attr($value);
            ?>"
						<?php 
            self::custom_attributes($args);
            ?>
					/>
					<?php 
            echo \esc_html($label);
            ?>
				</label><br/>
			<?php 
        }
        ?>
			<?php 
        self::field_description($args);
        ?>
		</fieldset>
		<?php 
        self::field_tooltip($args);
    }
    /**
     * Multicheckbox field.
     *
     * @param array $args
     */
    public static function settings_field_multicheckbox($args)
    {
        $current_value = self::get_value($args['id'], $args['default']);
        ?>

		<fieldset>
			<legend class="screen-reader-text"><?php 
        echo \esc_html($args['title']);
        ?></legend>

			<?php 
        foreach ($args['options'] as $value => $option) {
            ?>

				<label for="<?php 
            echo \esc_attr(\sprintf('%1$s-%2$s', $args['id'], $value));
            ?>">

					<input
						id="<?php 
            echo \esc_attr(\sprintf('%1$s-%2$s', $args['id'], $value));
            ?>"
						name="<?php 
            echo \esc_attr(\sprintf('%1$s[%2$s]', $args['id'], $value));
            ?>"
						class="<?php 
            echo \esc_attr($args['input_class']);
            ?>"
						type="checkbox"
						<?php 
            if (isset($current_value[$value])) {
                \checked($current_value[$value]);
            }
            ?>
						value="1"
						<?php 
            self::custom_attributes($args);
            ?>
					/>
					<?php 
            echo \esc_html($option);
            ?>
				</label><br>
			<?php 
        }
        ?>
		</fieldset>

		<?php 
        self::field_description($args);
        self::field_tooltip($args);
    }
    /**
     * Hidden field.
     *
     * @param array $args
     */
    public static function settings_field_hidden($args)
    {
        ?>
		<input type="hidden" name="<?php 
        echo \esc_attr($args['id']);
        ?>" value="<?php 
        echo \esc_attr($args['default']);
        ?>"<?php 
        self::custom_attributes($args);
        ?> />
		<?php 
    }
    /**
     * Color field.
     *
     * @param array $args
     */
    public static function settings_field_color($args)
    {
        \wp_enqueue_script('wp-color-picker');
        \wp_enqueue_style('wp-color-picker');
        $current_value = self::get_value($args['id'], $args['default']);
        ?>
		<div class="color-field">
			<input
				type="text"
				name="<?php 
        echo \esc_attr($args['id']);
        ?>"
				id="<?php 
        echo \esc_attr($args['id']);
        ?>"
				class="color-picker"
				value="<?php 
        echo \esc_attr($current_value);
        ?>"/>

			<?php 
        self::field_description($args);
        ?>
		</div>
		<?php 
    }
    /**
     * Color & Size field.
     *
     * @param array $args
     */
    public static function settings_field_color_size($args)
    {
        \wp_enqueue_script('wp-color-picker');
        \wp_enqueue_style('wp-color-picker');
        $current_value = self::get_value($args['id'], $args['default']);
        $color_id = $args['id'] . '[color]';
        $color_value = isset($current_value['color']) ? $current_value['color'] : '';
        $size_id = $args['id'] . '[size]';
        $size_value = isset($current_value['size']) ? $current_value['size'] : '';
        $size_placeholder = !empty($args['placeholder']) ? $args['placeholder'] : __('Size', 'document-library-pro');
        if (empty($args['custom_attributes'])) {
            $args['custom_attributes'] = [];
        }
        $args['custom_attributes'] = \array_merge(['min' => 0, 'size' => 4], $args['custom_attributes']);
        $size_attributes = self::get_custom_attributes($args);
        ?>
		<div class="color-size-field">
			<input
				type="text"
				name="<?php 
        echo \esc_attr($color_id);
        ?>"
				id="<?php 
        echo \esc_attr($color_id);
        ?>"
				class="color-picker"
				value="<?php 
        echo \esc_attr($color_value);
        ?>"/>
			<input
				type="number"
				name="<?php 
        echo \esc_attr($size_id);
        ?>"
				id="<?php 
        echo \esc_attr($size_id);
        ?>"
				class="color-size"
				value="<?php 
        echo \esc_attr($size_value);
        ?>"
				placeholder="<?php 
        echo \esc_attr($size_placeholder);
        ?>"
				<?php 
        /* Note: This is escaped in get_custom_attributes */
        echo $size_attributes;
        ?>
			/>

			<?php 
        self::field_description($args);
        ?>
		</div>
		<?php 
    }
    /**
     * Settings field help note (alias description).
     *
     * @param array $args
     */
    public static function settings_field_help_note($args)
    {
        self::field_description($args);
    }
}
