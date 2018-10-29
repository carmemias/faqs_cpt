/**
 * See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/bulk_quick_edit.js
 *
 * @package CarmeMias\FAQsFunctionality
 */

( function( $ ) {

	// we create a copy of the WP inline edit post function.
	var wpInlineEdit = inlineEditPost.edit;

	// and then we overwrite the function with our own code.
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function, we don't want to leave WordPress hanging.
		wpInlineEdit.apply( this, arguments );

		// get the post ID.
		var postId = 0;
		if ( 'object' == typeof( id ) ) {
			postId = parseInt( this.getId( id ) );
		}

		if ( 0 < postId ) {

			// define the edit row.
			var editRow = $( '#edit-' + postId );

			// get the FAQ Order.
			var orderValue = $( '#cm_faq_order-' + postId ).text();

			// set the FAQ Order.
			editRow.find( 'select[name="_cm_faq_order"] option[value="' + orderValue + '"]' ).prop( 'selected', true );

		}

	};

}( jQuery ) );
