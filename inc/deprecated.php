<?php  

function emqa_submit_question_form(){
    _deprecated_function( __FUNCTION__, '1.2.6', 'emqa_load_template( "submit-question", "form" )' );
    emqa_load_template( 'submit-question', 'form' );
}

function emqa_user_question_number( $user_id ) {
    _deprecated_function( __FUNCTION__, '1.3.2', 'emqa_user_question_count( "user_id" )' );
    emqa_user_question_count( $user_id );
}

function emqa_user_answer_number( $user_id ) {
    _deprecated_function( __FUNCTION__, '1.3.2', 'emqa_user_answer_count( "user_id" )' );
	emqa_user_answer_count( $user_id );
}

function emqa_add_answer() {
	global $emqa;
	_deprecated_function( __FUNCTION__, '1.3.4', 'EMQA_Answer::insert' );
	$emqa->insert();
}

function emqa_require_field_submit_question() {
	_deprecated_function( __FUNCTION__, '1.4.2', '' );
}

function emqa_require_field_submit_answer() {
	_deprecated_function( __FUNCTION__, '1.4.2', '' );
}

function emqa_single_postclass() {
	_deprecated_function( __FUNCTION__, '1.4.2', '' );
}

function emqa_paged_query() {
	_deprecated_function( __FUNCTION__, '1.4.2', '' );
}

function emqa_title( $title ) {
	_deprecated_function( __FUNCTION__, '1.4.2.1', '' );
}

function emqa_get_author( $post_id = 0 ) {
	_deprecated_function( __FUNCTION__, '1.4.2.3', '' );
}

class Walker_Category_EMQA {
	public function __construct() {
		_deprecated_function( __FUNCTION__, '1.3.4', 'EMQA_Walker_Category' );
		new EMQA_Walker_Category();
	}
}

class Walker_Tag_EMQA {
	public function __construct() {
		_deprecated_function( __FUNCTION__, '1.3.4', 'EMQA_Walker_Tag' );
		new EMQA_Walker_Tag();
	}
}

?>