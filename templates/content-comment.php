<?php
/**
 * The template for displaying content comment
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>

<?php global $comment; ?>
<div class="emqa-comment">
	<div class="emqa-comment-meta">
		<?php $user = get_user_by( 'id', $comment->user_id ); ?>
		<a href="<?php echo esc_url(emqa_get_author_link( $comment->user_id )); ?>"><?php echo get_avatar( $comment->user_id, 16 ) ?><?php echo esc_html(get_comment_author()) ?></a>
		<?php emqa_print_user_badge( $comment->user_id, true ); ?>
		<?php printf(
			// translators: %s is replaced with human-readable time difference
			esc_html_x( 'replied %s ago', '%s = human-readable time difference', 'emqa' ),
			esc_html( human_time_diff( get_comment_time( 'U', true ) ) )
		); ?>

		<div class="emqa-comment-actions">
			<?php if ( emqa_current_user_can( 'edit_comment' ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'comment_edit' => $comment->comment_ID ) ) ) ?>"><?php esc_html_e( 'Edit', 'emqa' ) ?></a>
			<?php endif; ?>
			<?php if ( emqa_current_user_can( 'delete_comment' ) ) : ?>
				<a class="emqa-delete-comment" href="<?php echo esc_url(wp_nonce_url( add_query_arg( array( 'action' => 'emqa-action-delete-comment', 'comment_id' => $comment->comment_ID ), admin_url( 'admin-ajax.php' ) ), '_emqa_delete_comment' )) ?>"><?php esc_html_e( 'Delete', 'emqa' ) ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php comment_text(); ?>
</div>
