(function( $ ) {
    'use strict';

    // Create a new category filter that we'll later use
    var FivoDocsFilters = wp.media.view.AttachmentFilters.extend({
        id: 'media-attachment-fivo-docs-filters',
        createFilters: function() {
            var filters = {},
                taxonomy = fivo_docs_admin.category_taxonomy,
                terms = fivo_docs_admin.terms,
                all_items = fivo_docs_admin.taxonomy_all_items;

            filters.all = {
                text:  all_items,
                props: {},
                priority: 1,
            };
            filters['all']['props'][taxonomy] = '';

            _.each( terms, function( term, index ) {
                filters[ index ] = {
                    text: term.name,
                    priority: index + 2,
                    props: {}
                };
                filters[index]['props'][taxonomy] = term.slug;
            });

            this.filters = filters;
        }
    });

    // Extend and override media.view.AttachmentsBrowser to include our new filter
    var FivoDocsAttachmentsBrowser = wp.media.view.AttachmentsBrowser;
    wp.media.view.AttachmentsBrowser = FivoDocsAttachmentsBrowser.extend({
        //className: 'attachments-browser fivo-docs-attachments-browser',
        createToolbar: function() {
            // Let's load the original toolbar
            FivoDocsAttachmentsBrowser.prototype.createToolbar.call( this );
            // Only on library and insert states
            if ( 'library' == this.controller._state || 'insert' == this.controller._state ) {
                this.toolbar.set( 'FivoDocsFilterLabel', new wp.media.view.Label({
                    value: fivo_docs_admin.filter_label,
                    attributes: {
                        'for': 'media-attachment-fivo-docs-filters'
                    },
                    priority: -75
                }).render() );
                this.toolbar.set( 'FivoDocs', new FivoDocsFilters({
                    controller: this.controller,
                    model:      this.collection.props,
                    priority:   -75
                }).render() );
            }
        }
    });

    // Open modal when the "Add Docs" button is pressed
    $( document ).on( 'click', '.insert-fivo-docs', function( event ) {
        event.preventDefault();
        // Remove focus from the button.
        // Prevents Opera from showing the outline of the button above the modal.
        $( this ).blur();
        $( 'body' ).append( $( '#tmpl-fivo-docs-modal' ).html() ).addClass( 'fivo-docs-modal-open' );
    });

    // Close modal when the close button in modal is pressed
    $( document ).on( 'click', '.fivo-docs-modal-close', function( event ) {
        event.preventDefault();
        $( '.fivo-docs-modal' ).remove();
        $( 'body' ).removeClass( 'fivo-docs-modal-open' );
    });

    // Slide in modal content selection
    $( document ).on( 'click', '.fivo-docs-modal-options', function( event ) {
        event.preventDefault();
        var open = $( this ).data( 'fivo-docs-open' );
        $( '.fivo-docs-modal-content' ).addClass( 'is-open' ).data( 'fivo-docs-is-open', open );
        $( '.fivo-docs-modal-select' ).slideUp( 200 );
        $( open ).slideDown( 200 );
        $( open + ' .fivo-docs-modal-categories' ).chosen({width: "100%"});
    });

    // Slide out modal content selection when the back button is pressed
    $( document ).on( 'click', '.fivo-docs-modal-back', function( event ) {
        event.preventDefault();
        var open = $( '.fivo-docs-modal-content' ).data( 'fivo-docs-is-open' );
        $( '.fivo-docs-modal-content' ).removeClass( 'is-open' );
        $( '.fivo-docs-modal-select' ).slideDown( 200 );
        $( open ).slideUp( 200 );
    });

    // Open custom media frame when the "Add Documents" is pressed
    $( document ).on( 'click', '.fivo-docs-add', function( event ) {
        event.preventDefault();

        // Remove focus from the button.
        // Prevents Opera from showing the outline of the button above the modal.
        $( this ).blur();

        var $docs_ids = $( '.fivo-docs-ids' ),
            $docs_thumbs = $( '.fivo-docs-thumbs' ),
            ids = $docs_ids.val(),
            ids_array = ids ? ids.split( ',' ) : [],
            frame;

        if ( frame ) {
            frame.open();
            return;
        }

        // Create the media frame.
        frame = wp.media({
            id:         'fivo-docs',
            className:  'media-frame fivo-docs-media-frame',
            multiple:   'add',
            title:      fivo_docs_admin.media_frame_title,
            button: {
                text: fivo_docs_admin.media_frame_button,
                requires: {
                    selection: true
                }
            },
            library: {
                type: 'document' // our custom type that lists only documents
            }
        });

        // When the modal is opened, run a callback.
        frame.on( 'open', function() {
            var selection = frame.state().get( 'selection' );
            if ( ids_array.length > 0 ) {
                ids_array.forEach( function( id ) {
                    selection.add( wp.media.attachment( id ) );
                });
            }
        });

        // When the documents are selected, run a callback.
        frame.on( 'select', function() {

            $docs_thumbs.empty();
            ids = '';

            var selection = frame.state().get( 'selection' );

            selection.map( function( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment && attachment.id != '0' ) {

                    ids = ids ? ids + "," + attachment.id : attachment.id;

                    $docs_thumbs.append( '\
                        <li data-fivo-docs-id="' + attachment.id + '" class="attachment">\
                            <div class="attachment-preview">\
                                <div class="thumbnail">\
                                    <div class="centered">\
                                        <img src="' + attachment.icon + '" />\
                                    </div>\
                                    <div class="filename">\
                                        <div>' + attachment.filename + '</div>\
                                    </div>\
                                </div>\
                                <span class="fivo-docs-actions-delete dashicons dashicons-no-alt fivo-docs-ui-color"></span>\
                            </div>\
                        </li>'
                    );
                }

            });
            $docs_ids.val( ids ).trigger( 'change' );
        });

        // Let's open the frame
        frame.open();

        // Document Sorting
        $( '.fivo-docs-thumbs' ).sortable({
            items: 'li',
            helper: 'clone',
            placeholder: 'attachment fivo-docs-sortable-placeholder',
            forceHelperSize: true,
            forcePlaceholderSize: false,
            opacity: 0.70,
            update: function( e, ui ) {
                var ids = '';
                $( '.fivo-docs-thumbs li' ).each( function() {
                    var id = $( this ).data( 'fivo-docs-id' );
                    ids = ids ? ids + "," + id : id;
                });
                $( '.fivo-docs-ids' ).val( ids ).trigger( 'change' );
            }
        });

    });

    // Remove Document from custom selection
    $( document ).on( 'click', '.fivo-docs-actions-delete', function() {
        event.preventDefault();

        var attachment_ids = '';

        $( this ).closest( 'li' ).remove();
        $( '.fivo-docs-thumbs li' ).each(function() {
            var attachment_id = $( this ).data( 'fivo-docs-id' );
            attachment_ids = attachment_ids ? attachment_ids + "," + attachment_id : attachment_id;
        });

        $( '.fivo-docs-ids' ).val( attachment_ids ).trigger( 'change' );
    });


    // When the options are changed generate a new shortcode
    $( document ).on( 'change', '.fivo-docs-option-value', function() {

        var $this = $( this ),
            $parent = $this.parents( '.fivo-docs-modal-inner' ),
            $usage = $parent.find( '.fivo-docs-usage-shortcode' ),
            sdata = $parent.find( 'form' ).serialize(),
            data = {},
            tags = {},
            atts = '',
            shortcode = '',
            options = {
                'fivo-docs-modal-categories' : 'cats',
                'fivo-docs-cats-align' : 'align',
                'fivo-docs-masonry' : 'masonry',
                'fivo-docs-date' : 'date',
                'fivo-docs-open' : 'open',
                'fivo-docs-scrollbar' : 'scrollbar',
                'fivo-docs-col' : 'col',
                'fivo-docs-title' : 'title',
                'fivo-docs-boxed' : 'boxed',
                'fivo-docs-custom-align' : 'align',
                'fivo-docs-custom-date' : 'date',
                'fivo-docs-custom-scrollbar' : 'scrollbar',
                'fivo-docs-ids' : 'ids'
            },
            defaults = {
                'cats' : '',
                'align' : '',
                'date' : '',
                'masonry' : '',
                'open' : '',
                'scrollbar' : '',
                'col' : '320:1,768:2,992:3,1200:3',
                'title' : '',
                'boxed' : '',
                'ids' : ''
            };

        parse_str( sdata, data );

        $.each( data, function( index, value ) {
            var tag = options[index];
            if ( $.type( value ) === "object" ) {
                if ( 'cats' == tag ) {
                    value = object_to_string( value, false );
                } else {
                    value = object_to_string( value, true );
                }
            }
            tags[tag] = value;
        });

        $.each( tags, function( tag, val ) {
            if ( val != defaults[tag] ) {
                atts += ' ' + tag + '="' + val + '"';
            }
        });

        shortcode = '[fivo_docs' + atts + ']';
        $usage.val( shortcode );
    });

    // Add shortcode to the editor
    $( document ).on( 'click', '.fivo-docs-modal-insert', function() {
        event.preventDefault();

        var $this = $( this ),
            $parent = $this.parents( '.fivo-docs-modal-inner' ),
            $info = $( '.fivo-docs-info' ),
            shortcode = $parent.find( '.fivo-docs-usage-shortcode' ).val(),
            type = $this.data( 'fivo-docs-type' );

        if ( 'custom' == type && '[fivo_docs]' == shortcode ) {
            $info.addClass( 'is-visible' );
            setTimeout( function() {
                $info.removeClass( 'is-visible' );
            }, 3000 );
        } else {
            window.send_to_editor( shortcode );
            $( '.fivo-docs-modal' ).remove();
            $( 'body' ).removeClass( 'fivo-docs-modal-open' );
        }

    });

    var object_to_string = function( object, with_keys ) {
        var result = '';
        $.each( object, function( key, val ) {
            if ( with_keys ) {
                result += key + ':' + val + ',';
            } else {
                result += val + ',';
            }
        });
        result = result.slice(0, -1); // remove the last comma
        return result;
    };

    /**
     * PHP's parse_str in JavaScript as seen on http://locutus.io/php/parse_str/
     *
     * original by: Cagri Ekin
     * improved by: Michael White (http://getsprink.com)
     * improved by: Jack
     * improved by: Brett Zamir (http://brett-zamir.me)
     */
    var parse_str = function(b,c){var f,g,h,i,j,k,l,m,n,o,p,q,r,s,d=String(b).replace(/^&/,"").replace(/&$/,"").split("&"),e=d.length,t=function(a){return decodeURIComponent(a.replace(/\+/g,"%20"))},u="undefined"!=typeof window?window:global;u.$locutus=u.$locutus||{};var v=u.$locutus;for(v.php=v.php||{},c||(c=u),f=0;f<e;f++){for(n=d[f].split("="),o=t(n[0]),p=n.length<2?"":t(n[1]);" "===o.charAt(0);)o=o.slice(1);if(o.indexOf("\0")>-1&&(o=o.slice(0,o.indexOf("\0"))),o&&"["!==o.charAt(0)){for(r=[],q=0,g=0;g<o.length;g++)if("["!==o.charAt(g)||q){if("]"===o.charAt(g)&&q&&(r.length||r.push(o.slice(0,q-1)),r.push(o.substr(q,g-q)),q=0,"["!==o.charAt(g+1)))break}else q=g+1;for(r.length||(r=[o]),g=0;g<r[0].length&&(m=r[0].charAt(g)," "!==m&&"."!==m&&"["!==m||(r[0]=r[0].substr(0,g)+"_"+r[0].substr(g+1)),"["!==m);g++);for(k=c,g=0,s=r.length;g<s;g++)if(o=r[g].replace(/^['"]/,"").replace(/['"]$/,""),j=k,""!==o&&" "!==o||0===g)k[o]===l&&(k[o]={}),k=k[o];else{h=-1;for(i in k)k.hasOwnProperty(i)&&+i>h&&i.match(/^\d+$/g)&&(h=+i);o=h+1}j[o]=p}}};

})( jQuery );
