/**
 * See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/bulk_quick_edit.js
 *
 * @package CarmeMias\FAQsFunctionality
 */

(function($) {

	// we create a copy of the WP inline edit post function.
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code.
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function, we don't want to leave WordPress hanging.
		$wp_inline_edit.apply( this, arguments );

		// get the post ID.
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {

			// define the edit row.
			var $edit_row = $( '#edit-' + $post_id );

			// get the FAQ Order.
			var $cm_faq_order_value = $( '#cm_faq_order-' + $post_id ).text();

			// set the FAQ Order.
			$edit_row.find( 'select[name="_cm_faq_order"] option[value="' + $cm_faq_order_value + '"]' ).prop( 'selected', true );

		}

	};

})( jQuery );
