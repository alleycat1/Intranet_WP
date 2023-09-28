const {
	SelectControl,
	RangeControl,
	ToggleControl,
	TextControl,
	FormTokenField,
	PanelBody
} = wp.components;
const { InspectorControls, MediaPlaceholder } = wp.blockEditor;
const { Fragment, useEffect, useState } = wp.element;
const { apiFetch, url } = wp;
const { __ } = wp.i18n;

const MIN_COLUMNS = 1;
const MAX_COLUMNS = 6;
const MAX_CATEGORIES_SUGGESTIONS = 20;
const ALLOWED_MEDIA_TYPES = [ 'application', 'text' ];
const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

export default (props) => {
	const { attributes, setAttributes, className } = props

	const setColumns = (type, value) => {
		const columns = attributes.columns;

		setAttributes( {
			columns: {
				...columns,
				[type]: value
			}
		} );
	};

	const setDocuments = docs => {
		const ids = docs.map(doc => doc.id);
		const newIds = ids.filter(id => id && !attributes.ids.includes(id))
		setAttributes( { ids: [...attributes.ids, ...newIds] } );
	};

	const [categoriesList, setCategoriesList] = useState( [] );

	useEffect( () => {
		apiFetch( {
			path: url.addQueryArgs( `/wp/v2/fivo_docs_category`, CATEGORIES_LIST_QUERY ),
		} )
			.then( ( categoriesList ) => {
				setCategoriesList( categoriesList );
			} )
			.catch( () => {
				setCategoriesList( [] );
			} );
	}, [] );

	const categorySuggestions = categoriesList.reduce(
		( accumulator, category ) => ( {
			...accumulator,
			[ category.id ]: category.name,
		} ),
		{}
	);

	const getCategoryId = (value) => {
		return Object.keys(categorySuggestions).find(key => categorySuggestions[key] === value);
	}

	const selectCategories = ( tokens ) => {
		const hasNoSuggestion = tokens.some(
			( token ) =>
				typeof token === 'string' && !getCategoryId( token )
		);

		if ( hasNoSuggestion ) {
			return;
		}

		const categories = tokens.map( ( token ) => {
			return getCategoryId( token );
		} );

		setAttributes( { categories: categories } );
	};

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody>
					<SelectControl
						label={ __( 'Type', 'fivo-docs' ) }
						value={ attributes.type }
						options={ [
							{ label: __( 'Categories', 'fivo-docs' ), value: 'categories' },
							{ label: __( 'Custom', 'fivo-docs' ), value: 'custom' },
						] }
						onChange={ (value) => setAttributes( { type: value } ) }
					/>
					{ attributes.type === 'categories' && ( <FormTokenField
						key="fivo-docs-categories-select"
						label={ __( 'Select Categories', 'fivo-docs' ) }
						value={
							attributes.categories &&
							attributes.categories.map( ( id ) => categorySuggestions[id] )
						}
						suggestions={ Object.values( categorySuggestions ) }
						onChange={ selectCategories }
						maxSuggestions={ MAX_CATEGORIES_SUGGESTIONS }
					/> ) }
					{ attributes.type === 'custom' && ( <TextControl
						label={ __( 'Title', 'fivo-docs' ) }
						value={ attributes.title }
						onChange={ (value) => setAttributes( { title: value } ) }
					/> ) }
					<ToggleControl
						label={ __( 'Show Date', 'fivo-docs' ) }
						checked={ attributes.date }
						onChange={ () => setAttributes( { date: !attributes.date } ) }
					/>
					{ attributes.type === 'categories' && ( <ToggleControl
						label={ __( 'Enable Masonry Layout', 'fivo-docs' ) }
						checked={ attributes.masonry }
						onChange={ () => setAttributes( { masonry: !attributes.masonry } ) }
					/> ) }
					{ attributes.type === 'categories' && ( <ToggleControl
						label={ __( 'Open First Subcategories', 'fivo-docs' ) }
						checked={ attributes.open }
						onChange={ () => setAttributes( { open: !attributes.open } ) }
					/> ) }
					{ attributes.type === 'custom' && ( <ToggleControl
						label={ __( 'Use Boxed Style', 'fivo-docs' ) }
						checked={ attributes.boxed }
						onChange={ () => setAttributes( { boxed: !attributes.boxed } ) }
					/> ) }
					<ToggleControl
						label={ __( 'Show Scrollbar for Long Lists', 'fivo-docs' ) }
						checked={ attributes.scrollbar }
						onChange={ () => setAttributes( { scrollbar: !attributes.scrollbar } ) }
					/>
					{ attributes.type === 'categories' && ( <RangeControl
						label={ __( 'Columns Mobile', 'fivo-docs' ) }
						value={ attributes.columns.xs }
						onChange={ (value) => setColumns( 'xs', value ) }
						min={ MIN_COLUMNS }
						max={ MAX_COLUMNS }
					/> ) }
					{ attributes.type === 'categories' && ( <RangeControl
						label={ __( 'Columns Mobile Wide', 'fivo-docs' ) }
						value={ attributes.columns.sm }
						onChange={ (value) => setColumns( 'sm', value ) }
						min={ MIN_COLUMNS }
						max={ MAX_COLUMNS }
					/> ) }
					{ attributes.type === 'categories' && ( <RangeControl
						label={ __( 'Columns Tablet', 'fivo-docs' ) }
						value={ attributes.columns.md }
						onChange={ (value) => setColumns( 'md', value ) }
						min={ MIN_COLUMNS }
						max={ MAX_COLUMNS }
					/> ) }
					{ attributes.type === 'categories' && ( <RangeControl
						label={ __( 'Columns Tablet Wide', 'fivo-docs' ) }
						value={ attributes.columns.lg }
						onChange={ (value) => setColumns( 'lg', value ) }
						min={ MIN_COLUMNS }
						max={ MAX_COLUMNS }
					/> ) }
					{ attributes.type === 'categories' && ( <RangeControl
						label={ __( 'Columns Desktop', 'fivo-docs' ) }
						value={ attributes.columns.xl }
						onChange={ (value) => setColumns( 'xl', value ) }
						min={ MIN_COLUMNS }
						max={ MAX_COLUMNS }
					/> ) }
				</PanelBody>
			</InspectorControls>
			<div className={className}>
				<div className="fivo-docs-editor-preview">
					<h4>Fivo Docs</h4>
					<p>Preview currently not available. Changes will be visible on the actual page.</p>
					{ attributes.type === 'custom' && <p>Selected docs: { JSON.stringify( attributes.ids ) }</p>}

				</div>
				{ attributes.type === 'custom' && ( <MediaPlaceholder
					icon="format-aside"
					labels={ {
						title: __( 'Documents', 'fivo-docs' ),
						instructions: __(
							'Upload documents or pick from your library (Use CTRL or CMD to select multiple).', 'fivo-docs'
						),
					} }
					value={ attributes.ids }
					onSelect={ (ids) => setDocuments( ids ) }
					allowedTypes={ ALLOWED_MEDIA_TYPES }
					multiple
				/> ) }
			</div>
		</Fragment>
	);
}
