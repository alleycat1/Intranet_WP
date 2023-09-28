
( function( $, window, document, undefined ) {
	'use strict';

	const toggleChildSettings = function( $parent ) {
		let show = false;

		const toggleVal = $parent.data( 'toggleVal' ),
			closestAncestorTag = $parent.data( 'ancestorTag' ) || 'tr',
			$children = $parent.closest( '.form-table' ).find( '.' + $parent.data( 'child-class' ) ).closest( closestAncestorTag );

		$children.each( function() {
			if ( 'radio' === $parent.attr( 'type' ) ) {
				show = $parent.prop( 'checked' ) && toggleVal == $parent.val();
			} else if ( 'checkbox' === $parent.attr( 'type' ) ) {
				if ( typeof toggleVal === 'undefined' || 1 == toggleVal ) {
					show = $parent.prop( 'checked' );
				} else {
					show = ! $parent.prop( 'checked' );
				}
			} else if ( 'SELECT' === $parent.prop( 'tagName' ) ) {
				if ( $( this ).find( `.${ $parent.data( 'child-class' ) }-${ $parent.val() }` ).length > 0 ) {
					show = true;
				} else if ( $parent.val() === $parent.data( 'toggle-val' ) ) {
					show = $( this ).find( `.${ $parent.data( 'child-class' ) }` ).length > 0;
				}
			} else {
				show = ( toggleVal == $parent.val() );
			}
			$( this ).toggle( show );
		} );
	};

	$( document ).ready( function() {
		$( '.color-picker' ).wpColorPicker();

		$( '.form-table .dlp-toggle-parent' ).each( function() {
			const $parent = $( this );
			const $children = $parent.closest( '.form-table' ).find( '.' + $parent.data( 'childClass' ) ).closest( 'tr' );

			toggleChildSettings( $parent, $children );

			$parent.on( 'change', function() {
				toggleChildSettings( $parent, $children );
			} );
		} );

		$( '.barn2-help-tip' ).tipTip( {
			attribute: 'data-tip',
			fadeIn: 50,
			fadeOut: 50,
			delay: 200,
			keepAlive: true,
		} );
	} );
}( jQuery, window, document ) );

