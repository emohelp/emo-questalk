<?php
/**
 * The template for displaying a message that questions cannot be found
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>
<?php if ( ! emqa_current_user_can( 'read_question' ) ) : ?>
	<div class="emqa-alert emqa-alert-info"><?php _e( 'You do not have permission to view questions', 'em-question-answer' ) ?></div>
<?php else : ?>
	<div class="emqa-alert emqa-alert-info"><?php _e( 'Sorry, but nothing matched your filter', 'em-question-answer' ) ?></div>
<?php endif; ?>
