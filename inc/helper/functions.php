<?php  
/** 
 * This file was used to include all functions which i can't classify, just use those for support my work
 */

/** 
 * Array
 */
function emqa_array_insert( &$array, $element, $position = null ) {
	if ( is_array( $element ) ) {
		$part = $element;
	} else {
		$part = array( $position => $element );
	}

	$len = count( $array );

	$firsthalf = array_slice( $array, 0, $len / 2 );
	$secondhalf = array_slice( $array, $len / 2 );

	$array = array_merge( $firsthalf, $part, $secondhalf );
	return $array;
}

if ( ! function_exists( 'em_strip_email_to_display' ) ) { 
	/**
	 * Strip email for display in front end
	 * @param  string  $text name
	 * @param  boolean $echo Display or just return
	 * @return string        New text that was stripped
	 */
	function em_strip_email_to_display( $text, $echo = false ) {
		preg_match( '/( [^\@]* )\@( .* )/i', $text, $matches );
		if ( ! empty( $matches ) ) {
			$text = $matches[1] . '@...';
		}
		if ( $echo ) {
			echo $text;
		}
		return $text;
	}
}  

// CAPTCHA
function emqa_valid_captcha( $type ) {
	global $emqa_general_settings;

	if ( 'question' == $type && ! emqa_is_captcha_enable_in_submit_question() ) {
		return true;
	}

	if ( 'single-question' == $type && ! emqa_is_captcha_enable_in_single_question() ) {
		return true;
	}
	
	return apply_filters( 'emqa_valid_captcha', false );
}

add_filter( 'emqa_valid_captcha', 'emqa_recaptcha_check' );
function emqa_recaptcha_check( $res ) {
	global $emqa_general_settings;
	$type_selected = isset( $emqa_general_settings['captcha-type'] ) ? $emqa_general_settings['captcha-type'] : 'default';

	$is_old_version = $type_selected == 'google-recaptcha' ? true : false;
	if ( $type_selected == 'default' || $is_old_version ) {
		$number_1 = isset( $_POST['emqa-captcha-number-1'] ) ? intval( $_POST['emqa-captcha-number-1'] ) : 0;
		$number_2 = isset( $_POST['emqa-captcha-number-2'] ) ? intval( $_POST['emqa-captcha-number-2'] ) : 0;
		$result = isset( $_POST['emqa-captcha-result'] ) ? intval( $_POST['emqa-captcha-result'] ) : 0;

		if ( ( $number_1 + $number_2 ) === $result ) {
			return true;
		}

		return false;
	}

	return $res;
}

/**
* Get tags list of question
*
* @param int $quetion id of question
* @param bool $echo
* @return string
* @since 1.4.0
*/
function emqa_get_tag_list( $question = false, $echo = false ) {
	if ( !$question ) {
		$question = get_the_ID();
	}

	$terms = wp_get_post_terms( $question, 'emqa-question_tag' );
	$lists = array();
	if ( $terms ) {
		foreach( $terms as $term ) {
			$lists[] = $term->name;
		}
	}

	if ( empty( $lists ) ) {
		$lists = '';
	} else {
		$lists = implode( ',', $lists );
	}

	if ( $echo ) {
		echo $lists;
	}

	return $lists;
}


function emqa_is_front_page() {
	global $emqa_general_settings;

	if ( !$emqa_general_settings ) {
		$emqa_general_settings = get_option( 'emqa_options' );
	}

	if ( !isset( $emqa_general_settings['pages']['archive-question'] ) ) {
		return false;
	}

	$page_on_front = get_option( 'page_on_front' );

	if ( (int) $page_on_front === (int) $emqa_general_settings['pages']['archive-question'] ) {
		return true;
	}

	return false;
}

function emqa_has_question( $args = array() ) {
	global $wp_query;

	return $wp_query->emqa_questions->have_posts();
}

function emqa_the_question() {
	global $wp_query;

	$wp_query->emqa_questions->set( 'orderby', 'modified' );
	return $wp_query->emqa_questions->the_post();
}

function emqa_has_question_stickies() {
	global $wp_query;

	return isset( $wp_query->emqa_question_stickies ) ? $wp_query->emqa_question_stickies->have_posts() : false;
}

function emqa_the_sticky() {
	global $wp_query;

	return $wp_query->emqa_question_stickies->the_post();
}

function emqa_has_answers() {
	global $wp_query;

	return isset( $wp_query->emqa_answers ) ? $wp_query->emqa_answers->have_posts() : false;
}

function emqa_the_answers() {
	global $wp_query;

	return $wp_query->emqa_answers->the_post();
}

function emqa_get_answer_count( $question_id = false ) {

	if ( ! $question_id ) {
		$question_id = get_the_ID();
	}

	$answer_count = get_post_meta( $question_id, '_emqa_answers_count', true );

	if ( current_user_can( 'edit_posts' ) ) {
		return $answer_count;
	} else {
		$answer_private = get_post_meta( $question_id, 'emqa_answers_private_count', true );

		if ( empty( $answer_private ) ) {
			global $wp_query;
			$args = array(
				'post_type' => 'emqa-answer',
				'post_status' => 'private',
				'post_parent' => $question_id,
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields' => 'ids'
			);

			$private_answer = new WP_Query( $args );

			update_post_meta( $question_id, 'emqa_answers_private_count', count( $private_answer ) );
			$answer_private = count( $private_answer );
		}

		return (int) $answer_count - (int) $answer_private;
	}
}

function emqa_is_ask_form() {
	global $emqa_general_settings;
	if ( !isset( $emqa_general_settings['pages']['submit-question'] ) ) {
		return false;
	}

	return is_page( $emqa_general_settings['pages']['submit-question'] );
}

function emqa_is_archive_question() {
	global $emqa_general_settings;
	if ( !isset( $emqa_general_settings['pages']['archive-question'] ) ) {
		return false;
	}
	
	return is_page( $emqa_general_settings['pages']['archive-question'] );
}

function emqa_question_status( $question = false ) {
	if ( !$question ) {
		$question = get_the_ID();
	}

	return get_post_meta( $question, '_emqa_status', true );
}

function emqa_current_filter() {
	return isset( $_GET['filter'] ) && !empty( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';
}

function emqa_get_ask_link() {
	global $emqa_general_settings;

	return get_permalink( $emqa_general_settings['pages']['submit-question'] );
}

function emqa_get_question_link( $post_id ) {
	if ( 'emqa-answer' == get_post_type( $post_id ) ) {
		$post_id = emqa_get_question_from_answer_id( $post_id );
	}

	return get_permalink( $post_id );
}

function emqa_get_post_parent_id( $post_id = false ){
	if(!$post_id){
		return false;
	}

	$parent_id = wp_cache_get( 'emqa_'. $post_id .'_parent_id', 'emqa' );
	if( $parent_id ){
		return $parent_id;
	}

	$parent_id = wp_get_post_parent_id( $post_id );
	//cache
	if($parent_id){
		wp_cache_set( 'emqa_'. $post_id .'_parent_id', $parent_id, 'emqa', 15*60 );
	}
	
	return $parent_id;
}
?>