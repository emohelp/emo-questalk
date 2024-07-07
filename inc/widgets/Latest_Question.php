<?php

class EMQA_Widgets_Latest_Question extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct() {
		$widget_ops = array( 'classname' => 'emqa-widget emqa-latest-questions', 'description' => __( 'Show a list of latest questions.', 'emqa' ) );
		parent::__construct( 'emqa-latest-question', __( 'EMQA Latest Questions', 'emqa' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$instance = wp_parse_args( $instance, array( 
			'title' => __( 'Latest Questions' , 'emqa' ),
			'number' => 5,
		) );
		
		echo wp_kses_post($before_widget);
		echo wp_kses_post($before_title);
		echo esc_html($instance['title']);
		echo wp_kses_post($after_title);
		
		$args = array(
			'posts_per_page'    => $instance['number'],
			'order'             => 'DESC',
			'orderby'           => 'post_date',
			'post_type'         => 'emqa-question',
			'suppress_filters'  => false,
		);
		$questions = new WP_Query( $args );
		if ( $questions->have_posts() ) {
			echo '<div class="emqa-popular-questions">';
			echo '<ul>';
			while ( $questions->have_posts() ) { $questions->the_post( );
				echo '<li>';
				echo '<a href="'. esc_url(get_permalink()) .'" class="question-title">';
				the_title();
				echo '</a>';
				echo ' '.esc_html__( 'asked by', 'emqa' ) . ' ' . get_the_author_link();
				if ( isset( $instance['question_date'] ) && $instance['question_date'] ) {
					// translators: %s is replaced with the login URL
					echo ', ' . sprintf( esc_html__( '%s ago', 'emqa' ), esc_html(human_time_diff( get_post_time('U', true, get_the_ID() ) ) ) ) . '.';
				}
				echo '</li>';
			}   
			echo '</ul>';
			echo '</div>';
		}
		wp_reset_query( );
		wp_reset_postdata( );
		echo wp_kses_post($after_widget);
	}

	function update( $new_instance, $old_instance ) {

		// update logic goes here
		$updated_instance = $new_instance;
		return $updated_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( $instance, array( 
			'title' => '',
			'number' => 5,
			'question_date' => false
		) );

		?>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'title' ) ) ?>"><?php esc_html_e( 'Widget title', 'emqa' ) ?></label>
		<input type="text" name="<?php echo esc_attr($this->get_field_name( 'title' )) ?>" id="<?php echo esc_attr($this->get_field_id( 'title' )) ?>" value="<?php echo esc_attr($instance['title']) ?>" class="widefat">
		</p>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'number' )) ?>"><?php esc_html_e( 'Number of posts', 'emqa' ) ?></label>
		<input type="text" name="<?php echo esc_attr($this->get_field_name( 'number' )) ?>" id="<?php echo esc_attr($this->get_field_id( 'number' )) ?>" value="<?php echo esc_attr($instance['number']) ?>" class="widefat">
		</p>
		<p>
			<input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'question_date' )) ?>" id="<?php echo esc_attr($this->get_field_id( 'question_date' )) ?>" <?php checked( 'on', $instance['question_date'] ) ?> class="widefat">
			<label for="<?php echo esc_attr($this->get_field_id( 'question_date' )) ?>"><?php esc_html_e( 'Show question date', 'emqa' ) ?></label>
		</p>
		<?php
	}
}

?>