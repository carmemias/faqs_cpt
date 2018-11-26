<?php
/**
 * Shortcode functionality for the FAQs Custom Post Type.
 *
 * @package     CarmeMias\FAQsFunctionality\src
 * @author      carmemias
 * @copyright   2017 Carme Mias Studio
 * @license     GPL-2.0+
 */

namespace CarmeMias\FAQsFunctionality\src;

/**
 * Enqueue javascript and stylesheet files used by the shortcode view
 */
function shortcode_enqueue_scripts() {
	global $post;

	wp_register_style( 'faqs_shortcode_style', FAQ_FUNCTIONALITY_URL . '/src/assets/css/faqs_shortcode_style.css', array(), '1.5.0' );
	wp_register_script( 'faqs_shortcode_script', FAQ_FUNCTIONALITY_URL . '/src/assets/js/faqs_shortcode_script.js', array(), '1.5.0', true );

	// See https://wordpress.stackexchange.com/questions/165754/enqueue-scripts-styles-when-shortcode-is-present.
	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'faqs' ) ) {
		wp_enqueue_style( 'faqs_shortcode_style' );
		wp_enqueue_script( 'faqs_shortcode_script' );
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\shortcode_enqueue_scripts' );

/**
 * Shortcode function
 * [faqs category="category-slug|category name"] category attrib value not case sensitive.
 * A category is required.
 *
 * @param (array) $atts Shortcode attributes.
 */
function shortcode_handler( $atts ) {
	$results_array     = [];
	$cm_faq_categories = [];
	$output_string     = '';

	// the default value for category.
	$a = shortcode_atts(
		array(
			'category' => '',
		),
		$atts
	);

	// find category/ies.
	if ( '' === $a['category'] ) {
		return '<p class="message-info">A category must be specified.</p>';
	} else {
		// the category attribute has been set.
		// does this category exist?
		$category_id = term_exists( $a['category'], 'faq-category' );
		if ( is_array( $category_id ) ) {
			$category_id = intval( array_shift( $category_id ) );
		}

		// if the category doesn't exist, return error message.
		if ( ( 0 === $category_id ) || ( null === $category_id ) ) {

			return '<p class="message-info">' . __( 'This category does not exist yet.', 'faqs-functionality' ) . '</p>';

		}

		// finds the category object and returns it.
		$cm_faq_category = get_term( $category_id, 'faq-category' );

		if ( ! $cm_faq_category || is_wp_error( $cm_faq_category ) ) {
			return '<p class="message-info">' . __( 'An error has occurred. Please check the shortcode has been entered correctly', 'faq-functionality' ) . '</p>';
		}

		$category_slug = $cm_faq_category->slug;
		$category_name = $cm_faq_category->name;

		$output_string .= '<h2 class="category-title">' . esc_attr( $category_name ) . '</h3>';
		$output_string .= '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';

		if ( $cm_faq_category->count > 0 ) {
			$cm_faq_args = array(
				'post_type'      => 'cm_faq',
				'post_status'    => 'publish',
				'faq-category'   => $category_slug,
				'order'          => 'ASC',
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_cm_faq_order',
				'posts_per_page' => 50,
			);

			$cm_faqs = get_posts( $cm_faq_args ); // returns an array.

			// now we have the data, we can build the view.
			foreach ( $cm_faqs as $question ) :
				// See https://codex.wordpress.org/Function_Reference/setup_postdata and http://www.php.net/manual/en/language.references.whatdo.php.
				global $post;
				setup_postdata( $question );

				$output_substring = '';
				$question_id      = $question->ID;
				$question_order   = get_post_meta( $question_id, '_cm_faq_order', true );

				if ( ( 'hidden' !== $question_order ) && ( 'not set' !== $question_order ) ) {
					$question_title = get_the_title( $question_id );
					$answer         = apply_filters( 'the_content', get_the_content() );

					$output_substring .= '<article id="post-' . esc_attr( $question_id ) . '" class="post-' . esc_attr( $question_id ) . ' cm_faq type-cm_faq status-publish hentry faq-category-' . esc_attr( $category_slug ) . '" >';
					$output_substring .= '<header class="entry-header" role="tab" id="heading-' . esc_attr( $question_id ) . '">';
					$output_substring .= '<h3 class="entry-title"><a role="button" class="collapsed" data-parent="#accordion" href="#collapse-' . esc_attr( $question_id ) . '" aria-expanded="false" aria-controls="collapse-' . esc_attr( $question_id ) . '">';
					$output_substring .= esc_attr( $question_title );
					$output_substring .= '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true" role="img"></span></a></h3>';
					$output_substring .= '</header><!-- .entry-header -->';
					$output_substring .= '<div class="entry-content collapse" role="tabpanel" aria-labelledby="heading-' . esc_attr( $question_id ) . '" id="collapse-' . esc_attr( $question_id ) . '">';
					$output_substring .= html_entity_decode( esc_textarea( $answer ) );
					$output_substring .= '</div --><!-- .entry-content -->';
					$output_substring .= '</article><!-- #post-' . esc_attr( $question_id ) . ' -->';
				} // end if $question_order.

				$output_string .= $output_substring;

			endforeach; // foreach questions array within single_result.

			wp_reset_postdata();

		} else {
			$output_string .= '<p class="message-info">' . __( 'There are no questions under category', 'faqs-functionality' ) . ' "' . esc_html( $category_name ) . '" yet.</p>';
		}

		$output_string .= '</div><!-- accordion -->';
		return $output_string;
	}
}
add_shortcode( 'faqs', __NAMESPACE__ . '\shortcode_handler' );
