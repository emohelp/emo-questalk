<?php

class EMQA_Widgets_Closed_Question extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct() {
		$widget_ops = array( 'classname' => 'emqa-widget emqa-closed-questions', 'description' => __( 'Show a list of questions marked as closed.', 'emqa' ) );
		parent::__construct( 'emqa-closed-question', __( 'EMQA Closed Questions', 'emqa' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$instance = wp_parse_args( $instance, array( 
			'title' => __( 'Closed Questions', 'emqa' ),
			'number' => 5,
		) );
		
		echo wp_kses_post($before_widget);
		echo wp_kses_post($before_title);
		echo wp_kses_post($instance['title']);
		echo wp_kses_post($after_title);
		$args = array(
			'post_type' => 'emqa-question',
			'posts_per_page' => $instance['number'],
			// @phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => '_emqa_status',
					'compare' => '=',
					'value' => 'resolved',
				),
				array(
					'key' => '_emqa_status',
					'compare' => '=',
					'value' => 'closed',
				),
			),
		);
		$questions = new WP_Query( $args );
		if ( $questions->have_posts() ) {
			echo '<div class="emqa-popular-questions">';
			echo '<ul>';
			while ( $questions->have_posts() ) { $questions->the_post( );
				echo '
				<li>
					<a href="' . esc_url( get_permalink() ) . '" class="question-title">
						' . esc_html( get_the_title() ) . '
					</a> 
					' . esc_html__( 'asked by', 'emqa' ) . ' 
					' . get_the_author_link() . '
				</li>';			
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
		) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Widget title', 'emqa' ); ?>
			</label>
			<input
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				value="<?php echo esc_attr( $instance['title'] ); ?>"
				class="widefat"
			>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<?php esc_html_e( 'Number of posts', 'emqa' ); ?>
			</label>
			<input
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
				value="<?php echo esc_attr( $instance['number'] ); ?>"
				class="widefat"
			>
		</p>

		<?php
	}
}
?>