<?php
/**
 * The template for displaying answers
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>
<div class="emqa-answers">
	<?php do_action( 'emqa_before_answers' ) ?>
	<?php if ( emqa_has_answers() ) : ?>
	<div class="emqa-answers-title"><?php printf(
		// translators: %s is replaced with the version number
		__( '%s Answers', 'em-question-answer' ), emqa_question_answers_count( get_the_ID() ) ) ?></div>
	<div class="emqa-answers-list">
		<?php do_action( 'emqa_before_answers_list' ) ?>
			<?php while ( emqa_has_answers() ) : emqa_the_answers(); ?>
				<?php $question_id = emqa_get_post_parent_id( get_the_ID() ); ?>
				<?php if ( ( 'private' == get_post_status() && ( emqa_current_user_can( 'edit_answer', get_the_ID() ) || emqa_current_user_can( 'edit_question', $question_id ) ) ) || 'publish' == get_post_status() ) : ?>
					<?php emqa_load_template( 'content', 'single-answer' ); ?>
				<?php endif; ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php do_action( 'emqa_after_answers_list' ) ?>
	</div>
	<?php endif; ?>
	<?php if ( emqa_current_user_can( 'post_answer' ) && !emqa_is_closed( get_the_ID() ) ) : ?>
		<?php emqa_load_template( 'answer', 'submit-form' ) ?>
	<?php endif; ?>
	<?php do_action( 'emqa_after_answers' ); ?>
</div>
