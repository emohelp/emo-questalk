<?php
/**
 * The template for displaying single answers
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>
<div class="<?php echo emqa_post_class() ?>">
	<div class="emqa-answer-vote" data-nonce="<?php echo wp_create_nonce( '_emqa_answer_vote_nonce' ) ?>" data-post="<?php the_ID(); ?>">
		<span class="emqa-vote-count"><?php echo emqa_vote_count() ?></span>
		<a class="emqa-vote emqa-vote-up" href="#"><?php _e( 'Vote Up', 'emqa' ); ?></a>
		<a class="emqa-vote emqa-vote-down" href="#"><?php _e( 'Vote Down', 'emqa' ); ?></a>
	</div>
	<?php if ( emqa_current_user_can( 'edit_question', emqa_get_question_from_answer_id() ) ) : ?>
		<?php $action = emqa_is_the_best_answer() ? 'emqa-unvote-best-answer' : 'emqa-vote-best-answer' ; ?>
		<a class="emqa-pick-best-answer" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'answer' => get_the_ID(), 'action' => $action ), admin_url( 'admin-ajax.php' ) ), '_emqa_vote_best_answer' ) ) ?>"><?php _e( 'Best Answer', 'emqa' ) ?></a>
	<?php elseif ( emqa_is_the_best_answer() ) : ?>
		<span class="emqa-pick-best-answer"><?php _e( 'Best Answer', 'emqa' ) ?></span>
	<?php endif; ?>
	<div class="emqa-answer-meta">
		<?php $user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : 0 ?>
		<?php printf(
			// translators: %1$s is replaced with the author link, %2$s is replaced with the avatar image, %3$s is replaced with the author name, %4$s is replaced with the user badge, %5$s is replaced with the time difference
			( '<span><a href="%1$s">%2$s%3$s</a> %4$s answered %5$s '.__('ago', 'emqa').'</span>' ),
			emqa_get_author_link( $user_id ),
			get_avatar( $user_id, 48 ),
			get_the_author(),
			emqa_print_user_badge( $user_id ),
			human_time_diff( get_post_time( 'U', true ) )
		); ?>

		<?php if ( 'private' == get_post_status() ) : ?>
			<span><?php _e( '&nbsp;&bull;&nbsp;', 'emqa' ); ?></span>
			<span><?php _e( 'Private', 'emqa' ) ?></span>
		<?php endif; ?>
		<span class="emqa-answer-actions"><?php emqa_answer_button_action(); ?></span>
	</div>
	<div class="emqa-answer-content"><?php the_content(); ?></div>
	<?php do_action('emqa_after_show_content_answer', get_the_ID()); ?>
	<?php comments_template(); ?>
</div>
