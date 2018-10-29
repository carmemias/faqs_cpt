<?php
namespace CarmeMias\FAQsFunctionality\src\views;

$current_num_faqs = wp_count_posts( 'cm_faq' )->publish;
?>

<fieldset class="inline-edit-col-right">
<div class="inline-edit-col">
	<span class="title"><?php echo esc_html__( 'FAQ Order', 'faqs-functionality' ); ?></span>
	<input type="hidden" name="cm_faq_noncename" id="cm_faq_noncename" value="<?php echo esc_attr( wp_create_nonce( 'cm_faq_order' ) ); ?>" />

	<select id="_cm_faq_order" name="_cm_faq_order" class=""> <!-- The selected attribute is set with javascript -->
		<option value="not set"><?php echo esc_html__( 'Select order...', 'faqs-functionality' ); ?></option>
		<option value="hidden"><?php echo esc_html__( 'Do not show', 'faqs-functionality' ); ?></option>
		<option value="10000"><?php echo esc_html__( 'Bottom of the list', 'faqs-functionality' ); ?></option>
		<?php for ( $i = 1; $i <= $current_num_faqs; $i++ ) { ?>
			<option value="<?php echo esc_attr( $i ); ?>"> <?php echo esc_attr( $i ); ?> </option>
		<?php } //end for loop ?>
	</select>
</div>
</fieldset>
