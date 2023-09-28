<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Query,
	Barn2\Plugin\Document_Library_Pro\Frontend_Scripts,
	Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Taxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the folders HTML output
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Folder_Tree {
	private $allowed_terms      = [];
	private $exclude_categories = [];
	private $include_categories = [];
	private $exclude_terms      = [];
	private $include_terms      = [];
	private $args;
	private $query;
	private $shortcode_atts;

	/**
	 * Constructor.
	 *
	 * @param array $args
	 * @param array $atts
	 */
	public function __construct( $args = [], $atts = [] ) {
		if ( (int) $args['post_limit'] < 500 && (int) $args['post_limit'] !== -1 ) {
			$args['post_limit'] = 500;
		}

		$args['lazy_load'] = false;

		$this->args           = new Table_Args( $args );
		$this->query          = new Table_Query( $this->args );
		$this->shortcode_atts = $atts;

		$this->init_category_options();
	}

	/**
	 * Get any terms options associated to or excluded from the folder structure
	 */
	private function init_category_options() {
		$this->exclude_categories = array_filter( explode( ',', $this->args->get_args()['exclude_doc_category'] ) );
		$this->include_categories = array_filter( explode( ',', $this->args->get_args()['doc_category'] ) );

		$include_args        = $this->args->get_args()['term'];
		$this->include_terms = false !== strpos( $include_args, '+' ) ? array_filter( explode( '+', $include_args ) ) : array_filter( explode( ',', $include_args ) );
		$this->include_terms = array_filter( array_map(
			function( $term ) {
				$term_split = explode( ':', $term, 2 );

				if ( $term_split[0] === 'doc_categories' ) {
					return $term_split[1];
				}
			},
			$this->include_terms
		) );

		$exclude_args        = $this->args->get_args()['exclude_term'];
		$this->exclude_terms = false !== strpos( $exclude_args, '+' ) ? array_filter( explode( '+', $exclude_args ) ) : array_filter( explode( ',', $exclude_args ) );
		$this->exclude_terms = array_filter( array_map(
			function( $term ) {
				$term_split = explode( ':', $term, 2 );

				if ( $term_split[0] === 'doc_categories' ) {
					return $term_split[1];
				}
			},
			$this->exclude_terms
		) );

		add_filter( 'document_library_pro_icon_svg', [ $this, 'icon_svgs' ] );
	}

	/**
	 * Retrieves the folder tree HTML
	 *
	 * @return  string  $folder_html
	 */
	public function get_html() {
		Frontend_Scripts::load_folder_scripts( $this->args );

		if ( ! $this->query->get_posts() ) {
			return __( 'No documents found.', 'document-library-pro' );
		}

		$category_tree = $this->generate_category_tree();

		if ( empty( $category_tree ) ) {
			$args = $this->args->get_args();
			$args['folders'] = false;

			return dlp_get_doc_library( $args );
		}

		ob_start();
		$this->generate_category_folder_html( $category_tree, true, $this->shortcode_atts );

		return ob_get_clean();
	}

	/**
	 * Generates the full category tree.
	 *
	 * @return array
	 */
	private function generate_category_tree() {
		$categories = $this->get_attached_categories();
		$tree_root  = $this->generate_categories_tree_root( $categories );
		$tree       = $this->populate_missing_categories_into_tree( $tree_root );

		return $tree;
	}

	/**
	 * Retrieves all categories currently attached to the current table query
	 *
	 * @return  \WP_Term[]  $categories
	 */
	private function get_attached_categories() {
		$post_ids = array_map(
			function ( \WP_Post $post ) {
				return $post->ID;
			},
			$this->query->get_posts()
		);

		// We need to fetch all attached terms to match agains.
		// Query options provided to the shortcode could exclude certain categories (e.g. include, exclude, status etc..)
		$categories = wp_get_object_terms(
			$post_ids,
			[ Taxonomies::CATEGORY_SLUG ],
			[
				'orderby' => apply_filters( 'document_library_pro_folder_orderby', $this->args->get_args()['folders_order_by'] ),
				'order'   => $this->args->get_args()['folders_order']
			]
		);

		// Fetch empty parents not assigned to any documents in order to display the full hierarchy
		$category_parents_objects = [];

		foreach ( $categories as $category ) {
			$category_parents_ids = get_ancestors( $category->term_id, Taxonomies::CATEGORY_SLUG );

			if ( ! empty( $category_parents_ids ) ) {
				$term_objects = array_map(
					function( $parent_id ) {
						$parent_term = get_term( $parent_id, Taxonomies::CATEGORY_SLUG );

						return $parent_term;
					},
					$category_parents_ids
				);

				$category_parents_objects = array_merge( $category_parents_objects, $term_objects );
			}
		}

		$categories = array_merge( $categories, $category_parents_objects );

		// We store all the allowed terms from the main query above. This is used to filter the categories in lower levels of the tree.
		$this->allowed_terms = array_map( function( $term ) { return $term->term_id; }, $categories );

		// Adjust for shortcode provided category query changes
		if ( ! empty( $this->include_terms ) ) {
			$using_term_ids = count( $this->include_terms ) === count( array_filter( $this->include_terms, 'is_numeric' ) );
			$field          = $using_term_ids ? 'term_id' : 'slug';
			$include_terms = $this->include_terms;

			$categories = array_filter(
				$categories,
				function( \WP_Term $item ) use ( $include_terms, $field ) {
					return in_array( (string) $item->{$field}, $include_terms, true );
				}
			);
		}

		if ( ! empty( $this->exclude_terms ) ) {
			$using_term_ids = count( $this->exclude_terms ) === count( array_filter( $this->exclude_terms, 'is_numeric' ) );
			$field          = $using_term_ids ? 'term_id' : 'slug';
			$exclude_terms = $this->exclude_terms;

			$categories = array_filter(
				$categories,
				function( \WP_Term $item ) use ( $exclude_terms, $field ) {
					return ! in_array( (string) $item->{$field}, $exclude_terms, true );
				}
			);
		}

		if ( ! empty( $this->include_categories ) ) {
			$using_term_ids = count( $this->include_categories ) === count( array_filter( $this->include_categories, 'is_numeric' ) );
			$field          = $using_term_ids ? 'term_id' : 'slug';

			$categories = array_filter(
				$categories,
				function( \WP_Term $item ) use ( $field ) {
					return in_array( (string) $item->{$field}, $this->include_categories, true );
				}
			);
		}

		if ( ! empty( $this->exclude_categories ) ) {
			$using_term_ids = count( $this->exclude_categories ) === count( array_filter( $this->exclude_categories, 'is_numeric' ) );
			$field          = $using_term_ids ? 'term_id' : 'slug';

			$categories = array_filter(
				$categories,
				function( \WP_Term $item ) use ( $field ) {
					return ! in_array( (string) $item->{$field}, $this->exclude_categories, true );
				}
			);
		}

		return $categories;
	}


	/**
	 * Generate the top level of the tree array
	 *
	 * @param \WP_Term[] $categories
	 */
	private function generate_categories_tree_root( $categories ) {
		$category_tree = [];

		// Get all term ids
		$term_ids = array_map(
			function( $term ) {
				return $term->term_id;
			},
			$categories
		);

		// Check for those without included parents
		$top_level_categories = array_filter(
			$categories,
			function ( \WP_Term $category ) use ( $term_ids ) {
				return ! in_array( $category->parent, $term_ids, true );
			}
		);

		// Get top level term ids
		$top_level_ids = array_map(
			function ( $category ) {
				return $category->term_id;
			},
			$top_level_categories
		);

		// Get the remaining sub categories
		$remaining_sub_categories = array_filter(
			$categories,
			function ( \WP_Term $category ) use ( $term_ids ) {
				return in_array( $category->parent, $term_ids, true );
			}
		);

		// Get the remaining sub categories ids
		$remaining_sub_categories_ids = array_map(
			function ( $category ) {
				return $category->term_id;
			},
			$remaining_sub_categories
		);

		// Sort the remaining sub categories
		$remaining_sub_categories = get_terms(
			[
				'taxonomy'   => Taxonomies::CATEGORY_SLUG,
				'include'    => $remaining_sub_categories_ids,
				'orderby'    => apply_filters( 'document_library_pro_folder_orderby', $this->args->get_args()['folders_order_by'] ),
				'order'      => $this->args->get_args()['folders_order'],
				'hide_empty' => false,
			]
		);

		// Check if any of the top root categories are children of each other
		foreach ( $top_level_categories as $index => $category ) {
			if ( $category->parent === 0 ) {
				continue;
			}

			$ancestors = get_ancestors( $category->term_id, Taxonomies::CATEGORY_SLUG, 'taxonomy' );

			if ( ! empty( array_intersect( $ancestors, $top_level_ids ) ) ) {
				$remaining_sub_categories[] = $category;
				unset( $top_level_categories[ $index ] );
			}
		}

		// Get top level term ids so we can sort by menu order
		$top_level_ids = array_map(
			function ( $category ) {
				return $category->term_id;
			},
			$top_level_categories
		);

		$top_level_categories = get_terms(
			[
				'taxonomy'   => Taxonomies::CATEGORY_SLUG,
				'include'    => $top_level_ids,
				'orderby'    => apply_filters( 'document_library_pro_folder_orderby', $this->args->get_args()['folders_order_by'] ),
				'order'      => $this->args->get_args()['folders_order'],
				'hide_empty' => false,
			]
		);

		// Create the tree root (top level)
		foreach ( $top_level_categories as $category ) {
			$category_tree[ $category->term_id ] = $this->fill_in_subcategories( $remaining_sub_categories, $category->term_id );
		}

		return $category_tree;
	}

	/**
	 * Recursively fills in the attached subcategories of a tree root
	 *
	 * @param   \WP_Term[]   $categories
	 * @param   int         $parent
	 * @return  array       $category_tree
	 */
	private function fill_in_subcategories( $categories, $parent ) {
		$terms = array_filter(
			$categories,
			function( \WP_Term $category ) use ( $parent ) {
				return $category->parent === $parent;
			}
		);

		$category_tree = [];

		foreach ( $terms as $term ) {
			$category_tree[ $term->term_id ] = $this->fill_in_subcategories( $categories, $term->term_id );
		}

		return $category_tree;
	}

	/**
	 * Populates in missing category hierarchy of included terms
	 *
	 * @param   array   $category_tree
	 * @return  array   $category_tree
	 */
	private function populate_missing_categories_into_tree( $category_tree ) {

		foreach ( $category_tree as $category_id => $sub_categories ) {
			$term_children = get_terms(
				[
					'taxonomy'   => Taxonomies::CATEGORY_SLUG,
					'parent'     => $category_id,
					'orderby'    => apply_filters( 'document_library_pro_folder_orderby', $this->args->get_args()['folders_order_by'] ),
					'order'      => $this->args->get_args()['folders_order'],
					'hide_empty' => false,
				]
			);
	
			// Check that the term children are in the allowed categories from the generated IDs of the whole query.
			if ( ! empty( $this->allowed_terms ) ) {
				$term_children = array_filter(
					$term_children,
					function( \WP_Term $item ) {
						return in_array( $item->term_id, $this->allowed_terms, true );
					}
				);
			}

			if ( ! empty( $this->exclude_categories ) ) {
				$using_term_ids = count( $this->exclude_categories ) === count( array_filter( $this->exclude_categories, 'is_numeric' ) );
				$field          = $using_term_ids ? 'term_id' : 'slug';

				$term_children = array_filter(
					$term_children,
					function( \WP_Term $item ) use ( $field ) {
						return ! in_array( (string) $item->{$field}, $this->exclude_categories, true );
					}
				);
			}

			if ( ! empty( $this->exclude_terms ) ) {
				$using_term_ids = count( $this->exclude_terms ) === count( array_filter( $this->exclude_terms, 'is_numeric' ) );
				$field          = $using_term_ids ? 'term_id' : 'slug';
				$exclude_terms = $this->exclude_terms;

				$term_children = array_filter(
					$term_children,
					function( \WP_Term $item ) use ( $exclude_terms, $field ) {
						return ! in_array( (string) $item->{$field}, $exclude_terms, true );
					}
				);
			}

			foreach ( $term_children as $term ) {
				$term_descendants = get_term_children( $term->term_id, Taxonomies::CATEGORY_SLUG );

				if ( empty( $term_descendants ) && $term->count === 0 ) {
					continue;
				}

				$category_tree[ $category_id ][ $term->term_id ] = [];
			}

			$category_tree[ $category_id ] = $this->populate_missing_categories_into_tree( $category_tree[ $category_id ] );
		}

		return $category_tree;
	}

	/**
	 * Generates the folder HTML.
	 *
	 * @param mixed $categories
	 * @param bool $root
	 * @param array $shortcode_atts
	 */
	private function generate_category_folder_html( $categories, $root = false, $shortcode_atts = [] ) {
		?>
		<div class="dlp-folders-container" style="display:none;">
			<?php
			if ( $root ) {
				$this->maybe_output_search_box( 'top' );
				$this->maybe_output_search_results_container();

				$folder_color = $this->args->folder_icon_custom ? $this->args->folder_icon_color : Options::get_dlp_specific_default_args()['folder_icon_color'];
			} else {
				$folder_color = $this->args->folder_icon_custom ? $this->args->folder_icon_subcolor : Options::get_dlp_specific_default_args()['folder_icon_subcolor'];
			}
			?>

			<ul class="dlp-folders <?php echo $root ? 'dlp-folders-root' : ''; ?>" <?php echo $root ? 'data-shortcode-atts=\'' . wp_json_encode( $shortcode_atts ) . '\'' : ''; ?>>
				<?php
				foreach ( $categories as $category_id => $subcategories ) :
					$term   = get_term( $category_id );
					$status = $this->get_term_status( $term );
					?>
					<li class="dlp-folder dlp-folder <?php echo esc_attr( $status ); ?>" data-category-id="<?php echo esc_attr( $category_id ); ?>">
						<span class="dlp-category">
							<?php SVG_Icon::render( 'folder', [], $folder_color ); ?>
							<?php SVG_Icon::render( 'folder_open', [], $folder_color ); ?>
							<span class="dlp-folder-label">
							<span class="dlp-category-name"><?php echo esc_html( $term->name ); ?></span>
							<?php
							$description = $term->description;
							if ( $description != '' && apply_filters( 'document_library_pro_should_display_category_description_in_folder', false ) ) {
								if ( isset( Options::get_shortcode_options()['shortcodes'] ) && Options::get_shortcode_options()['shortcodes'] ) {
									$description = do_shortcode( $description );
								}
								?>
							<span class="dlp-category-description"><?php echo wp_kses_post( $description ); ?></span>
							<?php } ?>
							</span>
						</span>

						<div class="dlp-folder-inner">
							<div class="dlp-category-table">
							</div>

							<div class="dlp-category-subcategories">
								<?php
								if ( ! empty( $subcategories ) ) {
									$this->generate_category_folder_html( $subcategories );
								}
								?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php
			if ( $root ) {
				$this->maybe_output_search_box( 'bottom' );
			}
			?>

		</div>
		<?php
	}

	private function get_term_status( $term ) {
		if ( 'custom' !== $this->args->folder_status ) {
			return 'closed' === $this->args->folder_status ? 'closed' : '';
		}

		$open_folders = array_filter( explode( ',', $this->args->folder_status_custom ) );

		// the term ID or the term slug is listed in the open folders field
		$is_open = in_array( (string) $term->term_id, $open_folders, true ) || in_array( $term->slug, $open_folders, true );

		if ( $is_open ) {
			return '';
		}

		foreach ( get_term_children( $term->term_id, Taxonomies::CATEGORY_SLUG ) as $child ) {
			$child   = get_term( $child );
			$is_open = ( in_array( (string) $child->term_id, $open_folders, true ) || in_array( $child->slug, $open_folders, true ) );

			if ( $is_open ) {
				return '';
			}
		}

		return 'closed';
	}

	/**
	 * Output the folder search box.
	 *
	 * @param string $context
	 */
	private function maybe_output_search_box( $context = 'top' ) {
		$search_position = $this->args->get_args()['search_box'];
		$search_html     = '';

		// Search Input
		if ( in_array( $search_position, [ $context, 'both' ], true ) ) {
			$search_label       = apply_filters( 'document_library_pro_search_label', __( 'Search:', 'document-library-pro' ) );
			$search_placeholder = apply_filters( 'document_library_pro_search_placeholder', '' );
			$search_query       = filter_input( INPUT_GET, 'dlp_search', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';

			$search_html = sprintf(
				'<div class="dlp-folders-search"><label>%1$s<input type="search" placeholder="%2$s" value="%3$s"></label><div class="dlp-folders-reset"><a class="reset" href="#">%4$s %5$s</a></div></div>',
				esc_html__( $search_label, 'document-library-pro' ),
				esc_html__( $search_placeholder, 'document-library-pro' ),
				$search_query,
				SVG_Icon::get( 'reset' ),
				esc_html__( 'Reset', 'document-library-pro' )
			);
		}

		/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
		echo $search_html;
	}

	/**
	 * Output the search results container DIV.
	 */
	private function maybe_output_search_results_container() {
		$search_position = $this->args->get_args()['search_box'];

		if ( ! in_array( $search_position, [ 'top', 'bottom', 'both' ], true ) ) {
			return;
		}

		echo '<div class="dlp-folders-search-results"></div>';
	}

	public function icon_svgs( $icons ) {
		if ( ! $this->args->folder_icon_custom ) {
			return $icons;
		}

		$folder_closed = get_option( Options::FOLDER_CLOSE_SVG_OPTION_KEY );

		if ( $folder_closed ) {
			$icons['folder'] = html_entity_decode( $folder_closed );
		}

		$folder_open = get_option( Options::FOLDER_OPEN_SVG_OPTION_KEY );

		if ( $folder_open ) {
			$icons['folder_open'] = html_entity_decode( $folder_open );
		}

		return $icons;
	}
}
