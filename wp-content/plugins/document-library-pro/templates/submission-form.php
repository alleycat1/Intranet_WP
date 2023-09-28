<?php
/**
 * Displays the frontend submission form.
 *
 * This template can be overridden by copying it to yourtheme/ptp_templates/submission-form.php.
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

$is_success = isset( $_GET['success'] ) && $_GET['success'] === '1'; //phpcs:ignore

?>

<?php if ( ! empty( $errors ) ) : ?>
	<div class="dlp-notice dlp-error">
		<?php foreach ( $errors as $error ) : ?>
			<p><?php echo wp_kses_post( $error ); ?></p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if ( $is_success && empty( $errors ) ) : ?>
	<div class="dlp-notice dlp-success">
		<?php esc_html_e( 'Document successfully uploaded.', 'document-library-pro' ); ?>
	</div>
<?php endif; ?>

<form action="<?php echo esc_url( get_permalink() ); ?>" method="post" id="dlp-submit-form" class="dlp-submission-form dlp-theme-<?php echo esc_attr( $theme ); ?>" enctype="multipart/form-data">

	<?php do_action( 'dlp_before_submission_form' ); ?>

	<?php foreach ( $fields as $key => $field ) : ?>
		<fieldset class="fieldset-<?php echo esc_attr( $key ); ?> fieldset-type-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
			<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
				<?php echo $templates->get_template( 'form-fields/' . $field['type'] . '-field.php', [ 'key' => $key, 'field' => $field, 'templates' => $templates ] ); //phpcs:ignore ?>
			</div>
		</fieldset>
	<?php endforeach; ?>

	<p>
		<?php wp_nonce_field( 'dlp_frontend_submission', 'dlp_frontend_nonce' ); ?>
		<button type="submit" name="submit_job" class="button"><?php echo esc_attr( $submit_button_text ); ?></button>
	</p>

	<?php do_action( 'dlp_after_submission_form' ); ?>

</form>
