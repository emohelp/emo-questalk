<?php
/**
 * The template for displaying all single questions
 *
 * @package EMO Questalk 
 * @since EMO Questalk 1.0.0
 */
// global $wp_query; print_r( $wp_query );
?>
<div class="emqa-single-question">
<?php if ( have_posts() ) : ?>
	<?php do_action( 'emqa_before_single_question' ) ?>
	<?php do_action( 'emqa_before_single_question_comment_notice', true ) ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( !emqa_is_edit() ) : ?>
			<?php emqa_load_template( 'content', 'single-question' ) ?>
		<?php else : ?>
			<?php emqa_load_template( 'content', 'edit' ) ?>
		<?php endif; ?>
	<?php endwhile; ?>
	<?php do_action( 'emqa_after_single_question' ) ?>
<?php endif; ?>
</div>
