<?php
/**
 *  em Question Answer Shortcode
 */
class EMQA_Shortcode {
	private $shortcodes = array(
		'emqa-list-questions',
		'emqa-submit-question-form', 
		'emqa-popular-questions',
		'emqa-latest-answers',
		'emqa-question-followers',
		'emqa-question-list'
	);

	public function __construct() {
		if ( ! defined( 'EMQA_DIR' ) ) {
			return false;
		}

		if(is_admin()){
			//only use on frontend
			return false;
		}
		
		add_shortcode( 'emqa-list-questions', array( $this, 'archive_question') );
		add_shortcode( 'emqa-submit-question-form', array( $this, 'submit_question_form_shortcode') );
		add_shortcode( 'emqa-popular-questions', array( $this, 'shortcode_popular_questions' ) );
		add_shortcode( 'emqa-latest-answers', array( $this, 'shortcode_latest_answers' ) );
		add_shortcode( 'emqa-question-followers', array( $this, 'question_followers' ) );
		//add_shortcode( 'emqa-question-list', array( $this, 'question_list' ) );
		add_filter( 'the_content', array( $this, 'post_content_remove_shortcodes' ), 0 );
	}

	public function sanitize_output( $buffer ) {
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s',       // shorten multiple whitespace sequences
			"/\r/",
			"/\n/",
			"/\t/",
			'/<!--[^>]*>/s',
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			'',
			'',
			'',
			'',
		);

		$buffer = preg_replace( $search, $replace, $buffer );
		return $buffer;
	}

	public function archive_question( $atts = array() ) {
		global $wp_query, $emqa, $script_version, $emqa_sript_vars, $emqa_atts;
		$emqa_atts = (array)$atts;
		$emqa_atts['page_id'] = isset($wp_query->post) && isset($wp_query->post->ID) && $wp_query->post->ID ? $wp_query->post->ID : 0;
		ob_start();

		if ( isset( $atts['category'] ) ) {
			$atts['tax_query'][] = array(
				'taxonomy' => 'emqa-question_category',
				'terms' => esc_html( $atts['category'] ),
				'field' => 'slug'
			);
			unset( $atts['category'] );
		}

		if ( isset( $atts['tag'] ) ) {
			$atts['tax_query'][] = array(
				'taxonomy' => 'emqa-question_tag',
				'terms' => esc_html( $atts['tag'] ),
				'field' => 'slug'
			);
			unset( $atts['tag'] );
		}

		$emqa->template->remove_all_filters( 'the_content' );
		emqa()->filter->prepare_archive_posts( $atts );
		echo '<div class="emqa-container" >';
		emqa_load_template( 'archive', 'question' );
		echo '</div>';
		$html = ob_get_contents();

		$emqa->template->restore_all_filters( 'the_content' );

		ob_end_clean();
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'emqa-questions-list', EMQA_URI . 'templates/assets/js/emqa-questions-list.js', array( 'jquery', 'jquery-ui-autocomplete' ), $script_version, true );
		wp_localize_script( 'emqa-questions-list', 'emqa', $emqa_sript_vars );
		return apply_filters( 'emqa-shortcode-question-list-content', $this->sanitize_output( $html ) );
	}

	public function submit_question_form_shortcode() {
		global $emqa, $emqa_sript_vars, $script_version;
		ob_start();

		$emqa->template->remove_all_filters( 'the_content' );

		echo '<div class="emqa-container" >';
		emqa_load_template( 'question', 'submit-form' );
		echo '</div>';
		$html = ob_get_contents();

		$emqa->template->restore_all_filters( 'the_content' );

		ob_end_clean();
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'emqa-submit-question', EMQA_URI . 'templates/assets/js/emqa-submit-question.js', array( 'jquery', 'jquery-ui-autocomplete' ), $script_version, true );
		wp_localize_script( 'emqa-submit-question', 'emqa', $emqa_sript_vars );
		return $this->sanitize_output( $html );
	}

	public function shortcode_popular_questions( $atts ){
		extract( shortcode_atts( array(
			'number' => 5,
			'title' => __( 'Popular Questions', 'emqa' ),
		), $atts ) );

		$args = array(
			'posts_per_page'       => $number,
			'order'             => 'DESC',
			'orderby'           => 'meta_value_num',
			'meta_key'           => '_emqa_views',
			'post_type'         => 'emqa-question',
			'suppress_filters'  => false,
		);
		$questions = new WP_Query( $args );
		$html = '';

		if ( $title ) {
			$html .= '<h3>';
			$html .= $title;
			$html .= '</h3>';
		}
		if ( $questions->have_posts() ) {
			$html .= '<div class="emqa-popular-questions">';
			$html .= '<ul>';
			while ( $questions->have_posts() ) { $questions->the_post();
				$html .= '<li><a href="'.get_permalink().'" class="question-title">'.get_the_title().'</a> '.__( 'asked by', 'emqa' ).' ' . get_the_author_link() . '</li>';
			}   
			$html .= '</ul>';
			$html .= '</div>';
		}
		wp_reset_query();
		wp_reset_postdata();
		return $html;
	}

	public function shortcode_latest_answers( $atts ){

		extract( shortcode_atts( array(
			'number' => 5,
			'title' => __( 'Latest Answers', 'emqa' )
		), $atts ) );

		$args = array(
			'posts_per_page'    => $number,
			'post_type'         => 'emqa-answer',
			'suppress_filters'  => false,
		);
		$questions = new WP_Query( $args );
		$html = '';

		if ( $title ) {
			$html .= '<h3>';
			$html .= $title;
			$html .= '</h3>';
		}
		if ( $questions->have_posts() ) {
			$html .= '<div class="emqa-latest-answers">';
			$html .= '<ul>';
			while ( $questions->have_posts() ) { $questions->the_post();
				$answer_id = get_the_ID();
				$question_id = emqa_get_post_parent_id( $answer_id );
				if ( 'publish' != get_post_status( $question_id ) ) {
					continue;
				}
				if ( $question_id ) {
					$html .= '<li>'.__( 'Answer at', 'emqa' ).' <a href="'.get_permalink( $question_id ).'#answer-'.$answer_id.'" title="'.__( 'Link to', 'emqa' ).' '.get_the_title( $question_id ).'">'.get_the_title( $question_id ).'</a></li>';
				}
			}   
			$html .= '</ul>';
			$html .= '</div>';
		}
		wp_reset_query();
		wp_reset_postdata();
		return $html;
	}

	function question_followers( $atts ) {
		extract( shortcode_atts( array(
			'id'    => false,
			'before_title'  => '<h3 class="small-title">',
			'after_title'   => '</h3>',
		), $atts ) );
		if ( ! $id ) {
			global $post;
			$id = $post->ID;
		}
		$followers = emqa_get_following_user( $id );
		$question = get_post( $id );
		$followers[] = $question->post_author;
		if ( ! empty( $followers ) ) :
			echo '<div class="question-followers">';
			echo wp_kses_post($before_title);
			$count = count( $followers );
			printf(
    		// translators: %d is replaced with the number of people following the question
					esc_html__(
						_n(
								'%d person who is following this question',
								'%d people who are following this question',
								$count,
								'emqa'
						),
						'emqa'
				),
				esc_html( $count )
			);
			echo wp_kses_post($after_title);

			foreach ( $followers as $follower ) :
				$user_info = get_userdata( $follower );
				if ( $user_info ) :
					echo '<a href="' . esc_url( home_url() . '/profile/' . $user_info->user_nicename ) . '" title="' . esc_attr( $user_info->display_name ) . '">' . get_avatar( $follower, 32 ) . '</a>&nbsp;';
				endif;
			endforeach;
			echo '</div>';
		endif;
	}
   
	function post_content_remove_shortcodes( $content ) {
		$shortcodes = array(
			'emqa-list-questions',
			'emqa-submit-question-form',
		);
		if ( is_singular( 'emqa-question' ) || is_singular( 'emqa-answer' ) ) {
			foreach ( $shortcodes as $shortcode_tag ) 
				remove_shortcode( $shortcode_tag );
		}
		/* Return the post content. */
		return $content;
	}

	function question_list( $atts ) {
		extract( shortcode_atts( array(
			'categories' 	=> '',
			'number' 		=> '',
			'title' 		=> __( 'Question List', 'emqa' ),
			'orderby' 		=> 'modified',
			'order' 		=> 'DESC'
		), $atts ) );

		$args = array(
			'post_type' 		=> 'emqa-question',
			'posts_per_page' 	=> $number,
			'orderby' 			=> $orderby,
			'order' 			=> $order,
		);

		if ( $term ) {
			$args['tax_query'][] = array(
				'taxonomy' 	=> 'emqa-question_category',
				'terms' 	=> explode( ',', $categories ),
				'field' 	=> 'slug'
			);
		}

		if ( $title ) {
			echo '<h3>';
			echo esc_html($title);
			echo '</h3>';
		}

	}
}

?>