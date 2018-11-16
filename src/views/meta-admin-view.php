<?php
namespace CarmeMias\FAQsFunctionality\src\views;

// Get the data if its already been entered.
$cm_faq_order     = get_post_meta( $post->ID, '_cm_faq_order', true );
$current_num_faqs = wp_count_posts( 'cm_faq' )->publish;
// if in add new screen, increase num of faqs by 1.
$screen = get_current_screen();
if ( 'add' === $screen->action ) {
	$current_num_faqs++;
}

?>
<style>
	.cm_faq_admin_notice select { background-color:#ffb900; }
</style>

<table class="form-table">
	<tr>
		<td><?php echo esc_html__( 'Choose the question\'s position in the list:', 'faqs-functionality' ); ?></br><em><?php echo esc_html__( 'Low numbers show first.', 'faqs-functionality' ); ?></em></td>
	</tr>
	<tr style="border-bottom: 1px solid #eee;">
		<td
		<?php
		if ( ( '10000' !== $cm_faq_order ) && ( intval( $cm_faq_order ) > intval( $current_num_faqs ) ) ) {
			?>
			class="cm_faq_admin_notice"
			<?php
		}
		?>
		> <label>FAQ Order:</label>
			<select id="_cm_faq_order" name="_cm_faq_order" class="" value="<?php echo esc_attr( $cm_faq_order ); ?>">
				<option value="not set"><?php echo esc_html__( 'Select order...', 'faqs-functionality' ); ?></option>
				<option value="hidden"><?php echo esc_html__( 'Do not show', 'faqs-functionality' ); ?></option>
				<option value="10000"
				<?php
				if ( $cm_faq_order && ( '10000' === $cm_faq_order ) ) {
					?>
					selected <?php } ?> >
				<?php echo esc_html__( 'Bottom of the list', 'faqs-functionality' ); ?>
				</option>
				<?php for ( $i = 1; $i <= $current_num_faqs; $i++ ) { ?>
					<option value="<?php echo esc_attr( $i ); ?>"
											<?php
											if ( $cm_faq_order && ( strval( $i ) === $cm_faq_order ) ) {
												?>
						selected <?php } ?> >
						<?php echo esc_html( $i ); ?>
					</option>
				<?php } //end for loop ?>
			</select>
		</td><!-- FAQ order -->
	</tr>
</table>
