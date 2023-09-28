<?php
/**
 * Document Custom Fields
 *
 * This template can be overridden by copying it to yourtheme/dlp_templates/grid-card/custom_fields.php.
 *
 * HOWEVER, on occasion Barn2 will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version   1.0
 * @package   Document_Library_Pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( $custom_fields ) : ?>

	<?php foreach ( $custom_fields as $custom_field ){ ?>
	<div class="dlp-grid-card-custom-fields">
		<strong class="dlp-document-info-title"><?php echo $custom_field['label']; ?>:</strong>
		<?php echo $custom_field['value']; ?>
	</div>
	<?php } ?>

<?php endif; ?>
