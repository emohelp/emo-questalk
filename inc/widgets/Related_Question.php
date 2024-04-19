<?php

class EMQA_Widgets_Related_Question extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct() {
		$widget_ops = array( 'classname' => 'emqa-widget emqa-related-questions', 'description' => __( 'Show a list of questions that related to a question. Just show in single question page', 'em-question-answer' ) );
		parent::__construct( 'emqa-related-question', __( 'EMQA Related Questions', 'em-question-answer' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$instance = wp_parse_args( $instance, array( 
			'title'	=> '',
			'number' => 5,
		) );
		$post_type = get_post_type();
		if ( is_single() && ( $post_type == 'emqa-question' || $post_type == 'emqa-answer' ) ) {

			echo $before_widget;
			echo $before_title;
			echo $instance['title'];
			echo $after_title;
			echo '<div class="related-questions">';
			emqa_related_question( false, $instance['number'] );
			echo '</div>';
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {

		// update logic goes here
		$updated_instance = $new_instance;
		return $updated_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( $instance, array( 
			'title'	=> '',
			'number' => 5,
		) );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Widget title', 'em-question-answer' ) ?></label>
		<input type="text" name="<?php echo $this->get_field_name( 'title' ) ?>" id="<?php echo $this->get_field_id( 'title' ) ?>" value="<?php echo sanitize_text_field( $instance['title'] ); ?>" class="widefat">
		</p>
		<p><label for="<?php echo $this->get_field_id( 'number' ) ?>"><?php _e( 'Number of posts', 'em-question-answer' ) ?></label>
		<input type="text" name="<?php echo $this->get_field_name( 'number' ) ?>" id="<?php echo $this->get_field_id( 'number' ) ?>" value="<?php echo intval( $instance['number'] ); ?>" class="widefat">
		</p>
		<?php
	}
}

?>