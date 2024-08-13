<?php

class EMQA_Widgets_Related_Question extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct() {
		$widget_ops = array( 'classname' => 'emqa-widget emqa-related-questions', 'description' => __( 'Show a list of questions that related to a question. Just show in single question page', 'emqa' ) );
		parent::__construct( 'emqa-related-question', __( 'EMQA Related Questions', 'emqa' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$instance = wp_parse_args( $instance, array( 
			'title'	=> '',
			'number' => 5,
		) );
		$post_type = get_post_type();
		if ( is_single() && ( $post_type == 'emqa-question' || $post_type == 'emqa-answer' ) ) {

			echo wp_kses_post($before_widget);
			echo wp_kses_post($before_title);
			echo wp_kses_post($instance['title']);
			echo wp_kses_post($after_title);
			echo '<div class="related-questions">';
			emqa_related_question( false, $instance['number'] );
			echo '</div>';
			echo wp_kses_post($after_widget);
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
		<p><label for="<?php echo esc_attr($this->get_field_id( 'title' )) ?>"><?php esc_html_e( 'Widget title', 'emqa' ) ?></label>
		<input type="text" name="<?php echo esc_attr($this->get_field_name( 'title' )) ?>" id="<?php echo esc_attr($this->get_field_id( 'title' )) ?>" value="<?php echo esc_attr(sanitize_text_field( $instance['title'] )); ?>" class="widefat">
		</p>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'number' )) ?>"><?php esc_html_e( 'Number of posts', 'emqa' ) ?></label>
		<input type="text" name="<?php echo esc_attr($this->get_field_name( 'number' )) ?>" id="<?php echo esc_attr($this->get_field_id( 'number' )) ?>" value="<?php echo esc_attr(intval( $instance['number'] )); ?>" class="widefat">
		</p>
		<?php
	}
}

?>