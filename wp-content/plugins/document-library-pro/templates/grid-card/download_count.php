<?php

use Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;

/**
 * Document Download Count
 *
 * This template can be overridden by copying it to yourtheme/dlp_templates/grid-card/download_count.php.
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

if ( $download_count ) : ?>

	<div class="dlp-grid-card-download-count">
		<?php echo SVG_Icon::get( 'download', [ 'download-count-icon' ] ) . esc_html( $download_count ); ?>
	</div>

<?php endif; ?>
