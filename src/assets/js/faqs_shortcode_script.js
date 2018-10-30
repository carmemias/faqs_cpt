/**
 * Functionality for the FAQs shortcode view
 * This shouldn't run until the page content has been built
 *
 * @package CarmeMias\FAQsFunctionality
 */

( function() {
	document.addEventListener( 'DOMContentLoaded', docReady );

	function docReady( event ) {
		var faqs = document.querySelectorAll( '#accordion article' );
		event.preventDefault();

		/*
		* Add event listener to each FAQ header in the page
		*/
		for ( var i = 0 ; i < faqs.length ; i++ ) {
			var faq = faqs[i];

			// adds event listener to header.
			faq.querySelector( 'header' ).addEventListener( 'click', toggleFAQ );
		}

		/*
		* Shows/Hides the FAQ answer when the FAQ header is clicked.
		*/
		function toggleFAQ( event ) {
			var faqContent = this.nextElementSibling;
			var aTag = this.querySelector( 'a' );
			var dashIcon = this.querySelector( '.dashicons' );
			event.preventDefault();

			// faq header background turns grey when not collapsed.
			this.classList.toggle( 'grey-background' );
			this.parentElement.classList.toggle( 'has-shadow' );

			// faq content shows when not collapsed.
			faqContent.classList.toggle( 'in' );

			// a tag reflects changes too.
			aTag.classList.toggle( 'collapsed' );

			if ( aTag.className.includes( 'collapsed' ) ) {
				aTag.setAttribute( 'aria-expanded', 'false' );
			} else {
				aTag.setAttribute( 'aria-expanded', 'true' );
			}

			// svg icon changes depending on whether faq is collapsed or not.
			dashIcon.classList.toggle( 'dashicons-arrow-down-alt2' );
			dashIcon.classList.toggle( 'dashicons-arrow-up-alt2' );
		}
	} // end docReady.

}() );
