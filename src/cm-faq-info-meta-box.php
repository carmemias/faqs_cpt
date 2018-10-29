<?php
/**
 * Meta Box functionality to display the FAQs shortcode info in the Post/Page Edit pages.
 *
 * @package     CarmeMias\FAQsFunctionality\src
 * @author      carmemias
 * @copyright   2017 Carme Mias Studio
 * @license     GPL-2.0+
 */

namespace CarmeMias\FAQsFunctionality\src;

add_action( 'add_meta_boxes_post', __NAMESPACE__ . '\add_shortcode_info', 10, 2 );
add_action( 'add_meta_boxes_page', __NAMESPACE__ . '\add_shortcode_info', 10, 2 );

/**
 * Add the Shortcode info box on to the Edit screen
 *
 * @param (object) $post The Post object.
 */
function add_shortcode_info( $post ) {
	add_meta_box(
		'shortcode-info', // metabox id.
		__( 'Shortcode Information', 'faqs-functionality' ),
		__NAMESPACE__ . '\render_shortcode_info_view',
		[ 'post', 'page' ],
		'side',
		'low'
	);
}
/**
 * Render the information about the shortcode to the Edit screen
 */
function render_shortcode_info_view() {
	global $post;

	// Noncename needed to verify where the data originated
	// echo '<input type="hidden" name="shortcode_info_noncename" value="' . esc_attr( wp_create_nonce( plugin_basename(__FILE__) ) ) . '" />';
	require_once plugin_dir_path( __FILE__ ) . 'views/info-admin-view.php';
}
