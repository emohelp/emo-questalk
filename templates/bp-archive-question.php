<?php
/**
 * The template for displaying question archive pages
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.1
 */
// if ( isset( $_GET['test'] ) ) {
	// global $wp_query;
	// print_r( $wp_query->emqa_questions );
// }
?>
<div class="emqa-questions-archive">
	<?php do_action( 'emqa_before_questions_archive' ) ?>
	
		<div class="emqa-questions-list">
		<?php do_action( 'bp_emqa_before_questions_list' ) ?>
		<?php if ( emqa_has_question() ) : ?>
			<?php while ( emqa_has_question() ) : emqa_the_question(); ?>
				<?php if ( get_post_status() == 'publish' || ( get_post_status() == 'private' && ( emqa_current_user_can( 'edit_question', get_the_ID() ) || emqa_current_user_can( 'manage_question' ) || get_current_user_id() == get_post_field( 'post_author', get_the_ID() ) ) ) ) : ?>
					<?php emqa_load_template( 'content', 'question' ) ?>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php else : ?>
			<?php emqa_load_template( 'content', 'none' ) ?>
		<?php endif; ?>
		<?php do_action( 'emqa_after_questions_list' ) ?>
		</div>
		<div class="emqa-questions-footer">
			<?php emqa_question_paginate_link() ?>
			<?php if ( emqa_current_user_can( 'post_question' ) ) : ?>
				<div class="emqa-ask-question"><a href="<?php echo esc_url(emqa_get_ask_link()); ?>"><?php esc_html_e( 'Ask Question', 'emqa' ); ?></a></div>
			<?php endif; ?>
		</div>

	<?php do_action( 'emqa_after_questions_archive' ); ?>
</div>
