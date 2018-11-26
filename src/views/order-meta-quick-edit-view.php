<?php
/**
 * Extra fields for the FAQ order metabox added to the Quick Edit screen.
 *
 * @package faqs_cpt
 */

namespace CarmeMias\FAQsFunctionality\src\views;

global $post;

$cm_faq_order     = get_post_meta( $post->ID, '_cm_faq_order', true );
$current_num_faqs = wp_count_posts( 'cm_faq' )->publish;
?>

<fieldset class="inline-edit-col-right">
<div class="inline-edit-col">
	<span class="title"><?php echo esc_html__( 'FAQ Order', 'faqs-functionality' ); ?></span>
	<input type="hidden" name="cm_faq_noncename" id="cm_faq_noncename" value="<?php echo esc_attr( wp_create_nonce( 'cm_faq_order' ) ); ?>" />

	<select id="_cm_faq_order" name="_cm_faq_order" class=""> <!-- The selected attribute is set with javascript -->
		<option value="not set"><?php echo esc_html__( 'Select order...', 'faqs-functionality' ); ?></option>
		<option value="hidden"
		<?php
		if ( $cm_faq_order && ( 'hidden' === $cm_faq_order ) ) {
			?>
			selected <?php } ?> ><?php echo esc_html__( 'Hidden', 'faqs-functionality' ); ?></option>
		<option value="10000"
		<?php
		if ( $cm_faq_order && ( '10000' === $cm_faq_order ) ) {
			?>
			selected <?php } ?>><?php echo esc_html__( 'Bottom of the list', 'faqs-functionality' ); ?></option>
		<?php for ( $i = 1; $i <= $current_num_faqs; $i++ ) { ?>
			<option value="<?php echo esc_attr( $i ); ?>"
			<?php
			if ( $cm_faq_order && ( strval( $i ) === $cm_faq_order ) ) {
				?>
selected <?php } ?>> <?php echo esc_attr( $i ); ?> </option>
		<?php } //end for loop ?>
	</select>
</div>
</fieldset>
