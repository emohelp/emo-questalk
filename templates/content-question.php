<?php
/**
 * The template for displaying question content
 *
 * @package em Question & Answer
 * @since em Question & Answer 1.4.3
 */

?>
<div class="<?php echo emqa_post_class(); ?>">
	<div class="emqa-question-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
	<div class="emqa-question-meta">
		<?php emqa_question_print_status() ?>
		<?php
			global $post;
			$user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : false;
			$time = human_time_diff( get_post_time( 'U', true ) );
			$text = __( 'asked', 'em-question-answer' );
			$latest_answer = emqa_get_latest_answer();
			if ( $latest_answer ) {
				$time = human_time_diff( strtotime( $latest_answer->post_date_gmt ) );
				$text = __( 'answered', 'em-question-answer' );
			}
		?>
		<?php printf(
			// translators: %1$s is replaced with the author link, %2$s is replaced with the avatar image, %3$s is replaced with the author name, %4$s is replaced with additional text, %5$s is replaced with the time difference
			( '<span><a href="%1$s">%2$s%3$s</a> %4$s %5$s '.__('ago', 'em-question-answer').'</span>'),
			emqa_get_author_link( $user_id ),
			get_avatar( $user_id, 48 ),
			get_the_author(),
			$text,
			$time
		); ?>

		<?php echo get_the_term_list( get_the_ID(), 'emqa-question_category', '<span class="emqa-question-category">' . __( '&nbsp;&bull;&nbsp;', 'em-question-answer' ), ', ', '</span>' ); ?>
	</div>
	<div class="emqa-question-stats">
		<span class="emqa-views-count">
			<?php $views_count = emqa_question_views_count() ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				__( '<strong>%1$s</strong> views', 'em-question-answer' ), $views_count ); ?>
		</span>
		<span class="emqa-answers-count">
			<?php $answers_count = emqa_question_answers_count(); ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				__( '<strong>%1$s</strong> answers', 'em-question-answer' ), $answers_count ); ?>
		</span>
		<span class="emqa-votes-count">
			<?php $vote_count = emqa_vote_count() ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				__( '<strong>%1$s</strong> votes', 'em-question-answer' ), $vote_count ); ?>
		</span>
	</div>
</div>
