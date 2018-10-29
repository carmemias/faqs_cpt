<?php
/**
 * Frequently Asked Questions Custom Post Type.
 *
 * @package   CarmeMias\FAQsFunctionality
 * @author    carmemias info@carmemias.com
 * @copyright 2017 Carme Mias Studio
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Frequently Asked Questions Custom Post Type
 * Plugin URI:  http://carmemias.com
 * Description: Adds a new FAQs section and custom post type.
 * Version:     1.5.0
 * Author:      carmemias
 * Author URI:  http://carmemias.com
 * Text Domain: faqs-functionality
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace CarmeMias\FAQsFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Access denied.' );
}

/**
 * Setup the plugin's constants.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_constants() {
	$plugin_url = plugin_dir_url( __FILE__ );
	if ( is_ssl() ) {
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
	}

	// OPTIMIZE using constants like these is not recommended by WP Theme review team.
	define( 'FAQ_FUNCTIONALITY_URL', $plugin_url );
	define( 'FAQ_FUNCTIONALITY_DIR', plugin_dir_path( __DIR__ ) );
}

/**
 * Initialize the plugin hooks
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_hooks() {
	register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_rewrites' );
	register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
}

/**
 * Flush the rewrites.
 *
 * @since 1.0.0
 *
 * @return void
 */
function flush_rewrites() {
	init_autoloader();
	src\cm_faq_cpt();
	flush_rewrite_rules();
}

/**
 * Kick off the plugin by initializing the plugin files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_autoloader() {
	include_once 'src/support/autoloader.php';
	Support\autoload_files( __DIR__ . '/src/' );
}

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	init_constants();
	init_hooks();
	init_autoloader();
}

launch();
