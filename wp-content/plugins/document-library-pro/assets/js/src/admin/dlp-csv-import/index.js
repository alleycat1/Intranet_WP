/*global ajaxurl, dlp_import_params */
;(function ( $, window ) {

	/**
	 * documentImportForm handles the import process.
	 */
	var documentImportForm = function( $form ) {
		this.$form           = $form;
		this.xhr             = false;
		this.mapping         = dlp_import_params.mapping;
		this.position        = 0;
		this.file            = dlp_import_params.file;
		this.delimiter       = dlp_import_params.delimiter;
		this.security        = dlp_import_params.import_nonce;
		this.ajax_url		 = dlp_import_params.ajax_url;

		// Number of import successes/failures.
		this.imported = 0;
		this.failed   = 0;
		this.skipped  = 0;

		// Initial state.
		this.$form.find('.dlp-importer-progress').val( 0 );

		this.run_import = this.run_import.bind( this );

		// Start importing.
		this.run_import();
	};

	/**
	 * Run the import in batches until finished.
	 */
	documentImportForm.prototype.run_import = function() {
		var $this = this;

		$.ajax( {
			type: 'POST',
			url: $this.ajax_url,
			data: {
				action          : 'dlp_document_import',
				position        : $this.position,
				mapping         : $this.mapping,
				file            : $this.file,
				delimiter       : $this.delimiter,
				security        : $this.security
			},
			dataType: 'json',
			success: function( response ) {
				if ( response.success ) {
					$this.position  = response.data.position;
					$this.imported += response.data.imported;
					$this.failed   += response.data.failed;
					$this.skipped  += response.data.skipped;
					$this.$form.find('.dlp-importer-progress').val( response.data.percentage );

					if ( 'done' === response.data.position ) {
						var file_name = $this.file.split( '/' ).pop();
						window.location = response.data.url +
							'&documents-imported=' +
							parseInt( $this.imported, 10 ) +
							'&documents-failed=' +
							parseInt( $this.failed, 10 ) +
							'&documents-skipped=' +
							parseInt( $this.skipped, 10 ) +
							'&file-name=' +
							file_name;
					} else {
						$this.run_import();
					}
				}
			}
		} ).fail( function( response ) {
			window.console.log( response );
		} );
	};

	/**
	 * Function to call documentImportForm on jQuery selector.
	 */
	$.fn.dlp_importer = function() {
		new documentImportForm( this );
		return this;
	};

	$( '.dlp-importer' ).dlp_importer();

})( jQuery, window );
