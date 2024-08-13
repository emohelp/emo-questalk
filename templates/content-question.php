<?php
/**
 * The template for displaying question content
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */

?>
<div class="<?php echo esc_attr(emqa_post_class()); ?>">
	<div class="emqa-question-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
	<div class="emqa-question-meta">
		<?php emqa_question_print_status() ?>
		<?php
			global $post;
			$user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : false;
			$time = human_time_diff( get_post_time( 'U', true ) );
			$text = __( 'asked', 'emqa' );
			$latest_answer = emqa_get_latest_answer();
			if ( $latest_answer ) {
				$time = human_time_diff( strtotime( $latest_answer->post_date_gmt ) );
				$text = __( 'answered', 'emqa' );
			}
		?>
		<?php printf(
			// translators: %1$s is replaced with the author link, %2$s is replaced with the avatar image, %3$s is replaced with the author name, %4$s is replaced with additional text, %5$s is replaced with the time difference
			'<span><a href="%1$s">%2$s%3$s</a> %4$s %5$s ' . esc_html__('ago', 'emqa') . '</span>',
			esc_url( emqa_get_author_link( $user_id ) ), // Escaping URL
			get_avatar( $user_id, 48 ), // Assuming `get_avatar()` is safe
			esc_html( get_the_author() ), // Escaping author name
			esc_html( $text ), // Escaping additional text
			esc_html( $time ) // Escaping time difference
		); ?>


		<?php echo get_the_term_list( get_the_ID(), 'emqa-question_category', '<span class="emqa-question-category">' . __( '&nbsp;&bull;&nbsp;', 'emqa' ), ', ', '</span>' ); ?>
	</div>
	<div class="emqa-question-stats">
		<span class="emqa-views-count">
			<?php $views_count_escaped = emqa_question_views_count(); ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				wp_kses( __( '<strong>%1$s</strong> views', 'emqa' ), array( 'strong' => array() ) ),
				esc_html($views_count_escaped ) );  ?>
		</span>
		<span class="emqa-answers-count">
			<?php $answers_count_escaped = emqa_question_answers_count(); ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				wp_kses( __( '<strong>%1$s</strong> answers', 'emqa' ), array('strong' => array() ) ), 
				esc_html($answers_count_escaped) ); ?>
		</span>
		<span class="emqa-votes-count">
			<?php $vote_count_escaped = emqa_vote_count(); ?>
			<?php printf(
				// translators: %1$s is replaced with the version number
				wp_kses( __( '<strong>%1$s</strong> votes', 'emqa' ), array('strong' => array() ) ), 
				esc_html($vote_count_escaped) ); ?>
		</span>
	</div>
</div>
