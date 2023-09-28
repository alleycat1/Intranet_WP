<?php
/**
 * Displays the text field for the form.
 *
 * This template can be overridden by copying it to yourtheme/ptp_templates/form-fields/text-field.php.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Media <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<input type="text" class="input-text" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"<?php if ( isset( $field['autocomplete'] ) && false === $field['autocomplete'] ) { echo ' autocomplete="off"'; } ?> id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ); ?>" value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>" maxlength="<?php echo esc_attr( ! empty( $field['maxlength'] ) ? $field['maxlength'] : '' ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> />
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo wp_kses_post( $field['description'] ); ?></small><?php endif; ?>
