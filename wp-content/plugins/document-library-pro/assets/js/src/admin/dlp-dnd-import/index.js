/* global plupload, pluploadL10n, dndImportObject */
jQuery( function ( $ ) {
	/**
	 * plUpload DND JS
	 */
	const uploaderInit = function () {
		const uploader = new plupload.Uploader( dndImportObject.pluploadInit );

		uploader.bind( 'Init', function ( up ) {
			const uploadDiv = $( '#plupload-upload-ui' );

			if (
				up.features.dragdrop &&
				! $( document.body ).hasClass( 'mobile' )
			) {
				uploadDiv.addClass( 'drag-drop' );

				$( '#drag-drop-area' )
					.on( 'dragover.wp-uploader', function () {
						// dragenter doesn't fire right
						uploadDiv.addClass( 'drag-over' );
					} )
					.on(
						'dragleave.wp-uploader, drop.wp-uploader',
						function () {
							uploadDiv.removeClass( 'drag-over' );
						}
					);
			} else {
				uploadDiv.removeClass( 'drag-drop' );
				$( '#drag-drop-area' ).off( '.wp-uploader' );
			}

			if ( up.runtime === 'html4' ) {
				$( '.upload-flash-bypass' ).hide();
			}
		} );

		uploader.bind( 'postinit', function ( up ) {
			up.refresh();
		} );

		uploader.init();

		uploader.bind( 'FilesAdded', function ( up, files ) {
			$( '#media-upload-error' ).empty();
			dlpUploadStart();

			plupload.each( files, function ( file ) {
				if (
					file.type === 'image/heic' &&
					up.settings.heic_upload_error
				) {
					// Show error but do not block uploading.
					dlpQueueError( pluploadL10n.unsupported_image );
				}

				dlpFileQueued( file );
			} );

			up.refresh();
			up.start();
		} );

		uploader.bind( 'UploadFile', function ( up, file ) {
			dlpFileUploading( up, file );
		} );

		uploader.bind( 'UploadProgress', function ( up, file ) {
			dlpUploadProgress( up, file );
		} );

		uploader.bind( 'Error', function ( up, error ) {
			dlpUploadError( error.file, error.code, error.message, up );
			up.refresh();
		} );

		uploader.bind( 'FileUploaded', function ( up, file, response ) {
			dlpUploadSuccess( file, response.response );
		} );

		uploader.bind( 'UploadComplete', function () {
			dlpUploadComplete();
		} );
	};

	/**
	 * Init dlpPlupload.
	 */
	if ( typeof dndImportObject.pluploadInit === 'object' ) {
		uploaderInit();
	}
} );

function dlpUploadStart() {}
function dlpUploadComplete() {}

function dlpUploadSuccess( fileObj, serverData ) {
	const item = jQuery( '#media-item-' + fileObj.id );

	// On success serverData should be numeric,
	// fix bug in html4 runtime returning the serverData wrapped in a <pre> tag.
	if ( typeof serverData === 'string' ) {
		serverData = serverData.replace( /^<pre>(\d+)<\/pre>$/, '$1' );

		// If upload returned an error message, place it in the media item div and return.
		if ( /media-upload-error|error-div/.test( serverData ) ) {
			item.html( serverData );
			return;
		}
	}

	item.find( '.percent' ).html( pluploadL10n.crunching );

	dlpPrepareMediaItem( fileObj, serverData );
}

function dlpFileQueued( fileObj ) {
	// Create a progress bar containing the filename.
	jQuery( '<div class="media-item">' )
		.attr( 'id', 'media-item-' + fileObj.id )
		.append(
			'<div class="progress"><div class="percent">0%</div><div class="bar"></div></div>',
			jQuery( '<div class="filename original">' ).text(
				' ' + fileObj.name
			)
		)
		.appendTo( jQuery( '#media-items' ) );
}

function dlpFileUploading( up, file ) {
	var hundredmb = 100 * 1024 * 1024,
		max = parseInt( up.settings.max_file_size, 10 );

	if ( max > hundredmb && file.size > hundredmb ) {
		setTimeout( function () {
			if ( file.status < 3 && file.loaded === 0 ) {
				// Not uploading.
				fileError(
					file,
					pluploadL10n.big_upload_failed
						.replace( '%1$s', '<a class="uploader-html" href="#">' )
						.replace( '%2$s', '</a>' )
				);
				up.stop(); // Stop the whole queue.
				up.removeFile( file );
				up.start(); // Restart the queue.
			}
		}, 10000 ); // Wait for 10 seconds for the file to start uploading.
	}
}

function dlpUploadProgress( up, file ) {
	var item = jQuery( '#media-item-' + file.id );

	jQuery( '.bar', item ).width( ( 200 * file.loaded ) / file.size );
	jQuery( '.percent', item ).html( file.percent + '%' );
}

function dlpUploadError( fileObj, errorCode, message, up ) {
	var hundredmb = 100 * 1024 * 1024,
		max;

	switch ( errorCode ) {
		case plupload.FAILED:
			dlpFileError( fileObj, pluploadL10n.upload_failed );
			break;
		case plupload.FILE_EXTENSION_ERROR:
			dlpFileExtensionError( up, fileObj, pluploadL10n.invalid_filetype );
			break;
		case plupload.FILE_SIZE_ERROR:
			dlpUploadSizeError( up, fileObj );
			break;
		case plupload.IMAGE_FORMAT_ERROR:
			dlpFileError( fileObj, pluploadL10n.not_an_image );
			break;
		case plupload.IMAGE_MEMORY_ERROR:
			dlpFileError( fileObj, pluploadL10n.image_memory_exceeded );
			break;
		case plupload.IMAGE_DIMENSIONS_ERROR:
			dlpFileError( fileObj, pluploadL10n.image_dimensions_exceeded );
			break;
		case plupload.GENERIC_ERROR:
			dlpQueueError( pluploadL10n.upload_failed );
			break;
		case plupload.IO_ERROR:
			max = parseInt( up.settings.filters.max_file_size, 10 );

			if ( max > hundredmb && fileObj.size > hundredmb ) {
				dlpFileError(
					fileObj,
					pluploadL10n.big_upload_failed
						.replace( '%1$s', '<a class="uploader-html" href="#">' )
						.replace( '%2$s', '</a>' )
				);
			} else {
				dlpQueueError( pluploadL10n.io_error );
			}

			break;
		case plupload.HTTP_ERROR:
			dlpQueueError( pluploadL10n.http_error );
			break;
		case plupload.INIT_ERROR:
			jQuery( '.media-upload-form' ).addClass( 'html-uploader' );
			break;
		case plupload.SECURITY_ERROR:
			dlpQueueError( pluploadL10n.security_error );
			break;
		default:
			dlpFileError( fileObj, pluploadL10n.default_error );
	}
}

// Generic error message.
function dlpQueueError( message ) {
	jQuery( '#media-upload-error' )
		.show()
		.html( '<div class="error"><p>' + message + '</p></div>' );
}

function dlpUploadSizeError( up, file ) {
	const message = pluploadL10n.file_exceeds_size_limit.replace(
		'%s',
		file.name
	);

	// Construct the error div.
	const errorDiv = jQuery( '<div />' )
		.attr( {
			id: 'media-item-' + file.id,
			class: 'media-item error',
		} )
		.append( jQuery( '<p />' ).text( message ) );

	// Append the error.
	jQuery( '#media-file-errors' ).append( errorDiv );
	up.removeFile( file );
}

function dlpFileExtensionError( up, file, message ) {
	jQuery( '#media-items' ).append(
		'<div id="media-item-' +
			file.id +
			'" class="media-item error"><p>' +
			message +
			'</p></div>'
	);
	up.removeFile( file );
}

function dlpFileError( up, file, message ) {
	message = pluploadL10n.error_uploading.replace( '%s', file.name ) + message;

	// Construct the error div.
	const errorDiv = jQuery( '<div />' )
		.attr( {
			id: 'media-item-' + file.id,
			class: 'error',
		} )
		.append( jQuery( '<p />' ).text( message ) );

	// Append the error.
	jQuery( '#media-file-errors' ).append( errorDiv );
	up.removeFile( file );
}

function dlpPrepareMediaItem( fileObj, serverData ) {
	const item = jQuery( '#media-item-' + fileObj.id );

	item.load( dndImportObject.ajaxurl, {
		document_id: serverData,
		action: 'dlp_dnd_fetch',
	} );
}
