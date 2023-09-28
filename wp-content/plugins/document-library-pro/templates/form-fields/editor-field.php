<?php
/**
 * Displays the text editor field for the form.
 *
 * This template can be overridden by copying it to yourtheme/ptp_templates/form-fields/editor-field.php.
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

$editor = apply_filters(
	'dlp_form_wp_editor_args',
	[
		'textarea_name' => $key,
		'media_buttons' => false,
		'textarea_rows' => 8,
		'quicktags'     => false,
		'editor_css'    => '<style> .mce-top-part button { background-color: rgba(0,0,0,0.0) !important; } </style>',
		'tinymce'       => [
			'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
			'paste_as_text'                 => true,
			'paste_auto_cleanup_on_paste'   => true,
			'paste_remove_spans'            => true,
			'paste_remove_styles'           => true,
			'paste_remove_styles_if_webkit' => true,
			'paste_strip_class_attributes'  => true,
			'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
			'toolbar2'                      => '',
			'toolbar3'                      => '',
			'toolbar4'                      => '',
		],
	]
);

wp_editor( isset( $field['value'] ) ? wp_kses_post( $field['value'] ) : '', $key, $editor );
