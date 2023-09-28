(function( $ ) {
    'use strict';

    var window_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
        docs = {},
        cats = [],
        timer,
        rAF = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || function( callback ) { return window.setTimeout( callback, 1000 / 60 ); };

    $( window ).on( 'resize orientationchange', function() {
        clearTimeout( timer );
        timer = setTimeout( function() { rAF( resize ); }, 100 );
    });

    $( document ).on( 'click', '.fivo-docs-subcategory-title', function( event ) {
        event.preventDefault();

        var $title = $( this ),
        $all_titles = $title.parents( '.fivo-docs-subcategories' ).find( '.fivo-docs-subcategory-title' );

        if ( $title.hasClass( 'is-active' ) ) {
            $title.removeClass( 'is-active' ).next().slideUp( 200 );
            setTimeout( function() {
                $title.removeClass( 'is-open' );
                update_items();
            }, 200 );
        } else {
            $all_titles.removeClass( 'is-active' ).next().slideUp( 200 );
            $title.addClass( 'is-active' ).next().slideDown( 200 );
            setTimeout( function() {
                $title.addClass( 'is-open' );
                $all_titles.removeClass( 'is-open' );
                update_items();
            }, 200 );
        }
    });

    $( '.fivo-docs-masonry' ).each( function( index, value ) {

        var $wrapper = $( this ),
            $cats = $wrapper.find( '.fivo-docs-category' ),
            columns = $wrapper.data( 'fivo-docs-columns' ),
            items = [];

        $cats.each( function( index, value ) {
            items.push( $( this ) );
        });

        docs[index] = {
            'element': this,
            'columns': columns,
            'items' : items
        }

        cats[index] = items;

        $wrapper.addClass( 'is-ready' );

    });

    var update_items = function() {
        $.each( docs, function( index, doc ) {
            var items = cats[index],
                items_number = doc.items.length;
            cats[index] = [];
            for ( var i = 0; i < items_number; i++ ) {
                cats[index][i] = $( items[i][0] );
            }
        });
    };

    var masonry = function() {
        $.each( docs, function( index, doc ) {

            var column = 0,
                col_number = '',
                items = cats[index],
                items_number = doc.items.length;

            cats[index] = [];
            $.each( doc.columns, function( width, col ) {
                if ( width <= window_width ) {
                    col_number = parseInt( col );
                } else if ( '' == col_number ) {
                    col_number = 1;
                }
            });

            $( doc.element ).empty();

            for ( var i = 0; i < col_number; i++ ) {
                $( doc.element ).append( $( '<div class="fivo-docs-column"></div>' ) );
            }

            for ( var i = 0; i < items_number; i++ ) {

                $( doc.element ).find( '.fivo-docs-column:eq( '+ column +' )' ).append( $( items[i][0] ) );

                cats[index][i] = $( items[i][0] );
                column++;

                if ( column == col_number ) {
                    column = 0; // start over from the first column
                }

            }

        });
    };

    var resize = function() {
        window_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        masonry();
    };
    resize();

})( jQuery );
