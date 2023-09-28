( function ( $ ) {
	/**
	 * Bind Events
	 */
	const dlpCount = function () {
		$( document ).on(
			'click',
			'.dlp-download-link',
			this.handleDownloadClick
		);

		$( document ).on( 'dlp_multi_download', this.handleMultiDownloadClick );
	};

	/**
	 * Handle single download.
	 */
	dlpCount.prototype.handleDownloadClick = function ( event ) {
		const downloadId = $( this ).data( 'download-id' );

		$.ajax( {
			url: dlp_count_params.ajax_url,
			type: 'POST',
			data: {
				download_id: downloadId,
				action: dlp_count_params.ajax_action,
				_ajax_nonce: dlp_count_params.ajax_nonce,
			},
			xhrFields: {
				withCredentials: true,
			},
		} ).done( function ( response ) {
			console.log( response );
		} );
	};

	/**
	 * Handle multi download.
	 */
	dlpCount.prototype.handleMultiDownloadClick = function ( event, data ) {
		$.ajax( {
			url: dlp_count_params.ajax_url,
			type: 'POST',
			data: {
				download_ids: data.downloadIds,
				action: dlp_count_params.ajax_action,
				_ajax_nonce: dlp_count_params.ajax_nonce,
			},
			xhrFields: {
				withCredentials: true,
			},
		} ).done( function ( response ) {
			console.log( response );
		} );
	};


	/**
	 * Init dlpCount.
	 */
	new dlpCount();
} )( jQuery, window );
