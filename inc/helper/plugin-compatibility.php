<?php

/** Advanced Ads **/
add_filter( 'advanced-ads-ad-select-args', 'emqa_advanced_ads_select_args', 99 );
function emqa_advanced_ads_select_args( $args ) {
	if ( 'emqa-answer' == get_post_type() || 'emqa-question' == get_post_type() ) {
		$args['post']['post_type'] = get_post_type();
	}

	return $args;
}

/** Facebook Comments **/
add_filter( 'get_post_metadata', 'emqa_disable_wpdevart_facebook_comment', 10, 3 );
function emqa_disable_wpdevart_facebook_comment( $value, $post_id, $meta_key ) {
	$emqa_options = get_option( 'emqa_options', array() );
	if ( 
			'_disabel_wpdevart_facebook_comment' == $meta_key
			&& 
			( 
				'emqa-question' == get_post_type( $post_id ) // is single question
				|| 
				'emqa-answer' == get_post_type( $post_id ) // is single answer
				||
				(int) $emqa_options['pages']['submit-question'] == (int) $post_id // is submit question page
				||
				(int) $emqa_options['pages']['archive-question'] == (int) $post_id // is archive page
			)
		) {
		$value = 'disable';
	}

	return $value;
}

/** Facebook Comments Plugin **/
add_filter( 'the_content', 'emqa_disable_facebook_comments_plugin', 10 );
function emqa_disable_facebook_comments_plugin( $content ) {
	if ( 'emqa-question' == get_post_type() || 'emqa-answer' == get_post_type() ) {
		remove_filter('the_content', 'fbcommentbox', 100);
	}
	return $content;
}