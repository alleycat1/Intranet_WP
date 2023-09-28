(function(){
	/**
	 * Create a new MediaLibraryTaxonomyFilter we later will instantiate
	 */
	var MediaLibraryTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
		id: 'media-attachment-taxonomy-filter',

		createFilters: function() {
            var filters = {};

            filters.documents = {
                text: MediaLibraryDocumentLibraryFilterData.documents,
                props: {
                    document_download: 'document-download',
                }
            };

            filters.all = {
                text: MediaLibraryDocumentLibraryFilterData.all,
                props: {
                    document_download: ''
                },
                priority: 10
            };

            this.filters = filters;
		}
	});
	/**
	 * Extend and override wp.media.view.AttachmentsBrowser to include our new filter
	 */
	var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
	wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
		createToolbar: function() {
			// Make sure to load the original toolbar
			AttachmentsBrowser.prototype.createToolbar.call( this );
			this.toolbar.set( 'MediaLibraryTaxonomyFilter', new MediaLibraryTaxonomyFilter({
				controller: this.controller,
				model:      this.collection.props,
				priority: -75
			}).render() );
		}
	});
})();
