<?php

//phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited

?>

<div id="dlp_version_history_<?php echo esc_attr( $history_type ); ?>_list" class="dlp-version-history-list hidden">
	<ul>
		<?php

		$is_version_control_hidden = $version_control_mode === false || count( (array) $version_history ) === 0;

		foreach ( $version_history as $link_id => $link_info ) {
			$version   = $link_info['version'] ?? '';
			$last_used = (int) $link_info['last_used'] ?? $timestamp;

			switch ( $history_type ) {
				case 'file':
					$link        = get_attached_file( $link_id );
					$link_meta   = wp_prepare_attachment_for_js( $link_id );
					$timestamp   = ( $link_meta['date'] ?? 0 ) / 1000 ?: '';
					$date        = $link_meta['dateFormatted'] ?? '';
					$size        = $link_meta['filesizeHumanReadable'] ?? '';
					$is_selected = (int) $link_id === (int) $document->get_file_id();
					$url         = '';
					$target      = '';
					$href        = "#dlp_version-$link_id";

					if ( $link ) {
						$filename = basename( $link );
					}

					break;

				case 'url':
					$size        = $link_info['size'] ?? '';
					$filename    = basename( $link_info['url'] );
					$timestamp   = 0;
					$date        = 0;
					$is_selected = $link_info['url'] === $document->get_direct_link();
					$url         = $link_info['url'];
					$target      = '_blank';
					$href        = $url;
					break;
			}

			?>

            <li class="<?php echo $is_selected ? 'selected' : ''; ?>">
                <label>
                    <input
                        type="radio"
                        id="dlp_version-<?php echo esc_attr( $link_id ); ?>"
                        name="_dlp_version_history_<?php echo esc_attr( $history_type ); ?>_selected"
                        value="<?php echo esc_attr( $link_id ); ?>"
                        data-filename="<?php echo esc_attr( $filename ); ?>"
                        data-filesize="<?php echo esc_attr( $size ); ?>"
                        data-timestamp="<?php echo esc_attr( $timestamp ); ?>"
                        data-version="<?php echo esc_attr( $version ); ?>"
                        data-last_used="<?php echo esc_attr( $last_used ); ?>"
                        data-url="<?php echo esc_attr( $url ); ?>"
                        <?php checked( $is_selected ); ?>
                        class="<?php echo $version_control_mode === 'keep' ? '' : 'hidden'; ?>"
                    />
                    <input type="hidden" class="<?php echo esc_attr( $history_type ); ?>-version" name="_dlp_version_history[<?php echo esc_attr( $history_type ); ?>][<?php echo esc_attr( $link_id ); ?>][version]" value="<?php echo esc_attr( $version ); ?>" />
                    <input type="hidden" class="<?php echo esc_attr( $history_type ); ?>-size" name="_dlp_version_history[<?php echo esc_attr( $history_type ); ?>][<?php echo esc_attr( $link_id ); ?>][size]" value="<?php echo esc_attr( $size ); ?>" />
                    <input type="hidden" class="<?php echo esc_attr( $history_type ); ?>-last-used" name="_dlp_version_history[<?php echo esc_attr( $history_type ); ?>][<?php echo esc_attr( $link_id ); ?>][last_used]" value="<?php echo esc_attr( $last_used ); ?>" />
                    <input type="hidden" class="<?php echo esc_attr( $history_type ); ?>-url" name="_dlp_version_history[<?php echo esc_attr( $history_type ); ?>][<?php echo esc_attr( $link_id ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" />
                    <a href="<?php echo esc_attr( $href ); ?>" class="<?php echo esc_attr( $history_type ); ?>name" aria-hidden="true" target="<?php echo esc_attr( $target ); ?>"><?php echo esc_html( $filename ); ?></a>
                    <span class="screen-reader-text">
                        <?php
                        /* translators: %s: File name */
                        echo esc_html( sprintf( __( 'Select file: %s', 'document-library-pro' ), $filename ) );
                        ?>
                    </span>
                </label>

                <a href="#dlp_version-<?php echo esc_attr( $link_id ); ?>" class="edit-version version-action">
                    <span class="dashicons dashicons-edit"></span>
                </a>

                <a href="#dlp_version-<?php echo esc_attr( $link_id ); ?>" class="remove-version version-action">
                    <span class="dashicons dashicons-trash"></span>
                </a>

                <dl class="dlp_version_info">
                    <dt class="link-version-label"><?php esc_html_e( 'version', 'document-library-pro' ); ?></dt>
                    <dd class="link-version"><?php echo esc_attr( $version ); ?></dd>

                    <?php
                    if ( $size ) {
                        ?>
                        <dt class="link-size-label"><?php esc_html_e( 'size', 'document-library-pro' ); ?></dt>
                        <dd class="link-size"><?php echo esc_attr( $size ); ?></dd>
                        <?php
                    }
                    ?>

                    <?php
                    if ( $last_used ) {
                        ?>
                        <dt class="link-last_used-label"><?php esc_html_e( 'last used', 'document-library-pro' ); ?></dt>
                        <dd class="link-last_used"><?php echo esc_attr( wp_date( get_option( 'date_format' ), $last_used ) ); ?></dd>
                        <?php
                    }
                    ?>

                    <?php
                    if ( $timestamp ) {
                        ?>
                        <dt class="link-uploaded-label"><?php esc_html_e( 'uploaded', 'document-library-pro' ); ?></dt>
                        <dd class="link-uploaded"><?php echo esc_attr( wp_date( get_option( 'date_format' ), $timestamp ) ); ?></dd>
                        <?php
                    }
                    ?>
                </dl>
                <div class="dlp_version_label_inline_editor">
                    <label>
                        <?php esc_html_e( 'version', 'document-library-pro' ); ?>
                        <input type="text" class="version-input" value="" />
                    </label>
                    <?php
                    if ( $history_type === 'url' ) {
                        ?>

                        <label>
                            <?php esc_html_e( 'size', 'document-library-pro' ); ?>
                            <input type="text" class="size-input" value="" />
                        </label>
                        <?php
                    }
                    ?>
                    <a href="#dlp_version_label_inline_editor" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'document-library-pro' ); ?></a>
                    <a href="#dlp_version_label_inline_editor" class="hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel', 'document-library-pro' ); ?></a>
                </div>
            </li>
            <?php
		}
		?>
	</ul>
</div>

<a id="dlp_version_history_<?php echo esc_attr( $history_type ); ?>_toggle" class="dlp-version-history-toggle hide-if-no-js <?php echo $is_version_control_hidden ? 'hidden' : ''; ?>" href="" role="button" href="">
	<span aria-hidden="true"><?php $version_control_mode === 'keep' ? esc_html_e( 'Version history', 'document-library-pro' ) : esc_html_e( 'Version details', 'document-library-pro' ); ?></span>
	<span class="screen-reader-text"></span>
</a>

<?php
//phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
