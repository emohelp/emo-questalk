<?php
/**
 * The template for displaying a message that questions cannot be found
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>
<?php if ( ! emqa_current_user_can( 'read_question' ) ) : ?>
	<div class="emqa-alert emqa-alert-info"><?php esc_html_e( 'You do not have permission to view questions', 'emqa' ) ?></div>
<?php else : ?>
	<div class="emqa-alert emqa-alert-info"><?php esc_html_e( 'Sorry, but nothing matched your filter', 'emqa' ) ?></div>
<?php endif; ?>
