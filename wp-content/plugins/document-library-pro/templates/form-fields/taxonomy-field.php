<?php
/**
 * Displays the taxonomy field for the form.
 *
 * This template can be overridden by copying it to yourtheme/ptp_templates/form-fields/taxonomy-field.php.
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

$value = isset( $field['value'] ) ? $field['value'] : '';

if ( is_array( $value ) ) {
	$value = implode( ',', $value );
}

?>

<input class="dlp-taxonomy-select" data-selected-terms="<?php echo esc_attr( $value ); ?>" data-taxonomy="<?php echo esc_attr( isset( $field['taxonomy'] ) ? $field['taxonomy'] : '' ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ); ?>"/>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo wp_kses_post( $field['description'] ); ?></small><?php endif; ?>
