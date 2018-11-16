<?php
/**
 * FAQs Custom Post Type.
 *
 * @package     CarmeMias\FAQsFunctionality\src
 * @author      carmemias
 * @copyright   2017 Carme Mias Studio
 * @license     GPL-2.0+
 */

namespace CarmeMias\FAQsFunctionality\src;

add_action( 'init', __NAMESPACE__ . '\cm_faq_cpt' );
/**
 * Register CPT `cm_faq`
 * See http://web-profile.net/wordpress/docs/custom-post-types/
 * See https://codex.wordpress.org/Function_Reference/register_post_type
 */
function cm_faq_cpt() {
	// see https://codex.wordpress.org/Function_Reference/post_type_supports .
	$features = get_all_post_type_features(
		'post',
		array( // list of excluded features. See lines 59 and 88 .
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'author',
			'thumbnail',
			'post-formats',
		)
	);

	//phpcs:disable
	// TO BE USED WITH GENESIS CHILD THEME. See https://carriedils.com/genesis-2-0-archive-settings-custom-post-types/
	// add support for Genesis archive template
	// array_push($features, 'genesis-seo', 'genesis-cpt-archives-settings');
	//phpcs:enable

	register_post_type(
		'cm_faq',
		array(
			'labels'               => array(
				'name'               => _x( 'FAQs', 'faqs CPT general name', 'faqs-functionality' ),
				'singular_name'      => _x( 'FAQ', 'faqs CPT singular name', 'faqs-functionality' ),
				'all_items'          => __( 'All FAQs', 'faqs-functionality' ),
				'add_new_item'       => __( 'Add New Question', 'faqs-functionality' ),
				'edit_item'          => __( 'Edit Question', 'faqs-functionality' ),
				'new_item'           => __( 'New Question', 'faqs-functionality' ),
				'view_item'          => __( 'View Question', 'faqs-functionality' ),
				'view_items'         => __( 'View Questions', 'faqs-functionality' ),
				'search_items'       => __( 'Search Question', 'faqs-functionality' ),
				'not_found'          => __( 'No questions found.', 'faqs-functionality' ),
				'not_found_in_trash' => __( 'No questions found in Trash.', 'faqs-functionality' ),
			),
			'public'               => true,
			'show_ui'              => true,
			'has_archive'          => true, // this means it'll have an "index/loop" page .
			'rewrite'              => array(
				'slug'       => _x( 'faqs', 'CPT permalink slug', 'faqs-functionality' ),
				'with_front' => false,
			),
			'menu_icon'            => 'dashicons-megaphone',
			'menu_position'        => 20,
			'supports'             => $features, // see line 21 .
			'taxonomies'           => array( 'faq-category' ),
			'register_meta_box_cb' => __NAMESPACE__ . '\cm_faqs_add_meta_box',
		)
	);

	register_taxonomy(
		'faq-category',
		'cm_faq',
		array(
			'hierarchical'      => true,
			'show_in_nav_menus' => false,
			'labels'            => array(
				'name'                       => __( 'FAQ categories', 'faqs-functionality' ),
				'singular_name'              => __( 'FAQ category', 'faqs-functionality' ),
				'all_items'                  => __( 'All FAQ categories', 'faqs-functionality' ),
				'edit_item'                  => __( 'Edit FAQ category', 'faqs-functionality' ),
				'view_item'                  => __( 'View FAQ category', 'faqs-functionality' ),
				'update_item'                => __( 'Update FAQ category', 'faqs-functionality' ),
				'add_new_item'               => __( 'Add new FAQ category', 'faqs-functionality' ),
				'new_item_name'              => __( 'New FAQ category', 'faqs-functionality' ),
				'popular_items'              => __( 'Most used FAQ categories', 'faqs-functionality' ),
				'separate_items_with_commas' => __( 'Separate FAQ categories with commas', 'faqs-functionality' ),
				'add_or_remove_items'        => __( 'Add or remove FAQ categories', 'faqs-functionality' ),
				'choose_from_most_used'      => __( 'Choose from most used FAQ categories', 'faqs-functionality' ),
			),
		)
	); // categories will be set as "children classes" and "teacher training" .
}

/**
 * Get all the post type features for the given post type
 *
 * @param (string) $post_type  given post type .
 * @param (array)  $excluded_features  array of features to exclude .
 * @return array
 **/
function get_all_post_type_features( $post_type = 'post', $excluded_features = array() ) {
	// see https://knowthecode.io/labs/custom-post-type-basics#configure-features .
	$raw_features = get_all_post_type_supports( $post_type );

	if ( ! $excluded_features ) {
		return array_keys( $raw_features );
	}

	$included_features = array();
	foreach ( $raw_features as $key => $value ) {
		if ( ! in_array( $key, $excluded_features, true ) ) {
			$included_features[] = $key;
		}
	}
	return $included_features;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\cm_faq_updated_messages' );

/**
 * Metabox
 */
function cm_faqs_add_meta_box() {
		add_meta_box( 'cm_faq_meta', 'FAQ order', __NAMESPACE__ . '\render_cm_faq_metabox', 'cm_faq', 'side', 'default' );
}

/**
 * Render the metabox
 */
function render_cm_faq_metabox() {
	global $post;

	// Noncename needed to verify where the data originated.
	echo '<input type="hidden" name="cm_faq_noncename" value="' . esc_attr( wp_create_nonce( plugin_basename( __FILE__ ) ) ) . '" />';

	require_once plugin_dir_path( __FILE__ ) . 'views/meta-admin-view.php';
}

/**
 * Save the metabox content
 *
 * @param (int)    $post_id Post ID.
 * @param (object) $post Post.
 */
function save_cm_faq_meta( $post_id, $post ) {
	if ( ! isset( $_POST['cm_faq_noncename'] ) ) {
		return;
	} else {
		$nonce = filter_input( INPUT_POST, 'cm_faq_noncename', FILTER_SANITIZE_STRING );
	}
	// verify this came from the our screen and with proper authorization, because save_post can be triggered at other times.
	if ( ! wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) ) {
		return $post->ID;
	}

	// is the user allowed to edit the post or page?
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	// ok, we're authenticated: we need to find and save the data. We'll put it into an array to make it easier to loop through.
	$faq_meta['_cm_faq_order'] = filter_input( INPUT_POST, '_cm_faq_order', FILTER_SANITIZE_STRING );

	// Add values of $events_meta as custom fields.
	foreach ( $faq_meta as $key => $value ) {
		// Cycle through the $classes_meta array!
		if ( 'revision' === $post->post_type ) {
			return; // Don't store custom data twice.
		}

		$value = implode( ',', (array) $value ); // If $value is an array, make it a CSV (unlikely).

		if ( get_post_meta( $post->ID, $key, false ) ) {
			// If the custom field already has a value.
			update_post_meta( $post->ID, $key, $value );
		} else {
			// If the custom field doesn't have a value.
			add_post_meta( $post->ID, $key, $value );
		}

		if ( ! $value ) {
			delete_post_meta( $post->ID, $key ); // Delete if blank .
		}
	}
}

add_action( 'save_post', __NAMESPACE__ . '\save_cm_faq_meta', 1, 2 );


/**
 * FAQs update messages.
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function cm_faq_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['cm_faq'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'FAQs updated.', 'faqs-functionality' ),
		2  => __( 'Custom field updated.', 'faqs-functionality' ),
		3  => __( 'Custom field deleted.', 'faqs-functionality' ),
		4  => __( 'FAQs updated.', 'faqs-functionality' ),
		// translators: %s: date and time of the revision.
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'FAQ restored to revision from %s', 'faqs-functionality' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'FAQ published.', 'faqs-functionality' ),
		7  => __( 'FAQ saved.', 'faqs-functionality' ),
		8  => __( 'FAQ submitted.', 'faqs-functionality' ),
		9  => sprintf(
			// translators: Publish box date format, see http://php.net/date.
			__( 'FAQ scheduled for: <strong>%1$s</strong>.', 'faqs-functionality' ),
			date_i18n( __( 'M j, Y @ G:i', 'faqs-functionality' ), strtotime( $post->post_date ) )
		),
		10 => __( 'FAQ draft updated.', 'faqs-functionality' ),
	);

	if ( $post_type_object->publicly_queryable && 'cm_faq' === $post_type ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View FAQ', 'faqs-functionality' ) );

		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link      = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview FAQ', 'faqs-functionality' ) );

		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}


add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\cm_faq_bulk_updated_messages', 10, 2 );
/**
 * FAQs bulk update messages.
 * See https://codex.wordpress.org/Plugin_API/Filter_Reference/bulk_post_updated_messages
 *
 * @param array $bulk_messages Existing post bulk update messages.
 * @param array $bulk_counts Existing count for post bulk update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function cm_faq_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

	$bulk_messages['cm_faq'] = array(
		// translators: Number of updated FAQs.
		'updated'   => _n( '%s FAQ updated.', '%s FAQs updated.', $bulk_counts['updated'] ),
		// translators: Number of locked FAQs.
		'locked'    => _n( '%s FAQ not updated, somebody is editing it.', '%s FAQs not updated, somebody is editing them.', $bulk_counts['locked'] ),
		// translators: Number of deleted FAQs.
		'deleted'   => _n( '%s FAQ permanently deleted.', '%s FAQs permanently deleted.', $bulk_counts['deleted'] ),
		// translators: Number of FAQs sent to the bin.
		'trashed'   => _n( '%s FAQ moved to the Bin.', '%s FAQs moved to the Bin.', $bulk_counts['trashed'] ),
		// translators: Number of FAQs removed from the bin.
		'untrashed' => _n( '%s FAQ restored from the Bin.', '%s FAQs restored from the Bin.', $bulk_counts['untrashed'] ),
	);

	return $bulk_messages;

}

/**
 *    DISPLAY IN FAQs LIST TABLE
 */
// see http://shibashake.com/wordpress-theme/expand-the-wordpress-quick-edit-menu .
add_filter( 'manage_cm_faq_posts_columns', __NAMESPACE__ . '\add_custom_columns' );

/**
 * Add a new column for FAQ order in the FAQs List table.
 *
 * @param (array) $columns Columns in the FAQs table.
 */
function add_custom_columns( $columns ) {
	// remove Date column from its default position .
	$date = $columns['date'];
	unset( $columns['date'] );

	$columns['cm_faq_order']    = __( 'Order', 'faqs-functionality' );
	$columns['cm_faq-category'] = __( 'Category', 'faqs-functionality' );

	// Add the Date column again to the end of the table .
	$columns['date'] = $date;

	return $columns;
}

add_action( 'manage_posts_custom_column', __NAMESPACE__ . '\custom_columns', 10, 2 );

/**
 * Display the metaboxes value in the Post List table
 * See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php line 169
 *
 * @param (string) $column Column title.
 * @param (int)    $post_id Post ID.
 */
function custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'cm_faq_order':
			$faq_order = get_post_meta( $post_id, '_cm_faq_order', true );
			if ( '10000' === $faq_order ) {
				$faq_order = __( 'bottom of the list', 'faqs-functionality' );
			}
			echo '<div id="cm_faq_order-' . esc_attr( $post_id ) . '">' . esc_attr( $faq_order ) . '</div>';
			break;
		case 'cm_faq-category':
			$terms      = get_the_terms( $post_id, 'faq-category' );
			$terms_list = '';
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$terms_list = $terms_list . $term->name . ', ';
				}
			} else {
				$terms_list = __( 'not yet set', 'faqs-functionality' );
			}
			echo '<div id="cm_faq-category-' . esc_attr( $post_id ) . '">' . esc_attr( $terms_list ) . '</div>';
			break;
	}
}

add_filter( 'manage_edit-cm_faq_sortable_columns', __NAMESPACE__ . '\order_sortable_column' );

/**
 * Make new Post Priority column sortable
 *
 * @param (array) $columns Columns in the FAQs table.
 */
function order_sortable_column( $columns ) {

	$columns['cm_faq_order'] = 'cm_faq_order';

	return $columns;
}

add_action( 'pre_get_posts', __NAMESPACE__ . '\order_orderby_backend' );

/**
 * Priority sorting instructions for the backend only (front end is set in home.php)
 *
 * @param (object) $query The Object Query.
 */
function order_orderby_backend( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'cm_faq_order' === $orderby ) {
		$query->set( 'meta_key', '_cm_faq_order' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}

/**
 * QUICK EDIT MENU
 */
add_action( 'quick_edit_custom_box', __NAMESPACE__ . '\add_metabox_to_quick_edit', 10, 2 );

/**
 * Add FAQ Order metabox to Quick Edit Menu
 *
 * @param (string) $column_name Column name.
 * @param (string) $post_type Post type.
 */
function add_metabox_to_quick_edit( $column_name, $post_type ) {
	if ( ! in_array( $column_name, array( 'cm_faq_order' ), true ) ) {
		return;
	}
	require_once plugin_dir_path( __FILE__ ) . 'views/order-meta-quick-edit-view.php';
}

add_action( 'save_post', __NAMESPACE__ . '\save_metabox_quick_edit_data', 1, 2 );

/**
 * Save new FAQ order value, attributed through the Quick Edit menu.
 *
 * @param (int)    $post_id Post ID.
 * @param (object) $post Post Object.
 **/
function save_metabox_quick_edit_data( $post_id, $post ) {
	$post_type = get_post_type( $post );
	if ( 'cm_faq' !== $post_type ) {
		return;
	}

	// not to be run for new faqs.
	if ( function_exists( 'get_current_screen' ) ) {
		$current_screen = get_current_screen(); // was clashing with other plugins without this.
		if ( null != $current_screen ) {
			$current_action = $current_screen->action;
			if ( 'add' != $current_action ) {
				return;
			}
		}
	}

	// Verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything .
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// ok, we're authenticated: we need to find and save the data.
	// We'll put it into an array to make it easier to loop through .
	$faq_meta['_cm_faq_order'] = filter_input( INPUT_POST, '_cm_faq_order', FILTER_SANITIZE_STRING );

	// Add value as custom fields.
	foreach ( $faq_meta as $key => $value ) {
		// Cycle through the array.
		if ( 'revision' === $post->post_type ) {
			return;
		} // Don't store custom data twice .

		$value = implode( ',', (array) $value ); // If $value is an array, make it a CSV (unlikely) .

		if ( get_post_meta( $post->ID, $key, false ) ) {
			// If the custom field already has a value.
			update_post_meta( $post->ID, $key, $value );
		} else {
			// If the custom field doesn't already have a value or it has changed .
			add_post_meta( $post->ID, $key, $value );
		}

		if ( ! $value ) {
			// Delete if blank .
			delete_post_meta( $post->ID, $key );
		}
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice' );

/**
 * Show a warning notice in the FAQ Edit page if the FAQ's current order value is higher than the number of FAQS.
 */
function admin_notice() {
	global $pagenow;
	global $post;

	// this notice should not even be attempted in admin sections other than cm_faq edit.
	if ( ( 'post.php' !== $pagenow ) || ( 'cm_faq' !== $post->post_type ) ) {
		return;
	}

	$cm_faq_order     = get_post_meta( $post->ID, '_cm_faq_order', true );
	$current_num_faqs = wp_count_posts( 'cm_faq' )->publish;
	$display_warning  = isset( $cm_faq_order ) && ( '10000' !== $cm_faq_order ) && ( intval( $cm_faq_order ) > intval( $current_num_faqs ) );

	if ( $display_warning ) {
		$warning_message = '<div class="notice notice-warning is-dismissible"><p>';
		// translators: %1$d is the order number selected for that FAQ and %2$d is the total number of FAQs.
		$warning_message .= sprintf( esc_html__( 'The order currently selected for this FAQ is %1$d but there are only %2$d published FAQs. The FAQ will still show work but its order value will not be listed in the dropdown list further down this page.', 'faqs-functionality' ), intval( $cm_faq_order ), intval( $current_num_faqs ) );
		$warning_message .= '</p></div>';

		echo $warning_message;
	}
}

add_action( 'admin_print_scripts-edit.php', __NAMESPACE__ . '\metabox_enqueue_admin_scripts' );
/**
 * Enqueue javascript for the FAQs Quick Edit functionality
 * See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php AND https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 */
function metabox_enqueue_admin_scripts() {
	wp_register_script( 'cm_faq_populate_metabox_scripts', FAQ_FUNCTIONALITY_URL . '/src/assets/js/cm_faq_metabox_populate_quick_edit.js', array( 'jquery', 'inline-edit-post' ), '1.5.0', false );
	wp_enqueue_script( 'cm_faq_populate_metabox_scripts' );
}
