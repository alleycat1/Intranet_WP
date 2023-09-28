import { debounce } from 'lodash';

jQuery( function( $ ) {
	/**
	 * Grid JS
	 */
	class dlpGrid {
		constructor() {
			$( document ).on(
				'click',
				'.dlp-grid-paginate-button',
				this.handleFetchGrid
			);
			$( document ).on(
				'click',
				'.dlp-grid-card-featured-img',
				this.openPhotoswipe
			);
			$( document ).on(
				'input',
				'.dlp-grid-search input[type="search"]',
				debounce( this.handleGridSearch, 300 )
			);
			$( document ).on(
				'click',
				'.dlp-grid-reset',
				this.handleGridReset
			);
		}

		/**
		 * Fetch Grid
		 *
		 * @param  event
		 */
		handleFetchGrid( event ) {
			const $this = $( this );

			if ( $this.hasClass( 'disabled' ) || $this.hasClass( 'current' ) ) {
				return;
			}

			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const gridId = $gridContainer.attr( 'id' );
			const pageNumber = $( this ).data( 'page-number' );

			let searchQuery = '';

			// if we are in a folder search then we need to get the search query from the folder search input
			if ( $this.parents( '.dlp-folders-search-results' ).first()?.length ) {
				searchQuery = $this.parents( '.dlp-folders-container' )
					.first()
					.find( '.dlp-folders-search input[type="search"]' )
					.val();
			} else {
				searchQuery = $gridContainer
					.find( '.dlp-grid-search input[type="search"]' )
					.val();
			}

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			if ( searchQuery && searchQuery.length < dlp_grid_params.ajax_min_search_term_len ) {
				searchQuery = '';
			}

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					page_number: pageNumber,
					search_query: searchQuery,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function( response ) {
				$( 'html, body' ).animate(
					{
						scrollTop: $gridContainer.offset().top - 50,
					},
					300
				);

				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		/**
		 * Fetch Grid Search
		 *
		 * @param  event
		 */
		handleGridSearch( event ) {
			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const gridId = $gridContainer.attr( 'id' );
			let searchQuery = $gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.val();
			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			// Sync Multiple Search Inputs
			$gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.not( this )
				.each( function( index ) {
					$( this ).val( $this.val() );
				} );

			if ( searchQuery.length < dlp_grid_params.ajax_min_search_term_len ) {
				searchQuery = '';
			}

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: searchQuery,
					page_number: 1,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		/**
		 * Fetch Grid Search
		 *
		 * @param  event
		 */
		handleGridReset( event ) {
			event.preventDefault();

			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const gridId = $gridContainer.attr( 'id' );
			const $searchInputs = $gridContainer.find(
				'.dlp-grid-search input[type="search"]'
			);

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			$searchInputs.each( function( index ) {
				$( this ).val( '' );
			} );

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: '',
					page_number: 1,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );
		}

		/**
		 * Open Lightbox
		 *
		 * @param  event
		 */
		openPhotoswipe( event ) {
			event.stopPropagation();

			const pswpElement = $( '.pswp' )[ 0 ];
			const $img = $( this ).find( 'img' );

			if ( $img.length < 1 ) {
				return;
			}

			const items = [
				{
					src: $img.attr( 'data-large_image' ),
					w: $img.attr( 'data-large_image_width' ),
					h: $img.attr( 'data-large_image_height' ),
					title:
						$img.attr( 'data-caption' ) &&
						$img.attr( 'data-caption' ).length
							? $img.attr( 'data-caption' )
							: $img.attr( 'title' ),
				},
			];

			const options = {
				index: 0,
				shareEl: false,
				closeOnScroll: false,
				history: false,
				hideAnimationDuration: 0,
				showAnimationDuration: 0,
			};

			// Initializes and opens PhotoSwipe
			const photoswipe = new PhotoSwipe(
				pswpElement,
				PhotoSwipeUI_Default,
				items,
				options
			);
			photoswipe.init();

			return false;
		}
	}

	function dlpPopulateGridHtml( $gridContainer, response ) {
		const $gridCardsContainer = $gridContainer.find(
			'.dlp-grid-documents'
		);
		const $gridPaginationFooter = $gridContainer.find(
			'footer .dlp-grid-pagination'
		);
		const $gridPaginationHeader = $gridContainer.find(
			'header .dlp-grid-pagination'
		);
		const $gridTotalsFooter = $gridContainer.find(
			'footer .dlp-grid-totals'
		);
		const $gridTotalsHeader = $gridContainer.find(
			'header .dlp-grid-totals'
		);

		if ( response.grid ) {
			$gridCardsContainer.replaceWith( response.grid );
		}

		if ( $gridPaginationFooter.length > 0 && response.pagination ) {
			$gridPaginationFooter.replaceWith( response.pagination.footer );
		}

		if ( $gridPaginationHeader.length > 0 && response.pagination ) {
			$gridPaginationHeader.replaceWith( response.pagination.header );
		}

		if ( $gridTotalsFooter.length > 0 && response.totals ) {
			$gridTotalsFooter.replaceWith( response.totals.footer );
		}

		if ( $gridTotalsHeader.length > 0 && response.totals ) {
			$gridTotalsHeader.replaceWith( response.totals.header );
		}
	}

	/**
	 * Init dlpGrid.
	 */
	new dlpGrid();
} );
