<?php

class EMQA_Rewrite {
	public function __construct() {
		add_action( 'after_switch_theme', 'flush_rewrite_rules' );
	}

	function update_term_rewrite_rules() {
		//add rewrite for question taxonomy
		global $wp_rewrite;
		$options = get_option( 'emqa_options' );

		$page_id = $options['pages']['archive-question'];
		$question_list_page = get_post( $page_id );
		$rewrite_category = isset( $options['question-category-rewrite'] ) ? sanitize_title( $options['question-category-rewrite'] ) : 'question-category';
		$rewrite_tag = isset( $options['question-tag-rewrite'] ) ? sanitize_title( $options['question-tag-rewrite'] ) : 'question-tag';

		if ( $question_list_page ) {
			$emqa_rewrite_rules = array(
				'^'.$question_list_page->post_name.'/'.$rewrite_category.'/([^/]*)' => 'index.php?page_id='.$page_id.'&taxonomy=emqa-question_category&emqa-question_category=$matches[1]',
				'^'.$question_list_page->post_name.'/'.$rewrite_tag.'/([^/]*)' => 'index.php?page_id='.$page_id.'&taxonomy=emqa-question_tag&emqa-question_tag=$matches[1]',
			);
			foreach ( $emqa_rewrite_rules as $regex => $redirect ) {
				add_rewrite_rule( $regex, $redirect, 'top' );
			}
			// Add permastruct for pretty link
			add_permastruct( 'emqa-question_category', "{$question_list_page->post_name}/{$rewrite_category}/%emqa-question_category%", array( 'with_front' => false ) );
			add_permastruct( 'emqa-question_tag', "{$question_list_page->post_name}/{$rewrite_tag}/%emqa-question_tag%", array( 'with_front' => false ) );
		}
	}
}
?>
