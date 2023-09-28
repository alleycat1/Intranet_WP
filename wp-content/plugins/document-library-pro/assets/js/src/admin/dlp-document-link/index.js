/* eslint-disable camelcase */
jQuery( function( $ ) {
	/**
	 * Document Link Metabox JS
	 */
	const dlpDocumentLink = function() {
		$( '#dlp_add_file_button' ).on( 'click', this.handleAddFile );
		$( '#dlp_remove_file_button' ).on( 'click', true, this.handleRemoveFile );
		$( '#dlp_document_link_type' ).on( 'change', this.handleSelectBox );
		$( '.dlp-version-history-toggle' ).on( 'click', this.toggleVersionHistory );
		$( '.dlp-version-history-list' )
			.on( 'click', 'input[type="radio"], a.filename', this.selectHistoricalVersion )
			.on( 'click', 'a.edit-version', this.editVersionInfo )
			.on( 'click', 'a.remove-version', true, this.removeVersion )
			.on( 'click', '.dlp_version_label_inline_editor a.button', true, this.exitVersionInfoEdit )
			.on( 'click', '.dlp_version_label_inline_editor a.button-cancel', this.exitVersionInfoEdit );

		$( window ).on( 'beforeunload', this.checkIfDirty );
		$( 'form#post' ).on( 'submit', this.clearDirty );
	
	};

	dlpDocumentLink.wpMedia = null;
	dlpDocumentLink.isDirty = false;

	/**
	 * Handle page reload or close when unsave data is found
	 */
	 dlpDocumentLink.prototype.checkIfDirty = function( event ) {
		console.log( dlpDocumentLink.isDirty )
		if ( dlpDocumentLink.isDirty ) {
			return dlpAdminObject.i18n.before_unload;
		}
		return undefined;
	}

	/**
	 * Clear the dirty state of the form on submission
	 */
	dlpDocumentLink.prototype.clearDirty = function( event ) {
		dlpDocumentLink.isDirty = false;
	}

	/**
	 * Render second option
	 */
	dlpDocumentLink.prototype.handleSelectBox = function( event ) {
		const $this = $( this );
		const value = $this.find( ':selected' ).val();
		const $file_details = $( '#dlp_file_attachment_details' );
		const $url_details = $( '#dlp_link_url_details' );
		const $file_size_input = $( '#dlp_file_size_input' );

		switch ( value ) {
			case 'file':
				$url_details.removeClass( 'active' );
				$file_details.addClass( 'active' );
				$file_size_input.prop( 'disabled', true );
				break;
			case 'url':
				$url_details.addClass( 'active' );
				$file_details.removeClass( 'active' );
				$file_size_input.removeAttr( 'disabled' );
				break;
			case 'none':
				$url_details.removeClass( 'active' );
				$file_details.removeClass( 'active' );
				$file_size_input.removeAttr( 'disabled' );
				break;
			default:
				$url_details.removeClass( 'active' );
				$file_details.removeClass( 'active' );
				$file_size_input.removeAttr( 'disabled' );
				break;
		}
	};

	/**
	 * Handle Add File (WP Media)
	 */
	dlpDocumentLink.prototype.handleAddFile = function( event ) {
		event.preventDefault();

		const $this = $( this );
		const $file_name = $( '#dlp_file_name_input' );
		const $file_name_text = $( '.dlp_file_name_text' );
		const $file_id = $( '#dlp_file_id' );
		const $file_attached_area = $( '#dlp_file_attached' );

		if ( dlpDocumentLink.wpMedia !== null ) {
			if ( dlpDocumentLink.prototype.cancelFileReplacement( $file_name.val() ) ) {
				return;
			}

			dlpDocumentLink.wpMedia.open();
			return;
		}

		dlpDocumentLink.wpMedia = wp.media({
			title: dlpAdminObject.i18n.select_file,
			button: {
				text: dlpAdminObject.i18n.add_file
			}
		});

		dlpDocumentLink.wpMedia.on( 'select', function () {
			const selection = dlpDocumentLink.wpMedia.state().get('selection');

			selection.map( function (attachment) {
				attachment = attachment.toJSON();

				$file_name.val( attachment.filename );
				$file_name_text.text( attachment.filename );
				$file_id.val( attachment.id );
				$file_attached_area.addClass( 'active' );

				let buttonText = dlpAdminObject.i18n.replace_file;

				if ( dlpAdminObject.version_control_mode === 'keep' ) {
					buttonText = dlpAdminObject.i18n.add_new_file;
				}

				$this.text( buttonText );

				$( '#dlp_file_attachment_details.version-control #dlp_version_history_file_toggle' ).show().removeClass( 'hidden' );

				if ( $( `#dlp_version-${ attachment.id }` ).length === 0 ) {
					const versionItemTemplate = wp.template( 'dlp-version-history-item' );
					const versionInfoTemplate = wp.template( 'dlp-file-version-info' );
					const data = {
						attachment,
						href: '#dlp_version_history_list',
						target: '',
						history_type: 'file',
					};
					const $li = $( '<li>' ).html( versionItemTemplate( data ) ).addClass( 'selected' );
					$( 'dl.dlp_version_info', $li ).html( versionInfoTemplate( data ) );

					// if version_control_mode is `delete` then remove any other file version
					if ( dlpAdminObject.version_control_mode === 'delete' ) {
						$( '#dlp_version_history_file_list ul li' ).remove();
					}

					// add a new radio option at the top of the version history
					$( '#dlp_version_history_file_list ul' ).prepend( $li );
					$( 'dlp_version_history_file' ).toggle( $( '#dlp_version_history_file_list ul li' ).length > 0 )

					dlpDocumentLink.isDirty = true;
				}

				// unselect all the radio options...
				$( '#dlp_version_history_list input[type="radio"]' ).prop( 'checked', false )
				// ...then select the radio option corresponding to the selected attachment
				$( `#dlp_version-${ attachment.id }` ).trigger('click');
			});
		});

		if ( dlpDocumentLink.prototype.cancelFileReplacement( $file_name.val() ) ) {
			return;
		}

		dlpDocumentLink.wpMedia.open();
	};

	/**
	 * Handle the toggling of the version history list
	 */
	dlpDocumentLink.prototype.toggleVersionHistory = function( event ) {
		event.preventDefault();

		const $versionHistoryList = $( event.currentTarget ).closest( '.version-control' ).find( '.dlp-version-history-list' );

		if ( $versionHistoryList.hasClass( 'hidden' ) ) {
			$versionHistoryList.hide().removeClass( 'hidden' );
		}

		let buttonText = dlpAdminObject.i18n.replace_file;

		if ( ! $versionHistoryList.is( ':visible' ) && dlpAdminObject.version_control_mode === 'keep' ) {
			buttonText = dlpAdminObject.i18n.add_new_file;
		}

		$( '#dlp_add_file_button' ).text( buttonText );
		$versionHistoryList.slideToggle( 'fast' );
	};

	/**
	 * Handle the selection of an historical version
	 */
	 dlpDocumentLink.prototype.selectHistoricalVersion = function( event ) {
		if ( event.currentTarget.tagName === 'A' ) {
			event.preventDefault();
		}

		const $this = $( event.currentTarget );
		const $item = $this.closest( 'li' );
		const $version = $( 'input[type="radio"]', $item );

		if ( $version.length ) {
			$( '.dlp-version-history-list li.selected' ).removeClass( 'selected' );
			$item.addClass( 'selected' );
			$version.prop( 'checked', true );

			// update fields of files
			$( '#dlp_file_attachment_details.active #dlp_file_name_input' ).val( $version.data( 'filename' ) );
			$( '#dlp_file_attachment_details.active #dlp_file_id' ).val( $version.val() );
			$( '#dlp_file_attachment_details.active span.dlp_file_name_text' ).text( $version.data( 'filename' ) );
			$( '#dlp_file_attachment_details.active #dlp_file_attached' ).addClass( 'active' );

			// update fields of URLs
			$( '#dlp_link_url_details.active #dlp_direct_link_input' ).val( $version.data( 'url' ) );
		}
	};

	/**
	 * Handle the editing of a version information
	 */
	dlpDocumentLink.prototype.editVersionInfo = function( event ) {
		event.preventDefault();
		const $item = $( event.currentTarget ).closest( 'li' ).addClass( 'editing' );
		const $radio = $( 'input[type="radio"]', $item );
		const $versionInput = $( 'input.version-input', $item );
		const $sizeInput = $( 'input.size-input', $item );

		$versionInput.val( $radio.data( 'version' ) );
		$sizeInput.val( $radio.data( 'size' ) );
		$item.addClass( 'editing' );
	};

	/**
	 * Handle the editing of a version label
	 */
	dlpDocumentLink.prototype.exitVersionInfoEdit = function( event ) {
		event.preventDefault();

		const $item = $( event.currentTarget ).closest( 'li' );
		const $radio = $( 'input[type="radio"]', $item );

		if ( event.data ) {
			let value = $( 'input.version-input', $item ).val();
			$( 'input.file-version', $item ).val( value );
			$( 'dl.dlp_version_info dd.link-version', $item ).text( value );
			$radio.data( 'version', value );

			value = $( 'input.version-input', $item ).val();
			$( 'input.url-version', $item ).val( value );
			$( 'dl.dlp_version_info dd.link-version', $item ).text( value );
			$radio.data( 'size', value );

			value = $( 'input.size-input', $item ).val();
			$( 'input.url-size', $item ).val( value );
			$( 'dl.dlp_version_info dd.link-size', $item ).text( value );
			$radio.data( 'size', value );

			dlpDocumentLink.isDirty = true;
		}

		$item.removeClass( 'editing' );
	};

	/**
	 * Handle Remove File
	 */
	dlpDocumentLink.prototype.handleRemoveFile = function( event ) {
		event.preventDefault();

		const $file_name = $( '#dlp_file_name_input' );
		const filename = $file_name.val();
		const $file_id = $( '#dlp_file_id' );
		const file_id = $file_id.val();
		const $file_attached_area = $( '#dlp_file_attached' );
		const $add_file_button = $( '#dlp_add_file_button' );

		$file_attached_area.removeClass( 'active' );
		$file_name.val( '' );
		$file_id.val( '' );
		$add_file_button.text( dlpAdminObject.i18n.add_file );
		$( '#dlp_version_history_list input[type="radio"]' ).prop( 'checked', false );
		$( '#dlp_version_history_list li.selected' ).removeClass( 'selected' );
		$( '#dlp_version_history_list' ).slideUp( 'fast' );

		if ( dlpAdminObject.version_control_mode === 'delete' ) {
			$( '#dlp_version_history_file_toggle' ).toggle( false );
			$( '#dlp_version_history_list li' ).remove();
			$( '#dlp_version_history_list' ).removeClass( 'active' );
		}

		dlpDocumentLink.isDirty = true;
		if ( event.data === true ) {
			event.data = { file_id, filename };
			dlpDocumentLink.prototype.removeVersion( event );
		}
	};

	/**
	 * Handle the removal of a version
	 */
	dlpDocumentLink.prototype.removeVersion = function( event ) {
		event.preventDefault();
		const $target = $( event.currentTarget )
		let $item = null;
		let filename = '';

		if ( $target.hasClass( 'remove-version' ) ) {
			$item = $target.closest( 'li' );
			filename = $( 'a.filename', $item ).text().trim();
		} else if ( $target.attr( 'id', 'dlp_remove_file_button' ) ) {
			$item = $( `#dlp_version-${ event.data.file_id }` ).closest( 'li' );
			filename = event.data.filename;
		}

		if ( ! $item ) {
			return;
		}

		let removeFile = false;

		// eslint-disable-next-line no-alert
		if ( window.confirm( dlpAdminObject.i18n.shall_remove_version.replace( '%s', filename ) ) ) {
			if ( $( '#dlp_version_history_file_list li' ).length < 1 ) {
				$( '#dlp_version_history_file_list' ).slideUp( 'fast' );
				$( '#dlp_version_history_file_toggle' ).hide();
				removeFile = true;
			}

			if ( dlpAdminObject.version_control_mode === 'delete' ) {
				removeFile = true;
			}

			if ( $item.hasClass( 'selected' ) ) {
				$item.remove();
				const $newItem = $( '#dlp_version_history_file_list li' ).first();

				if ( $newItem.length ) {
					const $radio = $( 'input[type="radio"]', $newItem );
					$radio.prop( 'checked', true );
					$( '#dlp_file_name_input' ).val( $radio.data( 'filename' ) );
					$( '#dlp_file_attached .dlp_file_name_text' ).text( $radio.data( 'filename' ) );
					$( '#dlp_file_id' ).val( $radio.val() );
				}
			}

			$item.remove();

			dlpDocumentLink.isDirty = true;
			if ( event.data === true && removeFile ) {
				event.data = false;
				dlpDocumentLink.prototype.handleRemoveFile( event );
			}
		}
	};

	dlpDocumentLink.prototype.cancelFileReplacement = function( filename ) {
		// eslint-disable-next-line no-alert
		return filename && dlpAdminObject.version_control_mode === 'delete' && ! window.confirm( dlpAdminObject.i18n.shall_remove_version.replace( '%s', filename ) );
	};

	/**
	 * Init dlpDocumentLink.
	 */
	new dlpDocumentLink();
} );