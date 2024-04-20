<?php
/**
 * The template for displaying single questions
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>

<?php do_action( 'emqa_before_single_question_content' ); ?>
<div class="emqa-question-item">
	<div class="emqa-question-vote" data-nonce="<?php echo wp_create_nonce( '_emqa_question_vote_nonce' ) ?>" data-post="<?php the_ID(); ?>">
		<span class="emqa-vote-count"><?php echo emqa_vote_count() ?></span>
		<a class="emqa-vote emqa-vote-up" href="#"><?php _e( 'Vote Up', 'em-question-answer' ); ?></a>
		<a class="emqa-vote emqa-vote-down" href="#"><?php _e( 'Vote Down', 'em-question-answer' ); ?></a>
	</div>
	<div class="emqa-question-meta">
		<?php $user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : false ?>
		<?php 
			$post_id = get_the_ID();
			$post_time_created = get_post_field( 'post_date', $post_id );
		?>
		<?php printf(
    // translators: %1$s is replaced with the author link, %2$s is replaced with the avatar image, %3$s is replaced with the author name, %4$s is replaced with the user badge, %5$s is replaced with the time difference
    ( '<span><a href="%1$s">%2$s%3$s</a> %4$s asked %5$s '.__('ago', 'em-question-answer').'</span>'),
    emqa_get_author_link( $user_id ),
    get_avatar( $user_id, 48 ),
    get_the_author(),
    emqa_print_user_badge( $user_id ),
    $time_diff_created = human_time_diff( strtotime( $post_time_created ) )
	); ?>

		<span class="emqa-question-actions"><?php emqa_question_button_action() ?></span>
	</div>
	<div class="emqa-question-content"><?php the_content(); ?></div>
	<?php do_action('emqa_after_show_content_question', get_the_ID()); ?>
	<div class="emqa-question-footer">
		<div class="emqa-question-meta">
			<?php echo get_the_term_list( get_the_ID(), 'emqa-question_tag', '<span class="emqa-question-tag">' . __( 'Question Tags: ', 'em-question-answer' ), ', ', '</span>' ); ?>
			<?php if ( emqa_current_user_can( 'edit_question', get_the_ID() ) ) : ?>
				<?php if ( emqa_is_enable_status() ) : ?>
				<span class="emqa-question-status">
					<?php _e( 'This question is:', 'em-question-answer' ) ?>
					<select id="emqa-question-status" data-nonce="<?php echo wp_create_nonce( '_emqa_update_privacy_nonce' ) ?>" data-post="<?php the_ID(); ?>">
						<optgroup label="<?php _e( 'Status', 'em-question-answer' ); ?>">
							<option <?php selected( emqa_question_status(), 'open' ) ?> value="open"><?php _e( 'Open', 'em-question-answer' ) ?></option>
							<option <?php selected( emqa_question_status(), 'closed' ) ?> value="closed"><?php _e( 'Closed', 'em-question-answer' ) ?></option>
							<option <?php selected( emqa_question_status(), 'resolved' ) ?> value="resolved"><?php _e( 'Resolved', 'em-question-answer' ) ?></option>
						</optgroup>
					</select>
					</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php do_action( 'emqa_before_single_question_comment' ) ?>
	<?php comments_template(); ?>
	<?php do_action( 'emqa_after_single_question_comment' ) ?>
</div>
<?php do_action( 'emqa_after_single_question_content' ); ?>
