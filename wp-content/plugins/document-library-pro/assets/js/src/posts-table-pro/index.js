( function( $, window, params ) {
	'use strict';

	const blockConfig = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.7
		}
	};

	function addRowAttributes( $row ) {
		return function( key, value ) {
			if ( 'class' === key ) {
				$row.addClass( value );
			} else {
				$row.attr( key, value );
			}
		};
	}

	function appendFilterOptions( $select, items, depth ) {
		depth = ( typeof depth !== 'undefined' ) ? depth : 0;

		// Add each term to filter drop-down
		$.each( items, function( i, option ) {
			let name = option.name,
				value = 'slug' in option ? option.slug : name,
				pad = '';

			if ( depth ) {
				pad = Array( ( depth * 2 ) + 1 ).join( '\u00a0' ) + '\u2013\u00a0';
			}

			$select.append( '<option value="' + value + '">' + pad + name + '</option>' );

			if ( 'children' in option ) {
				appendFilterOptions( $select, option.children, depth + 1 );
			}
		} );
	}

	// Reduce the filter options to only those which are in the required list or are parents of such items.
	function reduceFilterOptions( options, required ) {
		let option,
			result = JSON.parse( JSON.stringify( options ) ); // clone the terms array, so the original is unmodified.

		for ( let i = result.length - 1; i >= 0; i-- ) {
			option = result[i];

			if ( option.hasOwnProperty( 'children' ) ) {
				option.children = reduceFilterOptions( option.children, required );

				if ( 0 === option.children.length ) {
					// No children left, so delete property from term.
					delete option.children;
				}
			}

			// Keep the term if it's found in requiredSlugs or it has children.
			if ( -1 === required.indexOf( option.slug ) && ! option.hasOwnProperty( 'children' ) ) {
				result.splice( i, 1 );
			}
		}

		return result;
	}

	function setFilterOptions( $select, heading, options ) {
		// Add the default option to the list.
		let allOptions = [ { slug: "", name: heading } ].concat( options );

		// Add the options to the filter.
		$select.empty();

		// Add the <option> elements to filter
		appendFilterOptions( $select, allOptions );
	}

	function initMedia( $el ) {
		if ( ! $el || ! $el.length ) {
			return;
		}

		// Replace our custom class names with the correct ones before running the media init functions.
		$el.find( '.ptp-playlist' ).addClass( 'wp-playlist' );
		$el.find( '.ptp-video-shortcode' ).addClass( 'wp-video-shortcode' );
		$el.find( '.ptp-audio-shortcode' ).addClass( 'wp-audio-shortcode' );

		if ( typeof WPPlaylistView !== 'undefined' ) {
			// Initialise audio and video playlists
			$el.find( '.wp-playlist:not(:has(.mejs-container))' ).each( function() {
				return new WPPlaylistView( { el: this } );
			} );
		}

		if ( 'wp' in window && 'mediaelement' in window.wp ) {
			$( window.wp.mediaelement.initialize );
		}

		// Run fitVids to ensure videos in table have correct proportions
		if ( $.fn.fitVids ) {
			$el.fitVids();
		}
	}

	/*
	 * A renderer for $.fn.DataTables.Responsive to display hidden data when using responsive child rows.
	 *
	 * @see https://datatables.net/reference/option/responsive.details.renderer
	 */
	function responsiveRendererListHidden() {
		return function( api, rowIdx, columns ) {
			let rowClass = api.row( rowIdx ).node().className;

			let data = $.map( columns, function( col ) {
				let klass = col.className ? 'class="' + col.className + '"' : '';

				return col.hidden ?
					'<li ' + klass + ' data-dtr-index="' + col.columnIndex + '" data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
					'<span class="dtr-title">' + col.title + '</span> ' +
					'<span class="dtr-data">' + col.data + '</span>' +
					'</li>' :
					'';
			} ).join( '' );

			return data ?
				$( '<ul data-dtr-index="' + rowIdx + '" class="dtr-details ' + rowClass + '" />' ).append( data ) :
				false;
		}
	}

	/*
	 * A renderer for $.fn.DataTables.Responsive to display all visible content for a row when using modal responsive display.
	 *
	 * @see https://datatables.net/reference/option/responsive.details.renderer
	 */
	function responsiveRendererAllVisible( options ) {
		options = $.extend( {
			tableClass: ''
		}, options );

		return function( api, rowIdx, columns ) {
			let rowClass = api.row( rowIdx ).node().className;

			let innerData = $.map( columns, function( col ) {
				// Bail if column data is hidden.
				if ( ! api.column( col.columnIndex ).visible() ) {
					return '';
				}

				let klass = col.className ? 'class="' + col.className + '"' : '';

				return '<li ' + klass + ' data-dtr-index="' + col.columnIndex + '" data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
					'<span class="dtr-title">' + col.title + '</span> ' +
					'<span class="dtr-data">' + col.data + '</span>' +
					'</li>';
			} ).join( '' );

			let data = '<ul data-dtr-index="' + rowIdx + '" class="dtr-details ' + rowClass + '" >' + innerData + '</ul>';
			let $modal = $( '<div class="' + options.tableClass + '" />' ).append( data );

			// Initialise media for modal.
			initMedia( $modal );

			// Prevent clicking on featured image in modal by removing the link.
			let $img = $modal.find( '.posts-table-image-wrapper > a > img' );

			if ( $img.length ) {
				$img.parent().parent().prepend( $img );
				$img.siblings( 'a' ).remove();
			}

			return $modal;
		};
	}

	function select2Enabled() {
		return ( 'select2' in $.fn ) && params.enable_select2;
	}

	/******************************************
	 * POSTSTABLE PROTOTYPE
	 ******************************************/

	let PostsTable = function( $table ) {
		// Properties
		this.$table = $table;
		this.id = $table.attr( 'id' );
		this.dataTable = null;
		this.config = null;
		this.ajaxData = [];
		this.hasAdminBar = $( '#wpadminbar' ).length > 0;

		this.$filters = [];
		this.$tableWrapper = this.$table.parent();
		this.$pagination = this.$tableWrapper.find( '.dataTables_paginate' );
		this.$tableControls = this.$tableWrapper.find( '.posts-table-controls' );

		// Register events
		$table
			.on( 'draw.dt', { table: this }, onDraw )
			.on( 'init.dt', { table: this }, onInit )
			.on( 'page.dt', { table: this }, onPage )
			.on( 'processing.dt', { table: this }, onProcessing )
			.on( 'responsive-display.dt', { table: this }, onResponsiveDisplay )
			.on( 'search.dt', { table: this }, onSearch )
			.on( 'xhr.dt', { table: this }, onAjaxLoad );

		$( window ).on( 'load.ptp', { table: this }, onWindowLoad );

		// Show the table - loading class removed on init.dt
		$table.addClass( 'loading' ).css( 'visibility', 'visible' );
	};

	PostsTable.prototype.buildConfig = function() {
		let config = {
			retrieve: true, // so subsequent calls to DataTable() return the same API instance
			responsive: $.fn.dataTable.Responsive.defaults,
			processing: true, // display 'processing' indicator when loading
			orderMulti: false, // disable ordering by multiple columns at once
			language: params.language
		};

		// Get config for this table instance.
		let tableConfig = this.$table.data( 'config' );

		if ( tableConfig ) {
			// We need to do deep copy for the 'language' property to be merged correctly.
			config = $.extend( true, {}, config, tableConfig );
		}

		// In info message, when only 1 result found, replace plural with singular form (e.g. 'post' instead of 'posts').
		if ( 'totalsPlural' in config.language && 'info' in config.language && -1 !== config.language.info.indexOf( config.language.totalsPlural ) ) {
			config.infoCallback = function( settings, start, end, max, total, pre ) {
				if ( pre && 1 === total ) {
					pre = pre.replace( config.language.totalsPlural, config.language.totalsSingle );
				}
				return pre;
			};
		}

		// Config for server side processing
		if ( config.serverSide && 'ajax_url' in params ) {
			config.deferRender = true;
			config.ajax = {
				url: params.ajax_url,
				type: 'POST',
				data: {
					table_id: this.id,
					action: params.ajax_action,
					_ajax_nonce: params.ajax_nonce
				},
				xhrFields: {
					withCredentials: true
				}
			};
		}

		// Set responsive display and renderer functions.
		if ( ( typeof config.responsive === 'object' ) && 'details' in config.responsive && 'display' in config.responsive.details ) {
			if ( 'child_row' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.childRow;
				config.responsive.details.renderer = responsiveRendererListHidden();
			} else if ( 'child_row_visible' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.childRowImmediate;
				config.responsive.details.renderer = responsiveRendererListHidden();
			} else if ( 'modal' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.modal();
				config.responsive.details.renderer = responsiveRendererAllVisible( { tableClass: params.table_class } );
			}
		}

		// Legacy config for language (we now use Gettext for translation).
		if ( 'lang_url' in params ) {
			config.language = { url: params.lang_url };
		}

		return config;
	};

	PostsTable.prototype.getDataTable = function() {
		if ( ! this.dataTable ) {
			this.init();
		}

		return this.dataTable;
	};

	PostsTable.prototype.init = function() {
		let table = this;

		table.$table.trigger( 'preInit.ptp', [ table ] );

		// Initialize DataTables instance.
		table.config = table.buildConfig();
		table.dataTable = table.$table.DataTable( table.config );

		return table;
	};

	PostsTable.prototype.initFilters = function() {
		let table = this,
			filtersData = table.$table.data( 'filters' );

		if ( ! filtersData ) {
			return table;
		}

		let $filtersWrap = $( '<div class="posts-table-select-filters" id="' + table.id + '_select_filters" />' ),
			filtersAdded = 0;

		if ( 'filterBy' in params.language && params.language.filterBy ) {
			$filtersWrap.append( '<label class="filter-label">' + params.language.filterBy + '</label>' );
		}

		// Build the filters
		for ( let column in filtersData ) {
			let filterData = filtersData[column];

			// Don't add the filter if there are no items.
			if ( ! ( 'terms' in filterData ) || 0 === filterData.terms.length ) {
				continue;
			}

			// Create <select> for the filter.
			let selectAtts = {
				'name': 'ptp_filter_' + column,
				'data-tax': filterData.taxonomy,
				'data-column': column,
				'data-search-column': filterData.searchColumn,
				'aria-label': filterData.heading,
				'data-placeholder': filterData.heading
			};

			if ( filterData.class ) {
				selectAtts['class'] = filterData.class;
			}

			let $select = $( '<select/>' ).attr( selectAtts );

			// Append the options.
			setFilterOptions( $select, filterData.heading, filterData.terms );

			// Append select to wrapper
			$select
				.on( 'change.ptp', { table: table }, onFilterChange )
				.appendTo( $filtersWrap );

			filtersAdded++;
		} // foreach filter

		// Add filters to table - before search box if present, otherwise as first element above table
		if ( filtersAdded > 0 ) {
			// Add filters to table
			let $searchBox = table.$tableControls.find( '.dataTables_filter' );

			if ( $searchBox.length ) {
				$filtersWrap.prependTo( $searchBox.closest( '.posts-table-controls' ) );
			} else {
				$filtersWrap.prependTo( table.$tableControls.filter( '.posts-table-above' ) );
			}
		}

		// Store filters here as we use this when searching columns.
		table.$filters = table.$tableControls.find( '.posts-table-select-filters select' );

		// Update filters so only applicable options are shown (for standard loading).
		table.updateFilterOptions( table.$filters );

		return table;
	};

	PostsTable.prototype.initPhotoswipe = function() {
		let table = this;

		if ( typeof PhotoSwipe === 'undefined' || typeof PhotoSwipeUI_Default === 'undefined' ) {
			return table;
		}

		table.$table.on( 'click.ptp', '.posts-table-gallery__image a', onOpenPhotoswipe );

		return table;
	};

	PostsTable.prototype.initResetButton = function() {
		let table = this;

		if ( ! table.config.resetButton ) {
			return table;
		}

		let $resetButton =
			$( '<div class="posts-table-reset"><a class="reset" href="#">' + params.language.resetButton + '</a></div>' )
				.on( 'click.ptp', 'a', { table: table }, onReset );

		// Append reset button
		let $firstChild = table.$tableControls.filter( '.posts-table-above' ).children( '.posts-table-select-filters, .dataTables_length, .dataTables_filter' ).eq( 0 );

		if ( $firstChild.length ) {
			$firstChild.append( $resetButton );
		} else {
			table.$tableControls.filter( '.posts-table-above' ).prepend( $resetButton );
		}

		return table;
	};

	PostsTable.prototype.initSearchOnClick = function() {
		let table = this;

		if ( table.config.clickFilter ) {
			// 'search_on_click' - add click handler for relevant links. When clicked, the table will filter by the link text.
			table.$table.on( 'click.ptp', 'a[data-column]', { table: table }, onClickToSearch );
		}

		return this;
	};

	PostsTable.prototype.initSelect2 = function() {
		let table = this;

		if ( ! select2Enabled() ) {
			return table;
		}

		let select2Options = {
			dropdownCssClass: 'posts-table-dropdown',
			escapeMarkup: function( markup ) {
				// Empty function to disable escaping - this is handled by WordPress and PTP.
				return markup;
			}
		};

		// Initialize select2 for search filters.
		if ( table.$filters.length ) {

			// Maybe adjust width of filters prior to initializing select2.
			table.$filters.each( function() {
				if ( $( this ).innerWidth() === $( this ).width() ) {
					// No padding on select element (e.g. Safari) so we adjust the width upwards slightly to ensure
					// select2 element is wide enough for select items.
					$( this ).width( $( this ).width() + 22 );
				}
			} );

			table.$filters.select2(
				Object.assign( select2Options, { minimumResultsForSearch: 5 } )
			);
		}

		// Initialize select2 for page length - minimumResultsForSearch of -1 disables the search box.
		table.$tableControls.find( '.dataTables_length select' ).select2(
			Object.assign( select2Options, { minimumResultsForSearch: -1 } )
		);

		return table;
	};

	PostsTable.prototype.processAjaxData = function() {
		let table = this;

		if ( ! table.config.serverSide || ! table.ajaxData.length ) {
			return;
		}

		let $rows = table.$table.find( 'tbody tr' );

		// Add row attributes to each row in table
		for ( let i = 0; i < table.ajaxData.length; i++ ) {
			if ( '__attributes' in table.ajaxData[i] && $rows.eq( i ).length ) {
				$.each( table.ajaxData[i].__attributes, addRowAttributes( $rows.eq( i ) ) );
			}
		}

		return table;
	};

	PostsTable.prototype.scrollToTop = function() {
		let table = this,
			scroll = table.config.scrollOffset;

		if ( scroll !== false && ! isNaN( scroll ) ) {
			let tableOffset = table.$tableWrapper.offset().top - scroll;

			if ( table.hasAdminBar ) { // Adjust offset for WP admin bar
				tableOffset -= 32;
			}

			$( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
		}

		return table;
	};

	PostsTable.prototype.showHidePagination = function() {
		let table = this;

		// Hide pagination if we only have 1 page
		if ( table.$pagination.length ) {
			let pageInfo = table.getDataTable().page.info();

			if ( pageInfo && pageInfo.pages <= 1 ) {
				table.$pagination.hide( 0 );
			} else {
				table.$pagination.show();
			}
		}

		return table;
	};

	PostsTable.prototype.updateFilterOptions = function( $filters ) {
		let table = this;

		// Updating filter options based on table contents is only supported for standard loading.
		if ( ! $filters.length || table.config.serverSide ) {
			return table;
		}

		let filtersData = table.$table.data( 'filters' );

		$filters.each( function() {
			let $select = $( this ),
				column = $select.data( 'column' ),
				val = $select.val(); // Store value so we can reset later.

			if ( ! ( column in filtersData ) ) {
				return;
			}

			let filterData = filtersData[column],
				options = filterData.terms;

			// Find all data in search column so we can restrict filter to relevant data only.
			let searchData = table.getDataTable()
				.column( $select.data( 'searchColumn' ) + ':name', { search: 'applied' } )
				.data()
				.filter( function( val ) {
					return val.length > 0;
				} );

			if ( searchData.any() ) {
				let sep = params.filter_term_separator;
				options = reduceFilterOptions( options, searchData.join( sep ).split( sep ) );
			} else {
				// No search data so filter will be empty.
				options = [];
			}

			setFilterOptions( $select, filterData.heading, options );

			// Restore previous selected value.
			$select.val( val );
		} );

		return table;
	};

	/******************************************
	 * EVENTS
	 ******************************************/

	function onAjaxLoad( event, settings, json ) {
		let table = event.data.table;

		if ( null !== json && 'data' in json && $.isArray( json.data ) ) {
			table.ajaxData = json.data;
		}

		table.$table.trigger( 'lazyload.ptp', [ table ] );
	}

	function onClickToSearch( event ) {
		let $link = $( this ),
			table = event.data.table,
			columnName = $link.data( 'column' ),
			slug = $link.children( '[data-slug]' ).length ? $link.children( '[data-slug]' ).data( 'slug' ) : '';

		// Bail if no term slug to search.
		if ( '' === slug ) {
			return true;
		}

		// If we have filters, update selection to match the value being searched for, and let onFilterChange handle the column searching.
		if ( table.$filters.length ) {
			let $filter = table.$filters.filter( '[data-column="' + columnName + '"]' ).first();

			// Check if the filter for this column exists and has the clicked value present. If so, we use the filter to perform the search and exit early.
			if ( $filter.length && $filter.children( 'option[value="' + slug + '"]' ).length ) {
				$filter.val( slug ).trigger( 'change' );

				table.scrollToTop();
				return false;
			}
		}

		let dataTable = table.getDataTable(),
			column = dataTable.column( columnName + ':name' );

		if ( table.config.serverSide ) {
			column.search( slug ).draw();
		} else {
			// Standard loading uses the link text to search column.
			let searchVal = '(^|, )' + $.fn.dataTable.util.escapeRegex( $link.text() ) + '(, |$)';
			column.search( searchVal, true, false ).draw();
		}

		table.scrollToTop();
		return false;
	}

	function onDraw( event ) {
		let table = event.data.table;

		// Add row attributes to each <tr> if using lazy load
		if ( table.config.serverSide ) {
			table.processAjaxData();
		}

		initMedia( table.$table );

		table
			.showHidePagination()
			.$table.trigger( 'draw.ptp', [ table ] );
	}

	function onFilterChange( event, setValueOnly ) {
		let $select = $( this ),
			table = event.data.table;

		if ( setValueOnly ) {
			return true;
		}

		// Disable onSearch to prevent this running during onFilterChange.
		table.$table.off( 'search.dt', onSearch );

		let value = $select.val(),
			column = $select.data( 'column' ),
			dataTable = table.getDataTable(),
			searchColumn = dataTable.column( $select.data( 'searchColumn' ) + ':name' );

		if ( table.config.serverSide ) {
			// Lazy load search.
			searchColumn.search( value ).draw();
		} else {
			// Standard load search.
			let sep = params.filter_term_separator;

			if ( '' !== value ) {
				// Escape search value before adding to search regex pattern.
				value = $.fn.dataTable.util.escapeRegex( value );
				value = `(^|${sep})${value}(${sep}|$)`;
			}

			searchColumn.search( value, true, false ).draw();
		}

		let $thisFilterGroup = table.$filters.filter( '[data-column="' + column + '"]' ),
			$otherFilters = table.$filters.not( $thisFilterGroup );

		// If we have filters above and below table, update corresponding filter to match.
		$thisFilterGroup
			.not( $select[0] )
			.val( value )
			.trigger( 'change', [ true ] );

		// Update other filters to show only relevant search items.
		table.updateFilterOptions( $otherFilters );

		// Re-enable onSearch.
		table.$table.on( 'search.dt', { table: table }, onSearch );
	}

	function onInit( event ) {
		let table = event.data.table;

		table.$tableWrapper = table.$table.parent();
		table.$pagination = table.$tableWrapper.find( '.dataTables_paginate' );
		table.$tableControls = table.$tableWrapper.find( '.posts-table-controls' );

		table
			.initFilters()
			.initSelect2()
			.initResetButton()
			.initSearchOnClick()
			.initPhotoswipe()
			.showHidePagination();

		// fitVids will run on every draw event for lazy load, but for standard loading
		// we need to run here as well because initMedia only runs on subsequent draw events.
		if ( ! table.config.serverSide && $.fn.fitVids ) {
			table.$table.fitVids();
		}

		table.$table
			.removeClass( 'loading' )
			.trigger( 'init.ptp', [ table ] );
	}

	function onOpenPhotoswipe( event ) {
		event.stopPropagation();

		// Only open for click events.
		if ( 'click' !== event.type ) {
			return false;
		}

		let pswpElement = $( '.pswp' )[0],
			$target = $( event.target ),
			$galleryImage = $target.closest( '.posts-table-gallery__image' ),
			items = [];

		if ( $galleryImage.length > 0 ) {
			$galleryImage.each( function( i, el ) {
				let img = $( el ).find( 'img' ),
					large_image_src = img.attr( 'data-large_image' ),
					large_image_w = img.attr( 'data-large_image_width' ),
					large_image_h = img.attr( 'data-large_image_height' ),
					item = {
						src: large_image_src,
						w: large_image_w,
						h: large_image_h,
						title: ( img.attr( 'data-caption' ) && img.attr( 'data-caption' ).length ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
					};
				items.push( item );
			} );
		}

		const options = {
			index: 0,
			shareEl: false,
			closeOnScroll: false,
			history: false,
			hideAnimationDuration: 0,
			showAnimationDuration: 0
		};

		// Initializes and opens PhotoSwipe
		let photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
		photoswipe.init();

		return false;
	}

	function onPage( event ) {
		// Animate back to top of table on next/previous page event
		event.data.table.scrollToTop();
	}

	function onProcessing( event, settings, processing ) {
		if ( processing ) {
			event.data.table.$table.block( blockConfig );
		} else {
			event.data.table.$table.unblock();
		}
	}

	function onReset( event ) {
		event.preventDefault();

		let table = event.data.table,
			dataTable = table.getDataTable();

		// Disable onSearch to prevent this running during reset.
		table.$table.off( 'search.dt', onSearch );

		// Reset responsive child rows
		table.$table.find( 'tr.child' ).remove();
		table.$table.find( 'tr.parent' ).removeClass( 'parent' );

		// Clear search for all filtered columns
		dataTable.columns( 'th[data-searchable="true"]' ).search( '' );

		// Reset ordering
		let initialOrder = table.$table.attr( 'data-order' );

		if ( initialOrder.length ) {
			let orderArray = initialOrder.replace( /[\[\]" ]+/g, '' ).split( ',' );

			if ( 2 === orderArray.length ) {
				dataTable.order( orderArray );
			}
		}

		// Find initial search term
		let searchTerm = ( 'search' in table.config && 'search' in table.config.search ) ? table.config.search.search : '';

		// Reset global search and page length
		dataTable
			.search( searchTerm )
			.page.len( table.config.pageLength )
			.draw( true );

		if ( select2Enabled() ) {
			// If using select2, we need to trigger change to update the value.
			table.$tableControls.find( '.dataTables_length select' ).trigger( 'change' );
		}

		// Reset filters
		if ( table.$filters.length ) {
			table.$filters.val( '' ).trigger( 'change', [ true ] );
			table.updateFilterOptions( table.$filters );
		}

		// Re-enable onSearch.
		table.$table.on( 'search.dt', { table: table }, onSearch );
	}

	function onResponsiveDisplay( event, datatable, row, showHide ) {
		if ( showHide && ( typeof row.child() !== 'undefined' ) ) {
			// Initialise elements in child row
			initMedia( row.child() );

			let table = event.data.table;

			table.$table.trigger( 'responsiveDisplay.ptp', [ table, datatable, row, showHide ] );
		}
	}

	function onSearch( event, settings ) {
		// Update filter options during global search. This also runs during column searches (i.e. filter change) so
		// we disable this event handler for those events.
		let table = event.data.table;
		table.updateFilterOptions( table.$filters );
	}

	function onWindowLoad( event ) {
		let table = event.data.table;

		// Recalc column sizes on window load (e.g. to correctly contain media playlists)
		table.getDataTable()
			.columns.adjust()
			.responsive.recalc();

		table.$table.trigger( 'load.ptp', [ table ] );
	}

	/******************************************
	 * JQUERY PLUGIN
	 ******************************************/

	/**
	 * jQuery plugin to create a post table for the current set of matched elements.
	 *
	 * @returns jQuery object - the set of matched elements the function was called with (for chaining)
	 */
	$.fn.postsTable = function() {
		return this.each( function() {
			let table = new PostsTable( $( this ) );
			table.init();
		} );
	};

	$( function() {
		if ( 'DataTable' in $.fn && $.fn.DataTable.ext ) {
			// Change DataTables error reporting to throw rather than alert
			$.fn.DataTable.ext.errMode = 'throw';
		}

		// Initialise all post tables
		$( '.' + params.table_class ).postsTable();
	} );

} )( jQuery, window, posts_table_params );