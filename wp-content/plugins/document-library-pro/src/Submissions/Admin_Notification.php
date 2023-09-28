<?php
namespace Barn2\Plugin\Document_Library_Pro\Submissions;

use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Document;

/**
 * Responsible for sending admin notifications regarding
 * frontend submissions.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Notification {

	/**
	 * Determine if moderation is enabled.
	 *
	 * @var boolean
	 */
	public $moderation_enabled = false;

	/**
	 * The ID of the document for which we're sending an email.
	 *
	 * @var Document
	 */
	protected $document = false;

	/**
	 * Get things started.
	 *
	 * @param Document $document The document object for which we're sending an email.
	 */
	public function __construct( Document $document ) {
		$this->moderation_enabled = Options::is_submission_moderated();
		$this->document           = $document;
	}

	/**
	 * Get the subject of the email.
	 *
	 * @return string
	 */
	private function get_subject() {
		if ( $this->moderation_enabled ) {
			return __( 'New document awaiting moderation', 'document-library-pro' );
		}
		return __( 'New document submitted', 'document-library-pro' );
	}

	/**
	 * Get the message of the email.
	 *
	 * @return string
	 */
	private function get_message() {

		$site_address = esc_url( get_site_url() );
		$post_id      = $this->document->get_id();
		$edit_link    = add_query_arg(
			[
				'post'   => $post_id,
				'action' => 'edit',
			],
			admin_url( 'post.php' )
		);

		if ( $this->moderation_enabled ) {
			return sprintf(
				__( 'A new document has been submitted to <a href="%1$s">%1$s</a>. To approve or reject it, visit the following link and click either "Publish" or "Move to Trash" - <a href="%2$s">%2$s</a>.', 'document-library-pro' ),
				$site_address,
				$edit_link
			);
		}
		return sprintf(
			__( 'A new document has been submitted to <a href="%1$s">%1$s</a>. You can see it at: <a href="%2$s">%2$s</a>.', 'document-library-pro' ),
			$site_address,
			$edit_link
		);
	}

	/**
	 * Send email notification.
	 *
	 * @return void
	 */
	public function send() {

		add_filter( 'wp_mail_from', [ $this, 'set_sender_email' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'set_sender_name' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'set_html_content' ] );

		$to      = get_option( 'admin_email' );
		$subject = $this->get_subject();
		$body    = $this->get_message();

		wp_mail( $to, $subject, $body );

		remove_filter( 'wp_mail_from', [ $this, 'set_sender_email' ] );
		remove_filter( 'wp_mail_from_name', [ $this, 'set_sender_name' ] );
		remove_filter( 'wp_mail_content_type', [ $this, 'set_html_content' ] );
	}

	/**
	 * Set the sender email address for the mail.
	 *
	 * @return string
	 */
	public function set_sender_email() {
		return get_option( 'admin_email' );
	}

	/**
	 * Set the send name for the mail.
	 *
	 * @return string
	 */
	public function set_sender_name() {
		return get_bloginfo( 'name' );
	}

	/**
	 * Set the content type for the mail.
	 *
	 * @return string
	 */
	public function set_html_content() {
		return 'text/html';
	}

}
