<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://dejan-wp.dev
 * @since      1.0.0
 *
 * @package    Simple_Voting
 * @subpackage Simple_Voting/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Simple_Voting
 * @subpackage Simple_Voting/public
 * @author     Dejan Rudic Vranic <email@example.com>
 */
class Simple_Voting_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Simple_Voting_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simple_Voting_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simple-voting-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Simple_Voting_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simple_Voting_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simple-voting-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'simplevoting_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'simplevoting_ajax_nonce' ),
			)
		);
	}

	/**
	 * Append voting buttons to the content.
	 *
	 * This function appends voting buttons to the content passed as a parameter.
	 *
	 * @param string $content The content to which the voting buttons will be appended.
	 * @return string The content with the appended voting buttons.
	 */
	public function append_voting_buttons( $content ) {
		if ( is_user_logged_in() && is_single() && in_the_loop() && is_main_query() ) {

			$post_id = get_the_ID();

			$user_id = get_current_user_id();

			$voting_result = $this->get_votes( $post_id );

			$voting_content = '';

			$voting_content .= '<div id="voting-container" data-post-id="' . $post_id . '">';
			if ( $this->is_user_voted( $voting_result['votes'], $user_id ) ) {
				$voting_content .= '<p>Thank you for your feedback.';
				$voting_content .= '<span class="voting-result">' . $voting_result['yes'] . '</span>';
				$voting_content .= '<span class="voting-result">' . $voting_result['no'] . '</span>';
				$voting_content .= '</p>';
			} else {
				$voting_content .= '<p>Hover / Press States</p>';
				$voting_content .= '<p>Was This Article Helpful?';
				$voting_content .= '<button class="vote-button" data-vote="yes" data-result="' . $voting_result['yes'] . '">Yes</button>';
				$voting_content .= '<button class="vote-button" data-vote="no" data-result="' . $voting_result['no'] . '">No</button>';
				$voting_content .= '</p>';
			}
			$voting_content .= '</div>';

			return $content . $voting_content;
		}
		return $content;
	}

	/**
	 * Manage the voting process.
	 *
	 * This function handles the voting process by performing the necessary actions
	 * to record and display votes.
	 *
	 * @since 1.0.0
	 */
	public function manage_voting() {
		// check nonce.
		if ( ! wp_verify_nonce( $_POST['_nonce'], 'simplevoting_ajax_nonce' ) ) { //phpcs:ignore
			wp_die( 'Security check' );
		}

		// get post ID.
		$post_id = intval( sanitize_text_field( $_POST['post_id'] ) ); //phpcs:ignore

		// get user ID.
		$user_id = get_current_user_id();

		// get vote type.
		$vote_type = sanitize_text_field( $_POST['button_type'] ); //phpcs:ignore

		// get votes.
		$votes = get_post_meta( $post_id, 'simple-votes', true );

		// check if votes exist.
		if ( empty( $votes ) ) {
			$votes = array(
				'yes' => array(),
				'no'  => array(),
			);
		}

		// add vote.
		if ( 'yes' === $vote_type ) {
			$votes['yes'][] = $user_id;
		} elseif ( 'no' === $vote_type ) {
			$votes['no'][] = $user_id;
		}

		// update votes.
		update_post_meta( $post_id, 'simple-votes', $votes );

		// get votes.
		$voting_result = $this->get_votes( $post_id );

		$voting_content = '';

		$voting_content .= '<p>Thank you for your feedback.';
		$voting_content .= '<span class="voting-result">' . $voting_result['yes'] . '</span>';
		$voting_content .= '<span class="voting-result">' . $voting_result['no'] . '</span>';
		$voting_content .= '</p>';

		// return votes.
		echo $voting_content; //phpcs:ignore
		die();
	}

	/**
	 * Get votes.
	 *
	 * @param int $post_id The ID of the post for which to get votes.
	 * @return array The array of votes and results.
	 */
	public function get_votes( $post_id ) {
		$votes = get_post_meta( $post_id, 'simple-votes', true );

		$yes_percentage = 0;
		$no_percentage  = 0;

		// check if votes exist.
		if ( ! empty( $votes ) ) {
			$yes_percentage = round( ( count( $votes['yes'] ) / ( count( $votes['yes'] ) + count( $votes['no'] ) ) ) * 100 );
			$no_percentage  = round( ( count( $votes['no'] ) / ( count( $votes['yes'] ) + count( $votes['no'] ) ) ) * 100 );
		}

		return array(
			'votes' => $votes,
			'yes'   => $yes_percentage . '%',
			'no'    => $no_percentage . '%',
		);
	}

	/**
	 * Check if user voted.
	 *
	 * @param array $votes The array of votes.
	 * @param int   $user_id The ID of the user to check in votes.
	 * @return bool True if user voted, false otherwise.
	 */
	public function is_user_voted( $votes, $user_id ) {
		if ( ! empty( $votes ) && ( in_array( $user_id, $votes['yes'], true ) || in_array( $user_id, $votes['no'], true ) ) ) {
			return true;
		}
		return false;
	}
}
