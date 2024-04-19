<?php
/**
 * The template for displaying comments form
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */
?>

<?php if ( comments_open() ) : ?>
<div class="emqa-comments">
	<?php do_action( 'emqa_before_comments' ) ?>
	<div class="emqa-comments-list">
		<?php do_action( 'emqa_before_comments_list' ); ?>
		<?php if ( have_comments() ) : ?>
		<?php wp_list_comments( array( 'callback' => 'emqa_question_comment_callback' ) ); ?>
		<?php endif; ?>
		<?php do_action( 'dqwa_after_comments_list' ); ?>
	</div>
	<?php if ( ! emqa_is_closed( get_the_ID() ) && emqa_current_user_can( 'post_comment' ) ) : ?>
		<?php
			$args = array(
				'id_form' => 'comment_form_' . get_the_ID(),
			);
		?>
		<?php emqa_comment_form( $args ); ?>
	<?php endif; ?>
	<?php do_action( 'emqa_after_comments' ); ?>
</div>
<?php endif; ?>
