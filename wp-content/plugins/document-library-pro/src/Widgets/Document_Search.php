<?php
namespace Barn2\Plugin\Document_Library_Pro\Widgets;

use Barn2\Plugin\Document_Library_Pro\Search_Handler;

/**
 * Handles the display of a Document_Grid
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Search extends \WP_Widget {

	/**
	 * Initialize the widget
	 */
	public function __construct() {
		$options = [
			'description' => esc_html__( 'A global search box for your document library.', 'document-library-pro' ),
		];

		parent::__construct( 'doc-search-widget', esc_html__( 'Document Library Pro: Search Box', 'document-library-pro' ), $options );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		wp_enqueue_style( 'dlp-search-box' );

		/* phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo Search_Handler::get_search_box_html( 'widget', $instance['placeholder'], $instance['button_text'] );

		echo $args['after_widget'];
		/* phpcs:enable */
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Document search', 'document-library-pro' );
		$placeholder = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : esc_html__( 'Search documents...', 'document-library-pro' );
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : esc_html__( 'Search', 'document-library-pro' );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'document-library-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_attr_e( 'Placeholder:', 'document-library-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_attr_e( 'Button Text:', 'document-library-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];

		$instance['title']       = sanitize_text_field( $new_instance['title'] );
		$instance['placeholder'] = sanitize_text_field( $new_instance['placeholder'] );
		$instance['button_text'] = sanitize_text_field( $new_instance['button_text'] );

		return $instance;
	}
}
