<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dejan-wp.dev
 * @since      1.0.0
 *
 * @package    Simple_Voting
 * @subpackage Simple_Voting/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Simple_Voting
 * @subpackage Simple_Voting/admin
 * @author     Your Name <email@example.com>
 */
class Simple_Voting_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simple-voting-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simple-voting-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add voting meta box to post edit screen.
	 *
	 * @since    1.0.0
	 */
	public function add_voting_meta_box() {
		add_meta_box(
			'simple-voting',
			__( 'Simple Voting', 'simple-voting' ),
			array( $this, 'render_voting_meta_box' ),
			'post',
			'side',
			'high'
		);
	}

	/**
	 * Render voting meta box.
	 *
	 * @since    1.0.0
	 * @param    object $post Post object.
	 */
	public function render_voting_meta_box( $post ) {
		$votes = get_post_meta( $post->ID, 'simple-votes', true );

		$yes_percentage = 0;
		$no_percentage  = 0;

		// check if votes exist.
		if ( ! empty( $votes ) ) {
			$yes_percentage = round( ( count( $votes['yes'] ) / ( count( $votes['yes'] ) + count( $votes['no'] ) ) ) * 100 );
			$no_percentage  = round( ( count( $votes['no'] ) / ( count( $votes['yes'] ) + count( $votes['no'] ) ) ) * 100 );
		}

		?>
		<div class="simple-voting">
			<div class="simple-voting__option">
				<p>Article is helpful: <strong><?php echo esc_attr( $yes_percentage ); ?>%</strong></p>
			</div>
			<div class="simple-voting__option">
				<p>Article is NOT helpful: <strong><?php echo esc_attr( $no_percentage ); ?>%</strong></p>
			</div>
		</div>
		<?php
	}
}
