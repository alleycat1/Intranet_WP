( function ( $ ) {
	/**
	 * Multi Download JS
	 */
	const dlpMultiDownload = function () {
		$( document ).on(
			'change',
			'[name="zip-urls"]',
			this.handleDownloadCheckbox
		);

		$( document ).on(
			'init.dt',
			'.posts-data-table',
			this.addButtonToTable
		);

		$( document ).on(
			'click',
			'.dlp-multiple-download-btn',
			this.handleDownloadButton
		);
	};

	/**
	 * Object Variables
	 */
	dlpMultiDownload.downloadUrls = {};
	dlpMultiDownload.downloadIds = {};

	/**
	 * Handle Table Init
	 */
	dlpMultiDownload.prototype.addButtonToTable = function ( event, settings ) {
		const configData = $( settings.nTable ).data( 'config' );

		if ( configData.multiDownloadButton === false ) {
			return;
		}

		const containerClass = dlpMultiDownload.getButtonContainerClass(
			configData.multiDownloadPosition
		);

		$( this ).siblings( containerClass ).append(
			`<div class="dlp-multi-download-wrap">
				<button class="dlp-multiple-download-btn" disabled>${ configData.multiDownloadText }</button>
			</div>`
		);
	};

	/**
	 * Handle Checkbox
	 */
	dlpMultiDownload.prototype.handleDownloadCheckbox = function ( event ) {
		const $table = $( this ).parents( '.posts-data-table' ).first();
		const $multiDownloadBtn = $( this )
			.parents( '.posts-table-wrapper' )
			.first()
			.find( '.dlp-multiple-download-btn' );
		const table_id = $table.prop( 'id' );

		if ( ! dlpMultiDownload.downloadUrls.hasOwnProperty( table_id ) ) {
			dlpMultiDownload.downloadUrls[ table_id ] = [];
		}

		if ( ! dlpMultiDownload.downloadIds.hasOwnProperty( table_id ) ) {
			dlpMultiDownload.downloadIds[ table_id ] = [];
		}

		if ( event.target.checked ) {
			dlpMultiDownload.downloadUrls[ table_id ].push(
				event.target.dataset.downloadUrl
			);

			dlpMultiDownload.downloadIds[ table_id ].push(
				event.target.dataset.downloadId
			);
		} else {
			dlpMultiDownload.downloadUrls[
				table_id
			] = dlpMultiDownload.downloadUrls[ table_id ].filter(
				( url ) => url !== event.target.dataset.downloadUrl
			);

			dlpMultiDownload.downloadIds[
				table_id
			] = dlpMultiDownload.downloadIds[ table_id ].filter(
				( id ) => id !== event.target.dataset.downloadId
			);
		}

		if ( dlpMultiDownload.downloadUrls[ table_id ].length === 0 ) {
			$multiDownloadBtn.prop( 'disabled', true );
		} else {
			$multiDownloadBtn.prop( 'disabled', false );
		}
	};

	/**
	 * Handle Download All Button
	 */
	dlpMultiDownload.prototype.handleDownloadButton = function ( event ) {
		const $button = $( this );
		const $table = $button
			.parents( '.posts-table-wrapper' )
			.first()
			.children( '.posts-data-table' )
			.first();
		const table_id = $table.prop( 'id' );

		const blockConfig = {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.7,
			},
		};

		$( '.dlp-multiple-download-error' ).remove();

		$button.block( blockConfig );

		getZipFile( dlpMultiDownload.downloadUrls[ table_id ], 'downloads.zip' )
			.then( ( success ) => {
				$button.unblock();
				const downloadIds = dlpMultiDownload.downloadIds[ table_id ];

				console.log( downloadIds );
				$( document ).trigger( 'dlp_multi_download', { downloadIds } );
			} )
			.catch( ( error ) => {
				$button
					.parents( '.posts-table-controls' )
					.first()
					.append(
						`<div class="dlp-multiple-download-error"><p>${ dlp_multi_download_params.zip_failed_error }</p></div>`
					);
				$button.unblock();
			} );
	};

	dlpMultiDownload.getButtonContainerClass = function ( setting ) {
		let containerClass;

		if ( ! [ 'above', 'below', 'both' ].includes( setting ) ) {
			setting = 'above';
		}

		switch ( setting ) {
			case 'above':
				containerClass = '.posts-table-above';
				break;

			case 'below':
				containerClass = '.posts-table-below';
				break;

			case 'both':
				containerClass = '.posts-table-controls';
				break;

			default:
				break;
		}

		return containerClass;
	};

	/**
	 * Init dlpMultiDownload.
	 */
	new dlpMultiDownload();
} )( jQuery, window );
