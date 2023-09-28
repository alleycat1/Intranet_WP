<?php
/**
 * Displays the file field for the form.
 *
 * This template can be overridden by copying it to yourtheme/ptp_templates/form-fields/file-field.php.
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

$classes            = [ 'input-text' ];
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );
$field_name         = isset( $field['name'] ) ? $field['name'] : $key;
$field_name         .= ! empty( $field['multiple'] ) ? '[]' : '';
$file_limit         = false;

if ( ! empty( $field['multiple'] ) && ! empty( $field['file_limit'] ) ) {
	$file_limit = $field['file_limit'];
}

?>

<input
	type="file"
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ); ?>"
	<?php if ( ! empty( $field['multiple'] ) ) echo 'multiple'; ?>
	<?php if ( $file_limit ) echo ' data-file_limit="' . absint( $file_limit ) . '"';?>
	<?php if ( ! empty( $field['file_limit_message'] ) ) echo ' data-file_limit_message="' . esc_attr( $field['file_limit_message'] ) . '"';?>
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?><?php if ( ! empty( $field['multiple'] ) ) echo '[]'; ?>"
	id="<?php echo esc_attr( $key ); ?>"
	placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ); ?>"
/>
<small class="description">
	<?php if ( ! empty( $field['description'] ) ) : ?>
		<?php echo wp_kses_post( $field['description'] ); ?>
	<?php endif; ?>
</small>
