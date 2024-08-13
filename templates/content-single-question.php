<?php
/**
 * The template for displaying single questions
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>

<?php do_action( 'emqa_before_single_question_content' ); ?>
<div class="emqa-question-item">
	<div class="emqa-question-vote" data-nonce="<?php echo esc_attr(wp_create_nonce( '_emqa_question_vote_nonce' )) ?>" data-post="<?php the_ID(); ?>">
		<span class="emqa-vote-count"><?php echo esc_html(emqa_vote_count()) ?></span>
		<a class="emqa-vote emqa-vote-up" href="#"><?php esc_html_e( 'Vote Up', 'emqa' ); ?></a>
		<a class="emqa-vote emqa-vote-down" href="#"><?php esc_html_e( 'Vote Down', 'emqa' ); ?></a>
	</div>
	<div class="emqa-question-meta">
		<?php $user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : false ?>
		<?php 
			$post_id = get_the_ID();
			$post_time_created = get_post_field( 'post_date', $post_id );
		?>
		<?php printf(
			// translators: %1$s is replaced with the author link, %2$s is replaced with the avatar image, %3$s is replaced with the author name, %4$s is replaced with the user badge, %5$s is replaced with the time difference
			'<span><a href="%1$s">%2$s%3$s</a> %4$s asked %5$s ' . esc_html__('ago', 'emqa') . '</span>',
			esc_url( emqa_get_author_link( $user_id ) ), // Escaping URL
			get_avatar( $user_id, 48 ), // No escaping needed here as `get_avatar()` returns safe HTML
			esc_html( get_the_author() ), // Escaping HTML
			wp_kses_post(emqa_print_user_badge( $user_id )), // Assuming this returns safe HTML
			esc_html( human_time_diff( strtotime( $post_time_created ) ) ) // Escaping time difference
		); ?>

		<span class="emqa-question-actions"><?php emqa_question_button_action() ?></span>
	</div>
	<div class="emqa-question-content"><?php the_content(); ?></div>
	<?php do_action('emqa_after_show_content_question', get_the_ID()); ?>
	<div class="emqa-question-footer">
		<div class="emqa-question-meta">
			<?php echo get_the_term_list( get_the_ID(), 'emqa-question_tag', '<span class="emqa-question-tag">' . __( 'Question Tags: ', 'emqa' ), ', ', '</span>' ); ?>
			<?php if ( emqa_current_user_can( 'edit_question', get_the_ID() ) ) : ?>
				<?php if ( emqa_is_enable_status() ) : ?>
				<span class="emqa-question-status">
					<?php esc_html_e( 'This question is:', 'emqa' ) ?>
					<select id="emqa-question-status" data-nonce="<?php echo esc_attr(wp_create_nonce( '_emqa_update_privacy_nonce' )) ?>" data-post="<?php the_ID(); ?>">
						<optgroup label="<?php esc_html_e( 'Status', 'emqa' ); ?>">
							<option <?php selected( emqa_question_status(), 'open' ) ?> value="open"><?php esc_html_e( 'Open', 'emqa' ) ?></option>
							<option <?php selected( emqa_question_status(), 'closed' ) ?> value="closed"><?php esc_html_e( 'Closed', 'emqa' ) ?></option>
							<option <?php selected( emqa_question_status(), 'resolved' ) ?> value="resolved"><?php esc_html_e( 'Resolved', 'emqa' ) ?></option>
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
