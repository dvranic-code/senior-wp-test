<?php
/**
 * Plugin Name: Simple Voting
 * Description: Simple Voting Plugin
 * Version: 1.0.0
 * Author: Dejan Rudic Vranic
 * Author URI: https://dejan-wp.dev
 * Text Domain: simple-voting
 *
 * @package Simple Voting
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SIMPLE_VOTING_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-voting-activator.php
 */
function activate_simple_voting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-voting-activator.php';
	Simple_Voting_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-voting-deactivator.php
 */
function deactivate_simple_voting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-voting-deactivator.php';
	Simple_Voting_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simple_voting' );
register_deactivation_hook( __FILE__, 'deactivate_simple_voting' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simple-voting.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_voting() {

	$plugin = new Simple_Voting();
	$plugin->run();
}

run_simple_voting();
