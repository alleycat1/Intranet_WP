( function( $ ) {
	/**
	 * Bind Events
	 */
	const dlpPreview = function() {
		$( document ).on(
			'click',
			'.dlp-preview-button',
			this.handleModalInit
		);

		$( document ).on(
			'click',
			'.dlp-preview-modal-close, .dlp-preview-modal-overlay',
			this.handleCloseModal
		);
	};

	/**
	 * Handle Preview Button / Open Modal
	 *
	 * @param  event
	 */
	dlpPreview.prototype.handleModalInit = function( event ) {
		const type = $( this ).data( 'download-type' );
		const view = $( this ).data( 'view' );
		const url = $( this ).data( 'download-url' );
		const title = $( this ).data( 'title' );
		let modalId;

		switch ( view ) {
			case 'table':
				modalId = `modal_${ $( this ).parents( '.posts-data-table' ).first().attr( 'id' ) }`;
				break;

			case 'grid':
				modalId = `modal_${ $( this ).parents( '.dlp-grid-container' ).first().attr( 'id' ) }`;
				break;

			case 'single':
				modalId = $( '.dlp-preview-modal' ).attr( 'id' );
				break;

			default:
				break;
		}

		MicroModal.show( modalId, {
			onShow: ( modal ) => dlpPreview.loadContent( modal, { title, type, url } ),
			onClose: ( modal ) => dlpPreview.destroyContent( modal ),
			openTrigger: 'data-dlp-preview-open',
			closeTrigger: 'data-dlp-preview-close',
		} );
	};

	dlpPreview.prototype.handleCloseModal = function( event ) {
		event.stopPropagation();

		if ( $( event.target ).parents( '.dlp-preview-modal-container' ).length > 0 ) {
			return;
		}

		const modalId = $( this ).data( 'dlp-preview-close' ).substring( 1 );

		MicroModal.close( modalId );
	};

	dlpPreview.loadContent = function( modal, data ) {
		const $modalContent = $( modal ).find( '.dlp-preview-modal-content' ).first();
		const $modalFooter = $( modal ).find( '.dlp-preview-modal-footer' ).first();
		const embedHtml = dlpPreview.getEmbedHtml( data );

		$modalContent.html( embedHtml );
		$modalFooter.html( data.title );

		$( document.body ).addClass( 'dlp-preview-modal-open' );
	};

	dlpPreview.destroyContent = function( modal ) {
		const $modalContent = $( modal ).find( '.dlp-preview-modal-content' ).first();

		$modalContent.html( '' );

		$( document.body ).removeClass( 'dlp-preview-modal-open' );
	};

	dlpPreview.getEmbedHtml = function( data ) {
		let embedHtml = '';

		switch ( data.type ) {
			case 'application/pdf':
			case 'application/x-pdf':
				embedHtml = `<iframe src="${ data.url }" width="100%" height="100%">`;
				break;

			case 'image/jpeg':
			case 'image/gif':
			case 'image/png':
			case 'image/webp':
			case 'image/svg+xml':
				embedHtml = `<img src="${ data.url }" />`;
				break;

			case 'video/mp4':
			case 'video/ogg':
				embedHtml = `<video
                    controls type="${ data.type }"
                    src="${ data.url }">
                    ${ dlp_preview_params.video_error }
                </video>`;
				break;

			case 'audio/mp3':
			case 'audio/mp4':
			case 'audio/mpeg':
			case 'audio/ogg':
			case 'audio/aac':
			case 'audio/aacp':
			case 'audio/flac':
			case 'audio/wav':
			case 'audio/webm':
				embedHtml = `<audio
                    controls
                    src="${ data.url }">
                    ${ dlp_preview_params.audio_error }
                </audio>`;
				break;
		}

		return embedHtml;
	};

	/**
	 * Init dlpPreview.
	 */
	new dlpPreview();
}( jQuery, window ) );

