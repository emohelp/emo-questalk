<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'buddypress' ) ) {
	function buddypress() {
		return isset( $GLOBALS['bp'] ) ? $GLOBALS['bp'] : false;
	}
}

function dp_emqa_screen_questions() {
	add_action( 'bp_template_content', 'bp_emqa_question_content' );
	bp_core_load_template( apply_filters( 'bp_emqa_screen_question', 'members/single/plugins' ) );
}
function dp_emqa_screen_answers() {
	add_action( 'bp_template_content', 'bp_emqa_answer_content' );
	bp_core_load_template( apply_filters( 'bp_emqa_screen_question', 'members/single/plugins' ) );
}
function dp_emqa_screen_comments() {
	add_action( 'bp_template_content', 'bp_emqa_comment_content' );
	bp_core_load_template( apply_filters( 'bp_emqa_screen_question', 'members/single/plugins' ) );
}

//question
function bp_emqa_question_content() {
	add_filter('emqa_prepare_archive_posts', 'bp_emqa_question_filter_query',12);
	remove_action( 'emqa_before_questions_archive', 'emqa_archive_question_filter_layout', 12 );
	include(EMQA_DIR .'templates/bp-archive-question.php');
}
function bp_emqa_question_filter_query($query){
	$bp_displayed_user_id = bp_displayed_user_id();
	$query['author'] = $bp_displayed_user_id;
	return $query;
}

//answer
function bp_emqa_answer_content() {
	add_filter('emqa_prepare_archive_posts', 'bp_emqa_answer_filter_query',12);
	remove_action( 'emqa_before_questions_archive', 'emqa_archive_question_filter_layout', 12 );
	include(EMQA_DIR .'templates/bp-archive-question.php');	
}
function bp_emqa_answer_filter_query($query){
	$bp_displayed_user_id = bp_displayed_user_id();
	$post__in = array();
	
	$array = $query;
	$array['post_type'] = 'emqa-answer';
	$array['author'] = $bp_displayed_user_id;
	
	// add_filter( 'posts_groupby', 'bp_emqa_answers_groupby' );
	// use this function to fill per page
	while(count($post__in) < $query['posts_per_page']){
		$array['post__not_in '] = $post__in;
		$results = new WP_Query( $array );
		
		if($results->post_count > 0){
			foreach($results->posts as $result){
				$post__in[] = $result->post_parent;
			}
		}else{
			break;
		}
	}
	if(empty($post__in)){
		$post__in = array(0);
	}
	$query['post__in'] = $post__in;
	$query['orderby'] = 'post__in';

	return $query;
}
