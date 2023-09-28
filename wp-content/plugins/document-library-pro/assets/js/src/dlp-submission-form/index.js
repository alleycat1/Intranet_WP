import TomSelect from 'tom-select';
import { decodeEntities } from '@wordpress/html-entities';

( function( window, document, $, undefined ) {
	'use strict';

	const dlpSubmission = {};

	/**
	 * Initialize the script.
	 */
	dlpSubmission.init = function() {
		dlpSubmission.cacheSelectors();
		dlpSubmission.setupDropdowns();
		dlpSubmission.setupUploader();
		dlpSubmission.setupLoader();
	};

	/**
	 * Cache selectors.
	 */
	dlpSubmission.cacheSelectors = function() {
		dlpSubmission.form = $( '#dlp-submit-form' );
		dlpSubmission.taxonomyDropdowns = $( '.dlp-taxonomy-select' );
		dlpSubmission.docTypeSelector = $( '.dlp-document-type-selector' );
		dlpSubmission.docFileSelector = $( '#document_link' );
		dlpSubmission.docFileUploader = $( '#document_file' );
		dlpSubmission.docFileURL = $( '#document_url' );
	};

	/**
	 * Find the parent fieldset element of a given input.
	 *
	 * @param {Object} element
	 */
	function findParentContainer( element ) {
		return element.closest( 'fieldset' );
	}

	/**
	 * Query the rest api for terms given the taxonomy.
	 *
	 * @param {string} taxonomy
	 * @return {Object} Ajax function object
	 */
	dlpSubmission.getTaxonomyTerms = function( taxonomy ) {
		const endpoint = DLP_Frontend_Submission.rest + 'terms';

		return $.ajax( {
			url: endpoint,
			method: 'GET',
			beforeSend( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', DLP_Frontend_Submission.restNonce );
			},
			data: {
				taxonomy,
			},
		} );
	};

	/**
	 * Parse the list of terms received from the rest api into an array
	 * compatible with the TomSelect script.
	 *
	 * @param {Array} terms
	 * @return {Array} list of terms
	 */
	dlpSubmission.parseTermsToOptions = function( terms = [] ) {
		const parsed = [];

		terms.forEach( ( term, index ) => {
			parsed.push(
				{
					value: term.term_id,
					text: decodeEntities( term.name ),
				}
			);
		} );

		return parsed;
	};

	/**
	 * Setup dropdowns via TomSelect
	 *
	 * @return {void}
	 */
	dlpSubmission.setupDropdowns = function() {
		if ( ! dlpSubmission.taxonomyDropdowns.length ) {
			return;
		}

		dlpSubmission.taxonomyDropdowns.each( ( index, element ) => {
			const initialSettings = {
				options: [],
				plugins: {
					remove_button: {
						title: DLP_Frontend_Submission.labels.removeItem,
					},
				},
				placeholder: element.getAttribute( 'placeholder' ) ?? '',
			};

			// Initialize the dropdown.
			new TomSelect( element, initialSettings );

			// Disable the input.
			element.tomselect.disable();

			dlpSubmission.getTaxonomyTerms( element.getAttribute( 'data-taxonomy' ) )
				.done( function( response ) {
					// Add options loaded via the api.
					element.tomselect.addOptions( dlpSubmission.parseTermsToOptions( response.terms ) );

					// Now unlock the dropdown.
					element.tomselect.enable();
				} ).fail( function( response ) {
					console.error( response );
				} ).always( function() {
					const selectedTerms = element.getAttribute( 'data-selected-terms' );

					if ( selectedTerms ) {
						const options = selectedTerms.split( ',' );
						element.tomselect.setValue( options );
					}
				} );
		} );
	};

	/**
	 * Conditionally display field values based on given type.
	 *
	 * @param {string} type
	 */
	function displayUploaderInput( type = 'none' ) {
		if ( type === 'file' ) {
			findParentContainer( dlpSubmission.docFileUploader ).show();
			findParentContainer( dlpSubmission.docFileURL ).hide();
		} else if ( type === 'url' ) {
			findParentContainer( dlpSubmission.docFileUploader ).hide();
			findParentContainer( dlpSubmission.docFileURL ).show();
		} else {
			findParentContainer( dlpSubmission.docFileUploader ).hide();
			findParentContainer( dlpSubmission.docFileURL ).hide();
		}
	}

	/**
	 * Handle display of the upload inputs.
	 */
	dlpSubmission.setupUploader = function() {
		// On first load - automatically handle the display based on the selected value.
		const selection = dlpSubmission.docFileSelector.val();

		displayUploaderInput( selection );

		// Now listen for changes and then change visibility.
		dlpSubmission.docFileSelector.change( function() {
			const selected = this.value;
			displayUploaderInput( selected );
		} );
	};

	/**
	 * Handle the form loader.
	 */
	dlpSubmission.setupLoader = function() {
		dlpSubmission.form.submit( function() {
			$( this ).find( '[type=submit]' ).addClass( 'loading' );
		} );
	};

	window.dlpSubmission = dlpSubmission;

	dlpSubmission.init();
}( window, document, jQuery ) );
