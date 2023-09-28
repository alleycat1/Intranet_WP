import edit from './edit';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

const DEFAULT_STRING_ATTR = {
    type: 'string',
    default: ''
};

const DEFAULT_ARRAY_ATTR = {
    type: 'array',
    default: []
};

const DEFAULT_BOOLEAN_ATTR = {
    type: 'boolean',
    default: false
};

registerBlockType( 'fivo-docs/docs', {
	title: __( 'Fivo Docs', 'fivo-docs' ),
    icon: 'format-aside',
	category: 'media',
    keywords: [ __( 'fivo docs', 'fivo-docs' ) ],
    supports: {
		align: true,
        html: false,
        reusable: false,
    },
    attributes: {
        type: {
            type: 'string',
            default: 'categories',
        },
        categories: DEFAULT_ARRAY_ATTR,
        align: DEFAULT_STRING_ATTR,
        date: DEFAULT_BOOLEAN_ATTR,
        masonry: DEFAULT_BOOLEAN_ATTR,
        open: DEFAULT_BOOLEAN_ATTR,
        scrollbar: DEFAULT_BOOLEAN_ATTR,
        columns: {
            type: 'object',
            default: {
                xs: 1,
                sm: 2,
                md: 2,
                lg: 3,
                xl: 3,
            }
        },
        title: DEFAULT_STRING_ATTR,
        boxed: DEFAULT_BOOLEAN_ATTR,
        ids: DEFAULT_ARRAY_ATTR,
    },
    edit,
    save: () => { return null },
} );

